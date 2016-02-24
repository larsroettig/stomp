<?php

/**
 * \AppserverIo\Stomp\Parser
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Lars Roettig <lr@appserver.io>
 * @copyright 2016 TechDivision GmbH - <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io/
 */

namespace AppserverIo\Stomp;

use AppserverIo\Stomp\Exception\ProtocolException;
use AppserverIo\Stomp\Interfaces\RequestParserInterface;
use AppserverIo\Stomp\Protocol\CommonValues;
use AppserverIo\Stomp\Protocol\Headers;
use AppserverIo\Stomp\Utils\ErrorMessages;

/**
 * Implementation for a StompParser.
 *
 * @author    Lars Roettig <lr@appserver.io>
 * @copyright 2016 TechDivision GmbH - <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io/
 * @link      https://github.com/stomp/stomp-spec/blob/master/src/stomp-specification-1.1.md
 */
class Parser implements RequestParserInterface
{

    /**
     * List with keys to validate the values
     *
     * @var array
     */
    protected $keyValidationList;

    /**
     * Holds the parsed headers as key => value array.
     *
     * @var array
     */
    protected $headers;

    /**
     * Init the stomp parser class.
     */
    public function __construct()
    {
        $this->setKeyValidationList(array(Headers::CONTENT_LENGTH => "int"));
        $this->clearHeaders();
    }

    /**
     * Clear the headers to parse new stomp request.
     *
     * @return void
     */
    public function clearHeaders()
    {
        $this->headers = array();
    }

    /**
     * Return the headers count.
     *
     * @return int
     */
    public function getHeaderSize()
    {
        if (!isset($this->headers)) {
            return 0;
        } else {
            return count($this->headers);
        }
    }

    /**
     * Returns the parsed headers.
     *
     * @return array
     */
    public function getParsedHeaders()
    {
        // is accept-version not set than set to stomp version 1.0
        if (!array_key_exists(Headers::ACCEPT_VERSION, $this->headers)) {
            $this->headers[Headers::ACCEPT_VERSION] = CommonValues::V1_0;
        }

        // returns the parsed stomp headers
        return $this->headers;
    }

    /**
     * Parse the stomp frame headers.
     *
     * @param string $frameHeaders The frame headers.
     *
     * @return void
     * @throws \AppserverIo\Stomp\Exception\ProtocolException
     */
    public function parseHeaders($frameHeaders)
    {
        // get lines by explode header stomp newline
        $lines = explode(Frame::NEWLINE, $frameHeaders);

        // iterate over all header lines
        foreach ($lines as $line) {
            $this->parseHeaderLine($line);
        }
    }

    /**
     * Parse's the given header line.
     *
     * @param string $line The line defining a stomp request header
     *
     * @return void
     *
     * @throws \AppserverIo\Stomp\Exception\ProtocolException
     */
    public function parseHeaderLine($line)
    {
        // checks contains the line a separator
        if (strpos($line, Frame::COLON) === false) {
            throw new ProtocolException(ErrorMessages::UNABLE_PARSE_HEADER_LINE);
        }

        // explode the line by frame colon
        list($key, $value) = explode(Frame::COLON, $line, 2);

        // checks is the header key set
        if (strlen($key) === 0) {
            throw new ProtocolException(ErrorMessages::UNABLE_PARSE_HEADER_LINE);
        }

        // decode the key value pair
        $key = $this->decodeHeaderString($key);
        $value = $this->decodeHeaderString($value);

        // ignore existing keys
        if (array_key_exists($key, $this->headers) === true) {
            return;
        }

        // validate the value by given key
        if ($this->validateHeaderValue($key, $value) === false) {
            $type = $this->keyValidationList[$key];
            throw new ProtocolException(sprintf(ErrorMessages::HEADER_VALIDATION_ERROR, $key, $type));
        }

        // set the key value pair
        $this->headers[$key] = $value;
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
            '\\n' => Frame::NEWLINE,
            '\\c' => Frame::COLON,
            '\\\\' => Frame::ESCAPE,
        ));
    }

    /**
     * Validates the given header value by given key.
     *
     * @param string $key   The key to find the validation type.
     * @param string $value The value to validated by type.
     *
     * @return bool
     */
    protected function validateHeaderValue($key, $value)
    {
        // loads the validation list
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
        }

        // no validation available for the type.
        return false;
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
