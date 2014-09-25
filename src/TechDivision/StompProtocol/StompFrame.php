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
class StompFrame
{
    /**
     *
     * @var string
     */
    const COLON = ':';

    /**
     *
     */
    const ESCAPE = '\\';

    /**
     *
     */
    const NEWLINE = "\n";

    /**
     *
     */
    const NULL = "\x00";

    /**
     * Holds the message command.
     *
     * @var string
     */
    protected $command;

    /**
     * Holds the message headers.
     *
     * @var array
     */
    protected $headers;

    /**
     * Holds the message body.
     *
     * @var string
     */
    protected $body;

    /**
     * Create new stomp protocol frame.
     *
     * @param string $command The message command.
     * @param array  $headers The message headers.
     * @param string $body    The message body.
     *
     * @return void
     */
    public function __construct($command = null, array $headers = array(), $body = "")
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
     * Set the headers.
     *
     * @param array $headers The headers to set.
     *
     * @return void
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * Returns the value for the given header key.
     *
     * @param string $key The header to find the value
     *
     * @return string|null
     */
    public function getHeaderByKey($key)
    {
        return isset($this->headers[$key]) ? $this->headers[$key] : null;
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
     * Set the body for the frame.
     *
     * @param string $body The body to set.
     *
     * @return void
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Returns the message command.
     *
     * @return string
     */
    public function getCommand()
    {
        return $this->getCommand();
    }

    /**
     * Set the command for the frame.
     *
     * @param string $command The Command to set.
     *
     * @return void
     */
    public function setCommand($command)
    {
        $this->command = $command;
    }

    /**
     * Returns the frame object as string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->command .
        StompFrame::NEWLINE .
        $this->headersToString() .
        StompFrame::NEWLINE .
        $this->body .
        StompFrame::NULL;
    }

    /**
     * Convert teh frame headers to string.
     *
     * @return string
     */
    protected function headersToString()
    {
        $headerString = "";

        foreach ($this->headers as $key => $value) {
            $name = $this->encodeHeaderString($key);
            $value = $this->encodeHeaderString($value);

            $headerString .= $name . StompFrame::COLON . $value . StompFrame::NEWLINE;
        }

        return $headerString;
    }

    /**
     * Endcode the header string as stomp header string.
     *
     * @param string $value The value to convert
     *
     * @return string
     */
    protected function encodeHeaderString($value)
    {
        return strtr($value, array(
            StompFrame::NEWLINE => '\n',
            StompFrame::COLON => '\c',
            StompFrame::ESCAPE => '\\\\',
        ));
    }
}
