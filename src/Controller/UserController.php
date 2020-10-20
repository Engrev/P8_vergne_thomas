<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserController
 * @package App\Controller
 *
 * @Route("/users", name="user_")
 */
class UserController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * UserController constructor.
     *
     * @param UserRepository         $repository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(UserRepository $repository, EntityManagerInterface $entityManager)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="list")
     * @IsGranted("ROLE_ADMIN", statusCode=401, message="Seul les administrateurs peuvent accéder à cette page.")
     *
     * @return Response
     */
    public function list(): Response
    {
        $users = $this->repository->findAll();
        return $this->render('user/list.html.twig', ['users' => $users]);
    }

    /**
     * @Route("/create", name="create")
     * @IsGranted("ROLE_ADMIN", statusCode=401, message="Seul les administrateurs peuvent accéder à cette page.")
     *
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return Response
     */
    public function create(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword($user, $user->getPlainPassword())
            );
            $user->eraseCredentials();
            $user->setRoles([$request->request->get('user_roles')]);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté.");
            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/{id}/edit", name="edit")
     * @IsGranted("ROLE_ADMIN", statusCode=401, message="Seul les administrateurs peuvent accéder à cette page.")
     *
     * @param User                         $user
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return Response
     */
    public function edit(User $user, Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($user->getPlainPassword())) {
                $this->repository->upgradePassword(
                    $user,
                    $passwordEncoder->encodePassword(
                        $user,
                        $user->getPlainPassword()
                    )
                );
                $user->eraseCredentials();
            }
            $user->setRoles([$request->request->get('user_roles')]);
            $this->entityManager->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié.");
            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }
}
