<?php

class Utils {

    public static function formatDate(String $date) {
        return date("F j\, Y", strtotime($date));
    }

    public static function hashPassword(String $password) : String {
        return hash("sha256", $password);
    }

    public static function sanitize(String $str) : String {
        $newStr = trim($str); // remove spaces
        $newStr = stripslashes($newStr); // remove slashes
        $newStr = strip_tags($newStr); // remove html tags
        return $newStr;
    }

    public static function validateData($str, $min, $max) {
        return isset($str) && strlen($str) > $min && strlen($str) < $max;
    }

    // display register or unregister depending if the user is registered for the event/session or not
    public static function getRegisterButton(bool $isEvent, String $id) {

        require_once "./utils/DB.class.php";

        $db = new DB();        
        // get the current user's id
        $currentUser = (CurrentUser::getCurrentUser());
        $currentId = $currentUser->getIdattendee();

        // check if user is registered for this event
        if ($isEvent) {
            $registration = $db->associativeSelect("SELECT * FROM attendee_event WHERE event = :id AND attendee = :idattendee", [":id"=>$id, ":idattendee"=>$currentId]);
        } else {
            $registration = $db->associativeSelect("SELECT * FROM attendee_session WHERE session = :id AND attendee = :idattendee", [":id"=>$id, ":idattendee"=>$currentId]);
        }
        
        $linkStr = "./editpages/registerUser.php?idattendee=$currentId&isEvent=$isEvent&isEdit=false&id=$id";

        if (count($registration) > 0) {
            // they are registered
            return "<a href=\"./editpages/registerUser.php?idattendee=$currentId&isEvent=$isEvent&isEdit=true&id=$id\"><button class=\"unregister\">Unregister</button></a>";
        } else {
            // they are not registered
            if ($isEvent) {
                return "<a href=\"$linkStr\"><button>Register</button></a>";
            }
            return "<a href=\"$linkStr\"><button class=\"button2\">Register</button></a>";
        }
    }

}