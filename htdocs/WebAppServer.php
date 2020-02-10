<?php
include "Rest.php";
include "API.PHP";

foreach(glob('Functions/*.php') as $file)
{
    include_once $file;
}


$PageSize = 10;
$salt ='$2a$07$usqsogesafytringfjsalt$';

session_start();

try {
    // Create connection
    $dbConn = new PDO('mysql:host=localhost;dbname=learningapp', 'root', '');
    // $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if (!isset($_POST['Action'])) {
        die(new Response(ResponseTypes::FatalError, "Request does nto contain a Action name"));
    }

    $commandID = $_POST["Action"];

    $commands = array("Login" => 'Login', "Register" => 'Register');

   // if (isset($commands[$commandID])) {
        call_user_func($commandID);
    //} else {
    //    die(new Response(ResponseTypes::FatalError, "Unkown command ID '" . $commandID . "'"));
   // }
} catch (PDOException $exception) {


    die(new Response(
        ResponseTypes::FatalError,
        "'FatalError: '" . $exception->getMessage() . "\n" . $exception->getFile() . ":" . $exception->getLine()
    ));
}
