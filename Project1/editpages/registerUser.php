<!DOCTYPE html>
<?php

// register user or register yourself for something

    spl_autoload_register(function($class){
        require_once("./../utils/$class.class.php");
    });

    (CurrentUser::setUpSessionWithCheck());

    // make vars
    $db = (new DB());

    // Header
    echo (PageFormat::getDocumentHeader("Register User", "./.."));


    // check to submit form
    handleFinish();
    handleDelete();

    /*

things in the url  
- idattendee
- id
- isEvent
- isEdit
*/


    echo "<h1>Register</h1>";

    $idattendee = isset($_GET["idattendee"]) ? $_GET["idattendee"] : -1;
    $id = isset($_GET["id"]) ? $_GET["id"] : -1;
    $isEvent = isset($_GET["isEvent"]) ? $_GET["isEvent"] : "false";
    $isEdit = isset($_GET["isEdit"]) ? $_GET["isEdit"] : "false";

    // echo "<p style=\"color: blue\">Id Attendee: $idattendee</p>";
    // echo "<p style=\"color: blue\">Id: $id</p>";
    // echo "<p style=\"color: blue\">Is Event: $isEvent</p>";
    // echo "<p style=\"color: blue\">Is Edit: $isEdit</p>";

/*

form feilds
- select for user (only can see for certain roles)
- select for event
- toggle for paid?

*/
    
?>
 <form method="POST">

    <?php

        if (($isEdit == "true" || $isEdit == 1) && $idattendee != -1 && $id != -1) {
            
            // get and print the name of the user
            echo printName($db, $idattendee);

            // get and print the event the user is registered for
            echo printEventOrSession($db, $isEvent, $id);
            


        } else {
            // not editing a registration

            // User is not set, select a user
            if ($idattendee == -1) {
                echo getSelectUser($db);
            } else {
                echo printName($db, $idattendee);
            }

            // Id is not set and isEvent is true, so its an event picker
            if ($id == -1 && ($isEvent == "true" || $isEvent == 1)) {
                echo getSelectEventOrSession($db, true);
            }

            // Id is not set and isEvent is false, so it's a session picker
            if ($id == -1 && ($isEvent == "false" || $isEvent == 0)) {
                echo getSelectEventOrSession($db, false);
            } 

            // if they are not editing the session or event then print it out
            if ($id != -1 ) {
                echo printEventOrSession($db, $isEvent, $id);
            }
        }

        if ($isEvent == "true" || $isEvent == 1) {
            echo getCheckbox($db);
        }
        
    ?>

    <!-- submit -->
    <?php 

        if ((($isEdit == "true" || $isEdit == 1) && ($isEvent == "true" || $isEvent == 1)) || ($isEdit == "false")) {
            // if it is not edit, show it
            // if it is edit, then only show if you are editing an event registration
            echo "<input type=\"submit\" class=\"button\" name=\"finish\" value=\"Finish\" >";
        }
        if ($isEdit == "true" || $isEdit == 1) {
            echo "<input type=\"submit\" class=\"button delete button2\" name=\"delete\" value=\"Unregister\" >";
        }
    ?>
