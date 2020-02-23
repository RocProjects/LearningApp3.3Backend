<?php
    include_once __DIR__.'/Node.php';
    class InfoNode extends Node
    {
        public $Info;
        protected static $Type = "Info";

        public static function LoadFromJson(object $jsonObj) : Node
        {
            $node = new InfoNode();
            //$node->ID = $jsonObj->ID;
            $node->Info = $jsonObj->Info;
            return $node;
        }

        public static function LoadFromSQL($nodeData)
        {
            global $dbConn;
            //TODO SORT ON LOCATION ID
            if (!($dbStatement = $dbConn->prepare("SELECT `info`FROM `infonode` WHERE `ID`=?  LIMIT 1"))) 
            {
                die(new Response(ResponseTypes::FatalError, "Login prepare failed: ".$dbConn->error));
            }
            try {
                $dbStatement->execute(array($nodeData->ID));
            } catch (PDOException $e) {
                die(new Response(ResponseTypes::FatalError, $e->getMessage()));
            }
            $row = $dbStatement->fetch(PDO::FETCH_OBJ);

            if($row == null)
            {
                die(new Response(ResponseTypes::FatalError,"Failed to find node info"));
            }

            $node = new InfoNode();
            $node->ID = $nodeData->ID;
            $node->Coordinate = $nodeData->Coordinate;
            $node->Info = $row->info;
            return $node;
        }


        public static function GetAssemblyType() : string
        {
            return "Core.PlaySpace.InfoNode, Assembly-CSharp";
        }

        public function GetNodeJson() : array
        {
            return array(
                'Info'=> $this->Info
            );
        }


        //TODO Pass db
        public function Save($LocationOBJ)
        {
            parent::Save($LocationOBJ);

            global $dbConn;
            $dbStatement = $dbConn->prepare("INSERT INTO `infonode` (`ID`, `info`) VALUES (?,?) ");
            try{
                $dbStatement->execute(array($this->ID, $this->Info));
            }catch(PDOException $e){
                die(new UserRegisterQuaryResponse($e));
            }
        }
    }