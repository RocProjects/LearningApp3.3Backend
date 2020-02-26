<?php
    include_once __DIR__.'/Node.php';
    class InfoNode extends Node
    {
        public $Info;
        protected static $Type = "Info";

        public static function LoadFromJson($jsonObj)
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
            $quary = "SELECT `info`FROM `infonode` WHERE `ID`=?  LIMIT 1";
            $rows = ExecuteSql($quary,array($nodeData->ID));
            if(!isset($rows[0]))
            {
                die(new Response(ResponseTypes::FatalError,"Failed to find node info"));
            }
            $node = new InfoNode();
            $node->Info = $rows[0]->info;
            return $node;
        }


        public static function GetAssemblyType()
        {
            return "Core.PlaySpace.InfoNode, Assembly-CSharp";
        }

        public function GetNodeJson()
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
            $quary = "INSERT INTO `infonode` (`ID`, `info`) VALUES (?,?) ";

            ExecuteSql($quary,array($this->ID, $this->Info));
        }
    }