<?php declare(strict_types=1);

namespace Amp\Ssh;

interface SshMessage extends SshSession
{
    public function encode(): string;
    public static function decode(string $payload): self;
    public static function getNumber(): int;
}