</form>

  
<?php

    // Footer
    echo (PageFormat::getDocumentFooter());
    

    function getSelectUser(DB $db) {

        $options = "";

        // load the available venues
        $users = $db->associativeSelect("SELECT idattendee, name FROM attendee", []);

        foreach ($users as $key => $value) {
            $options.= "<option value=\"{$value['idattendee']}\">{$value['name']}</option>\n";
        }

        $selectStr = "<label for=\"user\">Select User</label><br>\n<select class=\"select\" name=\"user\" id=\"user\">\n$options</select><br>";

        return $selectStr;

    }

    function getSelectEventOrSession(DB $db, String $isEvent) {
    
            $options = "";

            // load the available venues
            
            if ($isEvent == true) {
                $title = "Select Event";
                $result = $db->associativeSelect("SELECT idevent, name FROM event", []);

            } else {
                $title = "Select Session";
                $result = $db->associativeSelect("SELECT idsession, name FROM session", []);

            }

            foreach ($result as $key => $value) {
                $id = $isEvent == true ? $value['idevent'] : $value["idsession"];
                $options.= "<option value=\"{$id}\">{$value['name']}</option>";
            }

            $selectStr = "<label for=\"event\">$title</label><br>\n<select class=\"select\" name=\"event\" id=\"event\">\n$options</select><br>";

            return $selectStr;
    
        }

        function getCheckbox(DB $db) {

            $checked = "";

            if (isset($_GET["isEdit"]) && ($_GET["isEdit"] == "true" || $_GET["isEdit"] == 1)) {
                $hasPaid = $db->associativeSelect("SELECT paid FROM attendee_event WHERE attendee = :attendee AND event = :event", [":attendee"=>$_GET["idattendee"], ":event"=>$_GET["id"]]);

                if (count($hasPaid) < 1) {
                    // if the user doesn't show as registered for the event, they can not edit a registration
                    // echo "is not registered for this event";
                    header("Location: ../home.php");
                    exit;
                }

                if ($hasPaid[0]["paid"] == 1) {
                    $checked = "checked";
                }
            } 

            $checkboxString = "<label for=\"paid\">\n<input type=\"checkbox\" id=\"paid\" name=\"paid\" value=\"paid\" $checked>Have you paid?</label><br><br>";

            return $checkboxString;

        }

        function printName(DB $db, String $idattendee) {
            $user = $db->associativeSelect("SELECT name FROM attendee WHERE idattendee = :id", [":id"=>$idattendee]);
            if (count($user) < 1) {
                // if the user doesn't exist you can not edit the registration
                // echo "user doesn't exist";
                header("Location: ../home.php");
                exit;
            }
            return "<p>User: {$user[0]['name']}</p><br>";
        }

        function printEventOrSession(DB $db, String $isEvent, String $id) {

            if ($isEvent == "true" || $isEvent == 1) {
                $event = $db->associativeSelect("SELECT name FROM event WHERE idevent = :id", [":id"=>$id]);
            } else {
                $event = $db->associativeSelect("SELECT name FROM session WHERE idsession = :id", [":id"=>$id]);
            }
            
            if (count($event) < 1) {
                // if the event/session doesn't exist you can not edit the registration
                // echo "event doesn't exist";
                header("Location: ../home.php");
                exit;
            }
            if ($isEvent == "true" || $isEvent == 1) {
                return "<p>Event: {$event[0]['name']}</p><br>";
            } else {
                return "<p>Session: {$event[0]['name']}</p><br>";
            }
        }
    


    function handleFinish() {
        // handle creation / edit
        if(isset($_POST['finish'])) { 

            var_dump($_POST);

            // create database
            require_once("./../utils/DB.class.php");
            $db = (new DB());

            $idattendee = isset($_GET["idattendee"]) ? $_GET["idattendee"] : -1;
            $id = isset($_GET["id"]) ? $_GET["id"] : -1;
            $isEvent = isset($_GET["isEvent"]) ? $_GET["isEvent"] : "true";
            $isEdit = isset($_GET["isEdit"]) ? $_GET["isEdit"] : "false";

            // user can not enter data so we don't need to validate or check if everything is answered bc it already has a default value for each selected

            $hasPaid = isset($_GET['paid']) ? 1 : 0; // if the checkbox is selected, then it shows up and if its not it doesn't 

            // check if we are editing
            // i'm not sure why it has a new line in front, but it works with the new line so i'm not gonna touch it
            if (($isEdit == "true" || $isEdit == 1) && ($isEvent == "true" || $isEvent == 1)) {
                // is edit
                echo "update";
                // you can only update if you are doing it for an event because there is nothing to change except to delete for session bc you don't have paid
                $parameters = [
                    ":paid"=>$hasPaid,
                    ":event"=>$id,
                    ":attendee"=>$idattendee
                ];
                $success = $db->update("UPDATE attendee_event SET paid = :paid WHERE event = :event AND attendee = :attendee", $parameters);
                
            } else {
                // is create
                echo "create";

                $parameters = [
                    ":event"=>$id,
                    ":attendee"=>$_POST['user']
                ];

                if ($idattendee != -1) {
                    $parameters[":attendee"] = $idattendee;
                }

                if ($isEvent == "true" || $isEvent == 1) { 
                    // event
                    echo " event registration";

                    $parameters[":paid"] = $hasPaid;
                    $success = $db->insert("INSERT INTO attendee_event (event, attendee, paid) VALUES (:event, :attendee, :paid) ", $parameters);

                } else {
                    // session
                    echo " session registration";

                    // make sure that the user has registered for the event
                    $eventRegistration = $db->associativeSelect("SELECT session.name, attendee_event.event FROM session JOIN attendee_event ON attendee_event.event = session.event WHERE idsession = :idsession AND attendee_event.attendee = :idattendee", [":idsession"=>$parameters[':event'], ":idattendee"=>$parameters[':attendee']]);

                    if (count($eventRegistration) <= 0) {
                        echo "<p class=\"error\">Register for the event before registering for the session</p>";
                        return;
                    }

                    $success = $db->insert("INSERT INTO attendee_session (session, attendee) VALUES (:event, :attendee) ", $parameters);
                }
                
            }

            
            // send me home!!!
            echo "<p style=\"color: red\">Success: $success</p>";
            // if it can't create it, it will fail silently and send the person home to then realize for themselves that it didn't work because their name was not unique or something like that
            header("Location: ../home.php");
            exit;
        } 
    }

    function handleDelete() {
        
        if(isset($_POST['delete'])) { 

           require_once("./../utils/DB.class.php");
           $db = (new DB());

            $idattendee = isset($_GET["idattendee"]) ? $_GET["idattendee"] : -1;
            $id = isset($_GET["id"]) ? $_GET["id"] : -1;
            $isEvent = isset($_GET["isEvent"]) ? $_GET["isEvent"] : "true";
            $isEdit = isset($_GET["isEdit"]) ? $_GET["isEdit"] : "false";

            if ($isEvent == "true" || $isEvent == 1) {
                
                $result = $db->delete("DELETE FROM attendee_event WHERE event = :event AND attendee = :attendee ", [":event"=>$id, ":attendee"=>$idattendee]);

            } else {
                
                $result = $db->delete("DELETE FROM attendee_session WHERE session = :session AND attendee = :attendee ", [":session"=>$id, ":attendee"=>$idattendee]);
            }

        
            var_dump($result);
        //    header("Location: ../home.php");
        //    exit;
        } 
    }
?>
