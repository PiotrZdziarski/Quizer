<?php

namespace App\Controller;

use App\Entity\Answers;
use App\Entity\Categories;
use App\Entity\Endingquotes;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Questions;
use App\Entity\Quiz;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
class SitesController extends Controller
{
    public function index() {
        $QuizesDB = $this->getDoctrine()->getRepository(Quiz::class)->findBy(['public'=> 1], ['id' => 'DESC']);
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT quiz FROM App\Entity\Quiz quiz WHERE quiz.public = 1 ORDER BY quiz.id DESC');
        $carousel_quizesDB = $query->setMaxResults(15)->getResult();
        return $this->render('sites/home.html.twig', ['QuizesDB' => $QuizesDB, 'carousel_quizesDB' => $carousel_quizesDB]);
    }

    public function createquiztype() {
        if(!$this->isGranted('IS_AUTHENTICATED_FULLY') || !$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('login');
        }
        return $this->render('sites/createtype.html.twig');
    }

    /**
    * @Route("/createquiz/{quiztype}", name="createquiz", defaults={"quiztype"=1})
    */
    public function createquiz($quiztype) {
        if(!$this->isGranted('IS_AUTHENTICATED_FULLY') || !$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('index');
        }
        if(!$quiztype) {
            $quiztype = 1;
        }
        $CategoriesDB = $this->getDoctrine()->getRepository(Categories::class)->findAll();
        return $this->render('sites/create.html.twig', ['quiztype' => $quiztype, 'CategoriesDB' => $CategoriesDB]);
    }

    public function create_knowledge_test()
    {

    }

    public function quiz($id)
    {
        $QuizesDB = $this->getDoctrine()->getRepository(Quiz::class)->findOneBy(['id' => $id]);
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT quiz FROM App\Entity\Quiz quiz WHERE quiz.public = 1 ORDER BY quiz.id DESC');
        $carousel_quizesDB = $query->setMaxResults(15)->getResult();
        return $this->render('sites/quiz.html.twig', ['QuizesDB' => $QuizesDB, 'carousel_quizesDB' => $carousel_quizesDB]);
    }

    public function afterregister() {
        return $this->render('sites/afterregister.html.twig');
    }

    public function profile() {
        if(!$this->isGranted('IS_AUTHENTICATED_FULLY') || !$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('index');
        }
        return $this->render('sites/profile.html.twig');
    }

    public function dashboard()
    {
        if(!$this->isGranted('IS_AUTHENTICATED_FULLY') || !$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('index');
        }
        $user = $this->getUser();
        $username = $user->getUsername();
        $QuizesDB = $this->getDoctrine()->getRepository(Quiz::class)->findBy(['authorname' => $username],['id' => 'DESC']);
        return $this->render('sites/dashboard.html.twig', ['QuizesDB' => $QuizesDB]);
    }

    public function quizplay($id)
    {
        $questioncount = 0;
        $QuizesDB = $this->getDoctrine()->getRepository(Quiz::class)->findOneBy(['id' => $id]);
        $QuestionDB = $this->getDoctrine()->getRepository(Questions::class)->findBy(['quizid' => $id]);
        $AnswersDB = $this->getDoctrine()->getRepository(Answers::class)->findAll();
        $EndingquotesDB = $this->getDoctrine()->getRepository(Endingquotes::class)->findBy(['quizid' => $id]);
        foreach($QuestionDB as $question) {
            $questioncount++;
        }
        $maxscore = 0;

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT quiz FROM App\Entity\Quiz quiz WHERE quiz.public = 1 ORDER BY quiz.id DESC');
        $carousel_quizesDB = $query->setMaxResults(15)->getResult();

        return $this->render('sites/quizplay.html.twig', ['QuestionsDB' => $QuestionDB, 'AnswersDB' => $AnswersDB, 'QuizesDB' => $QuizesDB, 'maxscore' => $maxscore,
            'questioncount' => $questioncount, 'EndingquotesDB' => $EndingquotesDB, 'carousel_quizesDB' => $carousel_quizesDB]);
    }

    public function editquiz($id)
    {
        $user = $this->getUser();
        $username = $user->getUsername();
        $CategoriesDB = $this->getDoctrine()->getRepository(Categories::class)->findAll();
        $QuizesDB = $this->getDoctrine()->getRepository(Quiz::class)->findBy(['id' => $id]);
        return $this->render('sites/editquiz.html.twig', ['QuizesDB' => $QuizesDB, 'CategoriesDB' => $CategoriesDB, 'username' => $username]);
    }

