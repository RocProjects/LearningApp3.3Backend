<?php
    include_once 'Location.php';
    class PlaySpace
    {
        protected  $ID = -1;
        public $Name;
        public $Description;
        public $Locations = array();

        public static function LoadFromJson(string $jsonData) : PlaySpace
        {

            $jsonObject = json_decode($jsonData);

            $PlaySpace = new PlaySpace();
            $PlaySpace->Name = $jsonObject->Name;
            $PlaySpace->Description = $jsonObject->Description;
            //$PlaySpace->ID = $_SESSION['User']->GetID();


            for ($x = 0; $x < count($jsonObject->locations); $x++) {
                $element = $jsonObject->locations[$x];
                $element->Index = $x;
                array_push($PlaySpace->Locations,Location::LoadFromJson($element));
            }

            return $PlaySpace;
        }

        public function Save()
        {
            TeacherSessionActive();

            global $dbConn;
            $dbStatement = $dbConn->prepare("INSERT INTO `playspaces` (`Name`, `CreatorID`, `Description`) VALUES (?,?,?) ");
            try{
                $dbStatement->execute(array($this->Name, $_SESSION['User']->GetID(), $this->Description));
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

        public function __toString()
        {
            return "ID: ".$this->ID."  Name:".$this->Name." Description:".$this->Description." Locationsize:".count($this->Locations);
        }
    }