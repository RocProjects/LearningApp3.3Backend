<?php
    class Location
    {
        protected $ID = "-1";
        protected $Index;
        public $bg = "NULL";
        public $Nodes = array();
    
        public function __construct($ID_ ,$Index ,$Image )
        {
            $this->ID = $ID_;
            $this->$Index = $Index;
            if($Image == null)
            {
                $Image = "";
            }
            else
            {
                $this->bg = $Image;
            }
        }

        public static function LoadFromJson($jsonObj)
        {
            $Location = new Location(-1,$jsonObj->Index,$jsonObj->bg);
        
            foreach ($jsonObj->nodes as $node) {
                array_push($Location->Nodes, Node::LoadFromJson($node));
            }
            unset($node);
        
            return $Location;
        }

        public static function LoadFromSQL($OwnerID)
        {
            global $dbConn;
            //TODO SORT ON LOCATION ID
            $quary = "SELECT `ID` , `Image` FROM `playspacelocation` WHERE `Owner`=?";

            $result = ExecuteSql($quary,array($OwnerID));

            $locations = array();
            foreach ($result as $locationResult) 
            {
                $locationInstance = new Location($locationResult->ID,0,$locationResult->Image);
                $locationInstance->Nodes = Node::LoadFromSQL($locationResult->ID);
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
            $quary = "INSERT INTO `playspacelocation` (`Owner`, `Image`) VALUES (?,?)";

            ExecuteSql($quary,array($Playspace->GetID(), $this->bg));
        
            $this->ID = $dbConn->lastInsertId();
            foreach ($this->Nodes as &$Node) 
            {
                $Node->Save($this);
            }
            unset($Node);
        }
    }
    