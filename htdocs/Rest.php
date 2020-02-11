<?php
abstract class ResponseTypes
{
    const succeeded = 0;
    const FatalError = 1;
    const Silent_FatalError = 2;
}

class Response
{
    public int $ResponseStatus;
    public function __construct(int $Status, $body)
    {
        $this->ResponseStatus = $Status;
        $this->StatusMessage = $body;

        if ($Status == ResponseTypes::FatalError) {
            $e = new Exception();
            $this->StackCall = $e->getTraceAsString();
        }
    }

    public function __toString()
    {
        return json_encode($this);
    }
}

class UserLoginResponse extends Response
{
    public function __construct(int $Status,string $body ,User $user)
    {
        parent::__construct($Status,$body);
        $this->user = $user;
    }
    public function __toString()
    {
        return json_encode($this);
    }
}

class QuaryResponse extends Response
{
    public function GetDBStatusMessage(PDOException $e)
    {
        return $e->errno;
    }

    public function __construct(PDOException $e)
    {
        parent::__construct(ResponseTypes::FatalError,$this->GetDBStatusMessage($e));
    }
}

class UserRegisterQuaryResponse extends QuaryResponse
{
    public function GetDBStatusMessage(PDOException $e)
    {
        if ($e->errorInfo[1] == 1062) {
            return "UserName not available";
        }
        return $e->errorInfo[1];
    }
}

class EventSucceeded extends Response
{
    public function __construct()
    {
        parent::__construct(ResponseTypes::succeeded,"Event Executed");
    }
}

class UsersPageResponse extends Response
{
    public $UserInfo = array();
    public function __construct(PDOStatement $db)
    {
        $this->ResponseStatus = ResponseTypes::succeeded;
        $this->StatusMessage = json_encode($db->fetchAll(PDO::FETCH_OBJ));

        foreach ($db->fetchAll(PDO::FETCH_OBJ) as $element)
        {
            array_push($this->UserInfo,new user($element));
        }
    }
}


abstract class Node
{
    public string $Name;
    public object $Location;

    public function __construct()
    {
        $this['$type'] = self::GetType();
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
    public string $Info;
    
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
    protected int $ID = -1;
    protected int $Index;
    public string $Image = "0x00";
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
    protected  int $ID = -1;
    public string $Name;
    public string $Description;
    public $Locations = array();

    public static function LoadFromJson(string $jsonData) : PlaySpace
    {
        $jsonObject = json_decode($jsonData);

        $PlaySpace = new PlaySpace();
        $PlaySpace->Name = $jsonObject->Name;
        $PlaySpace->Description = $jsonObject->Description;
        $PlaySpace->ID = $_SESSION["User"]->GetID();


        for ($x = 0; $x < count($jsonObject->locations); $x++) {
            $element = $jsonObject->locations[$x];
            $element->Index = $x;
            array_push($PlaySpace->Locations,Location::LoadFromJson($element));
        }

        return $PlaySpace;
    }

    public function Save()
    {
        SessionActive();
        
        global $dbConn;
        $dbStatement = $dbConn->prepare("INSERT INTO `playspaces` (`Name`, `CreatorID`, `Description`) VALUES (?,?,?) ");
        try{
            $dbStatement->execute(array($this->Name, $this->ID, $this->Description));
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



/*
{
  "locations": [
    {
      "bg": null,
      "nodes": [
        {
          "$type": "Core.PlaySpace.InfoNode, Assembly-CSharp",
          "Info": null,
          "Name": "BaseNode",
          "Location": {
            "x": 0.0,
            "y": 0.0,
            "z": 0.0
          }
        },
        {
          "$type": "Core.PlaySpace.QuizNode, Assembly-CSharp",
          "quiz": {
            "questions": [
              {
                "answerInfo": [
                  "yes",
                  "no"
                ],
                "correctAnswers": null,
                "info": "Test",
                "maxTime": 69
              }
            ]
          },
          "Name": "BaseNode",
          "Location": {
            "x": 0.0,
            "y": 0.0,
            "z": 0.0
          }
        }
      ]
    }
  ],
  "Name": "",
  "Description": ""
}
*/

class User
{
    protected int $ID;
    public string $firstName;
    public string $lastName;
    public string $Klas;
    public bool $IsTeacher;

    public function __construct($data)
    {
        //var_dump($data);
        $this->FirstName = $data->firstname;
        $this->LastName = $data->lastname;
        $this->IsTeacher = $data->teacher;
        echo($data->ID);
        $this->ID = $data->ID;

        if($data->klas == null)
        {
            $this->Klas = "none";
        }
        else{
            $this->Klas = $data->klas;
        }
    }

    public function GetID() :int
    {
        return $this->ID;
    }

    public function __toString()
    {
        return json_encode($this);
    }
}
