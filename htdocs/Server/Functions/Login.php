<?php
    function Login()
    {
        global $dbConn,$salt;
    
        ValidateParameters($parameters = array("UserName", "Password"));
    
        $userName = $_POST["UserName"];
        $password = crypt($_POST["Password"], $salt);
    
        if (!($dbStatement = $dbConn->prepare("SELECT ID,username ,firstname, lastname, klas,teacher FROM `users` WHERE `username`=? AND `password`=? LIMIT 1"))) {
            die(new Response(ResponseTypes::FatalError, "Login prepare failed: ".$dbConn->error));
        }
    
    
        
        try {
            $dbStatement->execute(array($userName,$password));
        } catch (PDOException $e) {
            die(new Response(ResponseTypes::FatalError, $e->getMessage()));
        }
    
        $result = $dbStatement->fetchAll(PDO::FETCH_OBJ);
    
        if(count($result) >= 1)
        {
            $user = new User($result[0]);
            
            $_SESSION['User'] = $user;
        
            die(new UserLoginResponse(ResponseTypes::succeeded,"Auhtenticated", $user));
        }
        else
        {
            die(new Response(ResponseTypes::Silent_FatalError,"Failed to Login"));
        }
    }
