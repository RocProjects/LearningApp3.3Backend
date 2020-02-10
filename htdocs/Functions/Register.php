<?php
function Register()
{
    global $dbConn,$salt;

    ValidateParameters(array("FirstName", "LastName", "UserName", "Password"));

    $dbStatement = $dbConn->prepare("INSERT INTO users (firstname,lastname,username,password)VALUES (?,?,?,?)");

    $password = crypt($_POST["Password"], $salt); // password_hash($_POST["Password"] ,PASSWORD_BCRYPT, ['cost' =>15]);


    $dbStatement->bind_param("ssss", $_POST["FirstName"], $_POST["LastName"], $_POST["UserName"], $password);

    if (!$dbStatement->execute()) {
        die(new UserRegisterQuaryResponse($dbStatement));
    }

    die(new Response(ResponseTypes::succeeded, "no FatalError " . crypt($_POST["Password"], '$2a$07$usqsogesafytringfjsalt$')));
}
?>