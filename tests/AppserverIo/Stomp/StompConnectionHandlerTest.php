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

use AppserverIo\Stomp\Utils\ErrorMessages;
/**
 * Test Class for ConnectionHandler
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
class ConnectionHandlerTest extends HelperTestCase
{

    /**
     * @var \AppserverIo\Stomp\ConnectionHandler
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
     * @var
     */
    protected $configParam;


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
        $serverContext->method('getLogger')->will($this->returnValue($this->getMock('\Psr\Log\LoggerInterface')));
        $this->setServerContext($serverContext);

        /** @var \AppserverIo\Psr\Socket\SocketInterface $connection */
        $connection = $this->getMock('\AppserverIo\Psr\Socket\SocketInterface');
        $this->setConnection($connection);

        /** @var \AppserverIo\Server\Interfaces\WorkerInterface $worker */
        $worker = $this->getMock('\AppserverIo\Server\Interfaces\WorkerInterface');
        $this->setWorker($worker);

        /**
         * set config params for the connection handler.
         */
        $this->configParam = array();
        // size of the command maximun string length
        $this->configParam['maxCommandLength'] = 10;
        // lines count for headers
        $this->configParam['maxHeaders'] = 3;
        // maximum size of all header content
        $this->configParam['maxHeaderLength'] = 10;
        // set the maximum body length
        $this->configParam['maxDataLength'] = 10;

        // inject class to test
        /** @var \AppserverIo\Stomp\ConnectionHandler $stompConnectionHandler */
        $stompConnectionHandler = new ConnectionHandler();
        $stompConnectionHandler->init($serverContext, $this->configParam);
        $this->setStompConnectionHandler($stompConnectionHandler);
    }

    /**
     * @return \AppserverIo\Stomp\ConnectionHandler
     */
    public function getStompConnectionHandler()
    {
        return $this->stompConnectionHandler;
    }

    /**
     * @param \AppserverIo\Stomp\ConnectionHandler $stompConnectionHandler
     */
    public function setStompConnectionHandler($stompConnectionHandler)
    {
        $this->stompConnectionHandler = $stompConnectionHandler;
    }

    /**
     * @return \AppserverIo\Psr\Socket\SocketInterface
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
        return $key . ":" . str_repeat("A", 120) . "\n";
    }

    /**
     * @return void
     */
    public function testEndlessHeaderMessage()
    {
        /** @var $protocolHandler \AppserverIo\Stomp\Interfaces\ProtocolHandlerInterface $worker */
        $protocolHandler = $this->getMock('\AppserverIo\Stomp\Interfaces\ProtocolHandlerInterface');

        $this->getStompConnectionHandler()->init($this->getServerContext());

        $calls = 0;

        $this->getConnection()->method('readLine')->will(
                $this->returnCallback(
                    function () use (&$calls) {
                        if ($calls === 0) {
                            $calls++;
                            return "Connect\n";
                        } else {
                            return $this->generateHeaderLine();
                        }
                    }
                )
            );


        $protocolHandler->expects($this->once())
            ->method('setErrorState')
            ->with(ErrorMessages::HEADERS_WAS_EXCEEDED);

        $protocolHandler->method('getResponseStompFrame')->willReturn(
            $this->getMock('AppserverIo\Stomp\Frame')
        );

        $this->getConnection()->expects($this->once())->method('write');
        $this->getConnection()->expects($this->once())->method('close');


        $this->getStompConnectionHandler()->injectProtocolHandler($protocolHandler);

        $this->getStompConnectionHandler()->handle($this->getConnection(), $this->getWorker());
    }

    /**
     * Test case for handle heart beats from the clients
     *
     * @return void
     */
    public function testHeartBeat()
    {
        return;
        /** @var \AppserverIo\Stomp\Interfaces\ProtocolHandlerInterface $protocolHandler */
        $protocolHandler = $this->getMock('\AppserverIo\Stomp\Interfaces\ProtocolHandlerInterface');
        $this->getStompConnectionHandler()->injectProtocolHandler($protocolHandler);

        $this->getStompConnectionHandler()->init($this->getServerContext());

        $calls = 0;
        $this->getConnection()->method('readLine')->will(
                $this->returnCallback(
                    function () use (&$calls) {
                        if ($calls === 0) {
                            $calls++;
                            return "\n";
                        }
                        return "\n\n";
                    }
                )
            );

        $this->getStompConnectionHandler()->handle($this->getConnection(), $this->getWorker());
    }


    /**
     * @return void
     */
    public function testSendNormalFrame()
    {
        return;
        $this->getStompConnectionHandler()->init($this->getServerContext());

        /** @var \AppserverIo\Stomp\Interfaces\ProtocolHandlerInterface $protocolHandler */
        $protocolHandler = $this->getMock('\AppserverIo\Stomp\Interfaces\ProtocolHandlerInterface');

        $protocolHandler->method('getMustConnectionClose')->will($this->returnValue(true));

        $protocolHandler->method('getResponseStompFrame')->will($this->returnValue(new Frame()));

        $this->getStompConnectionHandler()->injectProtocolHandler($protocolHandler);

        $calls = 0;

        $this->getConnection()->expects($this->any())->method('readLine')->will(
                $this->returnCallback(
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
                )
            );
        $this->getConnection()->expects($this->any())->method('read')->will($this->returnValue("\x00"));

        $this->getConnection()->expects($this->once())->method('close');

        $this->getStompConnectionHandler()->handle($this->getConnection(), $this->getWorker());
    }

    public function testSendHeaderMaxCommandLength()
    {

        return;
        /** @var $protocolHandler \AppserverIo\Stomp\Interfaces\ProtocolHandlerInterface $worker */
        $protocolHandler = $this->getMock('\AppserverIo\Stomp\Interfaces\ProtocolHandlerInterface');
        $this->getStompConnectionHandler()->injectProtocolHandler($protocolHandler);
        $this->getStompConnectionHandler()->init($this->getServerContext());

        $this->getConnection()->method('readLine')->will(
                $this->returnCallback(
                    function () use (&$calls) {
                        return $this->generateHeaderLine();
                    }
                )
            );

        $this->getConnection()->expects($this->once())->method('close');

        $this->getConnection()->expects($this->once())->method('write');

        $this->getStompConnectionHandler()->handle($this->getConnection(), $this->getWorker());
    }

    public function testSendFrameWithMaxDataLength()
    {
    return ;
    }


    /**
     * @return void
     */
    public function testInjectAndGetModules()
    {
        $this->getStompConnectionHandler()->injectModules(array("stdClass" => new \stdClass()));
        $this->assertEquals(array("stdClass" => new \stdClass()), $this->getStompConnectionHandler()->getModules());
        $this->assertEquals(new \stdClass(), $this->getStompConnectionHandler()->getModule("stdClass"));
        $this->assertEquals(null, $this->getStompConnectionHandler()->getModule("foo"));
    }
}