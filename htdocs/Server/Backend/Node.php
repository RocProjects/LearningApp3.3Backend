<?php
    abstract class Node implements JsonSerializable 
    {
        public $ID = -1;
        public $Name = "";
        public $Location;
        protected static $Type = "Node";

        public static function LoadFromJson($jsonObj)
        {
            $NodeType = $jsonObj->{'$type'};

            switch($NodeType)
            {     
                case InfoNode::GetAssemblyType():
                    $Node = InfoNode::LoadFromJson($jsonObj);
                break;
                case QuizNode::GetAssemblyType():
                    $Node = QuizNode::LoadFromJson($jsonObj);
                break;
                case MediaNode::GetAssemblyType();
                    $Node = MediaNode::LoadFromSQL($jsonObj);
                break;
                default:
                    die(new Response(ResponseTypes::FatalError,"Unkown NodeType: ".$NodeType));
                break;
            }
            $Node->Location = $jsonObj->Location;
            $Node->Name = $jsonObj->Name;

            return $Node;
        }

        public static function LoadFromSQL($LocationID)
        {
            global $dbConn;
            //TODO SORT ON LOCATION ID
            $quary = "SELECT `ID` , `Name`, `Coordinate`,`Type`FROM `node` WHERE `LocationID`=?";
            $result = ExecuteSql($quary,array($LocationID));
            $nodes = array();
            foreach ($result as $NodeData) 
            {
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
                $Node->Location = json_decode ($NodeData->Coordinate);
                $Node->ID = $NodeData->ID;
                $Node->Name = $NodeData->Name;
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
                'Location' => $this->Location,
                'ID' => $this->ID,
                'Name' => $this->Name
            );

            return array_merge($JsonArray,$this->GetNodeJson());
        }


        //TODO Pass db
        public function Save($Location)
        {
            global $dbConn;
            
            //INSERT INTO `node` (`ID`, `LocationID`, `Coordinate`, `Type`) VALUES (NULL, '', '', '')
            $quary = "INSERT INTO `node` ( `LocationID`, `name`, `Coordinate`,`Type`) VALUES (?,?,?,?) ";

           // die($Location->GetID().":::".json_encode($this->Coordinate).":::".$this::$Type);
            ExecuteSql($quary,array($Location->GetID(), $this->Name,json_encode($this->Location), $this::$Type));

            $this->ID = $dbConn->lastInsertId();
        }
    }