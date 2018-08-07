<?php

namespace App\Controller;

use App\Entity\Answers;
use App\Entity\Categories;
use App\Entity\Endingquotes;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Questions;
use App\Entity\Quiz;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
class SitesController extends Controller
{
    public function index()
    {
        $QuizesDB = $this->getDoctrine()->getRepository(Quiz::class)->findBy(['public' => 1], ['id' => 'DESC']);
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT user.image, user.username, user.online FROM App\Entity\User user');
        $UsersDB = $query->getResult();
        $query = $em->createQuery('SELECT quiz FROM App\Entity\Quiz quiz WHERE quiz.public = 1 ORDER BY quiz.solutions DESC');
        $carousel_quizesDB = $query->setMaxResults(15)->getResult();
        $quizcount = count($QuizesDB);
             
        //categories
        $query = $em->createQuery('SELECT category FROM App\Entity\Categories category ORDER BY category.quizcount DESC');
        $categoriesDB = $query->getResult();

        return $this->render('sites/home.html.twig', ['QuizesDB' => $QuizesDB, 'carousel_quizesDB' => $carousel_quizesDB, 'appname' => APP_NAME,
            'categoriesDB' => $categoriesDB, 'UsersDB' => $UsersDB, 'quizcount' => $quizcount]);
    }


    public function top()
    {
        $QuizesDB = $this->getDoctrine()->getRepository(Quiz::class)->findBy(['public' => 1], ['solutions' => 'DESC']);
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT user.image, user.username, user.online FROM App\Entity\User user');
        $UsersDB = $query->getResult();
        $query = $em->createQuery('SELECT quiz FROM App\Entity\Quiz quiz WHERE quiz.public = 1 ORDER BY quiz.solutions DESC');
        $carousel_quizesDB = $query->setMaxResults(15)->getResult();
        $quizcount = count($QuizesDB);

        //categories
        $query = $em->createQuery('SELECT category FROM App\Entity\Categories category ORDER BY category.quizcount DESC');
        $categoriesDB = $query->getResult();

        return $this->render('sites/top.html.twig', ['QuizesDB' => $QuizesDB, 'carousel_quizesDB' => $carousel_quizesDB, 'appname' => APP_NAME,
            'categoriesDB' => $categoriesDB, 'UsersDB' => $UsersDB, 'quizcount' => $quizcount]);
    }

    public function createquiztype()
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY') && !$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {

