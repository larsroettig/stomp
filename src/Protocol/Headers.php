<?php
/**
 * AppserverIo\Stomp\Utils\Headers
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0;
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   AppserverIo
 * @package    Appserver
 * @subpackage Stomp
 * @author     Lars Roettig <l.roettig@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0;
 * @link       https://github.com/appserver-io/appserver
 */

namespace AppserverIo\Stomp\Protocol;

/**
 * Holds the advisable stomp frame headers.
 *
 * @category   AppserverIo
 * @package    Appserver
 * @subpackage Stomp
 * @author     Lars Roettig <l.roettig@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0;
 * @link       https://github.com/appserver-io/appserver
 *
 * @todo       add const documentation
 */
class Headers
{

    /**
     *
     *
     * @var string
     */
    const ACCEPT_VERSION = "accept-version";

    /**
     *
     *
     * @var string
     */
    const ACK_HEADER = "ack";
    /**
     *
     *
     * @var string
     */
    const BROWSER = "browser";

    /**
     *
     *
     * @var string
     */
    const BROWSER_END = "browser-end";

    /**
     *
     *
     * @var string
     */
    const CLIENT_ID = "client-id";

    /**
     *
     *
     * @var string
     */
    const CONTENT_LENGTH = "content-length";

    /**
     *
     *
     * @var string
     */
    const CONTENT_TYPE = "content-type";

    /**
     *
     *
     * @var string
     */
    const CORRELATION_ID = "correlation-id";

    /**
     *
     *
     * @var string
     */
    const CREDIT = "credit";

    /**
     *
     *
     * @var string
     */
    const DESTINATION = "destination";

    /**
     *
     *
     * @var string
     */
    const EXCLUSIVE = "exclusive";

    /**
     *
     *
     * @var string
     */
    const EXPIRES = "expires";

    /**
     *
     *
     * @var string
     */
    const FROM_SEQ = "from-seq";

    /**
     *
     *
     * @var string
     */
    const HEART_BEAT = "heart-beat";

    /**
     *
     *
     * @var string
     */
    const HOST = "host";

    /**
     *
     *
     * @var string
     */
    const HOST_ID = "host-id";

    /**
     *
     *
     * @var string
     */
    const ID = "id";

    /**
     *
     *
     * @var string
     */
    const INCLUDE_SEQ = "include-seq";

    /**
     *
     *
     * @var string
     */
    const LOGIN = "login";

    /**
     *
     *
     * @var string
     */
    const MESSAGE_GROUP = "message_group";

    /**
     *
     *
     * @var string
     */
    const MESSAGE_HEADER = "message";

    /**
     *
     *
     * @var string
     */
    const MESSAGE_ID = "message-id";

    /**
     *
     *
     * @var string
     */
    const PASSCODE = "passcode";

    /**
     *
     *
     * @var string
     */
    const PERSISTENT = "persistent";

    /**
     *
     *
     * @var string
     */
    const PRIORITY = "priority";

    /**
     *
     *
     * @var string
     */
    const PRORITY = "priority";

    /**
     *
     *
     * @var string
     */
    const RECEIPT_ID = "receipt-id";

    /**
     *
     *
     * @var string
     */
    const RECEIPT_REQUESTED = "receipt";

    /**
     *
     *
     * @var string
     */
    const REDELIVERED = "redelivered";

    /**
     *
     *
     * @var string
     */
    const REDIRECT_HEADER = "redirect";

    /**
     *
     *
     * @var string
     */
    const REMOVE = "remove";

    /**
     *
     *
     * @var string
     */
    const REPLY_TO = "reply-to";

    /**
     *
     *
     * @var string
     */
    const REQUEST_ID = "request-id";

    /**
     *
     *
     * @var string
     */
    const RESPONSE_ID = "response-id";

    /**
     *
     *
     * @var string
     */
    const RETAIN = "retain";

    /**
     *
     *
     * @var string
     */
    const SELECTOR = "selector";

    /**
     *
     *
     * @var string
     */
    const SERVER = "server";

    /**
     *
     *
     * @var string
     */
    const SESSION = "session";

    /**
     *
     *
     * @var string
     */
    const SET = "set";

    /**
     *
     *
     * @var string
     */
    const SUBSCRIPTION = "subscription";

    /**
     *
     *
     * @var string
     */
    const TEMP = "temp";

    /**
     *
     *
     * @var string
     */
    const TIMESTAMP = "timestamp";

    /**
     *
     *
     * @var string
     */
    const TRANSACTION = "transaction";

    /**
     *
     *
     * @var string
     */
    const TRANSFORMATION = "transformation";

    /**
     *
     *
     * @var string
     */
    const TRANSFORMATION_ERROR = "transformation-error";

    /**
     *
     *
     * @var string
     */
    const TYPE = "type";

    /**
     *
     *
     * @var string
     */
    const USER_ID = "user-id";

    /**
     *
     *
     * @var string
     */
    const VERSION = "version";
}
