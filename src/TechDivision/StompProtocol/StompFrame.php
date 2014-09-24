<?php
/**
 * \TechDivision\StompProtocol\StompFrame
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
 * @link      https://github.com/stomp/stomp-spec/blob/master/src/stomp-specification-1.1.md
 */

namespace TechDivision\StompProtocol;

/**
 * Implementation for a Stomp frame.
 *
 * @category  Library
 * @package   TechDivision_StompProtocol
 * @author    Lars Roettig <l.roettig@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/TechDivision_StompProtocol
 * @link      https://github.com/stomp/stomp-spec/blob/master/src/stomp-specification-1.1.md
 */
class StompFrame implements StompFrameInterface
{
    /**
     * Holds the message command.
     *
     * @var \TechDivision\StompProtocol\Command
     */
    protected $command;

    /**
     * Holds the message headers.
     *
     * @var
     */
    protected $headers;

    /**
     * Holds the message body.
     *
     * @var
     */
    protected $body;

    /**
     * Init new StompProtocol message.
     *
     * @param \TechDivision\StompProtocol\Command $command The message command.
     * @param array                               $headers The message headers.
     * @param string                              $body    The message body.
     *
     * @return void
     */
    public function __construct(Command $command, array $headers, $body)
    {

        $this->command = $command;
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * Returns the message headers.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->getHeaders();
    }

    /**
     * Returns the message body.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->getBody();
    }

    /**
     * Returns the message command.
     *
     * @return \TechDivision\StompProtocol\Command
     */
    public function getCommand()
    {
        return $this->getCommand();
    }
}
