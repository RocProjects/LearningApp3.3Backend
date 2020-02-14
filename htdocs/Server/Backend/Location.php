<?php
    class Location
    {
        protected $ID = -1;
        protected $Index;
        public $Image = "0x00";
        public $Nodes = array();
    
        public static function LoadFromJson(object $jsonObj): Location
        {
            $Location = new Location();
            if ($jsonObj->bg != null) {
                $Location->Image = $jsonObj->bg;
            }
            $Location->Index = $jsonObj->Index;
        
            foreach ($jsonObj->nodes as $node) {
                array_push($Location->Nodes, Node::LoadFromJson($node));
            }
        
            return $Location;
        }

        public function GetID()
        {
            return $this->ID;
        }
    
        public function Save($Playspace)
        {
            global $dbConn;
            $dbStatement = $dbConn->prepare("INSERT INTO `playspacelocation` (`Owner`, `LocationID`, `Image`) VALUES (?,?,?)");
        
            try {
                //TODO fix image
                $dbStatement->execute(array($Playspace->GetID(), $this->Index, '0x00'));
                $this->ID = $dbConn->lastInsertId();
            } catch (PDOException $e) {
                die(new UserRegisterQuaryResponse($e));
            }
        
            foreach ($this->Nodes as &$Node) {
                $Node->Save($this);
            }
        
            unset($Location);
        }
    }
    