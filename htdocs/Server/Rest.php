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
          return json_encode($this);
      }
  }

  class UserLoginResponse extends Response
  {
      public function __construct($Status,$body,$user)
      {
          parent::__construct($Status, $body);
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

  class UsersPageResponse extends Response
  {
      public $UserInfo = array();
      public function __construct(PDOStatement $db)
      {
          $this->ResponseStatus = ResponseTypes::succeeded;
          $this->StatusMessage = json_encode($db->fetchAll(PDO::FETCH_OBJ));

          foreach ($db->fetchAll(PDO::FETCH_OBJ) as $element) {
              array_push($this->UserInfo, new user($element));
          }
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
