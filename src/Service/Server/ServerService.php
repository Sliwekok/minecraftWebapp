<?php

declare(strict_types=1);

namespace App\Service\Server;

use App\Entity\Login;
use App\Entity\Server;
use App\Repository\ServerRepository;
use App\UniqueNameInterface\ServerInterface;
use Symfony\Component\Form\FormInterface;

class ServerService
{

    public function __construct (
        private ServerRepository    $serverRepository,
        private CreateServerService $createServerService,
    )
    {}

    public function createServer (
        FormInterface $data,
        Login         $user
    ): void {
        $this->createServerService->createServer($data, $user);

    }

    public function userServer (Login $user): ?Server {
        return $this->serverRepository->findOneBy([
            ServerInterface::ENTITY_ID => $user->getId()
        ]);
    }
}