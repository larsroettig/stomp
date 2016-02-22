<?php

/**
 * \AppserverIo\Stomp\FrameInterface
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author     Lars Roettig <l.roettig@techdivision.com>
 * @copyright  2016 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 */

namespace AppserverIo\Stomp\Interfaces;

/**
 * Stomp protocol authenticator interface
 *
 * @author     Lars Roettig <l.roettig@techdivision.com>
 * @copyright  2016 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       https://github.com/stomp/stomp-spec/blob/master/src/stomp-specification-1.1.md
 */

interface FrameInterface
{
    /**
     * Set the headers.
     *
     * @param array $headers The headers to set.
     *
     * @return void
     *
     * @link http://stomp.github.io/stomp-specification-1.1.html#Repeated_Header_Entries
     */
    public function setHeaders(array $headers);

    /**
     * Set the body for the frame.
     *
     * @param string $body The body to set.
     *
     * @return void
     */
    public function setBody($body);

    /**
     * Returns the message command.
     *
     * @return string
     */
    public function getCommand();

    /**
     * Set the command for the frame.
     *
     * @param string $command The Command to set.
     *
     * @return void
     */
    public function setCommand($command);

    /**
     * Returns the frame object as string.
     *
     * @return string
     */
    public function __toString();
}
