<!DOCTYPE html>
<?php

    spl_autoload_register(function($class){
        require_once("./utils/$class.class.php");
    });

    (CurrentUser::setUpSessionWithCheck());
    echo (PageFormat::getDocumentHeader("Admin"));
?>

        <!-- Body Content -->
        <h1>Actions</h1>

            <div class="action-block">
                <?php 

                    // get the user role and see which to display
                    $userRole = (CurrentUser::getCurrentUser())->getRole();

                    echo "<div><a href=\"editpages/editEvent.php\">Create Event</a></div>";
                    echo "<div><a href=\"editpages/editSession.php\">Create Session</a></div>";
                
                    if ($userRole == '1') {
                        echo "<div><a href=\"editpages/editVenue.php\">Create Venue</a></div>";
                        echo "<div><a href=\"editpages/editAttendee.php\">Create User</a></div>";
                    }
                
                ?>

                
            </div>

        <h1>Users</h1>
        <?php 
            $db = (new DB());
            $users = $db->objectSelect("SELECT * FROM attendee", [], "Attendee");
            
            foreach ($users as $key => $value) {
                echo $value->getHTML();
            }
        ?>
    
<?php

            if ($userRole == "1") {
                echo "<h1>Venues</h1>";
                $venues = $db->objectSelect("SELECT * FROM venue", [], "Venue");

                foreach ($venues as $key => $value) {
                    echo "<a class=\"user-row-link\" href=\"editpages/editVenue.php?id={$value->getIdvenue()}\"><p class=\"user-row-link\"><div class=\"user-row\"><h2>{$value->getName()}</h2><p>Capacity: {$value->getCapacity()}</p></div></p></a>";
                }
            }

    echo (PageFormat::getDocumentFooter());
?>
