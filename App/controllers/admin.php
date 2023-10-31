<?php
require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/../models.php";

use sirJuni\Framework\View\VIEW;


class AdminController {
    function show($request) {
        VIEW::init('admin.html');
    }

    function addUser($request) {
        // grab the user details
        $username = $request->formData('username');
        $email = $request->formData('email');
        $password = md5($request->formData('password'));

        // add the user to the data base
        // and return OK
        $db = new DB();

        if ($db->addUser($username, $email, $password)) {
            echo("OK");
        }
        else {
            echo("Failed");
        }
    }

    function dumpUsers($request) {
        // return all users;
        $db = new DB();

        $all_data = $db->dump_users();

        // set json header
        header("Content-Type: application/json");
        echo json_encode($all_data);
    }

    function showUser($request) {
        $email = $request->formData('email');

        $db = new DB();

        if ($email == '%') {
            $this->dumpUsers($request);
        }
        else {

            $user = $db->getUser($email);

            header('Content-Type: application/json');
            echo json_encode($user);
        }
    }

    function removeUser($request) {
        $email = $request->formData('email');

        // delete user
        $db = new DB();

        if ($db->deleteUser($email)) {
            echo "OK";
        }
        else {
            echo "Failed";
        }
    }

    function updateUser($request) {
        $target_email = $request->formData('target_email');
        $email = $request->formData('email');
        $username = $request->formData('username');
        $password = md5($request->formData('password'));

        $db = new DB();

        if ($db->updateUser($target_email, $username, $email, $password)) {
            echo "OK";
        }
        else {
            echo "Failed";
        }
    }
}
?>