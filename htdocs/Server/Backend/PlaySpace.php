<?php
    include_once __DIR__.'/Location.php';
    class PlaySpace
    {
        public $ID = -1;
        public $Name;
        public $Description;
        public $DisplayImage;
        public $Locations = array();


        public function __construct($ID = -1,$Name = "",$Description = "",$DisplayImage= "")
        {
            $this->ID = $ID;
            $this->Name = $Name;
            $this->Description = $Description;

            if($DisplayImage == null)
            {
                $DisplayImage = "";
            }
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
        public static function LoadFromSQL($ID) : PlaySpace
        {
            global $dbConn;
                $quary = "SELECT `Name` , `Description`, `image`FROM `playspaces` WHERE `ID`=? LIMIT 1";
                $results = ExecuteSql($quary,array($ID)); 
            
                if(count($results) >= 1)
                {
                    $result = $results[0];
                    $PlaySpace = new PlaySpace($ID,$result->Name,$result->Description,$result->image);

                    $PlaySpace->Locations = Location::LoadFromSQL($ID);

                    return $PlaySpace;
                }

                return new PlaySpace();

            
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


            foreach($this->Locations as $location)
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