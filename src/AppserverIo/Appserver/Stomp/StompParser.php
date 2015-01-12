<?php
/**
 * \AppserverIo\Appserver\Stomp
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   AppserverIo
 * @package    Appserver
 * @subpackage Stomp
 * @author     Lars Roettig <l.roettig@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 */

namespace AppserverIo\Appserver\Stomp;

use AppserverIo\Appserver\Stomp\Exception\StompProtocolException;
use AppserverIo\Appserver\Stomp\Interfaces\StompRequestParserInterface;
use AppserverIo\Appserver\Stomp\Protocol\CommonValues;
use AppserverIo\Appserver\Stomp\Protocol\Headers;
use AppserverIo\Appserver\Stomp\Utils\ErrorMessages;

/**
 * Implementation for a StompParser.
 *
 * @category   AppserverIo
 * @package    Appserver
 * @subpackage Stomp
 * @author     Lars Roettig <l.roettig@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       https://github.com/stomp/stomp-spec/blob/master/src/stomp-specification-1.1.md
 */
class StompParser implements StompRequestParserInterface
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
     * @param string $frameHeaders The frame headers
     *
     * @return void
     * @throws \AppserverIo\Appserver\Stomp\Exception\StompProtocolException
     */
    public function parseHeaders($frameHeaders)
    {
        // the parsed headers
        $headers = array();

        // get lines by explode header stomp newline
        $lines = explode(StompFrame::NEWLINE, $frameHeaders);

        // iterate over all header lines
        foreach ($lines as $line) {
            $this->parseHeaderLine($line, $headers);
        }
    }

    /**
     * Parse's the given header line
     *
     * @param string $line The line defining a stomp request header
     *
     * @return void
     *
     * @throws \AppserverIo\Appserver\Stomp\Exception\StompProtocolException
     */
    public function parseHeaderLine($line)
    {
        // checks contains the line a separator
        if (strpos($line, StompFrame::COLON) === false) {
            throw new StompProtocolException(ErrorMessages::UNABLE_PARSE_HEADER_LINE);
        }

        // explode the line by frame colon
        list($key, $value) = explode(StompFrame::COLON, $line, 2);

        // checks is the header key set
        if (strlen($key) === 0) {
            throw new StompProtocolException(ErrorMessages::UNABLE_PARSE_HEADER_LINE);
        }

        // decode the key value pair
        $key = $this->decodeHeaderString($key);
        $value = $this->decodeHeaderString($value);

        // ignore existing keys
        if (array_key_exists($key, $this->headers) == true) {
            return;
        }

        // validate the value by given key
        if ($this->validateHeaderValue($key, $value) === false) {
            $type = $this->keyValidationList[$key];
            throw new StompProtocolException(sprintf(ErrorMessages::HEADER_VALIDATION_ERROR, $key, $type));
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
            '\\n' => StompFrame::NEWLINE,
            '\\c' => StompFrame::COLON,
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
