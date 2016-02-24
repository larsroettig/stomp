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
 * @author     Lars Roettig <lr@appserver.io>
 * @copyright  2016 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 */

namespace AppserverIo\Stomp\Interfaces;

/**
 * Stomp protocol authenticator interface
 *
 * @author     Lars Roettig <lr@appserver.io>
 * @copyright  2016 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       https://github.com/stomp/stomp-spec/blob/master/src/stomp-specification-1.1.md
 */

interface FrameInterface
{

    /**
     * Create new stomp protocol frame.
     *
     * @param string $command The message command.
     * @param array  $headers The message headers.
     * @param string $body    The message body.
     */
    public function __construct($command = null, array $headers = array(), $body = "");

    /**
     * Returns the message body.
     *
     * @return string
     */
    public function getBody();

    /**
     * Set the value for the given header key.
     *
     * @param string $key   The header to find the value
     * @param string $value The value to set
     *
     * @return void
     */
    public function setHeaderValueByKey($key, $value);

    /**
     * Returns the value for the given header key.
     *
     * @param string $key The header to find the value
     *
     * @return string|null
     */
    public function getHeaderValueByKey($key);

    /**
     * Returns the message headers.
     *
     * @return array
     */
    public function getHeaders();

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
