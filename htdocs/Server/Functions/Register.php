<?php
    function Register()
    {
        global $dbConn, $salt;
    
        ValidateParameters(array("FirstName", "LastName", "UserName", "Password"));
    
        $dbStatement = $dbConn->prepare("INSERT INTO users (firstname,lastname,username,password)VALUES (?,?,?,?)");
    
        $password = crypt($_POST["Password"], $salt); // password_hash($_POST["Password"] ,PASSWORD_BCRYPT, ['cost' =>15]);
    
        try {
            $dbStatement->execute(array($_POST["FirstName"], $_POST["LastName"], $_POST["UserName"], $password));
            die(new Response(ResponseTypes::succeeded, "no FatalError " . crypt($_POST["Password"], '$2a$07$usqsogesafytringfjsalt$')));
        } catch (PDOException $e) {
            die(new UserRegisterQuaryResponse($e));
        }
    }
