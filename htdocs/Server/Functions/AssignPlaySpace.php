<?php
    function AssignPlaySpaceToKlas()
    {
        TeacherSessionActive();
        ValidateParameters(array("PlaySpaceID","KlasID"));

        $quary = "INSERT INTO  `playspaceklasassignments` (`PlaySpaceID`,`KlasID`) VALUES (?,?)";
        ExecuteSql($quary,array($_POST['PlaySpaceID'],$_POST['KlasID']));
        die(new Response(ResponseTypes::succeeded, "Klas has been assigned to playspace"));
    }