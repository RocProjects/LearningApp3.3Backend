<?php

  foreach (glob(__DIR__.'/Backend/*.php') as $file) {
      include_once $file;
  }

  abstract class ResponseTypes
  {
      const succeeded = 0;
      const FatalError = 1;
      const Silent_FatalError = 2;
  }

  class Response
  {
      public $ResponseStatus;

      public function __construct($Status, $body)
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
          $json = json_encode($this);
          if($json == "")
          {
            return die(New Response(ResponseTypes::FatalError,"Json Encoding failed ! :". json_last_error()));     
          }
         
          return $json;
      }
  }

  class UserLoginResponse extends Response
  {
      public function __construct($Status,$body,$user)
      {
          parent::__construct($Status, $body);
          $this->user = $user;
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
          parent::__construct(ResponseTypes::FatalError, $this->GetDBStatusMessage($e));
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
          parent::__construct(ResponseTypes::succeeded, "Event Executed");
      }
  }

  class UserPage
  {
      public $ID;
      public $FirstName;
      public $LastName;

      public function __construct($data)
      {
          $this->ID = $data->ID;
          $this->FirstName = $data->firstname;
          $this->LastName = $data->lastname;
      }
  }

  class UsersPageResponse extends Response
  {
      public $UserInfo = array();
      public function __construct($Result)
      {
          $this->ResponseStatus = ResponseTypes::succeeded;
          $this->StatusMessage = "Recieved Users";

          foreach ($Result as $element) {
              array_push($this->UserInfo, new UserPage($element));
          }
      }
  }

  class PlaySpacePageResponse extends Response 
  {
    public $PlaySpaces = array();
    public function __construct(array $returnedData)
    {
        $this->ResponseStatus = ResponseTypes::succeeded;

        foreach ($returnedData as $element) {
            array_push($this->PlaySpaces, new PlaySpacePage($element));
        }
    }

  }

  class Klas
  {
      public $ID;
      public $Name;

      public function __construct($data)
      {
          
        $this->ID = $data->ID;
        $this->Name = isset($data->DisplayName) ? $data->DisplayName : $data->Name;
      }
  }

  class KlasResponse extends Response
  {
    public $klas;

    public function __construct($data)
    {
        $this->ResponseStatus = ResponseTypes::succeeded;
        $this->klas = new klas($data);
    }
  }

  class KlassenResponse extends Response
  {
      
    public $Klassen = array();
    public function __construct(array $returnedData)
    {
        $this->ResponseStatus = ResponseTypes::succeeded;

        foreach ($returnedData as $element) {
            array_push($this->Klassen, new Klas($element));
        }
    }
  }

  class ExceptionResponse extends Response
  {
    public function __construct(Exception $e)
    {
        $this->ResponseStatus = ResponseTypes::FatalError;
        $this->StatusMessage = $e->getMessage();

        $this->StackCall = $e->getTraceAsString();
    }
  }

  