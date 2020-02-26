<?php
    function Login()
    {
        global $dbConn,$salt;
    
        ValidateParameters($parameters = array("UserName", "Password"));
    
        $userName = $_POST["UserName"];
        $password = crypt($_POST["Password"], $salt);
    
            $quary = "SELECT `users`.ID as UserID,
            username, firstname, lastname,teacher ,
            `klassen`.`ID` as KlasID,
            DisplayName as KlasName
        FROM `users` LEFT OUTER JOIN `klassen` ON (`users`.`klas` = `klassen`.`ID`) 
        WHERE `username`=? AND `password`=? LIMIT 1";
       
    
        $result = ExecuteSql($quary,array($userName,$password));
    
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
