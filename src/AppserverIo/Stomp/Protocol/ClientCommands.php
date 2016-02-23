<?php

/**
 * AppserverIo\Stomp\Utils\ClientCommands
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0;
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author     Lars Roettig <l.roettig@techdivision.com>
 * @copyright  2016 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0;
 * @link       https://github.com/appserver-io/appserver
 */

namespace AppserverIo\Stomp\Protocol;

/**
 * Holds the available stomp frame client commands.
 *
 * @author     Lars Roettig <l.roettig@techdivision.com>
 * @copyright  2016 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0;
 * @link       https://github.com/appserver-io/appserver
 *
 */
class ClientCommands
{
    // Documentation for the Commands
    // https://stomp.github.io/stomp-specification-1.2.html#Client_Frames

    /**
     * @var string
     */
    const ABORT = "ABORT";

    /**
     * @var string
     */
    const ACK = "ACK";

    /**
     * @var string
     */
    const BEGIN = "BEGIN";

    /**
     * @var string
     */
    const COMMIT = "COMMIT";

    /**
     * @var string
     */
    const CONNECT = "CONNECT";

    /**
     * @var string
     */
    const DISCONNECT = "DISCONNECT";

    /**
     * @var string
     */
    const NACK = "NACK";

    /**
     * @var string
     */
    const SEND = "SEND";

    /**
     * @var string
     */
    const STOMP = "STOMP";

    /**
     * @var string
     */
    const SUBSCRIBE = "SUBSCRIBE";

    /**
     * @var string
     */
    const UNSUBSCRIBE = "UNSUBSCRIBE";
}
