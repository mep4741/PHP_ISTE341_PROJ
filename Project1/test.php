<?php

    spl_autoload_register(function($class){
        require_once("./utils/$class.class.php");
    });

    (CurrentUser::setUpSession());
    echo (PageFormat::getDocumentHeader("Test"));
    
    // Hashing Passwords
    echo "<h1>Hashing Passwords</h1>";
    echo (Utils::hashPassword("ashenoy"))."</br>";
    echo (Utils::hashPassword("mphilip"))."</br>";
    echo (Utils::hashPassword("mphilip"))."</br>";
    echo (Utils::hashPassword("kbrosnahan"))."</br>";
    echo (Utils::hashPassword("mlynch"))."</br>";

    
    // testing database functions
    // testDB();


    echo (PageFormat::getDocumentFooter("Test"));



    // functions
    function testDB() {
        echo "</br></br>";
        echo "<h1>Testing Database Functions</h1>";
        $db = (new DB());

        echo "Select Associative";
        $select = $db->associativeSelect("SELECT * FROM attendee" , []);
        var_dump($select);

        echo "Insert";
        $insert = $db->insert("INSERT INTO attendee (name, password, role) VALUES (:name, :password, :role) ", [":name"=>"Karina Kageki-Bonert", ":password"=>"blank", ":role"=>3]);
        var_dump($insert);

        echo "Select Object";
        $select = $db->objectSelect("SELECT * FROM attendee ", [], "Event");
        var_dump($select);

        echo "Update";
        $update = $db->update("UPDATE attendee SET password = :password WHERE idattendee = :id ", [":password"=>"kkagekibonnert", ":id"=>$insert]);
        var_dump($update);

        echo "Select Object";
        $select = $db->objectSelect("SELECT * FROM attendee ", [], "Event");
        var_dump($select);

        echo "Delete";
        $delete = $db->update("DELETE FROM attendee WHERE idattendee = :id ", [":id"=>$insert]);
        var_dump($delete);

        echo "Select Associative";
        $select = $db->associativeSelect("SELECT * FROM attendee" , []);
        var_dump($select);
    }

?>