<?php
abstract class ResponseTypes
{
    const succeeded = 0;
    const FatalError = 1;
    const Silent_FatalError = 2;
}

class Response
{
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

class ResponseBody extends Response
{
    public function __construct($value)
    {
        $this->value = $value;
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
    public function GetDBStatusMessage(mysqli_stmt $db)
    {
        return $db->errno;
    }

    public function __construct(mysqli_stmt $db)
    {
        $this->ResponseStatus = ResponseTypes::FatalError;
        $e = new Exception();
        $this->StackCall = $e->getTraceAsString();

        $this->StatusMessage = $this->GetDBStatusMessage($db);
    }
}

class UserRegisterQuaryResponse extends QuaryResponse
{
    public function GetDBStatusMessage(mysqli_stmt $db)
    {
        if ($db->errno == 1062) {
            return "UserName not available";
        }
        return "$db->errno";
    }
}


class User
{
    public function __construct(mysqli_stmt $data)
    {
        $data->bind_result($this->firstName, $this->lastName, $this->Klas, $this->isTeacher);
        $data->fetch();
    }

    public function __toString()
    {
        return json_encode($this);
    }
}
