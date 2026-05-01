<?php declare(strict_types=1);

namespace Amp\Ssh;

enum SshMessageType: int
{
    /**
     * Transport Layer (RFC 4253 & 8308)
     */
    case DISCONNECT = 1;
    case IGNORE = 2;
    case UNIMPLEMENTED = 3;
    case DEBUG = 4;
    // Client Only
    case SERVICE_REQUEST = 5;
    // Server Only
    case SERVICE_ACCEPT = 6;
    case EXT_INFO = 7;              // @TODO RFC 8308
    case NEWCOMPRESS = 8;           // @TODO RFC 8308

    /**
     * Algorithm Negotiation (RFC 4253)
     */
    case KEXINIT = 20;
    case NEWKEYS = 21;

    /**
     * Key Exchange (RFC 4253, RFC 5656, RFC 4419)
     */
    // 30 -> client
    // 31 -> server
    // 
    // Diffie-Hellman Group1/14/16/18 and ECDH (RFC 4253, RFC 5656)
    case KEXDH_INIT = 30;
    case KEXDH_REPLY = 31;
    case KEX_ECDH_INIT = 30;        // @TODO same as KEXDH_INIT
    case KEX_ECDH_REPLY = 31;       // @TODO same as KEXDH_REPLY
    
    // Diffie-Hellman Group Exchange (RFC 4419)
    case KEX_DH_GEX_REQUEST_OLD = 30;   // @TODO same as KEXDH_INIT
    case KEX_DH_GEX_GROUP = 31;         // @TODO same as KEXDH_REPLY
    // Client Only
    case KEX_DH_GEX_INIT = 32;          // @TODO RFC 4419
    // Server
    case KEX_DH_GEX_REPLY = 33;         // @TODO RFC 4419
    // Client
    case KEX_DH_GEX_REQUEST = 34;       // @TODO RFC 4419
    
    // ==================== User Authentication (RFC 4252 & 4256) ====================
    // Client
    case USERAUTH_REQUEST = 50;
    // Server
    case USERAUTH_FAILURE = 51;
    // Server
    case USERAUTH_SUCCESS = 52;
    // Server
    case USERAUTH_BANNER = 53;
    // Server
    case USERAUTH_INFO_REQUEST = 60;    // @TODO RFC 4256
    // Client
    case USERAUTH_INFO_RESPONSE = 61;   // @TODO RFC 4256
    // Server
    case USERAUTH_PK_OK = 60;           // @TODO same as INFO_REQUEST (RFC 4252)
    
    // ==================== Connection Protocol (RFC 4254) ====================
    
    case GLOBAL_REQUEST = 80;           // @TODO
    case REQUEST_SUCCESS = 81;          // @TODO
    case REQUEST_FAILURE = 82;          // @TODO
    
    // ==================== Channel Messages (RFC 4254) ====================
    
    case CHANNEL_OPEN = 90;
    case CHANNEL_OPEN_CONFIRMATION = 91;
    case CHANNEL_OPEN_FAILURE = 92;
    case CHANNEL_WINDOW_ADJUST = 93;
    case CHANNEL_DATA = 94;
    case CHANNEL_EXTENDED_DATA = 95;
    case CHANNEL_EOF = 96;
    case CHANNEL_CLOSE = 97;
    case CHANNEL_REQUEST = 98;
    case CHANNEL_SUCCESS = 99;
    case CHANNEL_FAILURE = 100;
    
    // ==================== Reserved / Private Use ====================
    
    // 101-127: Unassigned (Channel related messages)
    // 128-191: Reserved for client protocols
    // 192-255: Reserved for Private Use
}
