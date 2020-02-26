<?php

$salt = '$2a$07$usqsogesafytringfjsalt$';

function Session()
{
    SessionActive();

    die(new EventSucceeded());
}


function SessionActive()
{
    if (!isset($_SESSION['User'])) {
        //user is not logged in/ session is invalid
        die(new Response(ResponseTypes::Silent_FatalError, "Failed to find user session"));
    }
}

function TeacherSessionActive()
{
    SessionActive();

    if($_SESSION['User']->IsTeacher == 0)
    {
        die(new Response(ResponseTypes::Silent_FatalError,"Logged in User is not a teacher"));
    }
}

function ValidateParameters($parameters)
{
    foreach ($parameters as &$type) {
        if (!isset($_POST[$type])) {
            die(new Response(ResponseTypes::FatalError, "Register: Missing parameter '" . $type . "'"));
        }
    }
    unset($type);
}

function ExecuteSql($statement, $parameters)
{
    global $dbConn;
    try 
    {
        $dbStatement = $dbConn->prepare($statement);
        if (!$dbStatement) 
        {
          die(new Response(ResponseTypes::FatalError, "Sql prepare failed: ".$dbConn->errorInfo()));
        }

    }catch(Exception $e)
    {
        die(new ExceptionResponse($e));
    }
    
    try {
        
        if($dbStatement->execute($parameters) === false)
        {
            die(new Response(ResponseTypes::FatalError, json_encode($dbStatement->errorInfo())));
        }
    } catch (Exception $e) {
        die(new ExceptionResponse($e));
    }
    return $dbStatement->fetchAll(PDO::FETCH_OBJ);;
}

function PrepareSQL($statement)
{
    global $dbConn;
    try 
    {
        if (!($dbStatement = $dbConn->prepare($statement))) 
        {
          die(new Response(ResponseTypes::FatalError, "Sql prepare failed: ".$dbConn->error));
        }

    }catch(Exception $e)
    {
        die(new ExceptionResponse($e));
    }

    return $dbStatement;
}

function ExecuteSqlStatement($statement)
{
    try {
        $statement->execute();
    } catch (Exception $e) {
        die(new ExceptionResponse($e));
    }

    return $statement->fetchAll(PDO::FETCH_OBJ);;
}

function LimitStatement($page)
{
    global $PageSize;
    //this limit solution is terrible but it works for now

    return "LIMIT ".($page *  $PageSize)." , ".$PageSize;
}