    public function editquestions($id) {
        if(!$this->isGranted('IS_AUTHENTICATED_FULLY') || !$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('index');
        }
        $user = $this->getUser();
        $username = $user->getUsername();
        $QuestionsDB = $this->getDoctrine()->getRepository(Questions::class)->findBy(['quizid' => $id]);
        $AnswersDB = $this->getDoctrine()->getRepository(Answers::class)->findBy(['creatorname' => $username]);
        $questioncount = 0;
        foreach($QuestionsDB as $questions) {
            $questioncount++;
        }
        $quesitonsplus = $questioncount + 1;
        $QuizDB = $this->getDoctrine()->getRepository(Quiz::class)->find($id);
        $quiztitle = $QuizDB->getTitle();
        $quizid = $QuizDB->getId();
        return $this->render('sites/editquestions.html.twig', ['QuestionsDB' => $QuestionsDB, 'quiztitle' => $quiztitle, 'quizid' => $quizid, 'questionsplus' => $quesitonsplus,
            'questioncount' => $questioncount, 'AsnwersDB' => $AnswersDB]);
    }

    public function editquestion($id)
    {
        $QuestionsDB = $this->getDoctrine()->getRepository(Questions::class)->findBy(['id' => $id]);
        $AnswersDB = $this->getDoctrine()->getRepository(Answers::class)->findBy(['questionid' => $id]);
        $quizname = '';
        foreach($QuestionsDB as $question) {
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
            if($answer->getCorrectanswer() == 1) {
                $correct_answer_number = $answercounter;
            }
        }

        return $this->render('sites/editquestion.html.twig', ['QuestionsDB' => $QuestionsDB,'AsnwersDB' => $AnswersDB, 'answerarray' => $answersarray,
            'correct_answer_number' => $correct_answer_number, 'idarray' => $idarray, 'quizname' => $quizname]);
    }

    public function editresults($id) {
        if(!$this->isGranted('IS_AUTHENTICATED_FULLY') || !$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('index');
        }
        $QuizDB = $this->getDoctrine()->getRepository(Quiz::class)->findBy(['id' => $id]);
        $endingquotes = $this->getDoctrine()->getRepository(Endingquotes::class)->findBy(['quizid' => $id]);
        $user = $this->getUser();
        $username = $user->getUsername();

        //setting variables
        for($i = 0; $i< 4; $i++) {
            ${'result'.$i} = '';
        }
        //adding values to variables
        if(isset($endingquotes[0])) {
            $result0 = $endingquotes[0];
        }
        if(isset($endingquotes[1])) {
            $result1 = $endingquotes[1];
        }
        if(isset($endingquotes[2])) {
            $result2 = $endingquotes[2];
        }
        if(isset($endingquotes[3])) {
            $result3 = $endingquotes[3];
        }

        $endingquotescount = count($endingquotes);
        $counter = 0;
        foreach ($endingquotes as $result) {
            ${'result'.$counter} = $result;
            $counter++;
        }

        $quizname = '';
        $quizid = 0;
        foreach ($QuizDB as $quiz) {
            $quizname = $quiz->getTitle();
            $quizid = $quiz->getId();
        }
        $QuestionsDB = $this->getDoctrine()->getRepository(Questions::class)->findBy(['quizid' => $id]);
        $questioncount = count($QuestionsDB);

        return $this->render('sites/editresults.html.twig', ['result0' => $result0,'quesioncount' => $questioncount, 'quizname' => $quizname, 'quizid' => $quizid, 'endingquotescount' => $endingquotescount,
            'endingquotes' => $endingquotes, 'result1' => $result1, 'result2' => $result2, 'result3' => $result3, 'username' => $username]);
    }

    public function search(Request $request)
    {
        $lookingfor  = $request->request->get('lookingfor');
        $QuizesDB = $this->getDoctrine()->getRepository(Quiz::class)->findBy(['title' => $lookingfor]);
        $em = $this->getDoctrine()->getManager();
        $no_quiz_found = false;
        if(count($QuizesDB)  == 0) {
            $query = $em->createQuery('SELECT quiz FROM App\Entity\Quiz quiz WHERE quiz.public = 1 ORDER BY quiz.id DESC');
            $QuizesDB = $query->setMaxResults(10)->getResult();
            $no_quiz_found = true;
        }
        $query = $em->createQuery('SELECT quiz FROM App\Entity\Quiz quiz WHERE quiz.public = 1 ORDER BY quiz.id DESC');
        $carousel_quizesDB = $query->setMaxResults(15)->getResult();
        return $this->render('sites/search.html.twig', ['QuizesDB' => $QuizesDB, 'carousel_quizesDB' => $carousel_quizesDB, 'no_quiz_found' => $no_quiz_found,
            'lookingfor' => $lookingfor]);
    }
}