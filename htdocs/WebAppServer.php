<?php

    class Response
    {
        public function __construct($Status,$body)
        {
            $this->Status = $Status;
            $this->body = $body;
        }

    }

    class ResponseBody
    {
        public function __construct($value)
        {
            $this->value = $value;
        }
    }



    // Create connection
    try{
    // = new mysqli("localhost", "root", "");
     $dbConn = new PDO('mysql:host=localhost;dbname=learningapp','root','');
     $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    


    function Register()
    {
        global $dbConn;

        $parameters = array("FirstName","LastName","UserName","Password");
        foreach ($parameters as $type) 
        {
            if(!isset($_POST[$type]))
            {
                die("Register: Missing parameter '".$type."'");
            }
        }

        $dbStatement = $dbConn->prepare("INSERT INTO users (FirstName,LastName,UserName,Password)VALUES (:FirstName,:LastName,:UserName,:Password)");

        $password = password_hash($_POST["Password"] ,PASSWORD_BCRYPT, ['cost' =>15]);

        $dbStatement->execute(array(':FirstName'=>$_POST["FirstName"],':LastName' => $_POST["LastName"], ':UserName'=>$_POST["UserName"],':Password'=>$password));

    }

    function Login()
    {
        global $dbConn;

        $parameters = array("UserName","Password");
        foreach ($parameters as $type) 
        {
            if(!isset($_POST[$type]))
            {
                die("Login: Missing parameter '".$type."'");
            }
        }

        $userName = $_POST["UserName"];
        $password = password_hash($_POST["Password"] ,PASSWORD_BCRYPT, ['cost' =>15]);

        $dbStatement = $dbConn->prepare("SELECT (FirstName,LastName,Klas,Teacher)FROM users WHERE UserName=:UserName AND Password =:Password");
        $dbStatement->execute(array(':UserName' =>$userName,':Password'=>$password));
        $dbStatement->store_result();
        $dbStatement->bind_result()
    }

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