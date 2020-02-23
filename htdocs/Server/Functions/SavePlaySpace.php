<?php

    function SavePlaySpace()
    {
        
        ValidateParameters(array("PlaySpaceData"));

        $PlaySpaceData = $_POST["PlaySpaceData"];
        $space = PlaySpace::LoadFromJson($PlaySpaceData);

        var_dump( $PlaySpaceData);
        die();
        $space->Save();
        die(new Response(ResponseTypes::succeeded,json_encode($space)));
    }
