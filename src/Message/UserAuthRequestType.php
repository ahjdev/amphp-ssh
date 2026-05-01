<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

enum UserAuthRequestType: string
{
    case PASSWORD   = 'password';
    case PUBLIC_KEY = 'publickey';
}
