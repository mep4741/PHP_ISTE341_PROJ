<!DOCTYPE html>
<?php

    spl_autoload_register(function($class){
        require_once("./utils/$class.class.php");
    });
    
    (CurrentUser::setUpSession());
    echo (PageFormat::getDocumentHeader("Login", ".", true));
    

    if (isset($_POST['name']) && isset($_POST['password'])) {

        $name = $_POST['name'];
        $password = $_POST['password'];

        $validateName = (Utils::validateData($name, 0, 100));
        $validatePassword = (Utils::validateData($password, 0, 100));

        // are they valid
        if ($validateName && $validatePassword) {

            $sanitizedName = (Utils::sanitize($name));
            $sanitizedPassword = (Utils::sanitize($password));

            // does the login exist
            $loginWork = (CurrentUser::login($sanitizedName, $sanitizedPassword));
            if ($loginWork) {
                // login worked
                header("Location: home.php");
                exit;
            }
            else {
                echo "<p class=\"error\">That is not the correct username or password</p>";
            } // if login exists

        } else {
            echo "<p class=\"error\">Your username and password do not me the criteria, make sure they are between 1 and 99 characters</p>";
        } // if valid
        
    } // it values are set
?>

        <!-- Body Content -->
        <h1>You need </br> 
        to log in</h1>

        <form method="POST">
            <label for="name">Name:</label><br>
            <input type="text" id="name" name="name" placeholder="Enter your name"><br>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" placeholder="Enter your password"><br>
            <!-- submit -->
            <button>Log In</button>           
        </form>


    
<?php
    echo (PageFormat::getDocumentFooter());

?>
