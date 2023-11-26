<?php

declare(strict_types=1);

namespace App\Service\Mojang;

use App\Exception\Server\VersionNotFoundException;
use App\UniqueNameInterface\MojangInterface;

class MinecraftVersions
{

    public function __construct(
    )
    {}

    public function getAllVersionsData(): array {
        $url = MojangInterface::VERSIONS_URL;
        $allVersions = json_decode(file_get_contents($url, true), true)[MojangInterface::VERSIONS_MANIFEST_VERSIONS];
        $officialVersions = [];

        // along versions there are snapshots that are not compatible
        foreach ($allVersions as $version) {
            if (MojangInterface::VERSIONS_MANIFEST_TYPE_RELEASE === $version[MojangInterface::VERSIONS_MANIFEST_TYPE]) {
                $officialVersions[] = $version;
            }
        }

        return $officialVersions;
    }

    public function getAllVersions(): array {
        $onlyVersions = [];
        $allData = $this->getAllVersionsData();
        foreach ($allData as $version) {
            $onlyVersions[] = $version[MojangInterface::VERSIONS_MANIFEST_ID];
        }

        return $onlyVersions;
    }

    public function getSpecificVersion(string $version): array {
        $versions = $this->getAllVersionsData();
        $key = array_search($version, array_column($versions, MojangInterface::VERSIONS_MANIFEST_ID));

        if (null === $versions[$key]) {
            throw new VersionNotFoundException();
        }

        return $versions[$key];
    }
}