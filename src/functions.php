<?php declare(strict_types=1);

namespace Amp\Ssh;

use Revolt\EventLoop;
use Psr\Http\Message\UriInterface as PsrUri;
use Amp\Ssh\Channel\Rfc4253Connector;
use Amp\Http\Client\HttpException;
use Amp\Cancellation;

/**
 * Set or access the global websocket Connector instance.
 */
function sshConnector(?SshConnector $connector = null): SshConnector
{
    static $map;
    $map ??= new \WeakMap();
    $driver = EventLoop::getDriver();

    if ($connector) {
        return $map[$driver] = $connector;
    }

    return $map[$driver] ??= new Rfc4253Connector();
}

/**
 * @throws HttpException Thrown if the request fails.
 * @throws SshConnectException If the response received is invalid or is not a switching protocols (101) response.
 */
function connect(
    PsrUri|string $uri,
    SshAuthentication $authentication,
    ?Cancellation $cancellation = null,
    string $identification = "SSH-2.0-AmpSSH_0.1",
): SshResource {
    return sshConnector()->connect(
        $uri,
        $authentication,
        $cancellation,
        $identification,
    );
}

function twosComplement(string $data): string
{
    return \ord($data[0]) & 0x80 ? \chr(0) . $data : $data;
}

/**
 * A boolean value is stored as a single byte. The value 0 represents FALSE, and the value 1 represents TRUE.  All non-zero values MUST be interpreted as TRUE; however, applications MUST NOT store values other than 0 and 1.
 *
 * @return \Generator<bool>
 */
function boolean(): \Generator
{
    return (bool) (yield from byte());
}

/**
 * A byte represents an arbitrary 8-bit value (octet). Fixed length data is sometimes represented as an array of bytes, written byte[n], where n is the number of bytes in the array.
 *
 * @return \Generator<int>
 */
function byte(): \Generator
{
    $payload = (yield 1);
    return \unpack("N", $payload)[1];
}

/**
 * Represents a 32-bit unsigned integer. Stored as four bytes in the order of decreasing significance (network byte order).  For example: the value 699921578 (0x29b7f4aa) is stored as 29 b7 f4 aa.
 *
 * @return \Generator<int>
 */
function uint32(): \Generator
{
    $payload = (yield 4);
    return \unpack("N", $payload)[1];
}

/**
 * Represents a 64-bit unsigned integer. Stored as eight bytes in the order of decreasing significance (network byte order).
 *
 * @return \Generator<int>
 */
function uint64(): \Generator
{
    $payload = (yield 8);
    return \unpack("J", $payload)[1];
}

/**
 * Arbitrary length binary string. Strings are allowed to contain arbitrary binary data, including null characters and 8-bit characters. They are stored as an uint32 containing its length (number of bytes that follow) and zero (= empty string) or more bytes that are the value of the string. Terminating null characters are not used. Strings are also used to store text. In that case, US-ASCII is used for internal names, and ISO-10646 UTF-8 for text that might be displayed to the user. The terminating null character SHOULD NOT normally be stored in the string.  For example: the US-ASCII string "testing" is represented as 00 00 00 07 t e s t i n g. The UTF-8 mapping does not alter the encoding of US-ASCII characters.
 *
 * @return \Generator<string>
 */
function string(): \Generator
{
    $length = yield from uint32();
    $payload = (yield $length);
    return $payload;
}

/**
 * A string containing a comma-separated list of names.  A name-list is represented as an uint32 containing its length (number of bytes that follow) followed by a comma-separated list of zero or more names. A name MUST have a non-zero length, and it MUST NOT contain a comma (","). As this is a list of names, all the elements contained are names and MUST be in US-ASCII. Context may impose additional restrictions on the names. For example, the names in a name-list may have to be a list of valid algorithm identifiers (see Section 6 below), or a list of [RFC3066] language tags. The order of the names in a name-list may or may not be significant. Again, this depends on the context in which the list is used. Terminating null characters MUST NOT be used, neither for the individual names, nor for the list as a whole.
 *
 * @return \Generator<string[]>
 */
function namelist(): \Generator
{
    $payload = yield from string();

    if (\empty($payload)) {
        return [];
    }

    return \explode(",", $payload);
}

/**
 * Execute a generator factory a specified number of times and collect results
 *
 * @template T
 * @param positive-int $times Number of times to execute
 * @param \Closure():\Generator<T> $factory Factory that returns a Generator
 * @return \Generator<T[]>
 */
function times(int $times, \Closure $factory): \Generator
{
    /** @psalm-suppress TypeDoesNotContainType */
    if ($times <= 0) {
        throw new \ValueError(
            "The number of times to repeat must be a positive integer",
        );
    }

    $result = [];

    for ($i = 0; $i < $times; $i++) {
        $result[] = yield from $factory();
    }

    return $result;
}
