<?php

/**
 * \AppserverIo\Stomp\Utils\ServerCommands
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
 * Holds the available stomp frame server commands.
 *
 * @author     Lars Roettig <l.roettig@techdivision.com>
 * @copyright  2016 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0;
 * @link       https://github.com/appserver-io/appserver
 * @link       https://stomp.github.io/stomp-specification-1.2.html#Frames_and_Headers
 */
class ServerCommands
{
    // Documentation for the Commands
    // https://stomp.github.io/stomp-specification-1.2.html#Frames_and_Headers

    /**
     * @var string
     */
    const CONNECTED = "CONNECTED";

    /**
     * @var string
     */
    const ERROR = "ERROR";

    /**
     * @var string
     */
    const MESSAGE = "MESSAGE";

    /**
     * @var string
     */
    const RECEIPT = "RECEIPT";
}
