<!DOCTYPE html>
<?php

    spl_autoload_register(function($class){
        require_once("./utils/$class.class.php");
    });

    (CurrentUser::setUpSessionWithCheck());
    echo (PageFormat::getDocumentHeader("Your Registrations"));
?>

        <!-- Body Content -->
        <h1>Your Registrations</h1>

        <!-- Events -->
        <?php
            $db = (new DB());
            // get the current user's id
            $currentUser = (CurrentUser::getCurrentUser());
            $currentId = $currentUser->getIdattendee();
            
            // select the events that this user is registered for
            $events = $db->objectSelect("SELECT idevent, name, datestart, dateend, numberallowed, venue FROM event INNER JOIN attendee_event ON event.idevent = attendee_event.event WHERE attendee_event.attendee = :id ", [":id"=>$currentId], "Event");
            
            // print the events
            foreach ($events as $key => $value) {
                echo $value->getHTML(false);
            }
          
        ?>
        
    
<?php
    echo (PageFormat::getDocumentFooter());
?>
