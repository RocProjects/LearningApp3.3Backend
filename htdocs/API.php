<?php

$salt ='$2a$07$usqsogesafytringfjsalt$';

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




function Session()
{
    SessionActive();

    die(new EventSucceeded());
}

function GetUserPage()
{
    global $dbConn;
    ValidateParameters($parameters = array("page"));
    
    $statement = "SELECT firstname, lastname, klas,teacher FROM `users`";

     $metatable = array();


    $metaOptions = array("klas","teacher","firstName","lastName","userName");
    $foundOptions = array();

    foreach($metaOptions as &$metaOption)
    {
        if(isset($_POST[$metaOption]))
        {
            if(count($foundOptions) == 0)
            {
                $statement = $statement." WHERE";
            }

            $statement = $statement." '".$metaOption."'=? AND";
            array_push($foundOptions,$_POST[$metaOption]);
        }
    }

    if(count($foundOptions) > 0)
    {
        $statement = rtrim($statement," AND");
    }
    unset($metaOption);

    if (!($dbStatement = $dbConn->prepare($statement))) {
        die(new Response(ResponseTypes::FatalError, "Login prepare failed: ".$dbConn->error));
    }

    
    //$amount = "";
   // for ($x = 0; $x < count($foundOptions); $x++)
   // {
    //    $amount= $amount."s";
   // }
    $a = true;

    $dbStatement->bind_param("i",$foundOptions);

    
    try {
        $dbStatement->execute();
    } catch (PDOException $e) {
        die(new Response(ResponseTypes::FatalError, $e->getMessage()));
    }

    $dbStatement->store_result();
   
    die(new UsersPageResponse($dbStatement));
    
}


function SessionActive()
{
    if(!isset( $_SESSION['User']))
    {
        //user is not logged in/ session is invalid
        die(new Response(ResponseTypes::FatalError, "Failed to find user session"));
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
?>