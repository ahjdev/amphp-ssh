<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

enum ChannelRequestType: string
{
    case PTY = 'pty-req';
    case ENV = 'env';
    case SHELL = 'shell';
    case EXEC = 'exec';
    case SIGNAL = 'signal';
    case SUBSYSTEM = 'subsystem';
    case WINDOW_CHANGE  = 'window-change';
    case X11_FORWARDING = 'x11-req';
    case EXIT_STATUS    = 'exit-status';
    case EXIT_SIGNAL    = 'exit-signal';
}
