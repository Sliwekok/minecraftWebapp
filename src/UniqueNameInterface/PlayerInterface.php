<?php

declare(strict_types=1);

namespace App\UniqueNameInterface;

class PlayerInterface
{

    public const NAME = 'name';
    public const OP = 'op';
    public const BLACKLISTED = 'blacklisted';
    public const STATUS = 'status';
    public const STATUS_ONLINE = 'online';
    public const STATUS_OFFLINE = 'offline';
    public const REQUEST_PLAYERS = 'players';
}
