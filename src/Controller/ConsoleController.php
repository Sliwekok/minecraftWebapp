<?php

namespace App\Controller;

use App\Service\Console\ConsoleService;
use App\Service\Helper\OperatingSystemHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConsoleController extends AbstractController
{
    #[Route('/console', name: 'console_view')]
    public function view(
        ConsoleService  $consoleService
    ): Response
    {
        if (OperatingSystemHelper::isWindows()) {
            $allowed = false;
            $history = '';
        } else {
            $allowed = true;
            $history = $consoleService->getConsoleHistory();
        }

        return $this->render('console/view.html.twig', [
            'allowed'   => $allowed,
            'history'   => $history
        ]);
    }
}
