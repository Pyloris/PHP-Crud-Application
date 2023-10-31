<?php
require_once __DIR__ . "/../vendor/autoload.php";

use sirJuni\Framework\Model\Database;
use sirJuni\Framework\Helper\HelperFuncs;

// import config
require_once __DIR__ . "/config.php";

class DB extends Database {
    protected $db;

    function __construct() {
        $this->dbConnect();
    }

    // add a user
    function addUser($username, $email, $password){
        $query = "INSERT INTO users (username, email, password) VALUES (:name, :email, :pass)";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':name', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':pass', $password);

            // execute the query
            if ($stmt->execute()){
                return TRUE;
            }
            else {
                return FALSE;
            }
        } catch (PDOException $e) {
            HelperFuncs::report($e);
            return FALSE;
        }
    }

    // get a user
    function getUser($email) {
        $query = "SELECT * FROM users WHERE email=:email";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':email', $email);
            
            if ($stmt->execute()) {
                return $stmt->fetch();
            }
            else {
                return FALSE;
            }
        }
        catch (PDOException $e) {
            HelperFuncs::report($e);
            return FALSE;
        }
    }

    // get all users
    function dump_users() {

        $query = "SELECT * FROM users";

        try {
            $stmt = $this->db->query($query);

            return $stmt->fetchAll();
        }
        catch (PDOException $e) {
            HelperFuncs::report($e);
            return FALSE;
        }
    }

    // update user
    function updateUser($email, $new_username, $new_email, $new_password) {
        $query = "UPDATE users SET email=:new_email, username=:new_username, password=:new_password WHERE email=:prev_email";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':prev_email', $email);
            $stmt->bindParam(':new_email', $new_email);
            $stmt->bindParam(':new_username', $new_username);
            $stmt->bindParam(':new_password', $new_password);

            if ($stmt->execute() and $stmt->rowCount() > 0) {
                return TRUE;
            }
            else {
                return FALSE;
            }
            return TRUE;
        }
        catch (PDOException $e) {
            HelperFuncs::report($e);
            return FALSE;
        }
    }

    // delete user
    function deleteUser($email) {
        $query = "DELETE FROM users WHERE email=:email";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':email', $email);

            if ($stmt->execute() and $stmt->rowCount() > 0) {
                return TRUE;
            }
            else {
                return FALSE;
            }
        }
        catch (PDOException $e) {
            HelperFuncs::report($e);
            return FALSE;
        }
    }

    function bindMailToToken($email, $token, $expiry) {
        $query = "INSERT INTO tokens (email, token, expiry) VALUES (:email, :token, :expiry)";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':expiry', $expiry);

            if ($stmt->execute()) {
                return TRUE;
            }
            else {
                return FALSE;
            }
        }
        catch (PDOException $e) {
            HelperFuncs::report($e);
            return FALSE;
        }
    }

    function getTokenBinding($token) {
        $query = "SELECT email, token, expiry FROM tokens WHERE token=:token";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':token', $token);
            
            if ($stmt->execute()) {
                return $stmt->fetch();
            }
            else {
                return FALSE;
            }
        }
        catch (PDOException $e) {
            HelperFuncs::report($e);
            return FALSE;
        }
    }

    function changePassword($email, $password) {
        $query = "UPDATE users SET password=:new_password WHERE email=:email";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':new_password', $password);

            if ($stmt->execute() and $stmt->rowCount() > 0) {
                return TRUE;
            }
            else {
                return FALSE;
            }
            return TRUE;
        }
        catch (PDOException $e) {
            HelperFuncs::report($e);
            return FALSE;
        }
    }

    function deleteTokenBinding($token) {
        $query = "DELETE FROM tokens WHERE token=:token";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':token', $token);

            if ($stmt->execute() and $stmt->rowCount() > 0) {
                return TRUE;
            }
            else {
                return FALSE;
            }
        }
        catch (PDOException $e) {
            HelperFuncs::report($e);
            return FALSE;
        }
    }
}