<?php
    class User
    {
        protected  $ID;
        public $firstName;
        public $lastName;
        public $Klas;
        public $IsTeacher;
    
        public function __construct($data)
        {
            //var_dump($data);
            $this->FirstName = $data->firstname;
            $this->LastName = $data->lastname;
            $this->IsTeacher = $data->teacher;
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
?>