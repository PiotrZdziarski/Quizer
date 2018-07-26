<?php
namespace App\Controller;

use App\Entity\Answers;
use App\Entity\Endingquotes;
use App\Entity\Questions;
use App\Entity\Quiz;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UIcontroller extends Controller
{
    public function checkuser($username)
    {
        $nosuchuser = 0;
        $usernamecheck = $this->getDoctrine()->getRepository(User::class)->findBy(['username' => $username]);
        if($usernamecheck == null) {
            $nosuchuser = 1;
        }
        return new Response($nosuchuser);
    }

    public function checkscore($score, $id)
    {
        $endingqoutesDB = $this->getDoctrine()->getRepository(Endingquotes::class)->findBy(['quizid' => $id]);
        foreach($endingqoutesDB as $endingquote) {
            if($score >= $endingquote->getMinanswers() && $score <= $endingquote->getMaxanswers()) {
                $endingqouteid = $endingquote->getId();
            }
        }
        return new Response($endingqouteid);
    }

    public function checkemail($email)
    {
        $nosuchuser = 0;
        $usernamecheck = $this->getDoctrine()->getRepository(User::class)->findBy(['email' => $email]);
        if($usernamecheck == null) {
            $nosuchuser = 1;
        }
        return new Response($nosuchuser);
    }

    public function createquizmethod(Request $request)
    {
        if(!$this->isGranted('IS_AUTHENTICATED_FULLY') || !$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('index');
        }
        $title = $request->request->get('title');
        $description = $request->request->get('description');
        $category = $request->request->get('category');
        $publicprivate = $request->request->get('publicprivate');
        $type = $request->request->get('type');
        $user = $this->getUser();
        $username = $user->getUsername();
        $quizObject = new Quiz();
        $date = date("Y-m-d H:i:s");
        $date = date_create_from_format('Y-m-d H:i:s', $date);
        $date->getTimestamp();
        //uploading file from user
        if($_FILES['image']) {
            $uploaddir = "/$username/";
            $image = $_FILES['image']['name'];
            $newpath = $uploaddir.$image;
            //tmp_name needs realfilepath in index.php !important
            $tmp_name = $_FILES['image']['tmp_name'];
            move_uploaded_file($tmp_name, SITE_ROOT.$newpath);
            $quizObject->setImage($image);
        }

        $quizObject->setTitle($title);
        $quizObject->setPublic($publicprivate);
        $quizObject->setCategoryid(intval($category));
        $quizObject->setTypeid(intval($type));
        $quizObject->setDescription($description);
        $quizObject->setDate($date);
        $quizObject->setAuthorname($username);
        $quizObject->setSolutions(0);
        $quizObject->setComments(0);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($quizObject);
        $entityManager->flush();

        $this->addFlash(
            'status',
            'Quiz successfully uploaded!'
        );
        return $this->redirectToRoute('dashboard');
    }

    public function editquizmethod(Request $request)
    {
        if(!$this->isGranted('IS_AUTHENTICATED_FULLY') || !$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('index');
        }
        $title = $request->request->get('title');
        $description = $request->request->get('description');
        $category = $request->request->get('category');
        $quizid = $request->request->get('quizid');
        $publicprivate = $request->request->get('publicprivate');
        $user = $this->getUser();
        $username = $user->getUsername();
        $entityManager = $this->getDoctrine()->getManager();
        $quizObject = $entityManager->getRepository(Quiz::class)->find($quizid);
        $date = date("Y-m-d H:i:s");
        $date = date_create_from_format('Y-m-d H:i:s', $date);
        $date->getTimestamp();
        //uploading file from user
        if($_FILES['image']) {
            $uploaddir = "/$username/";
            $image = $_FILES['image']['name'];
            $newpath = $uploaddir.$image;
            //tmp_name needs realfilepath in index.php !important
            $tmp_name = $_FILES['image']['tmp_name'];
            move_uploaded_file($tmp_name, SITE_ROOT.$newpath);
            if($image == null || $image == '' || $image == false) {
                $previousimage = $quizObject->getImage();
                $quizObject->setImage($previousimage);
            } else {
                $quizObject->setImage($image);
            }
        }

        $type = $quizObject->getTypeid();

        $quizObject->setTitle($title);
        $quizObject->setPublic($publicprivate);
        $quizObject->setCategoryid(intval($category));
        $quizObject->setTypeid($type);
        $quizObject->setDescription($description);
        $quizObject->setDate($date);
        $quizObject->setAuthorname($username);
        $quizObject->setSolutions(0);
        $quizObject->setComments(0);

        $entityManager->persist($quizObject);
        $entityManager->flush();

        $this->addFlash(
            'status',
            'Quiz successfully edited!'
        );
        return $this->redirectToRoute('dashboard');
    }

    public function deletequiz($quizid)
    {
        $em = $this->getDoctrine()->getManager();
        $QuizDB = $em->getRepository(Quiz::class)->find($quizid);
        $QuestionsDB = $em->getRepository(Questions::class)->findBy(['quizid' => $quizid]);
        foreach ($QuestionsDB as $question) {
            $questionid =$question->getId();
            $AnswerDB = $em->getRepository(Answers::class)->findBy(['questionid' => $questionid]);
            foreach($AnswerDB as $answer) {
                 $em->remove($answer);
            }
            $em->remove($question);
        }
        $ResultsDB = $em->getRepository(Endingquotes::class)->findBy(['quizid' => $quizid]);
        foreach ($ResultsDB as $result) {
            $em->remove($result);
        }
        $em->remove($QuizDB);
        $em->flush();
        $this->addFlash(
            'status',
            'Quiz successfully deleted!'
        );
        return $this->redirectToRoute('dashboard');
    }
}