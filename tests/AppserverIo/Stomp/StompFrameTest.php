<?php
/**
 * \AppserverIo\Stomp\StompFrameTest
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
 * @link       https://github.com/appserver-io/appserver
 * @link      https://github.com/stomp/stomp-spec/blob/master/src/stomp-specification-1.1.md
 */

namespace AppserverIo\Stomp;

use AppserverIo\Stomp\Exception\StompProtocolException;
use AppserverIo\Stomp\Protocol\ClientCommands;
use AppserverIo\Stomp\Protocol\CommonValues;
use AppserverIo\Stomp\Protocol\Headers;
use AppserverIo\Stomp\Protocol\ServerCommands;

/**
 * Implementation for a Stomp Request.
 *
 * @category  Library
 * @package   TechDivision_StompProtocol
 * @author    Lars Roettig <l.roettig@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link      https://github.com/stomp/stomp-spec/blob/master/src/stomp-specification-1.1.md
 */
class StompFrameTest extends HelperTestCase
{

    /**
     * The parserinstance to test.
     *
     * @var \AppserverIo\Stomp\Frame
     */
    protected $frame;

    /**
     * Initializes the configuration instance to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->setFrame(new Frame());
    }

    /**
     * @return Frame
     */
    public function getFrame()
    {
        return $this->frame;
    }

    /**
     * @param Frame $frame
     */
    public function setFrame($frame)
    {
        $this->frame = $frame;
    }

    /**
     * @return  void
     */
    public function testSetHeaders()
    {
        // create some data for test
        $header = array(Headers::LOGIN => "foobar", Headers::PASSCODE => "password");

        /// call the method we want test
        $this->getFrame()->setHeaders($header);

        // checks the results
        $this->assertEquals($header, $this->getFrame()->getHeaders());
    }

    /**
     * @return  void
     */
    public function testSetHeaderLineWithDoubleKeys()
    {

        // create some data for test
        $header1 = array(Headers::LOGIN => "foobar", Headers::PASSCODE => "password1");
        $header2 = array(Headers::LOGIN => "tester", Headers::PASSCODE => "password2");

        /// call the method we want test
        $this->getFrame()->setHeaders($header1);
        $this->getFrame()->setHeaders($header2);

        // checks the results
        $this->assertEquals($header1, $this->getFrame()->getHeaders());
    }

    /**
     * @return void
     */
    public function testFrameToStringWithConnectedFrame()
    {
        $this->getFrame()->setCommand(ServerCommands::CONNECTED);
        $this->getFrame()->setHeaders(array(Headers::HEART_BEAT => "12,20"));

        $frameString = ServerCommands::CONNECTED . "\n".
            "heart-beat:12,20\n".
            "\n" ."\x00\n";

        $this->assertEquals( $frameString, (string) $this->getFrame());
    }

    /**
     * @return void
     */
    public function testFrameToStringWithConnectFrame()
    {
        $this->getFrame()->setCommand(ClientCommands::CONNECT);
        $this->getFrame()->setHeaders(array(Headers::LOGIN => "foobar", Headers::PASSCODE => "password1"));

        $frameString = ClientCommands::CONNECT . "\n" .
           "login:foobar\n".
           "passcode:password1\n".
            "\n" ."\x00\n";

        $this->assertEquals( $frameString, (string) $this->getFrame());
    }
}