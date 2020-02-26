<?php
    class UserName
    {
        public function __construct(string $FirstName, string $LastName)
        {
            $this->first = mb_convert_encoding($FirstName, 'UTF-8', 'UTF-8');
            $this->Last = mb_convert_encoding($LastName, 'UTF-8', 'UTF-8');
        }
    }

    class PlaySpacePage
    {
        public $ID = -1;
        public $Name = "";
        public $CreatorName;
        public $Description = "";
        public $Image = ""; 

        public function __construct($pageInfo)
        {
            $this->ID =  mb_convert_encoding($pageInfo->ID, 'UTF-8', 'UTF-8');
            $this->Name = mb_convert_encoding($pageInfo->Name, 'UTF-8', 'UTF-8');
            $this->CreatorName = new UserName($pageInfo->firstname,$pageInfo->lastname);
            $this->Description = mb_convert_encoding($pageInfo->Description, 'UTF-8', 'UTF-8');
           // $this->Image = $pageInfo->image;
        }
    }