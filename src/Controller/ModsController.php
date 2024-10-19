<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Alert;
use App\Form\ModsLoadCustomModsFormType;
use App\Service\Mods\ModsService;
use App\UniqueNameInterface\CurseforgeInterface;
use App\UniqueNameInterface\ModsInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
                }
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
    public function browse (
        ModsService $modsService,
        Request     $request,
    ): Response
    {
        $index = (int)$request->get(CurseforgeInterface::API_KEY_INDEX, 0);
        $searchFilter = trim($request->get(CurseforgeInterface::API_KEY_SEARCHFILTER, ''));
        $category = trim($request->get(CurseforgeInterface::API_KEY_CATEGORIES, ''));
        $sortBy = trim($request->get(CurseforgeInterface::API_KEY_SORTBY, ''));
        $params = [
            'category'      => $category,
            'index'         => $index,
            'sortBy'        => $sortBy,
            'searchFilter'  => $searchFilter
        ];
        $server = $this->getUser()->getServer();
        $mods = $modsService->getCurseforgeMods($server, $index, $sortBy, $category, $searchFilter);
        $categories = $modsService->getCurseforgeCategories();
        $sortables = $modsService->getCurseforgeSortables();
        $modsInstalled = $modsService->getModsIds($server);

        return $this->render('mods/browse.html.twig', [
            'server'        => $server,
            'pagination'    => $mods[CurseforgeInterface::API_PAGINATION],
            'mods'          => $mods[CurseforgeInterface::API_DATA],
            'categories'    => $categories[CurseforgeInterface::API_DATA],
            'params'        => $params,
            'sortables'     => $sortables,
            'modsInstalled' => $modsInstalled
        ]);
    }

    #[Route('/showAll', name: 'mods_show_all')]
    public function showAll (): Response
    {
        $server = $this->getUser()->getServer();

        return $this->render('mods/showAll.html.twig', [
            'server'    => $server,
            'mods'      => $server->getMods()
        ]);
    }

    #[Route('/installFromCurseforge', name: 'mods_install_from_curseforge')]
    public function installFromCurseforge (
        ModsService $modsService,
        Request     $request
    ): Response
    {
        try {
            $modId = (int)$request->get(CurseforgeInterface::API_KEY_ID);
            $server = $this->getUser()->getServer();
            $modsService->installModFromCurseforge($server, $modId);

            $alert = Alert::success("Installed new mod <br> Remember changes will apply after resetting your server");

        } catch (\Exception $e) {
            $alert = Alert::error($e->getMessage());
        }

        return new JsonResponse($alert->getMessage(), $alert->getCode());
    }

    #[Route('/delete', name: 'mods_delete')]
    public function delete (
        ModsService $modsService,
        Request     $request
    ): Response
    {
        try {
            $modId = (int)$request->get(CurseforgeInterface::API_KEY_ID);
            $server = $this->getUser()->getServer();
            $modsService->delete($server, $modId);

            $alert = Alert::success("Deleted mod <br> Remember changes will apply after resetting your server");

        } catch (\Exception $e) {
            $alert = Alert::error($e->getMessage());
        }

        return new JsonResponse($alert->getMessage(), $alert->getCode());
    }

}
