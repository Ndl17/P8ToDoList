<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class TaskController extends AbstractController
{

    #[Route('/tasks', name: 'task_list')]
    public function listAction(TaskRepository $taskRepository)
    {
        //récupère toutes les tâches grâce à la méthode findAll()
        $tasks = $taskRepository->findAll();
        //retourne la vue list.html.twig en lui passant en paramètre le tableau de tâches   
        return $this->render('task/list.html.twig', ['tasks' => $tasks]);
    }

    #[Route('/tasks/create', name: 'task_create')]
    public function createAction(EntityManagerInterface $entityManager, Request $request, Security $security)
    {
//avec security on récupère l'utilisateur connecté        
        $currentUser = $security->getUser();
        //création d'une nouvelle tâche
        $task = new Task();
        //on attribue l'utilisateur connecté à la tâche
        $task->setUser($currentUser);
        // on crée le formulaire grâce à la méthode createForm() du contrôleur
        $form = $this->createForm(TaskType::class, $task);
//on récupère les données du formulaire
        $form->handleRequest($request);
//Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            //on set la date de création de la tâche, l'utilisateur connecté et on set la tâche à non terminée
            $task->setCreatedAt(new \DateTime());
            $task->setUser($this->getUser());
            $task->setIsDone(false);
            //on persiste la tâche et on la flush
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }


    #[Route('/tasks/{id}/edit', name: 'task_edit')]
    public function editAction(Task $task, Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        //création du formulaire grâce à la méthode createForm() du contrôleur
        $form = $this->createForm(TaskType::class, $task);
       //on récupère l'utilisateur de la tâche
        $user = $task->getUser();
//on récupère les données du formulaire
        $form->handleRequest($request);
//si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            //si l'utilisateur est null, on lui attribue l'utilisateur anonyme qui a l'id 0
            if ($user == null) {
                $task->setUser($userRepository->find(0));
            }
            //on persiste la tâche et on la flush
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route('/tasks/{id}/toggle', name: 'task_toggle')]
    public function toggleTaskAction(Task $task, EntityManagerInterface $entityManager)
    {
        //on passe le booléen de la tâche à l'inverse de sa valeur actuelle
        $task->setIsDone(!$task->isIsDone());
        
        //on persiste la tâche et on la flush
        $entityManager->persist($task);
        $entityManager->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }

    #[Route('/tasks/{id}/delete', name: 'task_delete')]
    public function deleteTaskAction(Task $task, EntityManagerInterface $entityManager)
    {
        //on supprime la tâche
        $entityManager->remove($task);
        $entityManager->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }
}