            $this->addFlash(
            'login',
            'You need to log in first!'
            );
            return $this->redirectToRoute('login');
        }
        return $this->render('sites/createtype.html.twig');
    }

    /**
     * @Route("/createquiz/{quiztype}", name="createquiz", defaults={"quiztype"=1})
     */
    public function createquiz($quiztype)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY') && !$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('index');
        }
        if (!$quiztype) {
            $quiztype = 1;
        }
        $CategoriesDB = $this->getDoctrine()->getRepository(Categories::class)->findAll();
        return $this->render('sites/create.html.twig', ['quiztype' => $quiztype, 'CategoriesDB' => $CategoriesDB]);
    }

    public function quiz($id)
    {
        $QuizesDB = $this->getDoctrine()->getRepository(Quiz::class)->findOneBy(['id' => $id]);
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT quiz FROM App\Entity\Quiz quiz WHERE quiz.public = 1 ORDER BY quiz.solutions DESC');
        $carousel_quizesDB = $query->setMaxResults(15)->getResult();
        $appname = APP_NAME;
        $query = $em->createQuery('SELECT quiz.title, quiz.image, quiz.authorname, quiz.id FROM App\Entity\Quiz quiz WHERE quiz.public = 1 ORDER BY quiz.solutions DESC');
        $also_like_quizesDB = $query->setMaxResults(10)->getResult();

        //categories
        $query = $em->createQuery('SELECT category FROM App\Entity\Categories category ORDER BY category.quizcount DESC');
        $categoriesDB = $query->getResult();

        //questioncount
        $QuestionsDB = $this->getDoctrine()->getRepository(Questions::class)->findBy(['quizid' => $id]);
        $questioncount = count($QuestionsDB);

        return $this->render('sites/quiz.html.twig', ['QuizesDB' => $QuizesDB, 'carousel_quizesDB' => $carousel_quizesDB, 'appname' => $appname,
            'also_like_quizesDB' => $also_like_quizesDB, 'categoriesDB' => $categoriesDB, 'questioncount' => $questioncount]);
    }

    public function afterregister()
    {
        //categories
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT category FROM App\Entity\Categories category ORDER BY category.quizcount DESC');
        $categoriesDB = $query->getResult();

        $query = $em->createQuery('SELECT quiz FROM App\Entity\Quiz quiz WHERE quiz.public = 1 ORDER BY quiz.solutions DESC');
        $carousel_quizesDB = $query->setMaxResults(15)->getResult();
        return $this->render('sites/afterregister.html.twig', ['categoriesDB' => $categoriesDB, 'carousel_quizesDB' => $carousel_quizesDB]);
    }

    public function profile()
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY') && !$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('index');
        }

        $UserDB = $this->getUser();
        $username = $UserDB->getUsername();
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT quiz FROM App\Entity\Quiz quiz WHERE quiz.public = 1 ORDER BY quiz.solutions DESC');
        $carousel_quizesDB = $query->setMaxResults(15)->getResult();

        //categories
        $query = $em->createQuery('SELECT category FROM App\Entity\Categories category ORDER BY category.quizcount DESC');
        $categoriesDB = $query->getResult();

        return $this->render('sites/profile.html.twig', ['UserDB' => $UserDB, 'categoriesDB' => $categoriesDB, 'carousel_quizesDB' => $carousel_quizesDB,
            'username' => $username]);
    }

    public function dashboard()
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY') && !$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('index');
        }
        $user = $this->getUser();
        $username = $user->getUsername();
        $QuizesDB = $this->getDoctrine()->getRepository(Quiz::class)->findBy(['authorname' => $username], ['id' => 'DESC']);
        return $this->render('sites/dashboard.html.twig', ['QuizesDB' => $QuizesDB, 'appname' => APP_NAME]);
    }

    public function quizplay($id)
    {
        $QuizesDB = $this->getDoctrine()->getRepository(Quiz::class)->findOneBy(['id' => $id]);
        $QuestionsDB = $this->getDoctrine()->getRepository(Questions::class)->findBy(['quizid' => $id]);
        foreach ($QuestionsDB as $question) {
            $AnswersDB[] = $this->getDoctrine()->getRepository(Answers::class)->findBy(['questionid' => $question->getId()]);
        }
        $AnswersDB = $this->getDoctrine()->getRepository(Answers::class)->findAll();
        $EndingquotesDB = $this->getDoctrine()->getRepository(Endingquotes::class)->findBy(['quizid' => $id]);
        $questioncount = count($QuestionsDB);
        $maxscore = 0;

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT quiz FROM App\Entity\Quiz quiz WHERE quiz.public = 1 ORDER BY quiz.solutions DESC');
        $carousel_quizesDB = $query->setMaxResults(15)->getResult();
        $appname = APP_NAME;

        $query = $em->createQuery('SELECT quiz.title, quiz.image, quiz.authorname, quiz.id, quiz.categoryid FROM App\Entity\Quiz quiz WHERE quiz.public = 1 ORDER BY quiz.solutions DESC');
        $also_like_quizesDB = $query->setMaxResults(10)->getResult();

        //categories
        $query = $em->createQuery('SELECT category FROM App\Entity\Categories category ORDER BY category.quizcount DESC');
        $categoriesDB = $query->getResult();

        return $this->render('sites/quizplay.html.twig', ['QuestionsDB' => $QuestionsDB, 'AnswersDB' => $AnswersDB, 'QuizesDB' => $QuizesDB, 'maxscore' => $maxscore,
            'questioncount' => $questioncount, 'EndingquotesDB' => $EndingquotesDB, 'carousel_quizesDB' => $carousel_quizesDB, 'appname' => $appname,
            'also_like_quizesDB' => $also_like_quizesDB, 'categoriesDB' => $categoriesDB]);
    }

    public function editquiz($id)
    {
        $user = $this->getUser();
        $username = $user->getUsername();
        $CategoriesDB = $this->getDoctrine()->getRepository(Categories::class)->findAll();
        $QuizesDB = $this->getDoctrine()->getRepository(Quiz::class)->findBy(['id' => $id]);
        return $this->render('sites/editquiz.html.twig', ['QuizesDB' => $QuizesDB, 'CategoriesDB' => $CategoriesDB, 'username' => $username]);
    }

    public function editquestions($id)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY') && !$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('index');
        }
        $user = $this->getUser();
        $username = $user->getUsername();
        $QuestionsDB = $this->getDoctrine()->getRepository(Questions::class)->findBy(['quizid' => $id]);
        $AnswersDB = $this->getDoctrine()->getRepository(Answers::class)->findBy(['creatorname' => $username]);
        $EndingquotesDB = $this->getDoctrine()->getRepository(Endingquotes::class)->findBy(['quizid' => $id]);
        $endingquotecount = count($EndingquotesDB);
        $questioncount = count($QuestionsDB);
        $quesitonsplus = $questioncount + 1;
        $QuizDB = $this->getDoctrine()->getRepository(Quiz::class)->find($id);
        $quiztitle = $QuizDB->getTitle();
        $quizid = $QuizDB->getId();
        $typeid = $QuizDB->getTypeid();

        //MAX SCORE
        $maxscore = 0;
        if ($typeid == 2) {
            $maxscore = 0;
            $em = $this->getDoctrine()->getManager();
            foreach ($QuestionsDB as $question) {
                $questionid = $question->getId();
                $query = $em->createQuery('SELECT correctanswer FROM App\Entity\Answers correctanswer WHERE correctanswer.questionid = ' . $questionid . 'ORDER BY correctanswer.correctanswer DESC');
                $maxpointsfromanswer = $query->setMaxResults(1)->getResult();
                foreach ($maxpointsfromanswer as $maxpointfromanswer) {
                    $maxpointsfromanswer = $maxpointfromanswer->getCorrectanswer();
                }
                $maxscore += intval($maxpointsfromanswer);
            }
        }
        return $this->render('sites/editquestions.html.twig', ['QuestionsDB' => $QuestionsDB, 'quiztitle' => $quiztitle, 'quizid' => $quizid, 'questionsplus' => $quesitonsplus,
            'questioncount' => $questioncount, 'AsnwersDB' => $AnswersDB, 'typeid' => $typeid, 'endingquotecount' => $endingquotecount, 'maxscore' => $maxscore]);
    }

    public function editquestion($id)
    {
        $QuestionsDB = $this->getDoctrine()->getRepository(Questions::class)->findBy(['id' => $id]);
        $AnswersDB = $this->getDoctrine()->getRepository(Answers::class)->findBy(['questionid' => $id]);
        $quizname = '';
        foreach ($QuestionsDB as $question) {
            $quizid = $question->getQuizid();
            $QuizDB = $this->getDoctrine()->getRepository(Quiz::class)->findBy(['id' => $quizid]);
            foreach ($QuizDB as $quiz) {
                $quizname = $quiz->getTitle();
            }
        }
        $correct_answer_number = 1;
        $answercounter = 0;
        $answersarray = array();
        $idarray = array();
        foreach ($AnswersDB as $answer) {
            $answercounter++;
            array_push($answersarray, $answer->getAnswer());
            array_push($idarray, $answer->getId());
            if ($answer->getCorrectanswer() == 1) {
                $correct_answer_number = $answercounter;
            }
        }

        return $this->render('sites/editquestion.html.twig', ['QuestionsDB' => $QuestionsDB, 'AsnwersDB' => $AnswersDB, 'answerarray' => $answersarray,
            'correct_answer_number' => $correct_answer_number, 'idarray' => $idarray, 'quizname' => $quizname]);
    }

    public function editresults($id)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY') && !$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('index');
        }
        $QuizDB = $this->getDoctrine()->getRepository(Quiz::class)->findOneBy(['id' => $id]);
        $endingquotes = $this->getDoctrine()->getRepository(Endingquotes::class)->findBy(['quizid' => $id]);
        $user = $this->getUser();
        $username = $user->getUsername();

        //setting variables
        for ($i = 0; $i < 4; $i++) {
            ${'result' . $i} = '';
        }
        //adding values to variables
        if (isset($endingquotes[0])) {
            $result0 = $endingquotes[0];
        }
        if (isset($endingquotes[1])) {
            $result1 = $endingquotes[1];
        }
        if (isset($endingquotes[2])) {
            $result2 = $endingquotes[2];
        }
        if (isset($endingquotes[3])) {
            $result3 = $endingquotes[3];
        }

        $endingquotescount = count($endingquotes);
        $counter = 0;
        foreach ($endingquotes as $result) {
            ${'result' . $counter} = $result;
            $counter++;
        }

        $quizname = $QuizDB->getTitle();
        $quizid = $QuizDB->getId();
        $typeid = $QuizDB->getTypeid();
        $quiztitle = $QuizDB->getTitle();
        $QuestionsDB = $this->getDoctrine()->getRepository(Questions::class)->findBy(['quizid' => $id]);
        $questioncount = count($QuestionsDB);

        return $this->render('sites/editresults.html.twig', ['result0' => $result0, 'quesioncount' => $questioncount, 'quizname' => $quizname, 'quizid' => $quizid, 'endingquotescount' => $endingquotescount,
            'endingquotes' => $endingquotes, 'result1' => $result1, 'result2' => $result2, 'result3' => $result3, 'username' => $username,
            'typeid' => $typeid, 'quiztitle' => $quiztitle]);
    }

    public function search(Request $request)
    {
        $lookingfor = $request->request->get('lookingfor');
        $QuizesDB = $this->getDoctrine()->getRepository(Quiz::class)->findBy(['title' => $lookingfor]);
        $em = $this->getDoctrine()->getManager();
        $no_quiz_found = false;
        if (count($QuizesDB) == 0) {
            $query = $em->createQuery('SELECT quiz FROM App\Entity\Quiz quiz WHERE quiz.public = 1 ORDER BY quiz.id DESC');
            $QuizesDB = $query->setMaxResults(10)->getResult();
            $no_quiz_found = true;
        }
        $query = $em->createQuery('SELECT quiz FROM App\Entity\Quiz quiz WHERE quiz.public = 1 ORDER BY quiz.solutions DESC');
        $carousel_quizesDB = $query->setMaxResults(15)->getResult();

        //categories
        $query = $em->createQuery('SELECT category FROM App\Entity\Categories category ORDER BY category.quizcount DESC');
        $categoriesDB = $query->getResult();

        //users
        $query = $em->createQuery('SELECT user.image, user.username, user.online FROM App\Entity\User user');
        $UsersDB = $query->getResult();
        return $this->render('sites/search.html.twig', ['QuizesDB' => $QuizesDB, 'carousel_quizesDB' => $carousel_quizesDB, 'no_quiz_found' => $no_quiz_found,
            'lookingfor' => $lookingfor, 'categoriesDB' => $categoriesDB, 'appname' => APP_NAME, 'UsersDB' => $UsersDB]);
    }


    public function editquestionpsychotest($id)
    {
        $QuestionsDB = $this->getDoctrine()->getRepository(Questions::class)->findBy(['id' => $id]);
        $AnswersDB = $this->getDoctrine()->getRepository(Answers::class)->findBy(['questionid' => $id]);
        $quizname = '';
        $answercounter = 0;
        foreach ($QuestionsDB as $question) {
            $quizid = $question->getQuizid();
            $QuizDB = $this->getDoctrine()->getRepository(Quiz::class)->findBy(['id' => $quizid]);
            foreach ($QuizDB as $quiz) {
                $quizname = $quiz->getTitle();
            }
        }

        $answersarray = array();
        $correctanswers = array();
        $idarray = array();
        foreach ($AnswersDB as $answer) {
            $answercounter++;
            array_push($answersarray, $answer->getAnswer());
            array_push($idarray, $answer->getId());
            array_push($correctanswers, $answer->getCorrectanswer());
        }

        return $this->render('/layouts/components/EditQPsychotest.html.twig', ['QuestionsDB' => $QuestionsDB, 'AsnwersDB' => $AnswersDB, 'answerarray' => $answersarray,
            'correctanswers' => $correctanswers, 'idarray' => $idarray, 'quizname' => $quizname]);
    }


    public function categories()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery('SELECT quiz FROM App\Entity\Quiz quiz WHERE quiz.public = 1 ORDER BY quiz.solutions DESC');
        $carousel_quizesDB = $query->setMaxResults(15)->getResult();

        $categoriesDB = $this->getDoctrine()->getRepository(Categories::class)->findAll();
        return $this->render('sites/categories.html.twig', ['carousel_quizesDB' => $carousel_quizesDB, 'appname' => APP_NAME,
            'categoriesDB' => $categoriesDB]);
    }

    public function category($id)
    {
        $QuizesDB = $this->getDoctrine()->getRepository(Quiz::class)->findBy(['categoryid' => $id]);
        $CategoryDB = $this->getDoctrine()->getRepository(Categories::class)->findOneBy(['id' => $id]);
        $category = $CategoryDB->getCategory();
        $categoriesDB = $this->getDoctrine()->getRepository(Categories::class)->findAll(['quizcount' => 'DESC']);

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT quiz FROM App\Entity\Quiz quiz WHERE quiz.public = 1 ORDER BY quiz.solutions DESC');
        $carousel_quizesDB = $query->setMaxResults(15)->getResult();

        //users
        $query = $em->createQuery('SELECT user.image, user.username, user.online FROM App\Entity\User user');
        $UsersDB = $query->getResult();

        //categories
        $query = $em->createQuery('SELECT category FROM App\Entity\Categories category ORDER BY category.quizcount DESC');
        $categoriesDB = $query->getResult();

        return $this->render('sites/category.html.twig', ['carousel_quizesDB' => $carousel_quizesDB, 'category' => $category, 'QuizesDB' => $QuizesDB,
            'appname' => APP_NAME, 'categoriesDB' => $categoriesDB, 'UsersDB' => $UsersDB]);
    }
}