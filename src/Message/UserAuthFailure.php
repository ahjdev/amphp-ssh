<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;

final class UserAuthFailure extends SshMessage
{
    public function __construct(public readonly array $nextAuthentications, public readonly bool $partialSuccess)
    {
    }

    public function encode(): string
    {
        $nextAuthentications = $this->toNameList($this->nextAuthentications);
        return \pack(
            'C*Na*C',
            self::getNumber()->value,
            \strlen($nextAuthentications), $nextAuthentications,
            $this->partialSuccess,
        );
    }

    public static function decode(): \Generator
    {
        $nextAuthentications = yield from Ssh\namelist();
        $partialSuccess = yield from Ssh\boolean();

        return new static($nextAuthentications, $partialSuccess);
    }

    public static function getNumber(): SshMessageType
    {
        return SshMessageType::SSH_MSG_USERAUTH_FAILURE;
    }
}
