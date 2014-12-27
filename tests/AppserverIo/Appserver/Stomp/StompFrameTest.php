<?php
/**
 * \AppserverIo\Appserver\Stomp\StompFrameTest
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

namespace AppserverIo\Appserver\Stomp;

use AppserverIo\Appserver\Stomp\Exception\StompProtocolException;
use AppserverIo\Appserver\Stomp\Protocol\ClientCommands;
use AppserverIo\Appserver\Stomp\Protocol\CommonValues;
use AppserverIo\Appserver\Stomp\Protocol\Headers;

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
     * @var \AppserverIo\Appserver\Stomp\StompFrame
     */
    protected $frame;

    /**
     * Initializes the configuration instance to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->frame = new StompFrame();
    }

    /**
     * @return  void
     */
    public function testSetHeaders()
    {
        // create some data for test
        $header = array(Headers::LOGIN => "foobar", Headers::PASSCODE => "password");

        /// call the method we want test
        $this->frame->setHeaders($header);

        // checks the results
        $this->assertEquals($header, $this->frame->getHeaders());
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
        $this->frame->setHeaders($header1);
        $this->frame->setHeaders($header2);

        // checks the results
        $this->assertEquals($header1, $this->frame->getHeaders());
    }





}