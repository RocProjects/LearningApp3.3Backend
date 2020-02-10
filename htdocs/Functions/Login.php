<?php
function Login()
{
    global $dbConn,$salt;

    ValidateParameters($parameters = array("UserName", "Password"));

    $userName = $_POST["UserName"];
    $password = crypt($_POST["Password"], $salt); // password_hash($_POST["Password"] ,PASSWORD_BCRYPT, ['cost' =>15]);

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

        
        $_SESSION['User'] = $user;

        die(new UserLoginResponse(ResponseTypes::succeeded,"Auhtenticated", $user));
    }
    else
    {
        die(new Response(ResponseTypes::Silent_FatalError,"Failed to Login"));
    }
}
?>