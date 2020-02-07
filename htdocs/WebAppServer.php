<?php
include "Rest.php";
include "API.PHP";


try {
    // Create connection
    $dbConn = new mysqli("localhost", "root", "", "learningapp");
    // $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if (!isset($_POST['Action'])) {
        die(new Response(ResponseTypes::FatalError, "NO Action found"));
    }

    $commandID = $_POST["Action"];

    $commands = array("Login" => 'Login', "Register" => 'Register');

    if (isset($commands[$commandID])) {
        call_user_func($commands[$commandID]);
    } else {
        die(new Response(ResponseTypes::FatalError, "Unkown command ID '" . $commandID . "'"));
    }
} catch (PDOException $exception) {


    die(new Response(
        ResponseTypes::FatalError,
        "'FatalError: '" . $exception->getMessage() . "\n" . $exception->getFile() . ":" . $exception->getLine()
    ));
}
