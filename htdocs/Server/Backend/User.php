<?php

    class UserClass
    {
        public $DisplayName = "";
        public $ID = -1;
       //public $StartingDate = "";

        public function __construct($data)
        {
            $this->DisplayName = $data->KlasName;
            $this->ID = $data->KlasID;
           // $this->StartingDate = $data->KlasStartDate;
        }
    }

    class User
    {
        public  $ID;
        public $FirstName;
        public $LastName;
        public $Klas;// Class Object
        public $IsTeacher;
    
        public function __construct($data)
        {
            //var_dump($data);
            $this->FirstName = $data->firstname;
            $this->LastName = $data->lastname;
            $this->IsTeacher = $data->teacher;
            $this->Username = $data->username;
            $this->ID = $data->UserID;

            if(isset($data->KlasName) || isset($data->KlasID))
            {
                $this->Klas = new UserClass($data);
            }
            else
            {
                $this->Klas = null;
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
?>