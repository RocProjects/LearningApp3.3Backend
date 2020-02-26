<?php
    function UnAssignPlaySpaceFromKlas()
    {
        TeacherSessionActive();
        ValidateParameters(array("KlasID"));

        $quary = "DELETE FROM  `playspaceklasassignments` WHERE `KlasID`= ? ";
        ExecuteSql($quary,array($_POST['KlasID']));
        die(new Response(ResponseTypes::succeeded, "Klas has been assigned to playspace"));
    }