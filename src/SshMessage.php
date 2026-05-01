<?php declare(strict_types=1);

namespace Amp\Ssh;

abstract class SshMessage implements \Stringable
{
    abstract public static function getNumber(): int;

    final static public function toNameList(array $value): string
    {
        return \implode(',', $value);
    }

    final public function __toString(): string
    {
        return $this->encode();
    }

    public static function decode(): \Generator
    {
        $number = yield 2; // todo
        return new static();
    }

    public function encode(): string
    {
        return \pack('C', self::getNumber());
    }

    /**
     * Transport Layer (RFC 4253 & 8308)
     */
    const int SSH_MSG_DISCONNECT = 1;
    const int SSH_MSG_IGNORE = 2;
    const int SSH_MSG_UNIMPLEMENTED = 3;
    const int SSH_MSG_DEBUG = 4;
    const int SSH_MSG_SERVICE_REQUEST = 5;
    const int SSH_MSG_SERVICE_ACCEPT = 6;
    const int SSH_MSG_EXT_INFO = 7;
    const int SSH_MSG_NEWCOMPRESS = 8;

    /**
     * Algorithm Negotiation (RFC 4253)
     */
    const int SSH_MSG_KEXINIT = 20;
    const int SSH_MSG_NEWKEYS = 21;

    /**
     * Key Exchange (RFC 4253, RFC 5656, RFC 4419)
     */
    const int SSH_MSG_KEXDH_INIT = 30;
    const int SSH_MSG_KEXDH_REPLY = 31;
    
    const int SSH_MSG_KEX_DH_GEX_INIT = 32;          // @TODO RFC 4419
    const int SSH_MSG_KEX_DH_GEX_REPLY = 33;         // @TODO RFC 4419
    const int SSH_MSG_KEX_DH_GEX_REQUEST = 34;       // @TODO RFC 4419
    
    // ==================== User Authentication (RFC 4252 & 4256) ====================
    // Client
    const int SSH_MSG_USERAUTH_REQUEST = 50;
    // Server
    const int SSH_MSG_USERAUTH_FAILURE = 51;
    // Server
    const int SSH_MSG_USERAUTH_SUCCESS = 52;
    // Server
    const int SSH_MSG_USERAUTH_BANNER = 53;
    // Server
    const int SSH_MSG_USERAUTH_INFO_REQUEST = 60;    // @TODO RFC 4256
    // Client
    const int SSH_MSG_USERAUTH_INFO_RESPONSE = 61;   // @TODO RFC 4256
    // Server
    const int SSH_MSG_USERAUTH_PK_OK = 60;           // @TODO same as INFO_REQUEST (RFC 4252)
    
    // ==================== Connection Protocol (RFC 4254) ====================
    
    const int SSH_MSG_GLOBAL_REQUEST = 80;           // @TODO
    const int SSH_MSG_REQUEST_SUCCESS = 81;          // @TODO
    const int SSH_MSG_REQUEST_FAILURE = 82;          // @TODO
    
    // ==================== Channel Messages (RFC 4254) ====================
    
    const int SSH_MSG_CHANNEL_OPEN = 90;
    const int SSH_MSG_CHANNEL_OPEN_CONFIRMATION = 91;
    const int SSH_MSG_CHANNEL_OPEN_FAILURE = 92;
    const int SSH_MSG_CHANNEL_WINDOW_ADJUST = 93;
    const int SSH_MSG_CHANNEL_DATA = 94;
    const int SSH_MSG_CHANNEL_EXTENDED_DATA = 95;
    const int SSH_MSG_CHANNEL_EOF = 96;
    const int SSH_MSG_CHANNEL_CLOSE = 97;
    const int SSH_MSG_CHANNEL_REQUEST = 98;
    const int SSH_MSG_CHANNEL_SUCCESS = 99;
    const int SSH_MSG_CHANNEL_FAILURE = 100;
    
    // ==================== Reserved / Private Use ====================
    
    // 101-127: Unassigned (Channel related messages)
    // 128-191: Reserved for client protocols
    // 192-255: Reserved for Private Use
}
