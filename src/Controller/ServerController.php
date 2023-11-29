<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\Server\NoServerFoundException;
use App\Repository\LoginRepository;
use App\Service\Mojang\MinecraftVersions;
use App\Service\Server\ServerService;
use App\Form\CreateNewServerForm;
use App\UniqueNameInterface\ConfigInterface;
use App\UniqueNameInterface\JsonResponseInterface;
use ReflectionClass;
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
    ): Response
    {
        $user = $this->getUser();
        $server = $user->getServer();
        if (null === $server) {

            return $this->redirectToRoute('server_create_new');
        }

        return $this->render('server/preview.html.twig', [
            'user'  => $user
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
        $defaultServerName = $user->getUsername(). "'s server";
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
            $msg = $exception->getMessage();
            $code = $exception->getStatusCode();
        } else {
            $serverService->startServer($user->getServer());
            $msg = [
                JsonResponseInterface::MESSAGE => 'Server is online now'
            ];
        }

        return new JsonResponse($msg, $code ?? 200);
    }

    #[Route('/shutdown', name: 'server_shutdown')]
    public function shutdown (
        ServerService       $serverService,
        LoginRepository     $loginRepository
    ): JsonResponse
    {
        $user = $loginRepository->find($this->getUser()->getId());
        if (null === $user->getServer()) {
            $msg = new NoServerFoundException();
            $statusCode = 404;
        } else {
            $serverService->stopServer($user->getServer());
            $msg = [
                JsonResponseInterface::MESSAGE => 'Server is offline now'
            ];
        }

        return new JsonResponse($msg, $statusCode ?? null);
    }

}
