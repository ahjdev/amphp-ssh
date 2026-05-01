<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\Message\Channel;
use Amp\Ssh\Message\ChannelRequestExitSignal;
use Amp\Ssh\Message\ChannelRequestExitStatus;
use Amp\Ssh\Message\ChannelRequestPty;
use Amp\Ssh\Message\ChannelRequestShell;
use Amp\Ssh\Message\ChannelRequestSignal;
use Amp\Ssh\Message\ChannelRequestType;
use Amp\Ssh\Message\ChannelRequestWindowChange;
use Amp\Ssh\Message\Signal;

/**
 * @internal
 */
abstract class ChannelRequest extends Channel
{
    protected ChannelRequestType $type;

    public function __construct(int $recipientChannel, public readonly bool $wantReply = true)
    {
        parent::__construct($recipientChannel);
    }

    public function encode(): string 
    {
        $type = $this->type->value;

        return \pack('CN2a*C', self::getNumber(), $this->recipientChannel, \strlen($type), $type, $this->wantReply);
    }

    public static function decode(): \Generator
    {
        $recipientChannel = yield from Ssh\uint32();
        $type = yield from Ssh\string();
        $type = ChannelRequestType::from($type);
        $wantReply = yield from Ssh\boolean();

        if ($type === ChannelRequestType::SHELL) {
            return new ChannelRequestShell($recipientChannel, $wantReply);
        }

        if ($type === ChannelRequestType::EXEC) {
            $command = yield from Ssh\string();
            return new ChannelRequestExec($recipientChannel, $wantReply, $command);
        }

        if ($type === ChannelRequestType::EXIT_STATUS) {
            $code = yield from Ssh\uint32();
            return new ChannelRequestExitStatus($recipientChannel, $code);
        }

        if ($type === ChannelRequestType::ENV) {
            [$name, $value] = yield from Ssh\times(4, Ssh\string(...));
            return new ChannelRequestEnv($recipientChannel, $wantReply, $name, $value);
        }

        if ($type === ChannelRequestType::WINDOW_CHANGE) {
            [$columns, $rows, $width, $height] = yield from Ssh\times(4, Ssh\uint32(...));
            return new ChannelRequestWindowChange($recipientChannel, $columns, $rows, $width, $height);
        }

        if ($type === ChannelRequestType::PTY) {
            $term = yield from Ssh\string();
            [$columns, $rows, $width, $height] = yield from Ssh\times(4, Ssh\uint32(...));
            $mode = yield from Ssh\string();
            $modes = [];
            // todo: make it better
            while (\strlen($mode) > 0) {
                $id = \unpack('C', $mode)[1];
                $mode = \substr($mode, 1);

                if (
                    $id === ChannelRequestPtyMode::TTY_OP_END ||
                    $id >= ChannelRequestPtyMode::TTY_OP_NOT_DEFINED
                ) {
                    break;
                }
                $value = \unpack('N', $mode)[1];
                $mode  = \substr($mode, 4);
                $modes[$id] = $value;
            }

            return new ChannelRequestPty($recipientChannel, $wantReply, $term, $columns, $rows, $width, $height, $modes);
        }

        $signal = yield from Ssh\string();
        $signal = Signal::from($signal);
        $cordDumped = yield from Ssh\boolean();
        [$errorMessage, $languageTag] = yield from Ssh\times(2, Ssh\string(...));

        if ($type === ChannelRequestType::SIGNAL) {
            return new ChannelRequestSignal($recipientChannel, $signal, $cordDumped, $errorMessage, $languageTag);
        }

        return new ChannelRequestExitSignal($recipientChannel, $wantReply, $signal, $cordDumped, $errorMessage, $languageTag);
    }

    public static function getNumber(): int
    {
        return self::SSH_MSG_CHANNEL_REQUEST;
    }
}
