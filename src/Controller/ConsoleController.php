<?php

namespace App\Controller;

use App\Entity\Alert;
use App\Exception\Console\CouldNotExecuteCommandException;
use App\Repository\LoginRepository;
use App\Service\Console\ConsoleService;
use App\Service\Helper\OperatingSystemHelper;
use App\UniqueNameInterface\ConsoleInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConsoleController extends AbstractController
{
    #[Route('/console', name: 'console_view')]
    public function view (
        ConsoleService      $consoleService,
        LoginRepository     $loginRepository
    ): Response {
        if (OperatingSystemHelper::isWindows()) {
            $allowed = false;
            $history = '';
        } else {
            $user = $loginRepository->find($this->getUser()->getId());
            $server = $user->getServer();
            $allowed = true;
            $history = $consoleService->getConsoleHistory($server);
        }

        return $this->render('console/view.html.twig', [
            'allowed'   => $allowed,
            'history'   => $history,
        ]);
    }
    #[Route('/console_execute', name: 'console_execute_command')]
    public function executeCommand (
        ConsoleService      $consoleService,
        LoginRepository     $loginRepository,
        Request             $request
    ): JsonResponse {
        $command = $request->get(ConsoleInterface::FORM_COMMAND);
        $user = $loginRepository->find($this->getUser()->getId());
        $server = $user->getServer();

        try {
            if ($consoleService->executeConsoleCommand($server, $command)) {
                $alert = Alert::success("Successfully executed command");
            }
            else {
                throw new CouldNotExecuteCommandException();
            }
        } catch (\Exception $e) {
            $alert = Alert::error($e->getMessage());
        }

        return new JsonResponse($alert->getMessage(), $alert->getCode());
    }
}
