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

use TechDivision\StompProtocol\Exception\StompProtocolException;
use TechDivision\StompProtocol\Protocol\CommonValues;
use TechDivision\StompProtocol\Protocol\Headers;
use TechDivision\StompProtocol\Utils\ErrorMessages;

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
     * List with keys to validate the values
     *
     * @var array
     */
    protected $keyValidationList;

    /**
     * Init the stomp parser class.
     */
    public function __construct()
    {
        $this->setKeyValidationList(array(Headers::CONTENT_LENGTH => "int"));
    }

    /**
     * Parse the stomp frame headers.
     *
     * @param string                                 $header     The header to parse
     * @param \TechDivision\StompProtocol\StompFrame $stompFrame The stomp frame to set the header values
     *
     * @return array
     *
     * @throws \TechDivision\StompProtocol\Exception\StompProtocolException
     */
    public function parseStompHeaders($header)
    {
        // the parsed headers
        $headers = array();

        // get lines by explode header stomp newline
        $lines = explode(StompFrame::NEWLINE, $header);

        // iterate over all header lines
        foreach ($lines as $line) {
            $headers = $this->parseStompHeaderLine($line, $headers);
        }

        // is accept-version not set than set to stomp version 1.0
        if (!array_key_exists(Headers::ACCEPT_VERSION, $headers)) {
            $headers[Headers::ACCEPT_VERSION] = CommonValues::V1_0;
        }

        // returns the header array
        return $headers;
    }

    /**
     * Parse a single line from a stomp header
     *
     * @param string $headerLine The line to parse
     * @param array  $headers    The header to set the key value pair
     *
     * @return array
     * @throws \TechDivision\StompProtocol\Exception\StompProtocolException
     */
    public function parseStompHeaderLine($headerLine , array $headers)
    {
        // checks contains the line a separator
        if (strpos($headerLine, StompFrame::COLON) === false) {
            throw new StompProtocolException(ErrorMessages::UNABLE_PARSE_HEADER_LINE);
        }

        // explode the line by frame colon
        list($key, $value) = explode(StompFrame::COLON, $headerLine, 2);

        // checks is the header key set
        if (strlen($key) === 0) {
            throw new StompProtocolException(ErrorMessages::UNABLE_PARSE_HEADER_LINE);
        }

        // decode the key value pair
        $key = $this->decodeHeaderString($key);
        $value = $this->decodeHeaderString($value);

        // ignore existing keys
        if (array_key_exists($key, $headers) == true) {
          return $headers;
        }

        // validate the value by given key
        if ($this->validateHeaderValue($key, $value) === false) {
            $type = $this->keyValidationList[$key];
            throw new StompProtocolException(sprintf(ErrorMessages::HEADER_VALIDATION_ERROR, $key, $value));
        }

        // set the key value pair
        $headers[$key] = $value;

        // return the headers
        return $headers;
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
            '\\n'  => StompFrame::NEWLINE,
            '\\c'  => StompFrame::COLON,
            '\\\\' => StompFrame::ESCAPE,
        ));
    }

    /**
     * Validates the given header value by given key.
     *
     * @param string     $key   The key to find teh validation type.
     * @param string|int $value The value to validated by type.
     *
     * @return bool
     */
    protected function validateHeaderValue($key, $value)
    {
        $keyValidationList = $this->getKeyValidationList();

        // checks exist validation for the given key
        if (!array_key_exists($key, $keyValidationList)) {
            return true;
        }

        // validate the value by key type and returns the result
        $type = $keyValidationList[$key];
        switch ($type) {
            case "int":
                return ctype_digit($value);

            default:
                return false;
        }
    }

    /**
     * Get the validation list.
     *
     * @return array
     */
    public function getKeyValidationList()
    {
        return $this->keyValidationList;
    }

    /**
     * Set the validation list.
     *
     * @param array $keyValidationList The list to set for validation
     *
     * @return void
     */
    public function setKeyValidationList($keyValidationList)
    {
        $this->keyValidationList = $keyValidationList;
    }
}
