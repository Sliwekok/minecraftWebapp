<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Alert;
use App\Exception\Server\NoServerFoundException;
use App\Form\BackupLoadUserWorld;
use App\Repository\LoginRepository;
use App\Service\Backup\BackupService;
use App\Service\Backup\UserBackupService;
use App\Service\Config\ConfigService;
use App\UniqueNameInterface\BackupInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\BackupCreateNewFormType;
use DateTime;


#[Route('/backup')]
class BackupController extends AbstractController
{
    #[Route('/list', name: 'backup_list')]
    public function list (
        LoginRepository     $loginRepository,
    ): Response
    {
        $user = $loginRepository->find($this->getUser()->getId());
        $server = $user->getServer();
        if (null === $server) {

            return $this->redirectToRoute('server_create_new');
        }

        $backups = $server->getBackups()->toArray();
        // sort by newest backup
        usort($backups, function($a, $b) {
            return $a->getId() < $b->getId();
        });
        $defaultBackupName = 'backup_' . $server->getName(). '_' . (new DateTime('now'))->format('Y-m-d_H.i.s');
        $createNewForm = $this->createForm(BackupCreateNewFormType::class, [], [
            'defaultBackupName' => $defaultBackupName
        ]);

        $userForm = $this->createForm(BackupLoadUserWorld::class);

        return $this->render('backup/list.html.twig', [
            'user'              => $user,
            'backups'           => $backups,
            'server'            => $server,
            'createNewBackup'   => $createNewForm,
            'userBackup'        => $userForm
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
            $fileName = $form->get(BackupInterface::FORM_CREATENEW_NAME)->getData(). BackupInterface::FILE_EXTENSION_ZIP;
            $backupService->createNewBackup($fileName, $server);
        }

        return $this->redirectToRoute('backup_list');
    }

    #[Route('/download/{id}', name: 'backup_download')]
    public function download (
        LoginRepository     $loginRepository,
        BackupService       $backupService,
        int                 $id
    ): BinaryFileResponse|JsonResponse
    {
        $user = $loginRepository->find($this->getUser()->getId());
        $server = $user->getServer();
        if (null === $server) {
            $exception = new NoServerFoundException();
            $alert = Alert::error($exception->getMessage());

            return new JsonResponse($alert->getMessage(), $alert->getCode());
        }

        // throws error if something went wrong, otherwise path to back up
        $backup = $backupService->download($server, $id);
        if ($backup instanceof Alert) {

            return new JsonResponse($backup->getMessage(), $backup->getCode());
        }

        $response = new BinaryFileResponse($backup);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $backup->getFilename(). '.'. $backup->getExtension()
        );

        return $response;
    }

    #[Route('/loadCustom', name: 'backup_loadcustom')]
    public function loadCustom (
        LoginRepository     $loginRepository,
        BackupService       $backupService,
        ConfigService       $configService,
        Request             $request
    ): Response
    {
        $user = $loginRepository->find($this->getUser()->getId());
        $server = $user->getServer();
        if (null === $server) {

            return $this->redirectToRoute('server_create_new');
        }

        $form = $this->createForm(BackupLoadUserWorld::class)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get(BackupInterface::FORM_USERUPLOAD_FILE)->getData();

            $backupService->storeCustomBackup($file, $server);
            $backupService->unpackUserBackup($file->getClientOriginalName(), $server);
            $configService->createConfigFromPropertyFile($server);
//        } else {
//            $alert = Alert::error($form->getErrors()[0]);
        }


        return $this->redirectToRoute('backup_list');
    }

    #[Route('/load/{id}', name: 'backup_load')]
    public function load (
        LoginRepository     $loginRepository,
        BackupService       $backupService,
        ConfigService       $configService,
        int                 $id
    ): JsonResponse|Response
    {
        $user = $loginRepository->find($this->getUser()->getId());
        $server = $user->getServer();
        if (null === $server) {

            return $this->redirectToRoute('server_create_new');
        }

        $backupService->loadBackup($id, $server);
        $configService->createConfigFromPropertyFile($server);

        $alert = Alert::success("Updated config");

        return new JsonResponse($alert->getMessage(), $alert->getCode());
    }

}
