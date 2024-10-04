<?php

declare(strict_types=1);

namespace App\UniqueNameInterface;

class ConsoleInterface
{
    public const REPLACEMENT_NICKNAME = "{player_nickname}";

    public const FORM_COMMAND = 'command';
    public const SECUREDATA_CHECKKEYWORD_SUDO = 'sudo';

    public const COMMAND_PLAYER_LIST = '/list';
    public const COMMAND_PLAYER_WHITELIST_ADD = '/whitelist add '. self::REPLACEMENT_NICKNAME;
    public const COMMAND_PLAYER_WHITELIST_REMOVE = '/whitelist remove '. self::REPLACEMENT_NICKNAME;
    public const COMMAND_PLAYER_OP_ADD = '/op '. self::REPLACEMENT_NICKNAME;
    public const COMMAND_PLAYER_OP_REMOVE = '/deop '. self::REPLACEMENT_NICKNAME;
    public const COMMAND_PLAYER_BLACKLIST_ADD = '/ban '. self::REPLACEMENT_NICKNAME;
    public const COMMAND_PLAYER_BLACKLIST_REMOVE = '/pardon '. self::REPLACEMENT_NICKNAME;

}
