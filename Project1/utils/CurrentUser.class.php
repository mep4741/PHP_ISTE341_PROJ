<?php

class CurrentUser {

    public static function getCurrentUser($firstFilePath = ".") {

        $userid = $_SESSION['userId'];
        $db = new DB();
        $user = $db->objectSelect("SELECT * FROM attendee WHERE idattendee = :id ", [":id" => $userid], "Attendee", $firstFilePath);

        if (count($user) < 1) {
            self::logout();
            return null;
        }

        return $user[0];

    }

    public static function login(String $username, String $password) {

        $hashedPassword = Utils::hashPassword($password);

        // check if name and password match an existing user
        $db = new DB();
        $userID = $db->associativeSelect("SELECT idattendee FROM attendee WHERE name = :name AND password = :password ", [":name" => $username, ":password" => $hashedPassword]);

        if (count($userID) < 1) {
            // the user does not exist
            return false;
        }
        
        // user does exist

        // set session variable
        $_SESSION['loggedIn'] = true;
        $_SESSION['userId'] = $userID[0]['idattendee'];
        return true;

    } // login()

    public static function logout() {
        self::endSession();
    } // logout()

    
    public static function setUpSession() {
        // set up my session
        session_name("MaijaISTE341Project1");
        session_start();

    } // setUpSession()

    public static function setUpSessionWithCheck() {
        // set up my session
        self::setUpSession();

        // check to see if they are not logged in
        if (empty($_SESSION['loggedIn']) || $_SESSION['loggedIn'] == 0) {
            
            // echo "Not logged in, send them to login";
            header("Location: login.php");
            exit;

        } // if logged in

    } // setUpSession()

    // unset and destroy the cookies and session
    public static function endSession() {
        session_unset(); // unset vars so don't accidentally use
        // remove the session name cookie so no one can use the old session name
        if (isset($_COOKIE[session_name()])) {
            $params = session_get_cookie_params();
            setcookie(session_name(), "", 1, $params['domain'], $params['secure'], $params['httponly']);
        } // if its in the url it will remove itself 
        session_destroy(); // destroy
    } // endSession()

} // Login