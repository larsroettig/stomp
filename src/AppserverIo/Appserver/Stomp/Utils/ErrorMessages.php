<?php
/**
 * AppserverIo\Appserver\Stomp\Utils\ErrorMessages
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0;
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Library
 * @package    TechDivision_StompProtocol
 * @author     Lars Roettig <l.roettig@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0;
 * @link       https://github.com/appserver-io/appserver
 */

namespace AppserverIo\Appserver\Stomp\Utils;

/**
 * Holds the error messages.
 *
 * @category   AppserverIo
 * @package    Appserver
 * @subpackage Stomp
 * @author     Lars Roettig <l.roettig@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
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
}
