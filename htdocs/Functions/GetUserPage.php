<?php
    function GetUserPage()
    {
        global $dbConn;
        ValidateParameters($parameters = array("page"));

        $statement = "SELECT firstname, lastname, klas,teacher FROM `users`";

         $metatable = array();


        $metaOptions = array("klas","teacher","firstName","lastName","userName");
        $foundOptions = array();

        foreach($metaOptions as &$metaOption)
        {
            if(isset($_POST[$metaOption]))
            {
                if(count($foundOptions) == 0)
                {
                    $statement = $statement." WHERE";
                }

                $statement = $statement." '".$metaOption."'=? AND";
                array_push($foundOptions,$_POST[$metaOption]);
            }
        }

        if(count($foundOptions) > 0)
        {
            $statement = rtrim($statement," AND");
        }
        unset($metaOption);

        if (!($dbStatement = $dbConn->prepare($statement))) {
            die(new Response(ResponseTypes::FatalError, "prepare failed: ".$dbConn->error));
        }

        try {
            $dbStatement->execute($foundOptions);
        } catch (Exception $e) {
            die(new Response(ResponseTypes::FatalError, $e->getMessage()));
        }

        die(new UsersPageResponse($dbStatement));

    }
?>