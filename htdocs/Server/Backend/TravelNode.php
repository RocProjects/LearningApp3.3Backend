<?php
     //include_once __DIR__.'/Node.php';
     class TravelNode extends Node
     {
         protected static $Type = "Travel";
         public $TargetLocation = -1;
 
         public static function LoadFromJson(object $jsonObj) : Node
         {
             $node = new TravelNode();
             $node->TargetLocation = $jsonObj->TargetLocation;
             return $node;
         }
 
         public static function GetAssemblyType() : string
         {
             return "Core.PlaySpace.TravelNode, Assembly-CSharp";
         }
 
         //TODO Pass db
         public function Save($LocationOBJ)
         {
             parent::Save($LocationOBJ);
 
            // global $dbConn;
            // $dbStatement = $dbConn->prepare("INSERT INTO `infonode` (`ID`, `info`) VALUES (?,?) ");
             try{
             //    $dbStatement->execute(array($this->ID, $this->Info));
             }catch(PDOException $e){
                 die(new UserRegisterQuaryResponse($e));
             }
         }

         public function GetNodeJson() : array
         {
             return array(
                 'TRAVELNODE'=> "TODO"
             );
         }
    }