<?php
    function GetSession()
    {
        SessionActive();

        die(new UserLoginResponse(ResponseTypes::succeeded,"User session active",$_SESSION['User']));
    }