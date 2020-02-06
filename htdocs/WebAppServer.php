<?php
    include "Rest.php";
    include "API.PHP";


    die(json_encode(new Response(ResponseTypes::ERROR,"test")));

    // Create connection
    try{
    // = new mysqli("localhost", "root", "");
        $dbConn = new PDO('mysql:host=localhost;dbname=learningapp','root','');
        $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(!isset($_POST['CommandID']))
        {
            die("NO CommandID found");
        }

        $commandID = $_POST["CommandID"];

        $commands = array("Login" => 'Login',"Register" => 'Register');

        if(isset($commands[$commandID]))
        {
            call_user_func($commands[$commandID]);
        }
        else{
            die("Unkown command ID '".$commandID."'");
        }

    }catch(PDOException $exception)
    {
        echo 'LINE: '.$exception->getLine();
        echo 'FILE: '.$exception->getFile();
        echo 'ERROR: '.$exception->getMessage();
    }
?> 