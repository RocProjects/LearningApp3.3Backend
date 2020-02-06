<?php
    abstract class ResponseTypes
    {
        const succeeded = "succeeded";
        const ERROR = "ERROR";
    }

    class Response
    {
        public function __construct($Status,$body)
        {
            $this->ResponseStatus = $Status;
            $this->Error = $body;
        }

    }

    class ResponseBody extends Response
    {
        public function __construct($value)
        {
            $this->value = $value;
        }
    }
?>