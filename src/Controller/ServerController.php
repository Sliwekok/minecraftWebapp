<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Alert;
use App\Exception\Server\NoServerFoundException;
use App\Exception\Server\ServerIsAlreadyRunningException;
use App\Repository\LoginRepository;
use App\Service\Mojang\MinecraftVersions;
use App\Service\Server\DeleteServerService;
use App\Service\Server\ServerService;
use App\Form\CreateNewServerForm;
use App\UniqueNameInterface\ServerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/server')]
class ServerController extends AbstractController
{
    #[Route('/', name: 'server_preview')]
    public function management(
        ServerService       $serverService,
    ): Response
    {
        $user = $this->getUser();
        $server = $user->getServer();
        if (null === $server) {

            return $this->redirectToRoute('server_create_new');
        }

        $usage = $serverService->getServerUsageFile($server);

        $ip = file_get_contents("http://ipecho.net/plain");

        return $this->render('server/preview.html.twig', [
            'user'      => $user,
            'server'    => $server,
            'ip'        => $ip
        ]);
    }

    #[Route('/create_new', name: 'server_create_new')]
    public function createNew (
        Request             $request,
        ServerService       $serverService,
        MinecraftVersions   $minecraftVersions,
        LoginRepository     $loginRepository
    ): Response
    {
        $user = $loginRepository->find($this->getUser()->getId());
        if (null !== $user->getServer()) {

            return $this->redirectToRoute('server_preview');
        }
        $urlTos = $this->generateUrl('terms_of_use');
        $versions = $minecraftVersions->getAllVersions();
        $defaultServerName = $user->getUsername(). " server";
        $defaultServerName = str_replace(' ', '_', $defaultServerName);
        $form = $this
            ->createForm(CreateNewServerForm::class, [], [
                'urlTos'            => $urlTos,
                'defaultServerName' => $defaultServerName
            ])
            ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $serverService->createServer($form, $user);

            return $this->redirectToRoute('server_preview');
        }

        return $this->render('server/createNew.html.twig', [
            'user'              => $user,
            'form'              => $form,
            'minecraftVersions' => $versions
        ]);
    }

    #[Route('/start', name: 'server_start')]
    public function start (
        ServerService       $serverService,
        LoginRepository     $loginRepository
    ): JsonResponse
    {
        $user = $loginRepository->find($this->getUser()->getId());
        if (null === $user->getServer()) {
            $exception = new NoServerFoundException();
            $alert = Alert::error($exception->getMessage());
        } else {
            $serverService->startServer($user->getServer());
            $alert = Alert::success('Server is online now');
        }

        return new JsonResponse($alert->getMessage(), $alert->getCode());
    }

    #[Route('/shutdown', name: 'server_shutdown')]
    public function shutdown (
        ServerService       $serverService,
        LoginRepository     $loginRepository
    ): Response
    {
        $user = $loginRepository->find($this->getUser()->getId());
        if (null === $user->getServer()) {
            $exception = new NoServerFoundException();
            $alert = Alert::error($exception->getMessage());
        } else {
            $serverService->stopServer($user->getServer());
            $alert = Alert::success('Server is offline now');
        }

        return new JsonResponse($alert->getMessage(), $alert->getCode());
    }

    #[Route('/advanced', name: 'server_advanced')]
    public function advanced (
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

    #[Route('/delete', name: 'server_delete')]
    public function delete (
        LoginRepository     $loginRepository,
        DeleteServerService $deleteServerService,
        ServerService       $serverService
    ): Response
    {
        $user = $loginRepository->find($this->getUser()->getId());
        $server = $user->getServer();
        if (null === $server) {
            $alert = Alert::error('No server found');

            return new JsonResponse($alert->getMessage(), $alert->getCode());
        }

        $serverService->stopServer($server);
        $deleteServerService->deleteServer($server);

        $alert = Alert::success('Server has been deleted');

        return new JsonResponse($alert->getMessage(), $alert->getCode());
    }

    #[Route('/change_type', name: 'server_change_type')]
    public function changeType (
        ServerService       $serverService,
        Request             $request
    ): JsonResponse
    {
        try {
            $server = $this->getUser()->getServer();
            if (ServerInterface::STATUS_ONLINE === $server->getStatus()) {

                throw new ServerIsAlreadyRunningException();
            }
            $type = $request->get(ServerInterface::FORM_STEP2_GAMETYPE);
            $serverService->updateServerType($server, $type);
            $alert = Alert::success("Changed server mod loader");
        } catch (\Exception $e) {
            $alert = Alert::error($e->getMessage());
        }

        return new JsonResponse($alert->getMessage(), $alert->getCode());
    }

    #[Route('/usage', name: 'server_usage')]
    public function usage (
        ServerService       $serverService,
    ): JsonResponse
    {
        $user = $this->getUser();
        $server = $user->getServer();
        $usage = $serverService->getServerUsageFile($server);

        return new JsonResponse($usage, 200);
    }
}
