<?php
    class UserName
    {
        public function __construct(string $FirstName, string $LastName)
        {
            $this->first = $FirstName;
            $this->Last = $LastName;
        }
    }

    class PlaySpacePage
    {
        public $ID;
        public $Name;
        public $CreatorName;
        public $Description;
        public $Image; 

        public function __construct($pageInfo)
        {
            $this->ID = $pageInfo->ID;
            $this->Name = $pageInfo->Name;
            $this->CreatorName = new UserName($pageInfo->firstname,$pageInfo->lastname);
            $this->Description = $pageInfo->Description;
            $this->Image = $pageInfo->image;
        }
    }