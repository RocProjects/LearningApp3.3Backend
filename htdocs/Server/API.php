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
        die(new Response(ResponseTypes::FatalError, "Failed to find user session"));
    }
}

function TeacherSessionActive()
{
    SessionActive();

    if(!$_SESSION['User']->IsTeacher)
    {
        die(new Response(ResponseTypes::FatalError,"Logged in User is not a teacher"));
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

function ExecuteSql(string $statement,array $parameters) : array
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
        die(new Response(ResponseTypes::FatalError, "Sql prepare Exception: ".$e->getMessage()));
    }

    
    try {
        $dbStatement->execute($parameters);
    } catch (PDOException $e) {
        die(new Response(ResponseTypes::FatalError, $e->getMessage()));
    }

    return $dbStatement->fetchAll(PDO::FETCH_OBJ);;
}

function PrepareSQL(string $statement) : PDOStatement
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

function ExecuteSqlStatement(PDOStatement $statement) : array
{
    try {
        $statement->execute();
    } catch (PDOException $e) {
        die(new ExceptionResponse($e));
    }

    return $statement->fetchAll(PDO::FETCH_OBJ);;
}

function LimitStatement(int $page) : string
{
    global $PageSize;
    //this limit solution is terrible but it works for now

    return "LIMIT ".($page * $PageSize).",".$PageSize;
}