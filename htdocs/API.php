<?php

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
        $dbStatement->bind_result();
    }

?>