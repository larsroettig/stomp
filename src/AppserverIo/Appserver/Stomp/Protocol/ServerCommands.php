<?php
/**
 * \AppserverIo\Appserver\Stomp\Utils\ServerCommands
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

namespace AppserverIo\Appserver\Stomp\Protocol;

/**
 * Holds the available stomp frame server commands.
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
class ServerCommands
{
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
