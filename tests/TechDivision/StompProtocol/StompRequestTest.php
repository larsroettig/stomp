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
class StompRequestTest extends TestCase
{

    /**
     * The request instance to test.
     *
     * @var \TechDivision\StompProtocol\StompRequest
     */
    protected $request;

    /**
     * Initializes the configuration instance to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->request = new StompRequest();

    }

    /**
     *
     * @return void
     */
    public function testIdentifyFrameAsCompleted()
    {
        $bufferContent = array(
            Commands::CONNECT . "\n",
            "accept-version:1.1\n",
            "\x00"
        );

        foreach ($bufferContent as $line) {
            $this->request->push($line);
        }
        $this->assertTrue($this->request->isComplete(), "Stomp Frame is not marked as complete");
    }

    /**
     *
     * @return void
     */
    public function testGetStompParsedFrameV1_0()
    {
        $bufferContent = array(
            Commands::CONNECT . "\n",
            "login:foo\n",
            "passcode:bar1\n",
            "host:/\n\n",
            "Body\x00"
        );

        foreach ($bufferContent as $line) {
            $this->request->push($line);
        }

        /** @var \TechDivision\StompProtocol\StompFrame $parsedFrame */
        $parsedFrame = $this->request->getStompParsedFrame();
        $this->assertEquals(Commands::CONNECT, $parsedFrame->getCommand());
        $this->assertEquals(4, count($parsedFrame->getHeaders()));
        $this->assertEquals("Body", $parsedFrame->getBody());
        $this->assertEquals(CommonValues::V1_0, $parsedFrame->getHeaderByKey(Headers::ACCEPT_VERSION));
    }

    /**
     *
     * @return void
     */
    public function testGetParseStompHeadersV1_1()
    {
        // header string with duplicate header value
        $header = Commands::CONNECT . "\naccept-version:1.1\nlogin:foo\nlogin:test\npasscode:bar";
        $stompFrame = new StompFrame();

        /** @var \TechDivision\StompProtocol\StompFrame $parsedFrame */
        $parsedFrame = $this->invokeMethod($this->request, "parseStompHeaders", array($header, $stompFrame));

        $this->assertEquals(Commands::CONNECT, $parsedFrame->getCommand());
        $this->assertEquals(3, count($parsedFrame->getHeaders()));
        $this->assertEquals("foo", $parsedFrame->getHeaderByKey(Headers::LOGIN));
    }
}