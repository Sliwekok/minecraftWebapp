<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\LoginRepository;
use App\Service\Server\DeleteServerService;
use App\Service\Server\ServerService;
use App\UniqueNameInterface\ServerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'user_management')]
    public function management(): Response
    {
        $user = $this->getUser();

        return $this->render('user/user.html.twig', [
            'user'  => $user
        ]);
    }

    #[Route('/delete_account', name: 'user_delete_account')]
    public function deleteAccount(
        LoginRepository         $loginRepository,
        DeleteServerService     $deleteServerService,
        ServerService           $serverService,
        EntityManagerInterface  $entityManager
    ): Response
    {
        $user = $loginRepository->find($this->getUser()->getId());
        $server = $user->getServer();
        if ($server->getStatus() === ServerInterface::STATUS_ONLINE) $serverService->stopServer($server);
        $deleteServerService->deleteServer($server);
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('index');
    }

}
