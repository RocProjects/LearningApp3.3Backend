<?php
    function DeletePlaySpace()
    {
        TeacherSessionActive();
        ValidateParameters(array("PlaySpaceID"));

        $quary = "DELETE FROM `playspaces` WHERE  ID = ? AND CreatorID = ? LIMIT 1";
        ExecuteSql($quary,array($_POST['PlaySpaceID'],$_SESSION['User']->ID));
        die(new Response(ResponseTypes::succeeded,"Deleted playspace"));
    }