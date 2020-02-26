<?php

    function GetPlaySpacePage()
    {
        SessionActive();
        ValidateParameters($parameters = array("Page"));

        global $dbConn,$PageSize;

        
        $UserSession = $_SESSION['User'];

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
            `playspaces`.`Name` = COALESCE(:PlaySpaceName,`playspaces`.`Name`) AND 
            IF(:IsTeacher ,
                (`users`.`firstname` = :Firstname AND `users`.`lastname` = :Lastname),
                true ) ".LimitStatement($_POST["Page"]);


            

            //LIMIT 1,2
            $statement = PrepareSQL($quary);

            if($UserSession->IsTeacher)
            {
                $statement->bindValue(":KlasID",(isset($_POST['KlasID']) ? $_POST['KlasID'] : null));
            }
            else
            {
                $statement->bindValue(":KlasID",$UserSession->Klas->ID);
            }
            
            $statement->bindValue(":playspaceID", (isset($_POST['PlaySpaceID']) ? $_POST['PlaySpaceID'] : null));
            $statement->bindValue(":PlaySpaceName",(isset($_POST['PlaySpaceName']) ? $_POST['PlaySpaceName'] : null));
            $statement->bindValue(":IsTeacher",$UserSession->IsTeacher);
            $statement->bindValue(":Firstname",$UserSession->FirstName);
            $statement->bindValue(":Lastname",$UserSession->LastName);

        $result = ExecuteSqlStatement($statement);
        die(new PlaySpacePageResponse($result));
        
    }