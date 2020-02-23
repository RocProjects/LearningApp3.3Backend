<?php
     include_once __DIR__.'/Node.php';
     class MediaNode extends Node
     {
         public $image;
         public static $Type = "Media";
 
         public static function LoadFromJson(object $jsonObj) : Node
         {
             $node = new MediaNode();
             $node->image = $jsonObj->image;
             return $node;
         }
 
         public static function GetAssemblyType() : string
         {
             return "Core.PlaySpace.MediaNode, Assembly-CSharp";
         }
 
         //TODO Pass db
         public function Save($LocationOBJ)
         {
             parent::Save($LocationOBJ);
 
             global $dbConn;
             $dbStatement = $dbConn->prepare("INSERT INTO `medianode` (`ID`, `Image`) VALUES (?,?) ");
             try{
                 $dbStatement->execute(array($this->ID, $this->image));
             }catch(PDOException $e){
                 die(new UserRegisterQuaryResponse($e));
             }
         }
         public function GetNodeJson() : array
         {
             return array(
                 'image'=> $this->image
             );
         }
    }