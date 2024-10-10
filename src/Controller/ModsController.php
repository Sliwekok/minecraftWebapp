<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Alert;
use App\Form\ModsLoadCustomModsFormType;
use App\Service\Mods\ModsService;
use App\UniqueNameInterface\ModsInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/mods')]
class ModsController extends AbstractController
{
    #[Route('/', name: 'mods_index')]
    public function index(
        ModsService $modsService,
        Request     $request
    ): Response
    {
        $server = $this->getUser()->getServer();
        $form = $this
            ->createForm(ModsLoadCustomModsFormType::class)
            ->handleRequest($request)
        ;

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $modsService->saveCustomMods($server, $form->get(ModsInterface::FORM_FILES)->getData());
            }
            else {
                foreach ($form->getErrors(true) as $error) {

                    throw new \Exception($error->getMessage());
                };
            }
        } catch (\Exception $e) {
            Alert::error($e->getMessage(), isToDelete: false);
        }
        return $this->render('mods/index.html.twig', [
            'server'    => $server,
            'form'      => $form
        ]);
    }

    #[Route('/browse', name: 'mods_browse')]
    public function browse(): Response
    {
        $server = $this->getUser()->getServer();

        return $this->render('mods/browse.html.twig', [
            'server'    => $server
        ]);
    }

    #[Route('/showAll', name: 'mods_show_all')]
    public function showAll(): Response
    {
        $server = $this->getUser()->getServer();

        return $this->render('mods/showAll.html.twig', [
            'server'    => $server
        ]);
    }

}
