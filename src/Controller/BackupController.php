<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\LoginRepository;
use App\Service\Backup\BackupService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\BackupCreateNewFormType;
use DateTime;

#[Route('/backup')]
class BackupController extends AbstractController
{
    #[Route('/list', name: 'backup_list')]
    public function list (
        LoginRepository     $loginRepository
    ): Response
    {
        $user = $loginRepository->find($this->getUser()->getId());
        $server = $user->getServer();
        if (null === $server) {

            return $this->redirectToRoute('server_create_new');
        }

        $backups = $server->getBackups();
        $defaultBackupName = 'backup_' . $server->getName(). '_' . (new DateTime('now'))->format('Y-m-d_H.i.s');
        $createNewForm = $this->createForm(BackupCreateNewFormType::class, [], [
            'defaultBackupName' => $defaultBackupName
        ]);

        return $this->render('backup/list.html.twig', [
            'user'              => $user,
            'backups'           => $backups,
            'server'            => $server,
            'createNewBackup'   => $createNewForm,
        ]);
    }

    #[Route('/createNew', name: 'backup_create_new')]
    public function createNew (
        LoginRepository     $loginRepository,
        BackupService       $backupService,
        Request             $request
    ): Response
    {
        $user = $loginRepository->find($this->getUser()->getId());
        $server = $user->getServer();
        if (null === $server) {

            return $this->redirectToRoute('server_create_new');
        }

        $form = $this
            ->createForm(BackupCreateNewFormType::class)
            ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $backupService->createNewBackup($form, $server);
        }

        return $this->redirectToRoute('backup_list');
    }

    #[Route('/download', name: 'backup_download')]
    public function download (
        LoginRepository     $loginRepository,
        BackupService       $backupService,
        Request             $request
    ): Response
    {

    }

}
