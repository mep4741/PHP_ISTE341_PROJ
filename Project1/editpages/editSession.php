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
    echo (PageFormat::getDocumentHeader("Edit Session", "./.."));

    // check to submit form
    handleFinish();
    handleDelete();

    
    // am i editing or creating?
    if (isset($_GET['id'])) {
        
        // get the obj data
        $session = $db->objectSelect("SELECT * FROM session WHERE idsession = :id", [":id"=>$_GET['id']], "Session", "./..");

        // check if it exists
        $size = count($session);
        if ($size > 0) {
            echo "<h1>Edit Session</h1>";
            $isEdit = true;
        } else {
            echo "<h1>Create Session</h1>";
        }

    } else {
        echo "<h1>Create Session</h1>";
    }
    
?>
  
  <form method="POST">
    <label for="name">Name:</label><br>
    <input type="text" id="name" name="name" placeholder="Enter your session name" value="<?php 
        if ($isEdit) {
            echo $session[0]->getName();
        } 
        ?>"><br>
    
    <label for="numallowed">Number of attendees allowed:</label><br>
    <input type="number" id="numallowed" name="numallowed" placeholder=0 value="<?php 
        if ($isEdit) {
            echo $session[0]->getNumberallowed();
        } 
        ?>"><br>


    <label for="event">Choose an event</label><br>
    <select class="select" name="event" id="event">
        <?php
            // load the available venues if they are a event manager, only show 
            $currentUser = (CurrentUser::getCurrentUser("./.."));
            if ($currentUser->getRole() == "2") {
                $events = $db->associativeSelect("SELECT idevent, name FROM event INNER JOIN manager_event ON event.idevent = manager_event.event WHERE manager_event.manager = :id ", [":id"=>$currentUser->getIdattendee()]);
            } else {
                $events = $db->associativeSelect("SELECT idevent, name FROM event", []);
            }


            foreach ($events as $key => $value) {
                if ($isEdit && $value['idevent'] == $session[0]->getEvent()) {
                    echo "<option value=\"{$value['idevent']}\" selected=\"selected\">{$value['name']}</option>";
                } else {
                    echo "<option value=\"{$value['idevent']}\">{$value['name']}</option>";
                }
            }

        ?>
    </select><br>

    <label for="startdate">Start Date:</label><br>
    <input type="date" id="startdate" name="startdate" value="<?php 
        if ($isEdit) {
            // get rid of the time, just do year
            echo substr($session[0]->getStartdate(), 0, 10); 
        } 
        ?>"><br>

    <label for="enddate">End Date:</label><br>
    <input type="date" id="enddate" name="enddate" value="<?php 
        if ($isEdit) {
            // get rid of the time, just do year
            echo substr($session[0]->getEnddate(), 0, 10); 
        } 
        ?>"><br>

    
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

            var_dump($_POST);
            
            $hasName = Utils::validateData($_POST['name'], 0, 50);
            $hasNumAllowed = isset($_POST['numallowed']) && !empty($_POST['numallowed']); // text feild only allows numbers
            $hasStartDate = Utils::validateData($_POST['startdate'], 9, 11); 
            $hasEndDate = Utils::validateData($_POST['enddate'], 9, 11);
            // we don't need to check the event bc it is in a select and has to have one thing selected

            // check if all the feilds are set
            if (!($hasName && $hasStartDate && $hasEndDate && $hasNumAllowed)) {
                echo "<p class=\"error\">Fill in all the feilds</p>";
                return;
            }

            // sanatize data
            $parameters = [
                ":name"=>Utils::sanitize($_POST['name']),
                ":numberallowed"=>$_POST['numallowed'], // only nums
                ":event"=>$_POST['event'], // select
                ":datestart"=>Utils::sanitize($_POST['startdate']), 
                ":dateend"=>Utils::sanitize($_POST['enddate']), 
            ];

            require_once("./../utils/DB.class.php");
            $db = (new DB());

            // check if we are editing
            // i'm not sure why it has a new line in front, but it works with the new line so i'm not gonna touch it
            if ($_POST['isEdit'] == "\n1") {

                // is edit
                $parameters[":id"] = $_GET['id'];

                $success = $db->update("UPDATE session SET name = :name, numberallowed = :numberallowed, event = :event, startdate = :datestart, enddate = :dateend WHERE idsession = :id", $parameters);
                
            } else {
                // is create
                $success = $db->insert("INSERT INTO session (name, numberallowed, event, startdate, enddate) VALUES (:name, :numberallowed, :event, :datestart, :dateend) ", $parameters);
                

                // todo link to right event?
            }

            // send me home!
            echo "Success: $success";
            header("Location: ../home.php");
            exit;
        } 
    }

    function handleDelete() {
        
        if(isset($_POST['delete'])) { 

           require_once("./../utils/DB.class.php");
           $db = (new DB());

           // get the users
           $users = $db->associativeSelect("SELECT attendee FROM attendee_session WHERE session = :session ", [":session"=>$_GET['id']]);

           // if the session has users, it can not be deleted
           if (count($users) > 0) {
            echo "<p class=\"error\">There are users registered for this session, remove before deleting</p>";
            return;
         }

        //    $result = $db->delete("DELETE FROM session WHERE idsession = :id ", [":id"=>$_GET['id']]);

        //    header("Location: ../admin.php");
        //    exit;
        } 
    }

?>