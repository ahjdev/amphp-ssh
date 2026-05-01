<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

enum Signal: string
{
    case SIGABRT = 'ABRT';
    case SIGALRM = 'ALRM';
    case SIGFPE  = 'FPE';
    case SIGHUP  = 'HUP';
    case SIGILL  = 'ILL';
    case SIGINT  = 'INT';
    case SIGKILL = 'KILL';
    case SIGPIPE = 'PIPE';
    case SIGQUIT = 'QUIT';
    case SIGSEGV = 'SEGV';
    case SIGTERM = 'TERM';
    case SIGUSR1 = 'USR1';
    case SIGUSR2 = 'USR2';
    case SIGTRAP  = 'TRAP';
    case SIGBUS   = 'BUS';
    case SIGSYS   = 'SYS';
    case SIGCONT  = 'CONT';
    case SIGSTOP  = 'STOP';
    case SIGTSTP  = 'TSTP';
    case SIGWINCH = 'WINCH';
    case SIGINFO  = 'INFO';

    public static function fromCode(int $code): self
    {
        return match ($code) {

            // Exit Signals (RFC 4254, Section 6.10)
            6  => self::SIGABRT, // SIGABRT
            14 => self::SIGALRM, // SIGALRM
            8  => self::SIGFPE,  // SIGFPE
            1  => self::SIGHUP,  // SIGHUP
            4  => self::SIGILL,  // SIGILL
            2  => self::SIGINT,  // SIGINT
            9  => self::SIGKILL, // SIGKILL
            13 => self::SIGPIPE, // SIGPIPE
            3  => self::SIGQUIT, // SIGQUIT
            11 => self::SIGSEGV, // SIGSEGV
            15 => self::SIGTERM, // SIGTERM
            10 => self::SIGUSR1, // SIGUSR1
            12 => self::SIGUSR2, // SIGUSR2

            // Signals for Sending (Optional extensions)
            5  => self::SIGTRAP,  // SIGTRAP
            7  => self::SIGBUS,   // SIGBUS
            31 => self::SIGSYS,   // SIGSYS
            18 => self::SIGCONT,  // SIGCONT
            19 => self::SIGSTOP,  // SIGSTOP
            20 => self::SIGTSTP,  // SIGTSTP
            28 => self::SIGWINCH, // SIGWINCH
            29 => self::SIGINFO,  // SIGINFO (BSD)
        };
    }
}
