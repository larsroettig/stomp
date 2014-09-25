<?php
/**
 * \TechDivision\StompProtocol\StompRequest
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
 * Implementation for a Stomp Request.
 *
 * @category  Library
 * @package   TechDivision_StompProtocol
 * @author    Lars Roettig <l.roettig@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/TechDivision_StompProtocol
 * @link      https://github.com/stomp/stomp-spec/blob/master/src/stomp-specification-1.1.md
 */
class StompRequest
{

    /**
     * Holds the stomp line buffer
     *
     * @var string
     */
    protected $buffer;

    /**
     * Keeps response text that will sent to client after finish processing request.
     *
     * @var string
     */
    protected $response = '';

    /**
     * Holds completion state of the Request.
     *
     * @var boolean
     */
    protected $complete = false;

    /**
     * Central method for pushing data into VO object.
     *
     * @param string $line The actual request instance
     *
     * @return void
     */
    public function push($line)
    {
        $this->buffer .= $line;
        if (strpos($this->buffer, StompFrame::NULL) !== false) {
            $this->setComplete(true);
        }
    }

    /**
     *  Parse the stomp frame.
     *
     * @return \TechDivision\StompProtocol\StompFrame
     */
    public function getStompParsedFrame()
    {
        // init new stomp frame
        $stompFrame = new StompFrame();

        // extract the body and the header
        list($headers, $body) = explode("\n\n", $this->buffer, 2);

        // parse the stomp frame headers
        $stompFrame = $this->parseStompHeaders($headers, $stompFrame);

        // add the stomp body
        $stompFrame->setBody($body);

        // returns the stomp frame
        return $stompFrame;
    }

    /**
     * Parse the stomp frame headers and set in the given stomp frame.
     *
     * @param string                                 $header     The header to parse
     * @param \TechDivision\StompProtocol\StompFrame $stompFrame The stomp frame to set the header values
     *
     * @return \TechDivision\StompProtocol\StompFrame
     */
    protected function parseStompHeaders($header, StompFrame $stompFrame)
    {
        // the parsed headers
        $headers = array();

        // remove one new character
        $header = ltrim($header, StompFrame::NEWLINE);

        // get lines by explode header stomp newline
        $lines = explode(StompFrame::NEWLINE, $header);

        // the first line is the stomp command
        $stompFrame->setCommand(array_shift($lines));

        // iterate over all header lines
        foreach ($lines as $line) {
            // explode the line by frame colon
            list($key, $value) = explode(StompFrame::COLON, $line, 2);

            // decode the key value pair
            $key = $this->decodeHeaderString($key);
            $value = $this->decodeHeaderString($value);

            // ignore existing keys
            if (array_key_exists($key, $headers) == true) {
                continue;
            }

            // set the key value pair
            $headers[$key] = $value;
        }

        // add the parsed headers
        $stompFrame->setHeaders($headers);

        // returns the stomp frame
        return $stompFrame;
    }

    /**
     * Decode the header string.
     *
     * @param string $string The string to stomp decode.
     *
     * @return string
     */
    protected function decodeHeaderString($string)
    {
        return strtr($string, array(
            '\\n' => StompFrame::NEWLINE,
            '\\c' => StompFrame::COLON,
            '\\\\' => StompFrame::ESCAPE,
        ));
    }

    /**
     * Return's the current request state, TRUE for completed, else FALSE.
     *
     * @return boolean The current request state
     */
    protected function getComplete()
    {
        return $this->complete;
    }

    /**
     * Return's TRUE if the request is complete, ELSE false
     *
     * @return boolean TRUE if the request is complete, ELSE false
     * @see \TechDivision\MemcacheProtocol\CacheRequest::getComplete()
     */
    public function isComplete()
    {
        return $this->getComplete();
    }

    /**
     * Set's current request state, TRUE for completed, else FALSE.
     *
     * @param boolean $value The request state
     *
     * @return void
     */
    protected function setComplete($value)
    {
        $this->complete = $value;
    }
}
