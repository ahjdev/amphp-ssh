<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

final class UserAuthRequestAskPublicKey extends UserAuthRequestPublicKey
{
    public function hasSignature(): bool
    {
        return false;
    }
}
