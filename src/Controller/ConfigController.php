<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\ConfigFormType;
use App\Repository\LoginRepository;
use App\Service\Config\ConfigService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/config')]
class ConfigController extends AbstractController
{

    #[Route('/update', name: 'config_update')]
    public function shutdown (
        ConfigService       $configService,
        LoginRepository     $loginRepository,
        Request             $request
    ): Response
    {
        $user = $loginRepository->find($this->getUser()->getId());
        $config = $user->getServer()->getConfig();

        if (null === $config) {

            return $this->redirectToRoute("server_create_new");
        }

        $form = $this
            ->createForm(ConfigFormType::class, $config, [])
            ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $configService->updateConfig($form, $config->getId());

            return $this->redirectToRoute('server_preview');
        }

        return $this->render('config/update.html.twig', [
            'user'              => $user,
            'form'              => $form,
        ]);
    }

}
