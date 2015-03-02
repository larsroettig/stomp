<?php
/**
 * AppserverIo\Stomp\Interfaces\StompRequestParserInterface
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
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       https://github.com/stomp/stomp-spec/blob/master/src/stomp-specification-1.1.md
 */
namespace AppserverIo\Stomp\Interfaces;

/**
 * Interface for a stomp request parser class.
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
interface StompRequestParserInterface
{

    /**
     * Returns the parsed stomp headers.
     *
     * @return array
     */
    public function getParsedHeaders();

    /**
     * Parse's the given header line
     *
     * @param string $line The line defining a stomp request header
     *
     * @return void
     *
     * @throws \AppserverIo\Stomp\Exception\StompProtocolException
     */
    public function parseHeaderLine($line);

    /**
     * Parse the stomp frame headers.
     *
     * @param string $frameHeaders The frame headers
     *
     * @return void
     *
     * @throws \AppserverIo\Stomp\Exception\StompProtocolException
     */
    public function parseHeaders($frameHeaders);

    /**
     * Clear the headers to parse new stomp request.
     *
     * @return void
     */
    public function clearHeaders();
}
