<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Categories;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $utils)
    {
        if($this->isGranted('IS_AUTHENTICATED_FULLY') || $this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('index');
        }
        $error = $utils->getLastAuthenticationError();
        $lastUsername = $utils->getLastUsername();
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT quiz FROM App\Entity\Quiz quiz WHERE quiz.public = 1 ORDER BY quiz.id DESC');
        $carousel_quizesDB = $query->setMaxResults(15)->getResult();

        //categories
        $query = $em->createQuery('SELECT category FROM App\Entity\Categories category ORDER BY category.quizcount DESC');
        $categoriesDB = $query->getResult();

        return $this->render('security/login.html.twig', [
            'error' => $error,
            'last_username' => $lastUsername,
            'carousel_quizesDB' => $carousel_quizesDB,
            'categoriesDB' => $categoriesDB
        ]);
    }
    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        $this->addFlash(
            'status',
            'Logged out!'
        );
    }

    /**
     * @Route("/register", name="register")
     */

    public function register()
    {
        if($this->isGranted('IS_AUTHENTICATED_FULLY') || $this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('index');
        }
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT quiz FROM App\Entity\Quiz quiz WHERE quiz.public = 1 ORDER BY quiz.id DESC');
        $carousel_quizesDB = $query->setMaxResults(15)->getResult();

        //categories
        $query = $em->createQuery('SELECT category FROM App\Entity\Categories category ORDER BY category.quizcount DESC');
        $categoriesDB = $query->getResult();

        return $this->render('security/register.html.twig', ['carousel_quizesDB' => $carousel_quizesDB, 'categoriesDB' => $categoriesDB
        ]);
    }


    public function registermethod(Request $request)
    {
        $userEntity = $this->getDoctrine()->getRepository(User::class);
        $everything_good = true;
        $user = $request->request->get('_username');
        $email = $request->request->get('_email');
        $password = $request->request->get('_password');
        $confirm_password = $request->request->get('_confirm_password');

        if (strlen($user) > 40 || strlen($user) < 4) {
            $everything_good = false;
        } elseif ($userEntity->findBy(['username' => $user]) != null) {
            $everything_good = false;
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if ($userEntity->findBy(['email' => $email]) != null) {
                $everything_good = false;
            }
        }


        if (strlen('password') > 40 || strlen('password') < 4) {
            $everything_good = false;
        }

        if ($password != $confirm_password) {
            $everything_good = false;
        }

        if ($everything_good == true) {
            $fileSystem = new Filesystem();
            $fileSystem->mkdir($user);
            $entityManager = $this->getDoctrine()->getManager();
            $userObject = new User();
            $userObject->setUsername($user);
            $userObject->setPassword(password_hash($password, PASSWORD_BCRYPT));
            $userObject->setOnline(0);
            $userObject->setImage('');
            $userObject->setEmail($email);

            $entityManager->persist($userObject);
            $entityManager->flush();

            $this->addFlash(
                'status',
                'Registered successfully!'
            );

            $token = new UsernamePasswordToken($userObject, null, 'main', $userObject->getRoles());
            $this->container->get('security.token_storage')->setToken($token);
            $this->container->get('session')->set('_security_main', serialize($token));
        }

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT quiz FROM App\Entity\Quiz quiz WHERE quiz.public = 1 ORDER BY quiz.id DESC');
        $carousel_quizesDB = $query->setMaxResults(15)->getResult();
        return $this->redirectToRoute('afterregister', ['carousel_quizesDB' => $carousel_quizesDB]);
    }
}
