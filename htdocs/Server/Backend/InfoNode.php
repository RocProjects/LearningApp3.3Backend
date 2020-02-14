<?php
    include_once 'Node.php';
    class InfoNode extends Node
    {
        public $Info;
        protected $Type = "Info";

        public static function LoadFromJson(object $jsonObj) : Node
        {
            $node = new InfoNode();
            $node->Info = $jsonObj->Info;
            return $node;
        }

        public static function GetAssemblyType() : string
        {
            return "Core.PlaySpace.InfoNode, Assembly-CSharp";
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