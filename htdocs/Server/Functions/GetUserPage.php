<?php
    function GetUserPage()
    {
        TeacherSessionActive();
        //TODO
        global $dbConn;
        ValidateParameters($parameters = array("Page"));

        //Klas can be null and thats valid
        $quary = "SELECT ID,firstname, lastname FROM `users` 
        WHERE teacher = 0 
            AND IF(:Klas IS NULL ,klas IS NULL,klas = :Klas) 
            AND IF(:Firstname IS NULL, true,`firstname`=:Firstname)
            AND IF (:LastName IS NULL, true, `lastname` = :LastName)
            AND IF (:UserID IS NULL , true , `ID` = :UserID)";

        //LIMIT 1,2
        $statement = PrepareSQL($quary);
        
        $statement->bindValue(":Firstname", (isset($_POST['firstName']) ? $_POST['firstName'] : null));
        $statement->bindValue(":LastName",(isset($_POST['lastName']) ? $_POST['lastName'] : null));
        $statement->bindValue(":Klas",(isset($_POST['klasID']) ? $_POST['klasID'] : null));
        $statement->bindValue(":UserID",(isset($_POST['UserID']) ? $_POST['UserID'] : null));

        $result = ExecuteSqlStatement($statement);

        die(new UsersPageResponse($result));

    }
?>