<?php

namespace App\Controller;

use App\Entity\Quiz;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Questions;
use App\Entity\Answers;
use App\Entity\Endingquotes;
use Symfony\Component\HttpFoundation\Response;

class PsychotestController extends Controller
{
    public function addresultspsychotest(Request $request)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY') && !$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('index');
        }
        $user = $this->getUser();
        $username = $user->getUsername();
        $quizid = $request->request->get('quizid');

        for ($i = 1; $i <= 4; $i++) {
            $endingquote = new Endingquotes();
            $title = $request->request->get("text$i");
            $description = $request->request->get("description$i");

            //selectin percatages
            if($i == 1) {
                $from = 0;
                $to = 25;
            }
            if($i == 2) {
                $from = 26;
                $to = 50;
            }
            if($i == 3) {
                $from = 51;
                $to = 75;
            }
            if($i == 4) {
                $from = 76;
                $to = 100;
            }
            //upload question to database with image nothing special
            if ($_FILES["image$i"]) {
                $uploaddir = "/$username/";
                $image = $_FILES["image$i"]['name'];
                $newpath = $uploaddir . $image;
                //tmp_name needs realfilepath in index.php !important
                $tmp_name = $_FILES["image$i"]['tmp_name'];
                move_uploaded_file($tmp_name, SITE_ROOT . $newpath);
                $endingquote->setImage($image);
            }
            $endingquote->setAuthorname($username);
            $endingquote->setQuizid($quizid);
            $endingquote->setTitle($title);
            $endingquote->setQuote($description);
            $endingquote->setMinanswers($from);
            $endingquote->setMaxanswers($to);

            $em = $this->getDoctrine()->getManager();
            $em->persist($endingquote);
            $em->flush();
        }


        $this->addFlash(
            'status',
            'Results succesfully added!'
        );
        return $this->redirect("dashboard");
    }


    public function editresultspsychotest(Request $request)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY') && !$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('index');
        }
        $user = $this->getUser();
        $username = $user->getUsername();
        $em = $this->getDoctrine()->getManager();
        $quizid = $request->request->get('quizid');

        for ($i = 1; $i <= 4; $i++) {
            $endingquoteid = $request->request->get("resultid$i");
            $endingquote = $em->getRepository(Endingquotes::class)->find($endingquoteid);
            $title = $request->request->get("text$i");
            $description = $request->request->get("description$i");

            //selectin percatages
            if($i == 1) {
                $from = 0;
                $to = 25;
            }
            if($i == 2) {
                $from = 26;
                $to = 50;
            }
            if($i == 3) {
                $from = 51;
                $to = 75;
            }
            if($i == 4) {
                $from = 76;
                $to = 100;
            }
            //upload question to database with image nothing special
            if ($_FILES["image$i"]) {
                $uploaddir = "/$username/";
                $image = $_FILES["image$i"]['name'];
                $newpath = $uploaddir . $image;
                //tmp_name needs realfilepath in index.php !important
                $tmp_name = $_FILES["image$i"]['tmp_name'];
                move_uploaded_file($tmp_name, SITE_ROOT . $newpath);
                if($image == null || $image == '' || $image == false) {
                    $previousimage = $endingquote->getImage();
                    $endingquote->setImage($previousimage);
                } else {
                    $endingquote->setImage($image);
                }
            }
            $endingquote->setAuthorname($username);
            $endingquote->setQuizid($quizid);
            $endingquote->setTitle($title);
            $endingquote->setQuote($description);
            $endingquote->setMinanswers($from);
            $endingquote->setMaxanswers($to);

            $em->persist($endingquote);
            $em->flush();
        }


        $this->addFlash(
            'status',
            'Results succesfully upadated!'
        );
        return $this->redirect("dashboard");
    }


    public function addquestionpsychotest(Request $request)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY') && !$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('index');
        }
        $title = $request->request->get('question');
        $quizid = $request->request->get('quizid');
        $questionnumber = $request->request->get('questionnumber');
        $question = new Questions();
        $user = $this->getUser();
        $username = $user->getUsername();
        $answerarray = array();

        //deletin endingquotes
        $entityManager = $this->getDoctrine()->getManager();

        //upload question to database with image nothing special
        if ($_FILES['image']) {
            $uploaddir = "/$username/";
            $image = $_FILES['image']['name'];
            $newpath = $uploaddir . $image;
            //tmp_name needs realfilepath in index.php !important
            $tmp_name = $_FILES['image']['tmp_name'];
            move_uploaded_file($tmp_name, SITE_ROOT . $newpath);
            $question->setImage($image);
        }
        $question->setAuthorname($username);
        $question->setQuestion($title);
        $question->setQuestionnumber($questionnumber);
        $question->setQuizid($quizid);
        $entityManager->persist($question);
        $entityManager->flush();

        $questionid = $question->getId();

        //lets go with answers
        for ($i = 1; $i <= 10; $i++) {
            //check if answer isset
            if ($request->request->get("answer$i") != '') {
                //save answer to array
                $answerarray[] = $request->request->get("answer$i");
            }
        }
        $answercounter = 0;
        //saving answers to databse
        foreach ($answerarray as $answer) {
            $answerEntity = new Answers();
            $answercounter++;
            $score = $request->request->get("correctanswer$answercounter");
            if($answer != '' && $score != '' && is_numeric($score)) {
                echo $score.'<br>';
                $answerEntity->setAnswer($answer);
                $answerEntity->setCorrectanswer($score);
                $answerEntity->setCreatorname($username);
                $answerEntity->setQuestionid($questionid);

                $entityManager->persist($answerEntity);
                $entityManager->flush();
            }
        }

        $this->addFlash(
            'status',
            'Question succesfully added!'
        );
        return $this->redirect("editquestions/$quizid");
    }


    public function deletequestionpsychotest($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $question = $entityManager->getRepository(Questions::class)->find($id);
        $quizid = $question->getQuizid();

        $entityManager->remove($question);

        //need to edit question numbers
        $toeditquestionnumbers = $entityManager->getRepository(Questions::class)->findBy(['quizid' => $quizid]);
        $counter = 1;
        $questiontoedit = '';
        foreach($toeditquestionnumbers as $questiontoedit) {
            $questiontoedit->setQuestionnumber($counter);
            $counter++;
        }
        $entityManager->persist($questiontoedit);
        $entityManager->flush();

        $this->addFlash(
            'status',
            'Question succesfully deleted!'
        );
        return $this->redirectToRoute('editquestions', array('id' => $quizid));
    }


    public function EditQPMethod(Request $request)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY') && !$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('index');
        }
        $title = $request->request->get('question');
        $id = $request->request->get('questionid');
        $quizid = $request->request->get('quizid');
        $questionnumber = $request->request->get('questionnumber');
        $entityManager = $this->getDoctrine()->getManager();
        $question = $entityManager->getRepository(Questions::class)->find($id);
        $user = $this->getUser();
        $username = $user->getUsername();
        $answerarray = array();
        $prevoiusidarray = array();

        //upload question to database with image nothing special
        if ($_FILES['image']) {
            $uploaddir = "/$username/";
            $image = $_FILES['image']['name'];
            $newpath = $uploaddir . $image;
            //tmp_name needs realfilepath in index.php !important
            $tmp_name = $_FILES['image']['tmp_name'];
            move_uploaded_file($tmp_name, SITE_ROOT . $newpath);
            if($image == null || $image == '' || $image == false) {
                $previousimage = $question->getImage();
                $question->setImage($previousimage);
            } else {
                $question->setImage($image);
            }
        }
        $question->setAuthorname($username);
        $question->setQuestion($title);
        $question->setQuestionnumber($questionnumber);
        $question->setQuizid($quizid);

        $entityManager->persist($question);
        $entityManager->flush();

        $questionid = $question->getId();

        //lets go with answers
        for ($i = 1; $i <= 10; $i++) {
            //check if answer isset
                //save answer to array
                $answerarray[] = $request->request->get("answer$i");
        }

        //previous answers
        for ($i = 1; $i <= 10; $i++) {
            //check if answer isset
            if ($request->request->get("previousanswerid$i") != '') {
                //save answer to array
                $prevoiusidarray[] = $request->request->get("previousanswerid$i");
            }
        }
        $answercounter = 0;
        //saving answers to databse
        for($i = 0; $i < count($answerarray); $i++) {
            if(isset($prevoiusidarray[$i]) && isset($answerarray[$i])) {
                $answerEntity = $this->getDoctrine()->getManager()->getRepository(Answers::class)->find($prevoiusidarray[$i]);
                $answercounter++;
                $answerEntity->setAnswer($answerarray[$i]);


                $correctanswerindex = $i + 1;
                $correctanswer = $request->request->get("correctanswer$correctanswerindex");

                $answerEntity->setCorrectanswer($correctanswer);

                $answerEntity->setCreatorname($username);
                $answerEntity->setQuestionid($questionid);

                $entityManager->persist($answerEntity);
                $entityManager->flush();

            }
            if (array_key_exists($i, $prevoiusidarray) && $answerarray[$i] == '') {

                $answerEntity = $this->getDoctrine()->getManager()->getRepository(Answers::class)->find($prevoiusidarray[$i]);
                $entityManager->remove($answerEntity);
                $entityManager->flush();

            }
           if(!isset($prevoiusidarray[$i]) && $answerarray[$i] != '') {


                $answerEntity = new Answers();
                $answercounter++;
                $answerEntity->setAnswer($answerarray[$i]);

                $correctanswer = $request->request->get("correctanswer$i");
                $answerEntity->setCorrectanswer($correctanswer);
                $answerEntity->setCreatorname($username);
                $answerEntity->setQuestionid($questionid);

                $entityManager->persist($answerEntity);
                $entityManager->flush();

            }
        }

        $this->addFlash(
            'status',
            'Question succesfully updated!'
        );
        return $this->redirect("editquestions/$quizid");
    }

    public function checkscorepsychotest($score,$id)
    {
        $endingqoutesDB = $this->getDoctrine()->getRepository(Endingquotes::class)->findBy(['quizid' => $id]);
        $QuestionsDB = $this->getDoctrine()->getRepository(Questions::class)->findBy(['quizid' => $id]);

        $maxscore = 0;
        $em = $this->getDoctrine()->getManager();
        foreach ($QuestionsDB as $question) {
            $questionid = $question->getId();
            $query = $em->createQuery('SELECT correctanswer FROM App\Entity\Answers correctanswer WHERE correctanswer.questionid = '.$questionid.'ORDER BY correctanswer.correctanswer DESC');
            $maxpointsfromanswer = $query->setMaxResults(1)->getResult();
            foreach ($maxpointsfromanswer as $maxpointfromanswer) {
                $maxpointsfromanswer = $maxpointfromanswer->getCorrectanswer();
            }
            $maxscore += intval($maxpointsfromanswer);
        }
        $percentagescore = ($score / $maxscore) * 100;

        $endingquoteid = 0;
        foreach ($endingqoutesDB as $endingquote) {
            if($percentagescore >= $endingquote->getMinanswers() && $percentagescore <= $endingquote->getMaxanswers())  {
                $endingquoteid = $endingquote->getId();
            }
        }

        //solutions add
        $QuizDB = $this->getDoctrine()->getRepository(Quiz::class)->findOneBy(['id' => $id]);
        $solutions = $QuizDB->getSolutions();
        $solutions += 1;
        $QuizDB->setSolutions($solutions);
        $em->persist($QuizDB);
        $em->flush();

        $toreturn = array(0 => $endingquoteid, 1 => $percentagescore);
        return new Response(json_encode($toreturn));
    }


}