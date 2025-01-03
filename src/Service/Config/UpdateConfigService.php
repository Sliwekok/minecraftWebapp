<?php

declare(strict_types=1);

namespace App\Service\Config;

use App\Entity\Config;
use App\Entity\Server;
use App\Exception\Server\CouldNotOpenServerPropertyFileException;
use App\Repository\ConfigRepository;
use App\Service\Filesystem\FilesystemService;
use App\UniqueNameInterface\ConfigInterface;
use App\UniqueNameInterface\ServerDirectoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use ReflectionClass;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;


class UpdateConfigService
{

    public function __construct (
        private EntityManagerInterface  $entityManager,
        private ConfigRepository        $configRepository,
    )
    {}

    public function updateConfigEntity (
        Config|FormInterface  $config,
        ?int                  $configId = null
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
        } else {
            /** if it's updated from other source - prevent from overriding and set proper values*/
            $configOld = $this->configRepository->find($config->getId());
            $configOld
                ->setSeed($config->getSeed())
                ->setPort($config->getPort())
                ->setWhitelist($config->isWhitelist())
                ->setPvp($config->isPvp())
                ->setMaxPlayers($config->getMaxPlayers())
                ->setHardcore($config->isHardcore())
                ->setDifficulty($config->getDifficulty())
                ->setAllowFlight($config->isAllowFlight())
                ->setMotd($config->getMotd())
                ->setLevelName($config->getLevelName())
                ->setMaxRam($config->getMaxRam())
            ;
            $config = $configOld;
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
            $file = @file_get_contents($filename);
            $fileChecked = 0;
            // check if file is generated - if server is created then might be some delay
            while (false === $file && $fileChecked < 5) {
                sleep(3);
                $file = @file_get_contents($filename);
                $fileChecked++;
            }
            $fileContent = explode("\n", $file);
            $reflection = new ReflectionClass(ConfigInterface::class);
            $configArr = (array) $config;
            $configArr[ConfigInterface::ENTITY_ID] = $config->getId();

            /** iterate through lines to match content and overwrite with config */
            foreach ($fileContent as &$line) {
                if (str_starts_with('#', $line) || 0 === strlen($line)) {

                    continue;
                }
                foreach ($configArr as $entity => $value) {
                    if (is_bool($value)) $value ? $value = "true" : $value = "false";
                    $entityName = preg_replace('/[^\da-z]/i', '', strtoupper(str_replace(Config::class, '', $entity)));
                    $propertyName = ConfigInterface::PROPERTY. $entityName;
                    $property = $reflection->getConstant($propertyName);
                    if (false === $property) {

                        continue;
                    }
                    if (str_starts_with($line, $property)) {
                        $line = $property. '='. (string)$value;
                    }

                    /** set server connection to matching server ip and port */
                    switch ($line) {
                        case str_starts_with($line, ConfigInterface::PROPERTY_STATIC_IP):
                            $line = ConfigInterface::PROPERTY_STATIC_IP. '='. gethostbyname(gethostname());
                            break;
                        case str_starts_with($line, ConfigInterface::PROPERTY_STATIC_SERVERPORT):
                            $line = ConfigInterface::PROPERTY_STATIC_SERVERPORT. '='. $config->getPort();
                            break;
                        case str_starts_with($line, ConfigInterface::PROPERTY_STATIC_QUERYPORT):
                            $line = ConfigInterface::PROPERTY_STATIC_QUERYPORT. '='. $config->getPort();
                            break;
                        default:
                            break;
                    }
                }
            }

            file_put_contents($filename, implode("\n", $fileContent));
        } catch (Exception $exception) {

            throw new  CouldNotOpenServerPropertyFileException($exception->getMessage());
        }

        return true;
    }

    public function updateConfigEntityFromFile (
        Server  $server
    ): Config {
        try {
            $config = $server->getConfig();
            $path = (new FilesystemService($server->getDirectoryPath()))->getAbsoluteMinecraftPath();
            $filename = $path . '/' .ServerDirectoryInterface::MINECRAFT_SERVERPROPERTIES;
            $file = @file_get_contents($filename);
            $fileChecked = 0;
            $reflection = new ReflectionClass(ConfigInterface::class);
            $encoders = [new JsonEncoder()];
            /** prevent from object circular reference (server->config->server...) */
            $defaultContext = [
                AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function (object $object, string $format, array $context): string {
                    return get_class($object);
                },
            ];
            $normalizers = [
                new ObjectNormalizer(defaultContext: $defaultContext),
                new DateTimeNormalizer([
                    DateTimeNormalizer::FORMAT_KEY => "Y-m-d",
                ]),
            ];
            $serializer = new Serializer($normalizers, $encoders);
            $configArr = json_decode($serializer->serialize($config, 'json', ['groups' => ['normal']]), true);
            // check if file exists - if server is created then might be some delay
            while (false === $file && $fileChecked < 3) {
                sleep(3);
                $file = @file_get_contents($filename);
                $fileChecked++;
            }
            $fileContent = explode("\n", $file);
            foreach ($fileContent as &$line) {
                if (str_starts_with('#', $line) || 0 === strlen($line)) {

                    continue;
                }
                foreach ($configArr as $entity => $value) {
                    $entityName = preg_replace('/[^\da-z]/i', '', strtoupper(str_replace(Config::class, '', $entity)));
                    $propertyName = ConfigInterface::PROPERTY. $entityName;
                    $property = $reflection->getConstant($propertyName);
                    if (false === $property) {

                        continue;
                    }
                    if (str_starts_with($line, $property)) {
                        /**
                         * get value from line, typical it's property=value
                         * first we trim property name, next we trim '=' char
                         * lastly we delete new lines
                         */
                        $newValue = trim(substr(strstr($line, '='), 1));
                        $configArr[$entity] = $newValue;
                    }
                }
            }

            unset($configArr[ConfigInterface::ENTITY_SERVER]);
            /** @var Config $config */
            $config = $serializer->deserialize(
                json_encode($configArr), Config::class, 'json', $defaultContext
            );

            $config
                ->setServer($server)
                ->setId($configArr[ConfigInterface::ENTITY_ID])
            ;
        } catch (Exception $exception) {

            throw new  CouldNotOpenServerPropertyFileException($exception->getMessage());
        }

        return $config;
    }
}
