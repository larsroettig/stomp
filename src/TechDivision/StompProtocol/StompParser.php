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

use TechDivision\StompProtocol\Protocol\CommonValues;
use TechDivision\StompProtocol\Protocol\Headers;

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
class StompParser
{
    /**
     *
     * @var \TechDivision\StompProtocol\StompFrame
     */
    protected $stompFrame;

    /**
     * @param \TechDivision\StompProtocol\StompFrame $stompFrame
     */
   public function __construct(StompFrame $stompFrame)
   {
       $this->stompFrame = $stompFrame;
   }

    /**
     * Parse the stomp frame headers and set in the given stomp frame.
     *
     * @param string                                 $header     The header to parse
     * @param \TechDivision\StompProtocol\StompFrame $stompFrame The stomp frame to set the header values
     *
     * @return void
     */
   public function parseStompHeaders($header)
    {
        // the parsed headers
        $headers = array();

        // get lines by explode header stomp newline
        $lines = explode(StompFrame::NEWLINE, $header);

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

        // set the standard frame protocol to 1.0
        if (!array_key_exists(Headers::ACCEPT_VERSION, $headers)) {
            $headers[Headers::ACCEPT_VERSION] = CommonValues::V1_0;
        }

        // add the parsed headers
        $this->stompFrame->setHeaders($headers);
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
     * @return \TechDivision\StompProtocol\StompFrame
     */
    public function getStompFrame()
    {
        return $this->stompFrame;
    }
}
