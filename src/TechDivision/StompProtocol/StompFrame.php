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

use TechDivision\StompProtocol\Protocol\CommonValues;
use TechDivision\StompProtocol\Protocol\Headers;
use TechDivision\StompProtocol\Protocol\ServerCommands;

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
        $this->setCommand($command);
        $this->setHeaders($headers);
        $this->setBody($body);
    }

    /**
     * Returns the message headers.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
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
        if (is_array($this->headers)) {
            foreach ($headers as $key => $value) {
                $this->setHeaderValueByKey($key, $value);
            }
        } else {
            $this->headers = $headers;
        }
    }

    /**
     * Returns the value for the given header key.
     *
     * @param string $key The header to find the value
     *
     * @return string|null
     */
    public function getHeaderValueByKey($key)
    {
        return isset($this->headers[$key]) ? $this->headers[$key] : null;
    }

    /**
     * Set the value for the given header key.
     *
     * @param string $key   The header to find the value
     * @param string $value The value to set
     *
     * @return void
     */
    public function setHeaderValueByKey($key, $value)
    {
        // ignore already set header element
        if (isset($this->headers[$key])) {
            return;
        }

        $this->headers[$key] = $value;
    }

    /**
     * Returns the message body.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
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

        if (strlen($body) > 0) {
            $this->setHeaderValueByKey(Headers::CONTENT_TYPE, CommonValues::TEXT_PLAIN);
            $this->setHeaderValueByKey(Headers::CONTENT_LENGTH, strlen($body));
        }
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
        StompFrame::NULL .
        StompFrame::NEWLINE;
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
     * Encode the header string as stomp header string.
     *
     * @param string $value The value to convert
     *
     * @return string
     */
    protected function encodeHeaderString($value)
    {
        /*
         * CONNECTED frames do not escape the colon or newline octets
         * in order to remain backward compatible with STOMP 1.0.
         */
        if ($this->getCommand() === ServerCommands::CONNECTED) {
            return $value;
        }

        // escape "\n , : , \\" in value
        return strtr($value, array(
            StompFrame::NEWLINE => '\n',
            StompFrame::COLON   => '\c',
            StompFrame::ESCAPE  => '\\\\'
        ));
    }

    /**
     * Returns the message command.
     *
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
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
}
