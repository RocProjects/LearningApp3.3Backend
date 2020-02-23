<?php
    abstract class Node implements JsonSerializable 
    {
        public $ID = -1;
        public $Coordinate;
        protected static $Type = "Node";

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
                    die(new Response(ResponseTypes::FatalError,"Unkown NodeType: ".$NodeType));
                break;
            }
            $Node->Coordinate = $jsonObj->Location;

            return $Node;
        }

        public static function LoadFromSQL($LocationID)
        {
            global $dbConn;
            //TODO SORT ON LOCATION ID
            if (!($dbStatement = $dbConn->prepare("SELECT `ID` , `Coordinate`,`Type`FROM `node` WHERE `LocationID`=?"))) 
            {
                die(new Response(ResponseTypes::FatalError, "Login prepare failed: ".$dbConn->error));
            }
            try {
                $dbStatement->execute(array($LocationID));
            } catch (PDOException $e) {
                die(new Response(ResponseTypes::FatalError, $e->getMessage()));
            }
        
            $nodes = array();
            foreach ($dbStatement->fetchAll(PDO::FETCH_OBJ) as &$value) 
            {
                $Node = null;
                switch($value->Type)
                {     
                    case InfoNode::$Type:
                        $Node = InfoNode::LoadFromSQL($value);
                    break;
                    case QuizNode::$Type:
                        $Node = QuizNode::LoadFromSQL($value);
                    break;
                    default:
                        die("Unkown NodeType: ".$value->Type);
                    break;
                }

                $Node->{'$type'} = $Node::GetAssemblyType(); 
                $Node->Coordinate = $value->Coordinate;
                $Node->ID = $value->ID;
               // $locationInstance = new Location($value->ID,-1,$value->image);
                //$locationInstance->Nodes = Node::LoadFromSQL($locationInstance->ID);
                array_push($nodes, $Node);
            }


            return $nodes;
        }


        public static function GetAssemblyType() : string
        {
            return "NULL";
        }

        public abstract function GetNodeJson() : array;

        public function jsonSerialize ( )
        {
            $JsonArray = array(
            
                '$type' => $this->{'$type'},
                'Coordinate' => $this->Coordinate,
                'ID' => $this->ID
                
            );

            array_push($JsonArray,$this->GetNodeJson());

            return $JsonArray;
        }


        //TODO Pass db
        public function Save($Location)
        {
            global $dbConn;
            //INSERT INTO `node` (`ID`, `LocationID`, `Coordinate`, `Type`) VALUES (NULL, '', '', '')
            $dbStatement = $dbConn->prepare("INSERT INTO `node` ( `LocationID`, `Coordinate`,`Type`) VALUES (?,?,?) ");
            try{
                $dbStatement->execute(array($Location->GetID(), json_encode($this->Coordinate), $this::$Type));
                $this->ID = $dbConn->lastInsertId();
            }catch(PDOException $e){
                die(new UserRegisterQuaryResponse($e));
            }
        }
    }