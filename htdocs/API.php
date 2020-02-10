<?php

function Session()
{
    SessionActive();

    die(new EventSucceeded());
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
