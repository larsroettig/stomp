<?php

/**
 * Stomp protocol authenticator interface
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

namespace AppserverIo\Stomp;

use AppserverIo\Stomp\Protocol\ServerCommands;

/**
 * Test Class  for StompConnectionHandler
 *
 * @category   AppserverIo
 * @package    Appserver
 * @subpackage Stomp
 * @author     Lars Roettig <l.roettig@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       https://github.com/stomp/stomp-spec/blob/master/src/stomp-specification-1.1.md
 */


class StompConnectionHandlerTest extends HelperTestCase
{

    /**
     * @var \AppserverIo\Stomp\StompConnectionHandler
     */
    protected $stompConnectionHandler;

    /**
     * @var \AppserverIo\Psr\Socket\SocketInterface
     */
    protected $connection;

    /**
     * @var \AppserverIo\Server\Interfaces\WorkerInterface
     */
    protected $worker;

    /**
     * @var \AppserverIo\Server\Interfaces\ServerContextInterface
     */
    protected $serverContext;

    /**
     * Initializes the configuration instance to test.
     *
     * @return void
     */
    public function setUp()
    {
        // setup  mocking objects
        /** @var \AppserverIo\Server\Interfaces\ServerContextInterface $serverContext */
        $serverContext = $this->getMock('\AppserverIo\Server\Interfaces\ServerContextInterface');
        $serverContext->method('getLogger')
            ->will($this->returnValue($this->getMock('\Psr\Log\LoggerInterface')));
        $this->setServerContext($serverContext);

        /** @var \AppserverIo\Psr\Socket\SocketInterface $connection */
        $connection = $this->getMock('\AppserverIo\Psr\Socket\SocketInterface');
        $this->setConnection($connection);

        /** @var \AppserverIo\Server\Interfaces\WorkerInterface $worker */
        $worker = $this->getMock('\AppserverIo\Server\Interfaces\WorkerInterface');
        $this->setWorker($worker);

        // inject class to test
        /** @var \AppserverIo\Stomp\StompConnectionHandler $stompConnectionHandler */
        $stompConnectionHandler = new StompConnectionHandler();
        $this->setStompConnectionHandler($stompConnectionHandler);
    }

    /**
     * @return \AppserverIo\Stomp\StompConnectionHandler
     */
    public function getStompConnectionHandler()
    {
        return $this->stompConnectionHandler;
    }

    /**
     * @param \AppserverIo\Stomp\StompConnectionHandler $stompConnectionHandler
     */
    public function setStompConnectionHandler($stompConnectionHandler)
    {
        $this->stompConnectionHandler = $stompConnectionHandler;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $connection
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return \AppserverIo\Server\Interfaces\WorkerInterface
     */
    public function getWorker()
    {
        return $this->worker;
    }

    /**
     * @param \AppserverIo\Server\Interfaces\WorkerInterface $worker
     */
    public function setWorker($worker)
    {
        $this->worker = $worker;
    }

    /**
     * @return \AppserverIo\Server\Interfaces\ServerContextInterface
     */
    public function getServerContext()
    {
        return $this->serverContext;
    }

    /**
     * @param \AppserverIo\Server\Interfaces\ServerContextInterface $serverContext
     */
    public function setServerContext($serverContext)
    {
        $this->serverContext = $serverContext;
    }

    /**
     *
     */
    protected function generateHeaderLine()
    {
        $key = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 19);
        return $key . ":" . str_repeat("A", 1200000) . "\n";
    }

    /**
     * @return void
     */
    public function testEndlessHeaderMessage()
    {
        /** @var \AppserverIo\Stomp\Interfaces\StompProtocolHandlerInterface $worker */
        $protocolHandler = $this->getMock('\AppserverIo\Stomp\Interfaces\StompProtocolHandlerInterface');
        $this->getStompConnectionHandler()->injectProtocolHandler($protocolHandler);

        $this->getStompConnectionHandler()->init($this->getServerContext());
        $calls = 0;

        $this->getConnection()->method('readLine')
            ->will($this->returnCallback(
                function () use (&$calls) {
                    if ($calls === 0) {
                        $calls++;
                        return "Connect\n";
                    } else {
                        return $this->generateHeaderLine();
                    }
                }
            ));

        $this->getConnection()->expects($this->once())
            ->method('close');

        $this->getConnection()->expects($this->once())
            ->method('write');

        $this->getStompConnectionHandler()->handle($this->getConnection(), $this->getWorker());
    }

    /**
     * @return void
     */
    public function testSendNormalFrame()
    {
        $this->getStompConnectionHandler()->init($this->getServerContext());

        /** @var \AppserverIo\Stomp\Interfaces\StompProtocolHandlerInterface $worker */
        $protocolHandler = $this->getMock('\AppserverIo\Stomp\Interfaces\StompProtocolHandlerInterface');

        $protocolHandler->method('getMustConnectionClose')
            ->will($this->returnValue(true));

        $protocolHandler->method('getResponseStompFrame')
            ->will($this->returnValue(new StompFrame()));

        $this->getStompConnectionHandler()->injectProtocolHandler($protocolHandler);

        $calls = 0;

        $this->getConnection()->expects($this->any())->method('readLine')
            ->will($this->returnCallback(
                function () use (&$calls) {
                    $calls++;
                    if ($calls === 1) {
                        return "";
                    }
                    if ($calls === 2) {
                        return "Connect";
                    } elseif ($calls === 3) {
                        return "accept-version:1.1\nlogin:foo\nlogin:test\npasscode:bar\n";
                    } else {
                        return "\n";
                    }

                }
            ));
        $this->getConnection()->expects($this->any())->method('read')
            ->will($this->returnValue("\x00"));

        $this->getConnection()->expects($this->once())
            ->method('close');

        $this->getStompConnectionHandler()->handle($this->getConnection(), $this->getWorker());
    }

    /**
     * @return void
     */
    public function testInjectAndGetModules()
    {
        $this->getStompConnectionHandler()->injectModules(array("stdClass" => new \stdClass()));
        $this->assertEquals(array("stdClass" => new \stdClass()), $this->getStompConnectionHandler()->getModules());
        $this->assertEquals( new \stdClass(), $this->getStompConnectionHandler()->getModule("stdClass"));
        $this->assertEquals(null, $this->getStompConnectionHandler()->getModule("foo"));
    }
}