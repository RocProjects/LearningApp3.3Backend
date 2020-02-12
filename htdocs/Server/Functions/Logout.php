<?php
    function Logout()
    {
        session_destroy();  
        die(new EventSucceeded());
    }