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
    echo (PageFormat::getDocumentHeader("Edit Venue", "./.."));


    // check to submit form
    handleFinish();
    handleDelete();
    
    
    // am i editing or creating?
    if (isset($_GET['id'])) {
        
        // get the obj data
        $venue = $db->objectSelect("SELECT * FROM venue WHERE idvenue = :id", [":id"=>$_GET['id']], "Venue", "./..");

        // check if it exists
        $size = count($venue);
        if ($size > 0) {
            echo "<h1>Edit Venue</h1>";
            $isEdit = true;
        } else {
            echo "<h1>Create Venue</h1>";
        }

    } else {
        echo "<h1>Create Venue</h1>";
    }

    
?>
  
  <form method="POST">
    <label for="name">Venue Name:</label><br>
    <input type="text" id="name" name="name" placeholder="Enter your venue name" value="<?php 
        if ($isEdit) {
            echo $venue[0]->getName();
        } 
        ?>"><br>
    
    <label for="capacity">Venue Capacity:</label><br>
    <input type="number" id="capacity" name="capacity" placeholder=0 value="<?php 
        if ($isEdit) {
            echo $venue[0]->getCapacity();
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

            require_once("./../utils/Utils.class.php");

            // validate our params
            $hasName = Utils::validateData($_POST['name'], 0, 50);
            $hasCapacity = Utils::validateData($_POST['capacity'], 0, 50);

            // check if all the feilds are set
            if (!($hasName && $hasCapacity)) {
                echo "<p class=\"error\">Fill in all the feilds</p>";
                return;
            }

            // sanatize data
            $parameters = [
                ":name"=>Utils::sanitize($_POST['name']),
                ":capacity"=>Utils::sanitize($_POST['capacity']), 
            ];

            // create database
            require_once("./../utils/DB.class.php");
            $db = (new DB());

            // check if we are editing
            // i'm not sure why it has a new line in front, but it works with the new line so i'm not gonna touch it
            if ($_POST['isEdit'] == "\n1") {
                // is edit
                $parameters[":id"] = $_GET['id'];

                $success = $db->update("UPDATE venue SET name = :name, capacity = :capacity WHERE idvenue = :id", $parameters);
                
            } else {

                // is create
                $success = $db->insert("INSERT INTO venue (name, capacity) VALUES (:name, :capacity) ", $parameters);
                
            }

            
            // send me home!!!
            echo "Success: $success";
            // if it can't create it, it will fail silently and send the person home to then realize for themselves that it didn't work because their name was not unique or something like that
            header("Location: ../admin.php");
            exit;
        } 
    }

    function handleDelete() {
        
        if(isset($_POST['delete'])) { 

           require_once("./../utils/DB.class.php");
           $db = (new DB());

           // get the events
           $events = $db->associativeSelect("SELECT idevent FROM event WHERE venue = :venue ", [":venue"=>$_GET['id']]);

           // if venue is being used by events can not be deleted
           if (count($events) > 0) {
            echo "<p class=\"error\">There are events using this venue, remove before deleting</p>";
            return;
            }

        //    $result = $db->delete("DELETE FROM venue WHERE idvenue = :id ", [":id"=>$_GET['id']]);

        //    header("Location: ../admin.php");
        //    exit;
        } 
    }
?>
