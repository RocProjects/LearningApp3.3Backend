<?php
    
    if(!isset($_POST["Action"]))
        die("<h1>ACCES DENIED</h1>");

    include __DIR__."/Rest.php";
    include __DIR__."/API.php";

    foreach (glob(__DIR__.'/Functions/*.php') as $file) {
        include_once $file;
    }
    session_start();


    $PageSize = 10;
    $salt = '$2a$07$usqsogesafytringfjsalt$';
   
    try {
        // Create connection
        //use this line for real db instead
        // $dbConn = new PDO('mysql:host=localhost;dbname=deb47292_groep05', 'deb47292_groep05', 'groep05');
        $dbConn = new PDO('mysql:host=localhost;dbname=deb47292_groep05', 'deb47292_groep05', 'groep05');
        // $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if (!isset($_POST['Action'])) {
            die(new Response(ResponseTypes::FatalError, "Request does not contain a Action name"));
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