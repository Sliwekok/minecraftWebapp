<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Alert;
use App\Service\Players\PlayersService;
use App\UniqueNameInterface\PlayerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/players')]
class PlayersController extends AbstractController
{
    #[Route('/list', name: 'players_index')]
    public function index (
        PlayersService  $playersService
    ): Response
    {
        $server = $this->getUser()->getServer();

        $players = $playersService->getAllPlayersArranged($server);

        return $this->render('players/index.html.twig', [
            'players'   => $players
        ]);
    }

    #[Route('/whitelist', name: 'players_whitelist')]
    public function whitelist (
        PlayersService  $playersService
    ): Response
    {
        $server = $this->getUser()->getServer();

        $players = $playersService->getWhitelistedPlayers($server);

        return $this->render('players/whitelist.html.twig', [
            'players'   => $players
        ]);
    }

    #[Route('/whitelist/add', name: 'players_whitelist_add')]
    public function whitelistAddPlayer (
        PlayersService  $playersService,
        Request         $request
    ): JsonResponse
    {
        try {
            $data = explode(',', $request->get(PlayerInterface::REQUEST_PLAYERS));
            $server = $this->getUser()->getServer();
            $playersService->addToWhitelist($server, $data);
            $alert = Alert::success('Added to whitelist', isToDelete: false);
        } catch (\Exception $e) {
            $alert = Alert::error($e->getMessage());
        }

        return new JsonResponse($alert->getMessage(), $alert->getCode());
    }

    #[Route('/whitelist/remove', name: 'players_whitelist_remove')]
    public function whitelistRemovePlayer (
        PlayersService  $playersService,
        Request         $request
    ): JsonResponse
    {
        try {
            $data = trim($request->get(PlayerInterface::REQUEST_PLAYERS));
            $server = $this->getUser()->getServer();
            $playersService->removeFromWhitelist($server, $data);
            $alert = Alert::success('Removed from whitelist', isToDelete: false);
        } catch (\Exception $e) {
            $alert = Alert::error($e->getMessage());
        }

        return new JsonResponse($alert->getMessage(), $alert->getCode());
    }

    #[Route('op', name: 'players_op')]
    public function op (
        PlayersService  $playersService
    ): Response
    {
        $server = $this->getUser()->getServer();

        $players = $playersService->getOps($server);

        return $this->render('players/op.html.twig', [
            'players'   => $players
        ]);
    }
    #[Route('/op/add', name: 'players_op_add')]
    public function opAddPlayer (
        PlayersService  $playersService,
        Request         $request
    ): JsonResponse
    {
        try {
            $data = explode(',', $request->get(PlayerInterface::REQUEST_PLAYERS));
            $server = $this->getUser()->getServer();
            $playersService->addToOpList($server, $data);
            $alert = Alert::success('Added to OP list', isToDelete: false);
        } catch (\Exception $e) {
            $alert = Alert::error($e->getMessage());
        }

        return new JsonResponse($alert->getMessage(), $alert->getCode());
    }

    #[Route('/op/remove', name: 'players_op_remove')]
    public function opRemovePlayer (
        PlayersService  $playersService,
        Request         $request
    ): JsonResponse
    {
        try {
            $data = $request->get(PlayerInterface::REQUEST_PLAYERS);
            $server = $this->getUser()->getServer();
            $playersService->removeFromOpList($server, $data);
            $alert = Alert::success('Removed from OP list', isToDelete: false);
        } catch (\Exception $e) {
            $alert = Alert::error($e->getMessage());
        }

        return new JsonResponse($alert->getMessage(), $alert->getCode());
    }

    #[Route('/blacklist', name: 'players_blacklist')]
    public function blacklist (
        PlayersService  $playersService
    ): Response
    {
        $server = $this->getUser()->getServer();

        $players = $playersService->getBannedPlayers($server);

        return $this->render('players/blacklist.html.twig', [
            'players'   => $players
        ]);
    }
    #[Route('/blacklist/add', name: 'players_blacklist_add')]
    public function blacklistAddPlayer (
        PlayersService  $playersService,
        Request         $request
    ): JsonResponse
    {
        try {
            $data = explode(',', $request->get(PlayerInterface::REQUEST_PLAYERS));
            $server = $this->getUser()->getServer();
            $playersService->addToBlacklist($server, $data);
            $alert = Alert::success('Added to Blacklist', isToDelete: false);
        } catch (\Exception $e) {
            $alert = Alert::error($e->getMessage());
        }

        return new JsonResponse($alert->getMessage(), $alert->getCode());
    }

    #[Route('/blacklist/remove', name: 'players_blacklist_remove')]
    public function blacklistRemovePlayer (
        PlayersService  $playersService,
        Request         $request
    ): JsonResponse
    {
        try {
            $data = $request->get(PlayerInterface::REQUEST_PLAYERS);
            $server = $this->getUser()->getServer();
            $playersService->removeFromBlacklist($server, $data);
            $alert = Alert::success('Removed from Blacklist', isToDelete: false);
        } catch (\Exception $e) {
            $alert = Alert::error($e->getMessage());
        }

        return new JsonResponse($alert->getMessage(), $alert->getCode());
    }

}
