<?php
//we don't page this. you need to be able to retrieve all of them, please just cache it
    function GetKlassen()
    {
        global $dbConn,$PageSize;

        
        $UserSession = $_SESSION['User'];

        $quary = "SELECT `ID`,`DisplayName` FROM `klassen`";

        $result = ExecuteSql($quary,array());
        die(new KlassenResponse($result));
        
    }