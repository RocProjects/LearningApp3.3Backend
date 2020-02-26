<?php
    abstract class Node implements JsonSerializable 
    {
        public $ID = -1;
        public $Coordinate;
        protected static $Type = "Node";

        public static function LoadFromJson($jsonObj)
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
            $quary = "SELECT `ID` , `Coordinate`,`Type`FROM `node` WHERE `LocationID`=?";
            $result = ExecuteSql($quary,array($LocationID));
            $nodes = array();
            foreach ($result as $NodeData) 
            {
                $Node = null;
                switch($NodeData->Type)
                {     
                    case InfoNode::$Type:
                        $Node = InfoNode::LoadFromSQL($NodeData);
                    break;
                    case QuizNode::$Type:
                        $Node = QuizNode::LoadFromSQL($NodeData);
                    break;
                    case MediaNode::$Type:
                        $Node = MediaNode::LoadFromSQL($NodeData);
                    break;
                    default:
                        die(new Response(ResponseTypes::FatalError,"Unkown NodeType: ".$NodeData->Type));
                    break;
                }

                $Node->{'$type'} = $Node::GetAssemblyType(); 
                $Node->Coordinate = $NodeData->Coordinate;
                $Node->ID = $NodeData->ID;
                array_push($nodes, $Node);
            }


            return $nodes;
        }


        public static function GetAssemblyType()
        {
            return "NULL";
        }

        public abstract function GetNodeJson();

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
            $quary = "INSERT INTO `node` ( `LocationID`, `Coordinate`,`Type`) VALUES (?,?,?) ";

           // die($Location->GetID().":::".json_encode($this->Coordinate).":::".$this::$Type);
            ExecuteSql($quary,array($Location->GetID(), json_encode($this->Coordinate), $this::$Type));

            $this->ID = $dbConn->lastInsertId();
        }
    }