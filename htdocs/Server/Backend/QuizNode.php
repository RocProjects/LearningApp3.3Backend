<?php
    include_once 'Node.php';
    
    class Quiz
    {
        public $questions = array();
        public function __construct($data)
        {
            foreach ($data->questions as $question) {
                array_push($this->questions,new QuizQuestion($question));
            }
        }

    }

    class QuizQuestion 
    {
        protected $QuestionIndex = -1;
        public $info;
        public $maxTime;
        public $answerInfo;
        public $correctAnswers;

        public function GetIndex() : int
        {
            return $this->QuestionIndex;
        }

        public function SetIndex(int $newIndex)
        {
            $this->QuestionIndex = $newIndex;
        }

        public function __construct($data)
        {
            $this->info = $data->info;
            $this->maxTime = $data->maxTime;
            $this->answerInfo = $data->answerInfo;
            $this->correctAnswers = $data->correctAnswers;
        }

    }

    class QuizNode extends Node
    {
        public $quiz;
        protected $Type = "Quiz";

        public static function LoadFromJson(object $jsonObj) : Node
        {
            $node = new QuizNode();
            $node->quiz = new Quiz($jsonObj->quiz);
            $node->Questions = $jsonObj->quiz;
            return $node;
        }

        public static function GetAssemblyType() : string
        {
            return "Core.PlaySpace.QuizNode, Assembly-CSharp";
        }

        //TODO Pass db
        public function Save($LocationOBJ)
        {
            parent::Save($LocationOBJ);

            global $dbConn;
            $dbStatement = $dbConn->prepare("INSERT INTO `quiznode` (`ID`,`QuestionID`, `Question`, `Options`, `Answers`) VALUES (?, ?, ?, ?, ?)");
            try{
                $index = 0;
                foreach ($this->quiz->questions as &$question) {
                    $question->SetIndex($index);
                    $dbStatement->execute(array($this->ID, $question->GetIndex(),$question->info,json_encode($question->answerInfo),json_encode($question->correctAnswers)));
                    $index++;
                }
                unset($question);
            }catch(PDOException $e){
                die(new UserRegisterQuaryResponse($e));
            }
        }
    }