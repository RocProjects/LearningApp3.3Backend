<?php

    function GetPlaySpacePage()
    {
        SessionActive();
        ValidateParameters($parameters = array("Page"));

        global $dbConn,$PageSize;

        
        $UserSession = $_SESSION['User'];
      
       // if($UserSession->IsTeacher)
       // {
            $quary = "SELECT `playspaces`.`ID`,
            `playspaces`.`Name`,`playspaces`.`Description`,
            `playspaces`.`image` , `users`.`firstname`,`users`.`lastname` 
        FROM `playspaces` 
            JOIN `users` ON (`CreatorID`=`users`.`ID`) 
            LEFT OUTER JOIN `playspaceklasassignments` 
                ON IF( :KlasID IS NULL, false,`playspaceklasassignments`.`KlasID`= :KlasID)
        WHERE 
            `playspaces`.`ID` = COALESCE(:playspaceID,`playspaces`.`ID`) AND  
             IF(:KlasID IS NULL,true,`playspaces`.`ID` =`playspaceklasassignments`.`playspaceID`) AND
            `playspaces`.`Name` = COALESCE(:PlaySpaceName,`playspaces`.`Name`) ".LimitStatement($_POST["Page"]);


            //LIMIT 1,2
            $statement = PrepareSQL($quary);
            $statement->bindValue(":KlasID",isset($_SESSION['KlasID']) ? $_SESSION['KlasID'] : null);
            $statement->bindValue(":playspaceID", isset($_SESSION['PlaySpaceID']) ? $_SESSION['PlaySpaceID'] : null);
            $statement->bindValue(":PlaySpaceName",isset($_SESSION['PlaySpaceName']) ? $_SESSION['PlaySpaceName'] : null);
            

       // }
      //  else
      //TODO FIX THIS ONE
        if(false) {
            $dbStatement = "SELECT `playspaces`.`ID`,
            `playspaces`.`Name`,`playspaces`.`Description`,
            `playspaces`.`image` , `users`.`firstname`,`users`.`lastname` 
        FROM `playspaces` JOIN `users` ON (`CreatorID`=`users`.`ID`)  JOIN `playspaceklasassignments` ON (`KlasID` = ?)
        WHERE 
            `playspaces`.`ID` = COALESCE(?,`playspaces`.`ID`) AND `playspaces`.`ID` =`PlaySpaceID` AND 
            `playspaces`.`Name` = COALESCE(?,`playspaces`.`Name`)
            ";
        }


        $result = ExecuteSqlStatement($statement, array(1,2));
        die(new PlaySpacePageResponse($result));
        
    }