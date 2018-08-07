<?php

namespace App\Controller;

use App\Entity\Answers;
use App\Entity\Endingquotes;
use App\Entity\Questions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UtilityController extends Controller
{
    public function addquestionmethod(Request $request)
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
        $endingquotes = $entityManager->getRepository(Endingquotes::class)->findBy(['quizid' => $quizid]);

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
        foreach ($endingquotes as $result) {
            $entityManager->remove($result);
        }
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
        $correctanswer = $request->request->get("correctanswer");
        //saving answers to databse
        foreach ($answerarray as $answer) {
            $answerEntity = new Answers();
            $answercounter++;
            $answerEntity->setAnswer($answer);
            //check if answer is correctanswer
            if ($answercounter == $correctanswer) {
                $answerEntity->setCorrectanswer(1);
            } else {
                $answerEntity->setCorrectanswer(0);
            }
            $answerEntity->setCreatorname($username);
            $answerEntity->setQuestionid($questionid);

            $entityManager->persist($answerEntity);
            $entityManager->flush();
        }

        $this->addFlash(
            'status',
            'Question succesfully added!'
        );
        return $this->redirect("editquestions/$quizid");
    }

    public function deletequestion($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $question = $entityManager->getRepository(Questions::class)->find($id);
        $quizid = $question->getQuizid();
        $endingquotes = $entityManager->getRepository(Endingquotes::class)->findBy(['quizid' => $quizid]);

        $entityManager->remove($question);
        foreach ($endingquotes as $result) {
            $entityManager->remove($result);
        }
        $entityManager->flush();

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



    public function editquestionmethod(Request $request)
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
        $correctanswer = $request->request->get("correctanswer");
        //saving answers to databse
        for($i = 0; $i < count($answerarray); $i++) {
            echo $answerarray[$i];
            if(isset($prevoiusidarray[$i]) && isset($answerarray[$i])) {
                $answerEntity = $this->getDoctrine()->getManager()->getRepository(Answers::class)->find($prevoiusidarray[$i]);
                $answercounter++;
                $answerEntity->setAnswer($answerarray[$i]);

                //check if answer is correctanswer
                if ($answercounter == $correctanswer) {
                    $answerEntity->setCorrectanswer(1);
                } else {
                    $answerEntity->setCorrectanswer(0);
                }
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
                //check if answer is correctanswer
                if ($answercounter == $correctanswer) {
                    $answerEntity->setCorrectanswer(1);
                } else {
                    $answerEntity->setCorrectanswer(0);
                }
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
        //return new Response('');
    }


    public function editresultsmethod(Request $request)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY') && !$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('index');
        }
        $user = $this->getUser();
        $username = $user->getUsername();
        $quizid = $request->request->get('quizid');
        $questioncount = $request->request->get('questioncount');

        if($questioncount == 1) {
            for ($i = 1; $i < 3; $i++) {
                $endingquote = new Endingquotes();
                $title = $request->request->get("text$i");
                $description = $request->request->get("description$i");
                $from = $request->request->get("from$i");
                $to = $request->request->get("to$i");

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
        } elseif($questioncount == 2) {
            for ($i = 1; $i < 4; $i++) {
                $endingquote = new Endingquotes();
                $title = $request->request->get("text$i");
                $description = $request->request->get("description$i");
                $from = $request->request->get("from$i");
                $to = $request->request->get("to$i");

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
        } elseif($questioncount > 2) {
            for ($i = 1; $i < 5; $i++) {
                $endingquote = new Endingquotes();
                $title = $request->request->get("text$i");
                $description = $request->request->get("description$i");
                $from = $request->request->get("from$i");
                $to = $request->request->get("to$i");

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
        }

        $this->addFlash(
            'status',
            'Results succesfully upadated!'
        );
        return $this->redirect("dashboard");
    }

    public function realeditresultsmethod(Request $request)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY') && !$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('index');
        }
        $user = $this->getUser();
        $username = $user->getUsername();
        $quizid = $request->request->get('quizid');
        $questioncount = $request->request->get('questioncount');

        if($questioncount == 1) {
            for ($i = 1; $i < 3; $i++) {
                $id = $request->request->get("resultid$i");
                $em = $this->getDoctrine()->getManager();
                $endingquote = $em->getRepository(Endingquotes::class)->find($id);
                $title = $request->request->get("text$i");
                $description = $request->request->get("description$i");
                $from = $request->request->get("from$i");
                $to = $request->request->get("to$i");
                echo 'jd'. $i;

                //upload question to database with image nothing special
                if ($_FILES["image$i"]) {
                    echo 'jd';
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
        } elseif($questioncount == 2) {
            for ($i = 1; $i < 4; $i++) {
                $id = $request->request->get("resultid$i");
                $em = $this->getDoctrine()->getManager();
                $endingquote = $em->getRepository(Endingquotes::class)->find($id);
                $title = $request->request->get("text$i");
                $description = $request->request->get("description$i");
                $from = $request->request->get("from$i");
                $to = $request->request->get("to$i");

                //upload question to database with image nothing special
                if ($_FILES["image$i"]) {
                    $uploaddir = "/$username/";
                    $image = $_FILES["image$i"]['name'];
                    $newpath = $uploaddir . $image;
                    //tmp_name needs realfilepath in index.php !important
                    $tmp_name = $_FILES["image$i"]['tmp_name'];
                    echo 'jd<br>';
                    move_uploaded_file($tmp_name, SITE_ROOT . $newpath);
                    if($image === null || $image === '' || $image === false) {
                        echo 'xd';
                        $previousimage = $endingquote->getImage();
                        $endingquote->setImage($previousimage);
                    } else {
                        echo 'xd';
                        $endingquote->setImage($image);
                    }
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
        } elseif($questioncount > 2) {
            for ($i = 1; $i < 5; $i++) {
                $id = $request->request->get("resultid$i");
                $em = $this->getDoctrine()->getManager();
                $endingquote = $em->getRepository(Endingquotes::class)->find($id);
                $title = $request->request->get("text$i");
                $description = $request->request->get("description$i");
                $from = $request->request->get("from$i");
                $to = $request->request->get("to$i");

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

                $em = $this->getDoctrine()->getManager();
                $em->persist($endingquote);
                $em->flush();
            }
        }

        $this->addFlash(
            'status',
            'Results succesfully upadated!'
        );
        return $this->redirect("dashboard");
    }
}