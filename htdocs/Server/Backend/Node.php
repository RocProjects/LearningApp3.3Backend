<?php
    abstract class Node
    {
        public $ID = -1;
        public $Coordinate;
        public $LocationID;
        protected $Type = "Node";

        public static function LoadFromJson(object $jsonObj) : Node
        {
            $NodeType = $jsonObj->{'$type'};
            $Node = null;

            switch($NodeType)
            {     
                case InfoNode::GetAssemblyType():
                    $Node = InfoNode::LoadFromJson($jsonObj);
                break;
                case QuizNode::GetAssemblyType():
                    $Node = QuizNode::LoadFromJson($jsonObj);
                break;
                default:
                    die("Unkown NodeType: ".$NodeType);
                break;
            }
            $Node->Coordinate = $jsonObj->Location;

            return $Node;
        }



        public static function GetAssemblyType() : string
        {
            return "NULL";
        }

        //TODO Pass db
        public function Save($Location)
        {
            global $dbConn;
            //INSERT INTO `node` (`ID`, `LocationID`, `Coordinate`, `Type`) VALUES (NULL, '', '', '')
            $dbStatement = $dbConn->prepare("INSERT INTO `node` ( `LocationID`, `Coordinate`,`Type`) VALUES (?,?,?) ");
            try{
                $dbStatement->execute(array($Location->GetID(), json_encode($this->Coordinate), $this->Type));
                $this->ID = $dbConn->lastInsertId();
            }catch(PDOException $e){
                die(new UserRegisterQuaryResponse($e));
            }


          //  foreach($this->Locations as &$location)
           // {
          //      $location->Save($this);
           // }  
        }
    }