<?php
    function CreateKlas()
    {
        global $dbConn;
        TeacherSessionActive();
        ValidateParameters(array("Name"));

        $quary = "INSERT INTO `klassen` (`DisplayName`) VALUES (?)";
        ExecuteSql($quary,array($_POST['Name']));
        die(new KlasResponse((object) [ 'ID'=> $dbConn->lastInsertId(),'Name' =>$_POST['Name']]));
    }