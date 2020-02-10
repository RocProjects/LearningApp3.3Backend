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
        $this->ResponseStatus = $Status;
        $this->StatusMessage = $body;
        $this->user = $user;

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

class QuaryResponse extends Response
{
    public function GetDBStatusMessage(PDOStatement $db)
    {
        return $db->errno;
    }

    public function __construct(PDOStatement $db)
    {
        $this->ResponseStatus = ResponseTypes::FatalError;
        $e = new Exception();
        $this->StackCall = $e->getTraceAsString();

        $this->StatusMessage = $this->GetDBStatusMessage($db);
    }
}

class UserRegisterQuaryResponse extends QuaryResponse
{
    public function GetDBStatusMessage(PDOStatement $db)
    {
        if ($db->errno == 1062) {
            return "UserName not available";
        }
        return "$db->errno";
    }
}

class EventSucceeded extends Response
{
    public function __construct()
    {
        $this->ResponseStatus = ResponseTypes::succeeded;
        $this->StatusMessage = "Event Executed";
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



class User
{
    public function __construct($data)
    {
        //var_dump($data);
        $this->firstName = $data->firstname;
        $this->lastName = $data->lastname;
        $this->Klas = $data->klas;
        $this->isTeacher = $data->teacher;
    }

    public function __toString()
    {
        return json_encode($this);
    }
}
