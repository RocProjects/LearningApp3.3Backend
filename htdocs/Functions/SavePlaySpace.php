<?php

    function SavePlaySpace()
    {
        
        ValidateParameters(array("PlaySpaceData"));

        $PlaySpaceData = $_POST["PlaySpaceData"];
        $space = PlaySpace::LoadFromJson($PlaySpaceData);

        $space->Save();
        die(new Response(ResponseTypes::succeeded,json_encode($space)));
    }
?>