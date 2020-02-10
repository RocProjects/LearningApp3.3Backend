<?php

function SavePlaySpace()
{

    ValidateParameters(array("PlaySpaceData"));

    $PlaySpaceData = $_POST["PlaySpaceData"];

    
    var_dump($PlaySpaceData);

}
?>