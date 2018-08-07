<?php
namespace App\Controller;

use App\Entity\Answers;
use App\Entity\Categories;
use App\Entity\Endingquotes;
use App\Entity\Questions;
use App\Entity\Quiz;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
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
        $endingqouteid = 1;
        foreach($endingqoutesDB as $endingquote) {
            if($score >= $endingquote->getMinanswers() && $score <= $endingquote->getMaxanswers()) {
                $endingqouteid = $endingquote->getId();
            }
        }
        //solutions add
        $em = $this->getDoctrine()->getManager();
        $QuizDB = $this->getDoctrine()->getRepository(Quiz::class)->findOneBy(['id' => $id]);
        $solutions = $QuizDB->getSolutions();
        $solutions += 1;
        $QuizDB->setSolutions($solutions);
        $em->persist($QuizDB);
        $em->flush();
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
        if(!$this->isGranted('IS_AUTHENTICATED_FULLY') && !$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
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
        $entityManager = $this->getDoctrine()->getManager();
        $date = date("Y-m-d H:i:s");
        $date = date_create_from_format('Y-m-d H:i:s', $date);
        $date->getTimestamp();

        //categories quiz count
        $categoryDB = $this->getDoctrine()->getRepository(Categories::class)->findOneBy(['id' => intval($category)]);
        $categoryquizcount = $categoryDB->getQuizcount();
        $categoryquizcount += 1;
        $categoryDB->setQuizcount($categoryquizcount);
        $entityManager->persist($categoryDB);
        $entityManager->flush();

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
        if(!$this->isGranted('IS_AUTHENTICATED_FULLY') && !$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
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

        //category quiz count
        $previouscategory = $quizObject->getCategoryid();
        if($category != $previouscategory) {
            $previouscategoryDB = $this->getDoctrine()->getRepository(Categories::class)->findOneBy(['id' => $previouscategory]);
            $previouscount = $previouscategoryDB->getQuizcount();
            $previouscount -= 1;
            $previouscategoryDB->setQuizcount($previouscount);
            $entityManager->persist($previouscategoryDB);
            $entityManager->flush();

            $currentcategoryDB = $this->getDoctrine()->getRepository(Categories::class)->findOneBy(['id' => intval($category)]);
            $currentcount = $currentcategoryDB->getQuizcount();
            $currentcount += 1;
            $currentcategoryDB->setQuizcount($currentcount);
            $entityManager->persist($currentcategoryDB);
            $entityManager->flush();
        }

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

    public function profilemethod(Request $request)
    {
        $user =$this->getUser();
        $oldusername = $user->getUsername();
        $userEntity = $this->getDoctrine()->getRepository(User::class);
        $em = $this->getDoctrine()->getManager();
        $newusername = $request->request->get('_username');
        $newemail = $request->request->get('_email');

        //email
        if(filter_var($newemail, FILTER_VALIDATE_EMAIL)) {
            if($userEntity->findBy(['email' => $newemail]) == null) {
                $user->setEmail($newemail);
            }
        }
        //username
        if(strlen($newusername) > 4 && strlen($newusername) < 40) {
            if($userEntity->findBy(['username' => $newusername]) == null) {
                $filesystem = new Filesystem();
                $filesystem->rename($oldusername, $newusername);

                //for images to display


                //quizes
                $current_entity = $this->getDoctrine()->getRepository(Quiz::class)->findBy(['authorname' => $oldusername]);
                foreach ($current_entity as $quiz) {
                    $quiz->setAuthorname($newusername);
                    $em->persist($quiz);
                    $em->flush();
                }

                //answers
                $current_entity = $this->getDoctrine()->getRepository(Answers::class)->findBy(['creatorname' => $oldusername]);
                foreach ($current_entity as $answer) {
                    $answer->setCreatorname($newusername);
                    $em->persist($answer);
                    $em->flush();
                }

                //questions
                $current_entity = $this->getDoctrine()->getRepository(Questions::class)->findBy(['authorname' => $oldusername]);
                foreach ($current_entity as $question) {
                    $question->setAuthorname($newusername);
                    $em->persist($question);
                    $em->flush();
                }

                //endingquotes
                $current_entity = $this->getDoctrine()->getRepository(Endingquotes::class)->findBy(['authorname' => $oldusername]);
                foreach ($current_entity as $endingquote) {
                    $endingquote->setAuthorname($newusername);
                    $em->persist($endingquote);
                    $em->flush();
                }

                $user->setUsername($newusername);
            }
        }

        if ($_FILES['image']) {
            $uploaddir = "/$newusername/";
            $image = $_FILES['image']['name'];
            $newpath = $uploaddir . $image;
            //tmp_name needs realfilepath in index.php !important
            $tmp_name = $_FILES['image']['tmp_name'];
            move_uploaded_file($tmp_name, SITE_ROOT . $newpath);
            if ($image == null || $image == '' || $image == false) {
                $previousimage = $user->getImage();
                $user->setImage($previousimage);
            } else {
                $user->setImage($image);
            }
        }

        $em->persist($user);
        $em->flush();


        $this->addFlash(
            'status',
            'Profile successfully updated!'
        );
        return $this->redirect('profile');
    }
}