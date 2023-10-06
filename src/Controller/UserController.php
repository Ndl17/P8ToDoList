<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
 

    #[Route('/users', name: 'user_list')]
    public function listAction(UserRepository $userRepository)
    {
        $user = $userRepository->findAll();
        return $this->render('user/list.html.twig', ['users' => $user]);
    }

    
 
    #[Route('/users/{id}/edit', name: 'user_edit')]
    public function editAction(User $user, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher)
    {
        // Create the form using the UserType form type
        $form = $this->createForm(UserType::class, $user);

        // Handle form submission
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Save the updated user to the database    
            $user->setPassword($userPasswordHasher->hashPassword($user, $form->get('password')->getData()));
            $entityManager->persist($user);
            $entityManager->flush();

            // Redirect to a success page or display a success message
            $this->addFlash('success', 'User updated successfully.');

            return $this->redirectToRoute('user_list'); // Redirect to the user list page
        }

        // Render the edit form template
        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user, // Optionally, pass the user object to the template
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
