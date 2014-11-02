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
class StompParserTest extends TestCase
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
        $this->parser= new StompParser();
    }

    /**
     *
     * @return void
     */
    public function testGetStompParsedFrameV1_0()
    {
        $bufferContent = ClientCommands::CONNECT . "\n" .
            "login:foo\n" .
            "passcode:bar1\n" .
            "host:/\n\n" .
            "Body\x00";

        /** @var \TechDivision\StompProtocol\StompFrame $parsedFrame */
        $parsedFrame = $this->parser->getStompParsedFrame($bufferContent);
        $this->assertEquals(ClientCommands::CONNECT, $parsedFrame->getCommand());
        $this->assertEquals(6, count($parsedFrame->getHeaders()));
        $this->assertEquals("Body", $parsedFrame->getBody());
        $this->assertEquals(CommonValues::V1_0, $parsedFrame->getHeaderValueByKey(Headers::ACCEPT_VERSION));
    }

    /**
     *
     * @return void
     */
    public function testGetParseStompHeadersV1_1()
    {
        // header string with duplicate header value
        $header = "accept-version:1.1\nlogin:foo\nlogin:test\npasscode:bar";
        $stompFrame = new StompFrame();

        /** @var \TechDivision\StompProtocol\StompFrame $parsedFrame */
        $parsedFrame = $this->invokeMethod($this->parser, "parseStompHeaders", array($header, $stompFrame));

        $this->assertEquals(ClientCommands::CONNECT, $parsedFrame->getCommand());
        $this->assertEquals(3, count($parsedFrame->getHeaders()));
        $this->assertEquals("foo", $parsedFrame->getHeaderValueByKey(Headers::LOGIN));
    }
}