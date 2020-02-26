<?php
    function DeleteKlas()
    {
        TeacherSessionActive();
        ValidateParameters(array("ID"));

        $quary = "DELETE FROM `klassen` WHERE `ID` = ? LIMIT 1";
        ExecuteSql($quary,array($_POST['ID']));
        die(new Response(ResponseTypes::succeeded, "Klas Has been deleted Succesfully"));
    }