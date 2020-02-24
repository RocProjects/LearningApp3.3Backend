<?php
    include_once __DIR__.'/Location.php';
    class PlaySpace
    {
        public $ID = -1;
        public $Name;
        public $Description;
        public $DisplayImage;
        public $Locations = array();


        public function __construct(int $ID = -1,string $Name = "",string $Description = "",string $DisplayImage= "")
        {
            $this->ID = $ID;
            $this->Name = $Name;
            $this->Description = $Description;
            $this->DisplayImage = $DisplayImage;
        }

        public static function LoadFromJson(string $jsonData) : PlaySpace
        {
            $jsonObject = json_decode($jsonData);
            //TODO INSERT BACKGROUND
            $PlaySpace = new PlaySpace($jsonObject->ID,$jsonObject->Name,$jsonObject->Description,$jsonObject->DisplayImage);

            for ($x = 0; $x < count($jsonObject->locations); $x++) {
                $element = $jsonObject->locations[$x];
                $element->Index = $x;
                array_push($PlaySpace->Locations,Location::LoadFromJson($element));
            }

            return $PlaySpace;
        }

        //info from a playspace is not relevant for students so this is only replicated for teachers
        public static function LoadFromSQL(int $ID) : PlaySpace
        {
            global $dbConn;
            if($_SESSION['User']->IsTeacher)
            {
                if (!($dbStatement = $dbConn->prepare("SELECT `Name` , `Description`, `image`FROM `playspaces` WHERE `ID`=? LIMIT 1"))) 
                {
                    die(new Response(ResponseTypes::FatalError, "Login prepare failed: ".$dbConn->error));
                }
                try {
                    $dbStatement->execute(array($ID));
                } catch (PDOException $e) {
                    die(new Response(ResponseTypes::FatalError, $e->getMessage()));
                }
            
                $results = $dbStatement->fetchAll(PDO::FETCH_OBJ);
            
                if(count($results) >= 1)
                {
                    $result = $results[0];
                    $PlaySpace = new PlaySpace($ID,$result->Name,$result->Description,$result->image);

                    $PlaySpace->Locations = Location::LoadFromSQL($ID);

                    return $PlaySpace;
                }

                    die(new Response(ResponseTypes::Silent_FatalError,"Failed to recieve playspace :".$ID));
                //return new PlaySpace();

            }
        }

        public function Save()
        {
            TeacherSessionActive();

            global $dbConn;
            $dbStatement = $dbConn->prepare("INSERT INTO `playspaces` (`Name`, `CreatorID`, `Description`,`image`) VALUES (?,?,?,?) ");
            try{
                $dbStatement->execute(array($this->Name, $_SESSION['User']->GetID(), $this->Description,$this->DisplayImage));
                $this->ID = $dbConn->lastInsertId();
            }catch(PDOException $e){
                die(new UserRegisterQuaryResponse($e));
            }


            foreach($this->Locations as &$location)
            {
                $location->Save($this);
            }
        
           die(new Response(ResponseTypes::succeeded, "Playspace saved"));
        }

        public function GetID() : int
        {
            return $this->ID;
        }
    }