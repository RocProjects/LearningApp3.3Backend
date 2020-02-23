<?php
    class GetPlaySpaceResponse extends Response
    {
        public $playSpace;
        public function __construct(PlaySpace $playSpace = null)
        {
            $this->playSpace = $playSpace;

            $this->ResponseStatus = ResponseTypes::succeeded;
        }
    }