<?php
/**
 * \TechDivision\StompProtocol\StompRequestTest
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
use TechDivision\StompProtocol\Protocol\ClientCommands;
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
class StompParserTest extends HelperTestCase
{

    /**
     * The parserinstance to test.
     *
     * @var \TechDivision\StompProtocol\StompParser
     */
    protected $parser;

    /**
     * Initializes the configuration instance to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->parser = new StompParser();
    }

    /**
     * @return void
     */
    public function testParseStompHeadersSuccessful()
    {
        // header string with duplicate header value
        $header = "accept-version:1.1\nlogin:foo\nlogin:test\npasscode:bar";

        $headers = $this->parser->parseStompHeaders($header);

        $this->assertEquals(3, count($headers));
        $this->assertEquals("foo", $headers[Headers::LOGIN]);
    }

    /**
     * @return void
     */
    public function testParseStompHeadersSuccessfulWithOutVersion()
    {
        // header string with duplicate header value
        $header = "login:foo\npasscode:bar";
        $headers = $this->parser->parseStompHeaders($header);
        $this->assertEquals(3, count($headers));
        $this->assertEquals("1.0", $headers[Headers::ACCEPT_VERSION]);
    }


    /**
     * @expectedException \TechDivision\StompProtocol\Exception\StompProtocolException
     *
     * @return void
     */
    public function testParseStompHeadersWithOutColon()
    {
        $header = "accept-version1.1\n";
        $this->parser->parseStompHeaders($header);
    }

    /**
     * @expectedException \TechDivision\StompProtocol\Exception\StompProtocolException
     *
     * @return void
     */
    public function testParseStompHeadersWithEmptyKey()
    {
        $header = "accept-version:1.1\nlogin:foo\nlogin:test\n:passcode";
        $this->parser->parseStompHeaders($header);
    }

    /**
     * @expectedException \TechDivision\StompProtocol\Exception\StompProtocolException
     *
     * @return void
     */
    public function testParseStompHeadersWithNotValidContentLength()
    {
        $header = "content-length:aaaaa212w23\n";
        $this->parser->parseStompHeaders($header);
    }

    /**
     * @expectedException \TechDivision\StompProtocol\Exception\StompProtocolException
     *
     * @return void
     */
    public function testParseStompHeadersWithNotValidionHandler()
    {
        $this->parser->setKeyValidationList(array("test" => "foobar"));
        $header = "test:aaaaa212w23\n";
        $this->parser->parseStompHeaders($header);
    }
}