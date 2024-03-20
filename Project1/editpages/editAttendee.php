<!DOCTYPE html>
<?php

    spl_autoload_register(function($class){
        require_once("./../utils/$class.class.php");
    });
    require_once "./../classes/Attendee.class.php";

    (CurrentUser::setUpSessionWithCheck());

    // make vars
    $db = (new DB());
    $isEdit = false;

    // Header
    echo (PageFormat::getDocumentHeader("Edit Attendee", "./.."));


    // check to submit form
    handleFinish();
    handleDelete();

    
    // am i editing or creating?
    if (isset($_GET['id'])) {
        
        // get the obj data
        $attendee = $db->objectSelect("SELECT * FROM attendee WHERE idattendee = :id", [":id"=>$_GET['id']], "Attendee", "./..");

        // check if it exists
        $size = count($attendee);
        if ($size > 0) {
            echo "<h1>Edit Attendee</h1>";
            $isEdit = true;
        } else {
            echo "<h1>Create Attendee</h1>";
        }

    } else {
        echo "<h1>Create Attendee</h1>";
    }

    
?>
  
  <form method="POST">
    <label for="name">Name:</label><br>
    <input type="text" id="name" name="name" placeholder="Enter your name" value="<?php 
        if ($isEdit) {
            echo $attendee[0]->getName();
        } 
        ?>"><br>
    
    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password"><br>

    <?php 
        // give an empty attendee if it is a new attendee being created because it won't use the attendee that way anyway
        $obj = (new Attendee());
        if ($isEdit) {
            $obj = $attendee[0];
        }
        echo getSelect($db, $isEdit, $obj);
    ?>

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


    function getSelect(DB $db, bool $isEdit, Attendee $attendee ) {

        $options = "";

        // load the available roles
        $roles = $db->associativeSelect("SELECT idrole, name FROM role", []);

        foreach ($roles as $key => $value) {
            if ($isEdit && $value['idrole'] == $attendee->getRole()) {
                $options .= "<option value=\"{$value['idrole']}\" selected=\"selected\">{$value['name']}</option><br>";
            } else {
                $options .=  "<option value=\"{$value['idrole']}\">{$value['name']}</option><br>";
            }
        }

        $select = "<label for=\"role\">Choose a role</label><br>
        <select class=\"select\" name=\"role\" id=\"role\"><br>$options</select><br>";

        return $select;        
    }


    function handleFinish() {
        // handle creation / edit
        if(isset($_POST['finish'])) { 

            var_dump($_POST);
            require_once("./../utils/Utils.class.php");
            
            $hasName = Utils::validateData($_POST['name'], 0, 100);
            $hasPassword = Utils::validateData($_POST['password'], 0, 100);
            // we don't need to check the role bc it is in a select and has to have one thing selected

            // check if all the feilds are set and valid
            if (!$hasName || ($_POST['isEdit'] == 'false' && !$hasPassword)) {
                echo "<p class=\"error\">Fill in all the feilds</p>";
                return;
            }

            // sanatize data
            $parameters = [
                ":name"=>Utils::sanitize($_POST['name']),
                ":password"=>Utils::hashPassword(Utils::sanitize($_POST['password'])), 
                ":role"=>$_POST['role'], // select 
            ];
            // sanatize data without password
            $parametersNoPassword = [
                ":name"=>Utils::sanitize($_POST['name']),
                ":role"=>$_POST['role'], // select
            ];

            require_once("./../utils/DB.class.php");
            $db = (new DB());

            // check if we are editing
            // i'm not sure why it has a new line in front, but it works with the new line so i'm not gonna touch it
            if ($_POST['isEdit'] == "\n1") {
                // is edit, check if they are resseting the password or not
                $parameters[':id'] = $_GET['id'];

                if ($hasPassword) {
                    // resest password
                    $success = $db->update("UPDATE attendee SET name = :name, password = :password, role = :role WHERE  idevent = :id", $parameters);
                    
                } else {
                    // ignore password feild
                    $success = $db->update("UPDATE attendee SET name = :name, role = :role WHERE idattendee = :id", $parametersNoPassword);
                }
                
            } else {
                // is create

                $success = $db->insert("INSERT INTO attendee (name, password, role) VALUES (:name, :password, :role) ", $parameters);
            }

            echo "Success: $success";
            header("Location: ../admin.php");
            exit;
        } 
    }

    function handleDelete() {
        
        if(isset($_POST['delete'])) { 

           require_once("./../utils/DB.class.php");
           $db = (new DB());

            // get the events the user is registered for
            $registrations = $db->associativeSelect("SELECT event FROM attendee_event WHERE attendee = :attendee ", [":attendee"=>$_GET['id']]);

           // if user is registered for any events, can not delete
           if (count($registrations) > 0) {
                echo "<p class=\"error\">User is registered for events, remove the registration before deleting</p>";
                return;
           }


           $result = $db->delete("DELETE FROM attendee WHERE idattendee = :id ", [":id"=>$_GET['id']]);

           header("Location: ../admin.php");
           exit;
        } 
    }
?>