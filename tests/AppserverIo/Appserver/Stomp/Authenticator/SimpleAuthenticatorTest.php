<?php
/**
 * \AppserverIo\Appserver\Stomp\SimpleAuthenticatorTest
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
 */

namespace AppserverIo\Appserver\Stomp\Authenticator;


use AppserverIo\Appserver\Stomp\HelperTestCase;

/**
 * Stomp protocol authenticator class.
 *
 * @category  Library
 * @package   TechDivision_StompProtocol
 * @author    Lars Roettig <l.roettig@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link      https://github.com/stomp/stomp-spec/blob/master/src/stomp-specification-1.1.md
 */
class SimpleAuthenticatorTest extends HelperTestCase
{

    /**
     * @var \AppserverIo\Appserver\Stomp\Interfaces\Authenticator
     */
    protected $authenticator;

    /**
     * Initializes the configuration instance to test.
     *
     * @return void
     */
    public function setUp()
    {
     $this->authenticator = new SimpleAuthenticator();
    }


    /**
     * @return void
     */
    public function testConnectSuccessfully()
    {
        $res = $this->authenticator->connect("system" , "manager");
        $this->assertGreaterThan(0, strlen($res));
        $this->assertTrue($this->authenticator->getIsAuthenticated());
    }

    /**
     * @expectedException \AppserverIo\Appserver\Stomp\Exception\StompProtocolException
     *
     * @return void
     */
    public function testConnectWithError()
    {
        $this->authenticator->connect("system" , "barz");
        $this->assertFalse($this->authenticator->getIsAuthenticated());
    }
}
