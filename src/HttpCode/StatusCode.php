<?php


namespace Zjwansui\EasyLaravel\HttpCode;

/**
 *
 * Class StatusCode
 * @package Zjwansui\EasyLaravel\HttpCode
 */
class StatusCode
{
    /**
     * Message
     */
    public const MESSAGE_CONTINUE = 100;
    public const MESSAGE_SWITCH_PROTOCOL = 101;
    public const MESSAGE_PROCESSING = 102;

    /**
     * Success
     */
    public const SUCCESS_OK = 200;
    public const SUCCESS_CREATED = 201;
    public const SUCCESS_ACCEPTED = 202;
    public const SUCCESS_NO_CONTENT = 204;
    public const SUCCESS_RESET_CONTENT = 205;
    /* Request range header */
    public const SUCCESS_PARTIAL_CONTENT = 206;
    public const SUCCESS_MULTI_STATUS = 207;

    /*
    |----------------------
    |      Redirection
    |----------------------
    */
    public const REDIRECT_MULTIPLE_CHOICE = 300;
    public const REDIRECT_PERMANENTLY = 301;
    public const REDIRECT_FOUND = 302;
    public const REDIRECT_SEE_OTHER = 303;
    public const REDIRECT_NOT_MODIFIED = 304;
    public const REDIRECT_USER_PROXY = 305;
    public const REDIRECT_REMAINED = 306; // Not used now
    public const REDIRECT_TEMPORARY = 307;

    /*
    |----------------------
    |     Client Error
    |----------------------
    */
    public const CLIENT_BAD_REQUEST = 400;
    public const CLIENT_UNAUTHORIZED = 401;
    public const CLIENT_PAYMENT_REQUIRED = 402;
    public const CLIENT_FORBIDDEN = 403;
    public const CLIENT_NOT_FOUND = 404;
    public const CLIENT_METHOD_NOT_ALLOWED = 405;
    public const CLIENT_NOT_ACCEPTABLE = 406;
    public const CLIENT_PROXY_AUTHENTICATE_REQUIRED = 407;
    public const CLIENT_REQUEST_TIMEOUT = 408;
    public const CLIENT_CONFLICT = 409;
    public const CLIENT_GONE = 410;
    public const CLIENT_LENGTH_REQUIRED = 411;
    public const CLIENT_PRECONDITION_FAILED = 412;
    public const CLIENT_REQUEST_ENTITY_TOO_LARGE = 413;
    public const CLIENT_REQUEST_URI_TOO_LONG = 414;
    public const CLIENT_UNSUPPORTED_MEDIA_TYPE = 415;
    public const CLIENT_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    public const CLIENT_EXPECTATION_FAILED = 417;

    public const CLIENT_UNPROCESSABLE_ENTITY = 422;
    public const CLIENT_LOCKED = 423;
    public const CLIENT_FAILED_DEPENDENCY = 424;
    public const CLIENT_UNORDERED_COLLECTION = 425;
    public const CLIENT_UPGRADE_REQUIRED = 426;
    public const CLIENT_PRECONDITION_REQUIRED = 428;
    public const CLIENT_TOO_MANY_REQUESTS = 429;
    public const CLIENT_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
    public const CLIENT_RETRY_WITH = 449;
    public const CLIENT_UNAVAILABLE_FOR_LEGAL_REASONS = 451;

    /*
    |----------------------
    |     Server Error
    |----------------------
    */
    public const SERVER_INTERNAL_ERROR = 500;
    public const SERVER_NOT_IMPLEMENTED = 501;
    public const SERVER_BAD_GATEWAY = 502;
    public const SERVER_SERVICE_UNAVAILABLE = 503;
    public const SERVER_GATEWAY_TIMEOUT = 504;
    public const SERVER_HTTP_VERSION_NOT_SUPPORTED = 505;
    public const SERVER_VARIANT_ALSO_NEGOTIATES = 506;
    public const SERVER_INSUFFICIENT_STORAGE = 507;
    public const SERVER_BANDWIDTH_LIMIT_EXCEEDED = 509;
    public const SERVER_NOT_EXTENDED = 510;
    public const SERVER_NETWORK_AUTHENTICATION_REQUIRED = 511;

    public const ALWAYS_EXPECTS_OK = false;


    public static function getStatusCode(int $code): int
    {
        return static::ALWAYS_EXPECTS_OK ? static::SUCCESS_OK : $code;
    }
}