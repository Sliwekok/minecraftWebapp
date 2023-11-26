<?php

declare(strict_types=1);

namespace App\UniqueNameInterface;

class MojangInterface
{
    public const VERSIONS_URL = 'https://launchermeta.mojang.com/mc/game/version_manifest.json';
    public const VERSIONS_MANIFEST_VERSIONS = 'versions';
    public const VERSIONS_MANIFEST_URL = 'url';
    public const VERSIONS_MANIFEST_ID = 'id';
    public const VERSIONS_MANIFEST_TYPE = 'type';
    public const VERSIONS_MANIFEST_TYPE_SNAPSHOT = 'snapshot';
    public const VERSIONS_MANIFEST_TYPE_RELEASE = 'release';

    public const PACKAGES_DOWNLOADS = 'downloads';
    public const PACKAGES_DOWNLOADS_SERVER = 'server';
    public const PACKAGES_DOWNLOADS_SERVER_URL = 'url';
}