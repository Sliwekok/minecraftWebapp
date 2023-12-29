<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\LoginRepository;
use App\Form\CreateNewServerForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Backup;

#[Route('/backup')]
class BackupController extends AbstractController
{
    #[Route('/advanced', name: 'backup_list')]
    public function list (
        Request             $request,
        LoginRepository     $loginRepository
    ): Response
    {
        $user = $loginRepository->find($this->getUser()->getId());
        $server = $user->getServer();
        if (null === $server) {

            return $this->redirectToRoute('server_create_new');
        }

        return $this->render('server/advanced.html.twig', [
            'user'  => $user,
        ]);
    }

}
