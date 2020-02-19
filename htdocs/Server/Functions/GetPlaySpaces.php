<?php

    function GetPlaySpace()
    {
        SessionActive();
        ValidateParameters($parameters = array("page"));

        global $dbConn;

        $UserSession = $_SESSION['User'];
      

        $data = null;
        $dbStatement = null;
        if($UserSession->IsTeacher)
        {
            $dbStatement = "SELECT `playspaces`.`ID`,
                `playspaces`.`Name`,`playspaces`.`Description`,
                `playspaces`.`image` , `users`.`firstname`,`users`.`lastname` 
            FROM `playspaces` JOIN `users` ON (`CreatorID`=`users`.`ID`) JOIN `playspaces` on (`PlaySpaceID` =`playspaces`.`ID` = WHERE ";

            
        }
        else
        {
            $dbStatement = "SELECT ID, firstname, lastname, klas,teacher FROM `users` WHERE `username`=? AND `password`=?";
        }

        if(!($dbConn->prepare($dbStatement))) {
            die(new Response(ResponseTypes::FatalError, "GetPlaySpaces prepare failed: ".$dbConn->error));
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
        
    }