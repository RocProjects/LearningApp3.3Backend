<?php

function Register()
{
    global $dbConn;

    ValidateParameters(array("FirstName", "LastName", "UserName", "Password"));

    $dbStatement = $dbConn->prepare("INSERT INTO users (firstname,lastname,username,password)VALUES (?,?,?,?)");

    $password = crypt($_POST["Password"], '$2a$07$usqsogesafytringfjsalt$'); // password_hash($_POST["Password"] ,PASSWORD_BCRYPT, ['cost' =>15]);


    $dbStatement->bind_param("ssss", $_POST["FirstName"], $_POST["LastName"], $_POST["UserName"], $password);

    if (!$dbStatement->execute()) {
        die(new UserRegisterQuaryResponse($dbStatement));
    }

    die(new Response(ResponseTypes::succeeded, "no FatalError " . crypt($_POST["Password"], '$2a$07$usqsogesafytringfjsalt$')));
}

function Login()
{
    global $dbConn;

    ValidateParameters($parameters = array("UserName", "Password"));

    $userName = $_POST["UserName"];
    $password = crypt($_POST["Password"], '$2a$07$usqsogesafytringfjsalt$'); // password_hash($_POST["Password"] ,PASSWORD_BCRYPT, ['cost' =>15]);

    if (!($dbStatement = $dbConn->prepare("SELECT firstname, lastname, klas,teacher FROM `users` WHERE `username`=? AND `password`=?"))) {
        die(new Response(ResponseTypes::FatalError, "Login prepare failed: ".$dbConn->error));
    }

    $dbStatement->bind_param("ss", $userName, $password);

    
    try {
        $dbStatement->execute();
    } catch (PDOException $e) {
        die(new Response(ResponseTypes::FatalError, $e->getMessage()));
    }

    $dbStatement->store_result();
   
    if($dbStatement->num_rows >= 1)
    {
        $user = new User($dbStatement);

        die(new UserLoginResponse(ResponseTypes::succeeded,"Auhtenticated", $user));
    }
    else
    {
        die(new Response(ResponseTypes::Silent_FatalError,"Failed to find user"));
    }
}


function ValidateParameters(array $parameters)
{
    foreach ($parameters as $type) {
        if (!isset($_POST[$type])) {
            die(new Response(ResponseTypes::FatalError, "Register: Missing parameter '" . $type . "'"));
        }
    }
}
