<?php
require_once __DIR__ . "/../../vendor/autoload.php";

use sirJuni\Framework\View\VIEW;
use sirJuni\Framework\Middleware\Auth;
use sirJuni\Framework\Helper\HelperFuncs;
use sirJuni\Framework\Components\OAuth;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// grab the dataabase
require_once __DIR__ . "/../models.php";

VIEW::set_path(__DIR__ . "/../../templates");


class AuthController {
    function login($request) {
        if ($request->method() == "GET") {
            VIEW::init("login.html");
        }
        else if ($request->method() == "POST") {

            // grab db instance
            $db = new DB();

            // grab user details
            $email = $request->formData('email');
            $password = $request->formData('password');

            // grab the user with above email
            $user = $db->getUser($email);

            // if user exists and passwords match
            if ($user and md5($password) == $user['password']){

                // log the user in
                Auth::login($user);

                // redirect to admin page
                HelperFuncs::redirect("/crud_app/admin");
            }
            else {
                // if details are wrong
                HelperFuncs::redirect("/crud_app/login?message=Wrong username or password");
            }
        }
    }

    function signup($request) {
        if ($request->method() == "GET") {
            VIEW::init("signup.html");
        }
        else if ($request->method() == "POST") {

            // get a db instance
            $db = new DB();

            // grab the user details
            $username = $request->formData('username');
            $email = $request->formData('email');
            $password = md5($request->formData('password'));

            // add the user to the database
            if ($db->addUser($username, $email, $password)) {

                // login the user and redirect to admin page;
                $user = $db->getUser($email);
                Auth::login($user);

                HelperFuncs::redirect("/crud_app/admin");
            }
            else {
                // redirect back to signup page
                HelperFuncs::redirect("/crud_app/signup?message=Error on backend");
            }

        }
    }


    function logout($request) {
        Auth::logout();
        HelperFuncs::redirect("/crud_app/login");
    }


    function forgot_pass($request) {
        VIEW::init("forgot.html");
    }

    function reset_link($request) {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        $mail->Username = "letmeuseit158@gmail.com";
        $mail->Password = 'wrvj tzjv vgru wyiz';

        $mail->Port = 465;
        $mail->SMTPSecure= 'ssl';

        //sender information
        $mail->setFrom('CRUDAPP@gmail.com', 'CRUDMASTER');

        //receiver email address and name
        $mail->addAddress('shoaibwani010@gmail.com', 'Shoaib Wani'); 
        
        $mail->isHTML(true);

        // generate reset link for email account
        $email = $request->formData('email');
        // grab the user
        $db = new DB();
        $user = $db->getUser($email);

        if ($user) {
            $token = hash('sha256', $email . time());
            $expiry = (time() + 60) . "";

            // bind token to target email/account
            if (!$db->bindMailToToken($email, $token, $expiry)) {
                VIEW::init("status.html", ['message' => 'Error occurred on backend. Sorry we are trying to fix it now!! :(']);
                exit();
            }
        }
        else {
            $message = "No user found with this Email!";
            VIEW::init("status.html", ['message' => $message]);
            exit();
        }
        
        $mail->Subject = 'Password Reset Link for your account.';
        $mail->Body    = <<<DOC
        <h4> Visit below link to reset your password </h4>
        <p> <a href="http://localhost/crud_app/reset?token=$token">Reset Password</a> </p>
        DOC;

        // Send mail   
        if (!$mail->send()) {
            $message = 'Email not sent an error was encountered: ' . $mail->ErrorInfo;
        } else {
            $message = 'Check your INBOX';
        }

        $mail->smtpClose();

        VIEW::init("status.html", ['message' => $message]);
    }


    function reset($request) {
        $db = new DB();
        if ($request->method() == "GET") {
            if ($request->queryData('token')) {
                $token = $request->queryData('token');
                // grab the db entry
                $binding = $db->getTokenBinding($token);
                if ($binding) {
                    $target_mail = $binding['email'];
                    $expiry = (float) $binding['expiry'];

                    if (time() > $expiry) {
                        VIEW::init("status.html", ['message'=>'Token has already expired. Link is just valid for 1 min. Do it fast next time! :)']);
                        $db->deleteTokenBinding($token);
                        exit();
                    }
                    else {
                        VIEW::init("reset.html");
                    }
                }
                else {
                    VIEW::init('status.html', ['message' => 'Wrong Token supplied!']);
                    exit();
                }
            }

        }
        else if ($request->method() ==  "POST") {
            $token = $request->formData('token');
            $binding = $db->getTokenBinding($token);

            if ($binding) {
                $target_email = $binding['email'];
                $expiry = (float) $binding['expiry'];

                if (time() > $expiry) {
                    VIEW::init("status.html", ['message' => 'Bro, token has expired. Generate another reset link and visit it under a minute. You Got IT? :)']);
                    $db->deleteTokenBinding($token);
                    exit();
                }
                else {
                    if ($db->changePassword($target_email, md5($request->formData('password')))) {
                        $db->deleteTokenBinding($token);
                        VIEW::init("status.html", ['message' => 'Password has been changed. Enjoy.']);
                    }
                    else {
                        VIEW::init("status.html", ['message' => 'Password could not be changed for some reason. Sorry!']);
                    }

                }
            }
            else {
                VIEW::init("status.html", ['message' => 'You are providing a token not generated by my application. Don"t fuck with me, i"ll fuck you back :|']);
                exit();
            }
        }
    }

}


class OAuthController {
    public function login($request) {
        $oauth = new OAuth('secret.json', 'https://af5a-122-161-241-94.ngrok-free.app/crud_app/callback', ['userinfo.email', 'userinfo.profile']);
    }

    public function oauth_callback($request) {
        $oauth = new OAuth('secret.json', 'https://af5a-122-161-241-94.ngrok-free.app/crud_app/callback', ['userinfo.email', 'userinfo.profile']);
        $userinfo = $oauth->getUserInfo();

        // see if user is registered
        // otherwise save him to data base;
        $db = new DB();

        if ($db->getUser($userinfo['email'])) {

            Auth::login($db->getUser($userinfo['email']));
            HelperFuncs::redirect("/crud_app/admin");
        }
        else {

            // create the account
            $db->addUser($userinfo['username'], $userinfo['email'], 'x');

            // login the user
            Auth::login($db->getUser($userinfo['email']));
            HelperFuncs::redirect('/crud_app/admin');
        }
    }
}
?>