<?php

declare(strict_types=1);

namespace App\Service\Server;

use App\Entity\Login;
use App\Exception\Server\CouldNotCreateServerException;
use App\Service\Config\ConfigService;
use App\Service\Helper\OperatingSystemHelper;
use App\Service\Helper\ServerFileHelper;
use App\UniqueNameInterface\ConfigInterface;
use App\UniqueNameInterface\ServerDirectoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;
use App\Entity\Server;
use App\Service\Filesystem\FilesystemService;
use App\UniqueNameInterface\ServerInterface;
use Symfony\Component\Form\FormInterface;

class CreateServerService
{
    public function __construct (
        private ConfigService           $configService,
        private EntityManagerInterface  $entityManager,
        private ServerFileHelper        $serverFileHelper
    )
    {}

    public function createServer (
        FormInterface   $data,
        Login           $user
    ): Server {
        try {
            $version = $data->get(ServerInterface::FORM_STEP1)->get(ServerInterface::FORM_STEP1_VERSION)->getData();
            $serverName = trim($data->get(ServerInterface::FORM_STEP1)->get(ServerInterface::FORM_STEP1_NAME)->getData());
            $serverName = str_replace(['"', "'"], '', $serverName);
            $seed = $data->get(ServerInterface::FORM_STEP1)->get(ServerInterface::FORM_STEP1_SEED)->getData();
            $directory = $user->getUsername() . '/' . $serverName;
            $type = $data->get(ServerInterface::FORM_STEP2)->get(ServerInterface::FORM_STEP2_GAMETYPE)->getData();
            $fs = new FilesystemService($directory);

            if (!$fs->exists($directory)) {
                $fs->createDirectories();
            }

            $server = $this->createServerEntity($user, $directory, $serverName, $version, $type);
            $config = $this->configService->createConfig($server, $seed);
            $server->setConfig($config);

            $file = $this->serverFileHelper->getServerFile($version, $type, $fs->getAbsoluteMinecraftPath());
            $fs->storeFile(ServerDirectoryInterface::DIRECTORY_MINECRAFT, $file, ServerDirectoryInterface::MINECRAFT_SERVER_FILE);

            $this->entityManager->persist($server);
            $this->entityManager->persist($config);
            $this->entityManager->flush();

            if (OperatingSystemHelper::isUnix()) {
                $fs->createLogFile($serverName);
            }

            while (!file_exists($fs->getAbsoluteMinecraftPath() . DIRECTORY_SEPARATOR . ServerDirectoryInterface::MINECRAFT_SERVER_FILE)) {
                sleep(1); // wait until server file is created
            }

            return $server;


        } catch (\Exception $e){
            throw new CouldNotCreateServerException($e->getMessage());
        }
    }

    public function createServerEntity (
        Login   $user,
        string  $path,
        string  $serverName,
        string  $version,
        string  $type
    ): ?Server {
        $java = $this->getJavaVersion($version);
        $server = new Server();
        $server ->setCreateAt(new DateTime('now'))
                ->setModifiedAt(new DateTime('now'))
                ->setLogin($user)
                ->setDirectoryPath($path)
                ->setName($serverName)
                ->setVersion($version)
                ->setStatus(ServerInterface::STATUS_OFFLINE)
                ->setType($type)
                ->setJava($java);
            ;

        return $server;
    }

    public function updateEula (
        Server $server
    ): void {
        $directory = $server->getDirectoryPath();
        $fs = new FilesystemService($directory);
        $path = $fs->getAbsoluteMinecraftPath();
        $fs->dumpFile($path. '/' .ServerDirectoryInterface::MINECRAFT_EULA, ConfigInterface::EULA_AGREED);
    }

    private function getJavaVersion($minecraftVersion) {
        // Define Minecraft version ranges and corresponding Java versions
        $javaVersions = [
            ['start' => '1.0', 'end' => '1.16.5', 'java' => 'jdk8'],
            ['start' => '1.17', 'end' => '1.17.1', 'java' => 'jdk16'],
            ['start' => '1.18', 'end' => '1.20.5', 'java' => 'jdk17'],
            ['start' => '1.20.6', 'end' => '1.22', 'java' => 'jdk21'],
        ];

        foreach ($javaVersions as $range) {
            if (version_compare($minecraftVersion, $range['start'], '>=') &&
                version_compare($minecraftVersion, $range['end'], '<=')) {
                return $range['java'];
            }
        }

        // return default latest java version
        return $javaVersions[max($javaVersions)]['java'];
    }
}
