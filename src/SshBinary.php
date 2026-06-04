<?php declare(strict_types=1);

namespace Amp\Ssh;

use Amp\ByteStream\BufferedReader;
use Amp\ByteStream\PendingReadError;
use Amp\ByteStream\ReadableStream;

final class SshBinary
{
    private bool $pending = false;

    private string|BufferedReader $stream;

    public function __construct(string|ReadableStream $data)
    {
        if ($data instanceof ReadableStream) {
            $this->stream = new BufferedReader($data);
            return;
        }
        $this->stream = $data;
    }

    public function isReadable(): bool
    {
        if ($this->stream instanceof BufferedReader) {
            return $this->stream->isReadable();
        }

        return $this->stream !== '';
    }

    /**
     * A boolean value is stored as a single byte. The value 0 represents FALSE, and the value 1 represents TRUE.  All non-zero values MUST be interpreted as TRUE; however, applications MUST NOT store values other than 0 and 1.
     */
    public function readBoolean(): bool
    {
        return (bool) ($this->readByte());
    }

    /**
     * A byte represents an arbitrary 8-bit value (octet). Fixed length data is sometimes represented as an array of bytes, written byte[n], where n is the number of bytes in the array.
     */
    public function readByte(): int
    {
        return \unpack("C", $this->readLength(1))[1];
    }

    /**
     * Represents a 32-bit unsigned integer. Stored as four bytes in the order of decreasing significance (network byte order).  For example: the value 699921578 (0x29b7f4aa) is stored as 29 b7 f4 aa.
     */
    public function readInt(): int
    {
        return \unpack("N", $this->readLength(4))[1];
    }

    /**
     * Represents a 64-bit unsigned integer. Stored as eight bytes in the order of decreasing significance (network byte order).
     */
    public function readInt64(): int
    {
        return \unpack("J", $this->readLength(8))[1];
    }

    /**
     * Arbitrary length binary string. Strings are allowed to contain arbitrary binary data, including null characters and 8-bit characters. They are stored as an uint32 containing its length (number of bytes that follow) and zero (= empty string) or more bytes that are the value of the string. Terminating null characters are not used. Strings are also used to store text. In that case, US-ASCII is used for internal names, and ISO-10646 UTF-8 for text that might be displayed to the user. The terminating null character SHOULD NOT normally be stored in the string.  For example: the US-ASCII string "testing" is represented as 00 00 00 07 t e s t i n g. The UTF-8 mapping does not alter the encoding of US-ASCII characters.
     *
     */
    public function readString(): string
    {
        $length = $this->readInt();
        return $this->readLength($length);
    }

    /**
     * A string containing a comma-separated list of names.  A name-list is represented as an uint32 containing its length (number of bytes that follow) followed by a comma-separated list of zero or more names. A name MUST have a non-zero length, and it MUST NOT contain a comma (","). As this is a list of names, all the elements contained are names and MUST be in US-ASCII. Context may impose additional restrictions on the names. For example, the names in a name-list may have to be a list of valid algorithm identifiers (see Section 6 below), or a list of [RFC3066] language tags. The order of the names in a name-list may or may not be significant. Again, this depends on the context in which the list is used. Terminating null characters MUST NOT be used, neither for the individual names, nor for the list as a whole.
     *
     * @return string[]
     */
    public function readNamelist(): array
    {
        $data = $this->readString();

        if (\empty($data)) {
            return [];
        }

        return \explode(",", $data);
    }

    public function readLength(int $length): string
    {
        if ($this->stream instanceof BufferedReader) {
            return $this->stream->readLength($length);
        }

        /** @psalm-suppress TypeDoesNotContainType */
        if ($length <= 0) {
            throw new \ValueError('The number of bytes to read must be a positive integer');
        }

        return $this->guard(function () use ($length): string {
            $buffer = \substr($this->stream, 0, $length);
            $this->stream = \substr($this->stream, $length);
            return $buffer;
        });
    }

    /**
     * @template TString as string|null
     *
     * @param \Closure():TString $read
     */
    private function guard(\Closure $read): mixed
    {
        if ($this->pending) {
            throw new PendingReadError();
        }

        $this->pending = true;

        try {
            return $read();
        } finally {
            $this->pending = false;
        }
    }
}
