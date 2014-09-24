<?php
/**
 * \TechDivision\StompProtocol\Command
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category  Library
 * @package   TechDivision_StompProtocol
 * @author    Lars Roettig <l.roettig@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/TechDivision_StompProtocol
 */

namespace TechDivision\StompProtocol;

/**
 * Command implementations
 *
 * @category  Library
 * @package   TechDivision_StompProtocol
 * @author    Lars Roettig <l.roettig@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/TechDivision_StompProtocol
 */
class Command
{
    /**
     *
     *
     * @var string
     */
    const ABORT = "ABORT";

    /**
     *
     *
     * @var string
     */
    const BEGIN = "BEGIN";

    /**
     *
     *
     * @var string
     */
    const COMMIT = "COMMIT";

    /**
     *
     *
     * @var string
     */
    const CONNECT = "CONNECT";

    /**
     *
     *
     * @var string
     */
    const CONNECTED = "CONNECTED";

    /**
     *
     *
     * @var string
     */
    const DISCONNECT = "DISCONNECT";

    /**
     *
     *
     * @var string
     */
    const ERROR = "ERROR";

    /**
     *
     *
     * @var string
     */
    const MESSAGE = "MESSAGE";

    /**
     *
     *
     * @var string
     */
    const RECEIPT = "RECEIPT";

    /**
     *
     *
     * @var string
     */
    const SEND = "SEND";

    /**
     *
     *
     * @var string
     */
    const SUBSCRIBE = "SUBSCRIBE";

    /**
     *
     *
     * @var string
     */
    const UNSUBSCRIBE = "UNSUBSCRIBE";

    /**
     * Holds the command
     *
     * @var string
     */
    protected $command;

    /**
     * Init the comand with value
     *
     * @param string $command The StompProtocol command.
     *
     * @return void
     */
    public function __construct($command)
    {
        $this->command = $command;
    }

    /**
     * Returns the command string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->command;
    }
}
