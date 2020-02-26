<?php
    function RemoveKlasFromStudent()
    {
        TeacherSessionActive();
        ValidateParameters(array("StudentID"));

        $quary = "UPDATE  `users` SET klas = NULL WHERE `ID`= ? ";
        ExecuteSql($quary,array($_POST['StudentID']));
        die(new Response(ResponseTypes::succeeded, "Student has been removed from a Klas"));
    }