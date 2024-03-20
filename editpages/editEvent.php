<!DOCTYPE html>
<?php

    spl_autoload_register(function($class){
        require_once("./../utils/$class.class.php");
    });

    (CurrentUser::setUpSessionWithCheck());

    // make vars
    $db = (new DB());
    $isEdit = false;

    // Header
    echo (PageFormat::getDocumentHeader("Edit Events", "./.."));

    // check to submit form
    handleFinish();
    handleDelete();
    
    // am i editing or creating?
    if (isset($_GET['id'])) {
        
        // get the obj data
        $event = $db->objectSelect("SELECT * FROM event WHERE idevent = :id", [":id"=>$_GET['id']], "Event", "./..");

        // check if it exists
        $size = count($event);
        if ($size > 0) {
            echo "<h1>Edit Event</h1>";
            $isEdit = true;
        } else {
            echo "<h1>Create Event</h1>";
        }

    } else {
        echo "<h1>Create Event</h1>";
    }


    
    
?>
  
  <form method="POST">
    <label for="name">Event Name:</label><br>
    <input type="text" id="name" name="name" placeholder="Enter your event name" value="<?php 
        if ($isEdit) {
            echo $event[0]->getName();
        } 
        ?>"><br>
    
    <label for="startdate">Start Date:</label><br>
    <input type="date" id="startdate" name="startdate" value="<?php 
        if ($isEdit) {
            // get rid of the time, just do year
            echo substr($event[0]->getDatestart(), 0, 10); 
        } 
        ?>"><br>

    <label for="enddate">End Date:</label><br>
    <input type="date" id="enddate" name="enddate" value="<?php 
        if ($isEdit) {
            // get rid of the time, just do year
            echo substr($event[0]->getDateend(), 0, 10); 
        } 
        ?>"><br>

    <label for="numallowed">Number of people allowed:</label><br>
    <input type="number" id="numallowed" name="numallowed" placeholder=0 value="<?php 
        if ($isEdit) {
            // get rid of the time, just do year
            echo $event[0]->getNumberallowed(); 
        } 
        ?>"><br>
        
    <label for="venue">Choose a venue</label><br>
    <select class="select" name="venue" id="venue">
        <?php
            // load the available venues
            $venues = $db->associativeSelect("SELECT idvenue, name FROM venue", []);

            foreach ($venues as $key => $value) {
                if ($isEdit && $value['idvenue'] == $event[0]->getVenue()) {
                    echo "<option value=\"{$value['idvenue']}\" selected=\"selected\">{$value['name']}</option>";
                } else {
                    echo "<option value=\"{$value['idvenue']}\">{$value['name']}</option>";
                }
            }

        ?>
    </select><br> 
    <input type="hidden" name="isEdit" value="
        <?php 
            echo $isEdit;
        ?>" />


    <!-- submit -->
    <input type="submit" class="button" name="finish" value="Finish" />
    <?php 
        if ($isEdit) {
            echo "<input type=\"submit\" class=\"button delete button2\" name=\"delete\" value=\"Delete\" />";
        }
    ?>
</form>



  
<?php

    // Footer
    echo (PageFormat::getDocumentFooter());


    function handleFinish() {
        // handle creation / edit
        if(isset($_POST['finish'])) { 

            require_once("./../utils/Utils.class.php");

            // validate our params
            $hasName = Utils::validateData($_POST['name'], 0, 50);
            $hasStartDate = Utils::validateData($_POST['startdate'], 9, 11); 
            $hasEndDate = Utils::validateData($_POST['enddate'], 9, 11);
            $hasNumAllowed = isset($_POST['numallowed']) && !empty($_POST['numallowed']); // text feild only allows numbers
            // we don't need to check the venue bc it is in a select and has to have one thing selected
        


            // check if all the feilds are set
            if (!($hasName && $hasStartDate && $hasEndDate && $hasNumAllowed)) {
                echo "<p class=\"error\">Fill in all the feilds</p>";
                return;
            }

            // sanatize data
            $parameters = [
                ":name"=>Utils::sanitize($_POST['name']),
                ":datestart"=>Utils::sanitize($_POST['startdate']), 
                ":dateend"=>Utils::sanitize($_POST['enddate']), 
                ":numberallowed"=>$_POST['numallowed'], // only nums
                ":venue"=>$_POST['venue'] // select
            ];

            // create database
            require_once("./../utils/DB.class.php");
            $db = (new DB());


            // check if we are editing
            // i'm not sure why it has a new line in front, but it works with the new line so i'm not gonna touch it
            if ($_POST['isEdit'] == "\n1") {
                // is edit
                $parameters[":id"] = $_GET['id'];

                $success = $db->update("UPDATE event SET name = :name, datestart = :datestart, dateend = :dateend, numberallowed = :numberallowed, venue = :venue WHERE  idevent = :id", $parameters);
                
            } else {

                // is create
                $success = $db->insert("INSERT INTO event (name, datestart, dateend, numberallowed, venue) VALUES (:name, :datestart, :dateend, :numberallowed, :venue) ", $parameters);
                
                $currentUserId = (CurrentUser::getCurrentUser("./.."))->getIdattendee();

                if ($success >= 0) {
                    // set the current user as the event manager 
                    $setManager = $db->insert("INSERT INTO manager_event (event, manager) VALUES (:event, :manager) ", [":event" => $success, ":manager" => $currentUserId]);
                    echo "SetManager: $setManager";
                }

            }

            
            // send me home!!!
            echo "Success: $success";
            // if it can't create it, it will fail silently and send the person home to then realize for themselves that it didn't work because their name was not unique or something like that
            // header("Location: ../home.php");
            // exit;
        } 
    }

    function handleDelete() {
        
        if(isset($_POST['delete'])) { 

            require_once("./../utils/DB.class.php");
            $db = (new DB());

            // get the users and sessions
            $users = $db->associativeSelect("SELECT attendee FROM attendee_event WHERE event = :event ", [":event"=>$_GET['id']]);
            $sessions = $db->associativeSelect("SELECT idsession FROM session WHERE event = :event ", [":event"=>$_GET['id']]);   

            // if the event has users or sessions it can not be deleted
            if (count($users) > 0 || count($sessions) > 0) {
                echo "<p class=\"error\">There are users and or sessions associated with this event, remove before deleting</p>";
                return;
           }
           
           $result = $db->delete("DELETE FROM event WHERE idevent = :id ", [":id"=>$_GET['id']]);

           header("Location: ../home.php");
           exit;
        } 
    }

    
    
?>
