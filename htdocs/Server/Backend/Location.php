<?php
    class Location
    {
        protected $ID = -1;
        protected $Index;
        public $bg = "NULL";
        public $Nodes = array();
    
        public function __construct($ID = -1,$Index = -1,$Image = "NULL")
        {
            $this->ID = $ID;
            $this->$Index = $Index;
            $this->bg = $Image;
        }

        public static function LoadFromJson(object $jsonObj): Location
        {
            $Location = new Location(-1,$jsonObj->Index,$jsonObj->bg);
        
            foreach ($jsonObj->nodes as $node) {
                array_push($Location->Nodes, Node::LoadFromJson($node));
            }
        
            return $Location;
        }

        public function LoadFromSQL(int $ID) : array
        {
            global $dbConn;
            //TODO SORT ON LOCATION ID
            if (!($dbStatement = $dbConn->prepare("SELECT `ID` , `image` FROM `playspacelocation` WHERE `Owner`=?"))) 
            {
                die(new Response(ResponseTypes::FatalError, "Login prepare failed: ".$dbConn->error));
            }
            try {
                $dbStatement->execute(array($ID));
            } catch (PDOException $e) 
            {
                die(new Response(ResponseTypes::FatalError, $e->getMessage()));
            }
        
            $locations = array();
            foreach ($dbStatement->fetchAll(PDO::FETCH_OBJ) as &$value) 
            {
                $locationInstance = new Location($value->ID,-1,$value->image);
                $locationInstance->Nodes = Node::LoadFromSQL($locationInstance->ID);
                array_push($locations, $locationInstance);
            }


            return $locations;
        }

        public function GetID()
        {
            return $this->ID;
        }
    
        public function Save($Playspace)
        {
            global $dbConn;
            $dbStatement = $dbConn->prepare("INSERT INTO `playspacelocation` (`Owner`, `Image`) VALUES (?,?)");

            try {
                //die($this->Image);
                //TODO fix image
                $dbStatement->execute(array($Playspace->GetID(), $this->bg));
                $this->ID = $dbConn->lastInsertId();
            } catch (Exception $e) {
                die(new Response(ResponseTypes::FatalError,$e->getMessage()));
            }
        
            foreach ($this->Nodes as &$Node) {
                $Node->Save($this);
            }
        
            unset($Location);
        }
    }
    