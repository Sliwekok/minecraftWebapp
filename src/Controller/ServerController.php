<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\LoginRepository;
use App\Service\Mojang\MinecraftVersions;
use App\Service\Server\ServerService;
use App\Form\CreateNewServerForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/server')]
class ServerController extends AbstractController
{
    #[Route('/', name: 'server_preview')]
    public function management(
        ServerService $serverService
    ): Response
    {
        $user = $this->getUser();
        $server = $serverService->userServer($user);
        if (null === $server) {

            return $this->redirectToRoute('server_create_new');
        }

        return $this->render('user/user.html.twig', [
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

}
