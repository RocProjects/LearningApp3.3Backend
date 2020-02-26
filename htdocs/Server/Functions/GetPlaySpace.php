<?php
    include_once __DIR__.'/../Backend/PlaySpace.php';
    function GetPlaySpace()
    {
        SessionActive();
        ValidateParameters($parameters = array("ID"));

        global $dbConn;

        $UserSession = $_SESSION['User'];
        die(new GetPlaySpaceResponse(PlaySpace::LoadFromSQL($_POST['ID'])));
    }