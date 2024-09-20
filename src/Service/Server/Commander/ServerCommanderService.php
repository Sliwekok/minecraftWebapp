<?php

declare(strict_types=1);

namespace App\Service\Server\Commander;

use App\Entity\Server;
use App\UniqueNameInterface\ServerInterface;
use Doctrine\ORM\EntityManagerInterface;

class ServerCommanderService
{

    /**
     * that class validates machine that PHP is run on - commands depends on OS
     */

    public function __construct (
        private EntityManagerInterface  $entityManager,
        private LinuxCommanderService   $linuxCommander,
        private WindowsCommanderService $windowsCommander
    )
    {}

    public function startServer (
        Server $server
    ): Server {
        if (php_uname('s') === ServerInterface::OS_WINDOWS) {
            $this->linuxCommander->startServer($server);
        } else {
            $this->windowsCommander->startServer($server);
        }

        return $this->saveStartServer($server);
    }

    /**
     * end process server via CommandLine
     */
    public function stopServer (
        Server $server
    ): Server {
        if (PHP_OS === ServerInterface::OS_WINDOWS) {
            $this->linuxCommander->stopServer($server);
        } else {
            $this->windowsCommander->stopServer($server);
        }

        return $this->saveStopServer($server);
    }

    /**
     * save status to enitity
     */
    private function saveStartServer (
        Server  $server,
    ): Server {
        $server = $server
            ->setStatus(ServerInterface::STATUS_ONLINE)
        ;

        $this->entityManager->persist($server);
        $this->entityManager->flush();

        return $server;
    }

    /**
     * delete process PID from entity
     */
    private function saveStopServer (
        Server  $server,
    ): Server {
        $server = $server
            ->setStatus(ServerInterface::STATUS_OFFLINE)
        ;

        $this->entityManager->persist($server);
        $this->entityManager->flush();

        return $server;
    }

}
