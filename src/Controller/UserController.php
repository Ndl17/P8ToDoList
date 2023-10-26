<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Security\UsersAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class UserController extends AbstractController
{

    #[Route('/users', name: 'user_list')]
    public function listAction(UserRepository $userRepository)
    {
        //récupère toutes les user grâce à la méthode findAll()
        $user = $userRepository->findAll();
        //retourne la vue list.html.twig en lui passant en paramètre le tableau de user
        return $this->render('user/list.html.twig', ['users' => $user]);
    }

    #[Route('/users/create', name: 'user_create')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UsersAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        //on crée un nouvel utilisateur, on instancie la classe User
        $user = new User();
        //on crée le formulaire grâce à la méthode createForm() du contrôleur et on lui passe en paramètre le type de formulaire et l'instance de l'utilisateur
        $form = $this->createForm(UserType::class, $user);
        //on récupère les données du formulaire
        $form->handleRequest($request);
        //si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
        //on va créer le mot de passe de l'utilisateur et on va l'encoder
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            //on persiste l'utilisateur et on le flush
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email
            //on retourne l'utilisateur et l'authenticator à la méthode authenticateUser() du UsersAuthenticator
            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }
        //on retourne la vue register.html.twig en lui passant le formulaire créé
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/users/{id}/edit', name: 'user_edit')]
    public function editAction(User $user, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher)
    {
        //on crée le formulaire grâce à la méthode createForm() du contrôleur et on lui passe en paramètre le type de formulaire et l'instance de l'utilisateur
        $form = $this->createForm(UserType::class, $user);

        //on récupère les données du formulaire
        $form->handleRequest($request);
        //si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            //pour changer le mot de passe, on va créer le mot de passe de l'utilisateur et on va l'encoder
            $user->setPassword($userPasswordHasher->hashPassword($user, $form->get('password')->getData()));
            //on persiste l'utilisateur et on le flush
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'User updated successfully.');

            return $this->redirectToRoute('user_list'); 
        }
     
        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user, 
        ]);
    }

/*
#[Route('/users/create', name: 'user_create')]
public function createAction(Request $request, UserPasswordEncoderInterface $encoder)
{
$user = new User();
$form = $this->createForm(UserType::class, $user);

$form->handleRequest($request);

if ($form->isSubmitted() && $form->isValid()) {
$em = $this->getDoctrine()->getManager();
$password = $encoder->encodePassword($user, $user->getPassword());
$user->setPassword($password);

$em->persist($user);
$em->flush();

$this->addFlash('success', "L'utilisateur a bien été ajouté.");

return $this->redirectToRoute('user_list');
}

return $this->render('user/create.html.twig', ['form' => $form->createView()]);
}*/

    /*
#[Route('/users/{id}/edit', name: 'user_edit')]
public function editAction(User $user, Request $request, UserPasswordEncoderInterface $encoder)
{
$form = $this->createForm(UserType::class, $user);

$form->handleRequest($request);

if ($form->isSubmitted() && $form->isValid()) {
$password = $encoder->encodePassword($user, $user->getPassword());
$user->setPassword($password);

$this->getDoctrine()->getManager()->flush();

$this->addFlash('success', "L'utilisateur a bien été modifié");

return $this->redirectToRoute('user_list');
}

return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
}*/
}
