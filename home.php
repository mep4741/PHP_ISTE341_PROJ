<!DOCTYPE html>
<?php

    spl_autoload_register(function($class){
        require_once("./utils/$class.class.php");
    });

    (CurrentUser::setUpSessionWithCheck());
    echo (PageFormat::getDocumentHeader("Events"));
?>

        <!-- Body Content -->
        <h1>All Events</h1>
        
        <!-- Events -->
        <?php
            $db = (new DB());
            $events = $db->objectSelect("SELECT * FROM event", [], "Event");
            
            foreach ($events as $key => $value) {
                echo $value->getHTML();
            }
          
            
            // very last thing -> add fab
            // get the role of the current user
            $role = (CurrentUser::getCurrentUser())->getRole();
            if ($role != '3') {
                echo (PageFormat::getAddFAB());
            }
        ?>

    
<?php
    echo (PageFormat::getDocumentFooter());
?>
