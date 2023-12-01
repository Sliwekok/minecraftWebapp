<?php

declare(strict_types=1);

namespace App\Service\Config;

use App\Entity\Config;
use App\Exception\Server\CouldNotOpenServerPropertyFile;
use App\Repository\ConfigRepository;
use App\Service\Filesystem\FilesystemService;
use App\UniqueNameInterface\ConfigInterface;
use App\UniqueNameInterface\ServerDirectoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use ReflectionClass;
use Symfony\Component\Form\FormInterface;

class UpdateConfigService
{

    public function __construct (
        private EntityManagerInterface  $entityManager,
        private ConfigRepository        $configRepository
    )
    {}

    public function updateConfig (
        Config|FormInterface  $config,
        int                   $configId = null
    ): bool {
        if ($config instanceof FormInterface) {
            $configNew = $this->configRepository->find($configId) ?? new Config;
            $configNew
                ->setSeed($config->get(ConfigInterface::ENTITY_SEED)->getData())
                ->setPort($config->get(ConfigInterface::ENTITY_PORT)->getData())
                ->setWhitelist($config->get(ConfigInterface::ENTITY_WHITELIST)->getData())
                ->setPvp($config->get(ConfigInterface::ENTITY_PVP)->getData())
                ->setMaxPlayers($config->get(ConfigInterface::ENTITY_MAXPLAYERS)->getData())
                ->setHardcore($config->get(ConfigInterface::ENTITY_HARDCORE)->getData())
                ->setDifficulty($config->get(ConfigInterface::ENTITY_DIFFICULTY)->getData())
                ->setAllowFlight($config->get(ConfigInterface::ENTITY_ALLOWFLIGHT)->getData())
                ->setMotd($config->get(ConfigInterface::ENTITY_MOTD)->getData())
                ->setLevelName($config->get(ConfigInterface::ENTITY_LEVELNAME)->getData())
                ->setMaxRam(4)
            ;

            $config = $configNew;
        }

        $this->entityManager->persist($config);
        $this->entityManager->flush();

        return $this->updatePropertyFile($config);
    }

    public function updatePropertyFile (
        Config  $config
    ): bool {
        try {
            $server = $config->getServer();
            $path = (new FilesystemService($server->getDirectoryPath()))->getAbsoluteMinecraftPath();
            $filename = $path . '/' .ServerDirectoryInterface::MINECRAFT_SERVERPROPERTIES;
            $file = file_get_contents($filename);
            $fileContent = explode("\n", $file);
            $reflection = new ReflectionClass(ConfigInterface::class);
            $configArr = (array) $config;

            foreach ($fileContent as &$line) {
                if (str_starts_with('#', $line) || 0 === strlen($line)) {

                    continue;
                }
                foreach ($configArr as $entity => $value) {
                    $entityName = preg_replace('/[^\da-z]/i', '', strtoupper(str_replace(Config::class, '', $entity)));
                    $propertyName =  ConfigInterface::PROPERTY. $entityName;
                    $property = $reflection->getConstant($propertyName);
                    if (false === $property) {

                        continue;
                    }
                    if (str_starts_with($line, $property)) {
                        $line = $property. '='. (string)$value;
                    }
                }
            }

            file_put_contents($filename, implode("\n", $fileContent));
        } catch (Exception $exception) {

            throw new  CouldNotOpenServerPropertyFile($exception->getMessage());
        }

        return true;
    }
}
