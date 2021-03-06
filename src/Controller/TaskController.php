<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TaskController
 * @package App\Controller
 *
 * @Route("/tasks", name="task_")
 */
class TaskController extends AbstractController
{
    /**
     * @var TaskRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * TaskController constructor.
     *
     * @param TaskRepository         $repository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(TaskRepository $repository, EntityManagerInterface $entityManager)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="list")
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     *
     * @return Response
     */
    public function list(): Response
    {
        $user = $this->getUser();
        $tasks = $this->repository->findBy(['user'=>$user->getId(), 'isDone'=>0], ['createdAt'=>'DESC']);
        return $this->render('task/list.html.twig', ['tasks' => $tasks]);
    }

    /**
     * @Route("/anonymous", name="list_anonymous")
     * @IsGranted("ROLE_ADMIN")
     *
     * @return Response
     */
    public function listAnonymous(): Response
    {
        $tasks = $this->repository->findBy(['user'=>null, 'isDone'=>0], ['createdAt'=>'DESC']);
        return $this->render('task/list.html.twig', ['tasks' => $tasks]);
    }

    /**
     * @Route("/create", name="create")
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setUser($this->getUser());
            $this->entityManager->persist($task);
            $this->entityManager->flush();

            $this->addFlash('success', 'La tâche a bien été ajoutée.');
            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/{id}/edit", name="edit")
     * @IsGranted("edit", subject="task", statusCode=401, message="Seul la personne ayant créé la tâche peut la modifier.")
     *
     * @param Task    $task
     * @param Request $request
     *
     * @return Response
     */
    public function edit(Task $task, Request $request): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');
            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', ['form' => $form->createView(), 'task' => $task]);
    }

    /**
     * @Route("/{id}/toggle", name="toggle")
     * @IsGranted("toggle", subject="task", statusCode=401, message="Seul la personne ayant créé la tâche peut changer son état.")
     *
     * @param Task $task
     *
     * @return Response
     */
    public function toggle(Task $task): Response
    {
        $task->setIsDone(!$task->getIsDone());
        $this->entityManager->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));
        return $this->redirectToRoute('task_list');
    }

    /**
     * @Route("/{id}/delete", name="delete")
     * @IsGranted("delete", subject="task", statusCode=401, message="Seul la personne ayant créé la tâche peut la supprimer.")
     *
     * @param Task $task
     *
     * @return Response
     */
    public function delete(Task $task): Response
    {
        $this->entityManager->remove($task);
        $this->entityManager->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');
        return $this->redirectToRoute('task_list');
    }
}
