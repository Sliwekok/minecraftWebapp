<?php

declare(strict_types=1);

namespace App\Service\Server;

use App\Entity\Server;
use App\Repository\ServerRepository;
use App\UniqueNameInterface\ServerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ServerService
{

    public function __construct(
        private ServerRepository    $serverRepository,
        private CreateServerService $createServerService,
    )
    {}

    public function createServer(
        FormInterface $data,
        UserInterface $user
    ): void {
        $this->createServerService->createServer($data, $user);
    }

    public function userServer(UserInterface $user): ?Server {
        return $this->serverRepository->findOneBy([
            ServerInterface::ENTITY_ID => $user->getId()
        ]);
    }
}