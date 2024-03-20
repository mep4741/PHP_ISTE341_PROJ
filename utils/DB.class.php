<?php

class  DB {

    private $dbh;

    function __construct() {
        
        try {

            // in the constructor is the only thing that changes for a database, everything else is the same no matter the database
            $this->dbh = new PDO(
                "mysql:host={$_SERVER['DB_SERVER']};dbname={$_SERVER['DB']}",
                $_SERVER["DB_USER"], 
                $_SERVER["DB_PASSWORD"]
            );

            // default error mode is silent, but we want to see the errors
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $pe) {
            // usually would want to print this not echo this out so the client doesn't see this
            echo $pe->getMessage();
            die ("Bad Database Connection");
        }

    } // construct


    // run a sql select query with specified sql statement and prepared values
    function associativeSelect($sql, $params) {
        $data = [];

        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute($params);

            while ( $row = $stmt->fetch()) {
                $data[] = $row;
            }

        } catch (PDOException $pe) {
            echo $pe->getMessage();
        }

        // if we have a problem with our query we are just going to return an empty array
        return $data; 

    } // associativeSelect


    // run a sql select query with specified sql statement, prepared values, the file the class is in, and the class name
    function objectSelect($sql, $params, $class, $fileFirstPath = ".") {
        $data = [];

        try {

            include_once "$fileFirstPath/classes/$class.class.php";

            $stmt = $this->dbh->prepare($sql);
            $stmt->execute($params);

            $stmt->setFetchMode(PDO::FETCH_CLASS, $class);

            while ( $row = $stmt->fetch()) {
                $data[] = $row;
            }

        } catch (PDOException $pe) {
            echo $pe->getMessage();
        }

        // if we have a problem with our query we are just going to return an empty array
        return $data; 

    } // objectSelect


    // run sql insert query, with bound params from params
    function insert($sql, $params) {
        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute($params);

            return $this->dbh->lastInsertId();

        } catch (PDOException $pe) {
            echo $pe->getMessage();
            return -1;
        }
    }

    // run sql delete query, with bound params from params
    function delete($sql, $params) {
        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute($params);

            return $stmt->rowCount();

        } catch (PDOException $pe) {
            echo $pe->getMessage();
            return -1;
        }
    }

    // run sql delete query, with bound params from params
    function update($sql, $params) {
        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute($params);

            return $stmt->rowCount();

        } catch (PDOException $pe) {
            echo $pe->getMessage();
            return -1;
        }
    }


}