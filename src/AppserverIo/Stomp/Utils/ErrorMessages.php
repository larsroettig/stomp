<?php

/**
 * AppserverIo\Stomp\Utils\ErrorMessages
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Lars Roettig <l.roettig@techdivision.com>
 * @copyright 2016 TechDivision GmbH - <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io/
 */


namespace AppserverIo\Stomp\Utils;

/**
 * Holds the error messages.
 *
 * @author     Lars Roettig <l.roettig@techdivision.com>
 * @copyright  2016 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0;
 * @link       https://github.com/appserver-io/appserver
 */
class ErrorMessages
{
    /**
     * Error message for failed login.
     *
     * @var string
     */
    const FAILED_AUTH = "Failed login while attempting to authenticate user %s";

    /**
     * Error message for validation error.
     *
     * @var string
     */
    const HEADER_VALIDATION_ERROR = "Validation error %s is not valid to type: %s";

    /**
     * Error message for supported protocol versions.
     *
     * @var string
     */
    const SUPPORTED_PROTOCOL_VERSIONS = "Supported protocol versions are %s";

    /**
     * Error message for unable to parse header line.
     *
     * @var string
     */
    const UNABLE_PARSE_HEADER_LINE = "Unable to parse header line.";

    /**
     * Error message for command length was exceeded
     *
     * @var string
     */
    const HEADER_COMMAND_LENGTH  = "The maximum command length was exceeded";

    /**
     * Error message for maximum number of headers was exceeded
     *
     * @var string
     */
    const HEADER_LENGTH = "The maximum header length was exceeded";

    /**
     * Error message for maximum number of headers was exceeded
     *
     * @var string
     */
    const HEADERS_WAS_EXCEEDED = "The maximum number of headers was exceeded.";

    /**
     * Error message for maximum data length was exceeded
     *
     * @var string
     */
    const MAX_DATA_LENGTH = "The maximum data length was exceeded";
}
