<?php

abstract class Node
{
    public $Name;
    public $Location;

    public function __construct()
    {
        $this['$type'] = self::GetType();
        $bla = 0;
        $bla = "";
    }

  //  public static function LoadFromJson(object $jsonObj) : Node
  //  {
        //switch($jsonObj->$type)
        //
           // case InfoNode::GetType():
            //    return new InfoNode($jsonObj);
         //   break;
       // }
       // return null;
   // }



    public static function GetType() : string
    {
        return "NULL BASENODE";
    }

    //TODO Pass db
    public function Save()
    {
        //terrible case handeling but should be fine
     die("BASE CLASS CALLED");       
    }
}

class InfoNode extends Node
{
    public $Info;
    
    public static function LoadFromJson(object $jsonObj) : Node
    {
        $node = new InfoNode();
        return $node;
    }

    public static function GetType() : string
    {
        return "Core.PlaySpace.InfoNode, Assembly-CSharp";
    }

    //TODO Pass db
    public function Save()
    {
            
    }
}

class Location
{
    protected $ID = -1;
    protected $Index;
    public $Image = "0x00";
    public $Nodes = array(); 

    public static function LoadFromJson(object $jsonObj) : Location
    {
        $Location = new Location();
        if($jsonObj->bg != null){
            $Location->Image = $jsonObj->bg;
        }
        $Location->Index = $jsonObj->Index;

        foreach($jsonObj->nodes as $node)
        {
           // array_push($Location->Nodes, Node::LoadFromJson($node));
        }

        return $Location;

    }

    public function Save($Playspace)
    {
        global $dbConn;
        $dbStatement = $dbConn->prepare("INSERT INTO `playspacelocation` (`Owner`, `LocationID`, `Image`) VALUES (?,?,?)");

        try{
            //TODO fix image
            $dbStatement->execute(array($Playspace->GetID(), $this->Index, '0x00'));
        }catch(PDOException $e){
            die(new UserRegisterQuaryResponse($e));
        }

        foreach($this->Nodes as &$Node)
        {
            $Node->Save($this);
        }

        unset($Location);
    }

}

class PlaySpace
{
    protected  $ID = -1;
    public $Name = "";
    public $Description = "";
    public $Locations = array();

    public static function LoadFromJson(string $jsonData) : PlaySpace
    {
        
        $jsonObject = json_decode($jsonData);

        $PlaySpace = new PlaySpace();
        $PlaySpace->Name = $jsonObject->Name;
        $PlaySpace->Description = $jsonObject->Description;
        //$PlaySpace->ID = $_SESSION['User']->GetID();


        for ($x = 0; $x < count($jsonObject->locations); $x++) {
            $element = $jsonObject->locations[$x];
            $element->Index = $x;
            array_push($PlaySpace->Locations,Location::LoadFromJson($element));
        }

        return $PlaySpace;
    }

    public function Save()
    {
        TeacherSessionActive();
        
        global $dbConn;
        $dbStatement = $dbConn->prepare("INSERT INTO `playspaces` (`Name`, `CreatorID`, `Description`) VALUES (?,?,?) ");
        try{
            $dbStatement->execute(array($this->Name, $_SESSION['User']->GetID(), $this->Description));
            $this->ID = $dbConn->lastInsertId();
        }catch(PDOException $e){
            die(new UserRegisterQuaryResponse($e));
        }

        
        foreach($this->Locations as &$location)
        {
            $location->Save($this);
        }
    
      //  die(new Response(ResponseTypes::succeeded, " ".$this->Name." ID:".$this->ID));
    }

    public function GetID() :int
    {
        return $this->ID;
    }

    public function __toString()
    {
        return "ID: ".$this->ID."  Name:".$this->Name." Description:".$this->Description." Locationsize:".count($this->Locations);
    }
}
