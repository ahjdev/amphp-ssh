<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

enum ChannelType: string
{
    case X11 = 'x11';
    case SESSION = 'session';
    case DIRECT_TCPIP = 'direct-tcpip';
    case FORWARDED_TCPIP = 'forwarded-tcpip';
}
