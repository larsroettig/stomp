<?php
/**
 * \TechDivision\StompProtocol\StompConnectionHandler
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
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License
 *            (OSL 3.0)
 * @link      https://github.com/techdivision/TechDivision_StompProtocol
 */

namespace TechDivision\StompProtocol;

use Psr\Log\LogLevel;
use TechDivision\Server\Interfaces\ConnectionHandlerInterface;
use TechDivision\Server\Interfaces\RequestContextInterface;
use TechDivision\Server\Interfaces\ServerContextInterface;
use TechDivision\Server\Interfaces\WorkerInterface;
use TechDivision\Server\Sockets\SocketInterface;
use TechDivision\StompProtocol\Exception\StompProtocolException;
use TechDivision\StompProtocol\Utils\ErrorMessages;

/**
 * Stomp connection handler
 *
 * @category  Library
 * @package   TechDivision_StompProtocol
 * @author    Lars Roettig <l.roettig@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License
 *            (OSL 3.0)
 * @link      https://github.com/techdivision/TechDivision_StompProtocol
 * @link      https://github.com/stomp/stomp-spec/blob/master/src/stomp-specification-1.1.md
 */
class StompConnectionHandler implements ConnectionHandlerInterface
{

    /**
     * The supported protocol versions
     *
     * @var array
     */
    protected $supportedProtocolVersions;

    /**
     * The server context instance
     *
     * @var \TechDivision\Server\Interfaces\ServerContextInterface
     */
    protected $serverContext;

    /**
     * Hold's the request's context instance
     *
     * @var \TechDivision\Server\Interfaces\RequestContextInterface
     */
    protected $requestContext;

    /**
     * The connection instance
     *
     * @var \TechDivision\Server\Sockets\SocketInterface
     */
    protected $connection;

    /**
     * Hold's an array of modules to use for connection handler
     *
     * @var array
     */
    protected $modules;

    /**
     * The worker instance
     *
     * @var \TechDivision\Server\Interfaces\WorkerInterface
     */
    protected $worker;

    /**
     * The logger for the connection handler
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Inits the connection handler by given context and params
     *
     * @param \TechDivision\Server\Interfaces\ServerContextInterface $serverContext The server's context
     * @param array                                                  $params        The params for connection handler
     *
     * @return void
     */
    public function init(ServerContextInterface $serverContext, array $params = null)
    {

        // set server context
        $this->serverContext = $serverContext;

        // register shutdown handler
        register_shutdown_function(array(&$this, "shutdown"));

        // get the logger for the connection handler
        $this->logger = $serverContext->getLogger();
    }

    /**
     * Injects all needed modules for connection handler to process
     *
     * @param array $modules An array of Modules
     *
     * @return void
     */
    public function injectModules($modules)
    {
        $this->modules = $modules;
    }

    /**
     * Returns all needed modules as array for connection handler to process
     *
     * @return array An array of Modules
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * Injects the request context
     *
     * @param \TechDivision\Server\Interfaces\RequestContextInterface $requestContext The request's context instance
     *
     * @return void
     */
    public function injectRequestContext(
        RequestContextInterface $requestContext
    ) {
        $this->requestContext = $requestContext;
    }

    /**
     * Return's the request's context instance
     *
     * @return \TechDivision\Server\Interfaces\RequestContextInterface
     */
    public function getRequestContext()
    {
        return $this->requestContext;
    }

    /**
     * Returns the servers configuration
     *
     * @return \TechDivision\Server\Interfaces\ServerConfigurationInterface
     */
    public function getServerConfig()
    {
        return $this->getServerContext()->getServerConfig();
    }

    /**
     * Returns the server context instance
     *
     * @return \TechDivision\Server\Interfaces\ServerContextInterface
     */
    public function getServerContext()
    {
        return $this->serverContext;
    }

    /**
     * Handles the connection with the connected client in a proper way the
     * given protocol type and version expects for example.
     *
     * @param \TechDivision\Server\Sockets\SocketInterface    $connection The connection to handle
     * @param \TechDivision\Server\Interfaces\WorkerInterface $worker     The worker how started this handle
     *
     * @return bool Weather it was responsible to handle the firstLine or not.
     */
    public function handle(SocketInterface $connection, WorkerInterface $worker)
    {

        // add connection ref to self
        $this->connection = $connection;
        $this->worker = $worker;
        $closeConnection = false;
        $stompProtocolHandler = new StompProtocolHandler();

        do {

            try {


                $command = "";

                try {
                    // read the first line from the connection.
                    $command = $connection->readLine();
                } catch (\Exception $e) {
                    // do nothing connection must open
                }

                // if no command receive retry receive command
                if (strlen($command) == 0) {
                    continue;
                }

                // remove the newline from the command
                $command = rtrim($command, StompFrame::NEWLINE);

                // init new stomp frame with received command
                $stompFrame = new StompFrame($command);

                // init new stomp frame parser
                $stompParser = new StompParser();

                // read the headers from the connection
                do {
                    // read next line
                    $line = $connection->readLine();

                    // stomp header are complete
                    if ($line === StompFrame::NEWLINE) {
                        break;
                    }

                    // remove the last line break
                    $line = rtrim($line, StompFrame::NEWLINE);

                    // parse a single stomp header line
                    $stompParser->parseStompHeaderLine($line);
                } while (true);


                // set the headers for the stomp frame
                $stompFrame->setHeaders($stompParser->getParsedHeaders());

                // read the stomp body
                $stompBody = "";
                do {
                    $stompBody .= $connection->read(1);
                } while (false === strpos($stompBody, StompFrame::NULL));

                // set the body for the stomp frame
                $stompFrame->setBody($stompBody);

                //log for frame receive
                $this->log("FrameReceive", $stompFrame, LogLevel::INFO);

                $response = $stompProtocolHandler->handle($stompFrame);

                if (isset($response)) {
                    // stomp protocol handler
                    $this->writeFrame($response, $connection);
                }
            } catch (StompProtocolException $e) {
                $response = $stompProtocolHandler->handleError($e);
                $this->writeFrame($response, $connection);
                $closeConnection = true;
            }
        } while ($closeConnection == false);

        // finally close connection
        $connection->close();
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param string $message The message to log
     * @param mixed  $params  The params to export
     * @param string $level   The level to log
     *
     * @return void
     */
    protected function log($message, $params, $level = LogLevel::INFO)
    {
        if (isset($params)) {
            $message .= var_export($params, true);
        }

        $this->logger->log($level, $message);
    }

    /**
     * Write a stomp frame
     *
     * @param \TechDivision\StompProtocol\StompFrame       $stompFrame
     * @param \TechDivision\Server\Sockets\SocketInterface $connection
     *
     * @return void
     */
    public function writeFrame(StompFrame $stompFrame, SocketInterface $connection)
    {
        $stompFrameStr = (string)$stompFrame;
        $this->log("FrameSend", $stompFrame, LogLevel::INFO);
        $connection->write($stompFrameStr);
    }

    /**
     * Does shutdown logic for worker if something breaks in process.
     *
     * @return void
     */
    public function shutdown()
    {
        // get refs to local vars
        $connection = $this->getConnection();
        $worker = $this->getWorker();

        // check if connections is still alive
        if ($connection) {

            // close client connection
            $this->getConnection()->close();
        }

        // check if worker is given
        if ($worker) {
            // call shutdown process on worker to re spawn
            $this->getWorker()->shutdown();
        }
    }

    /**
     * Returns the connection used to handle with
     *
     * @return \TechDivision\Server\Sockets\SocketInterface
     */
    protected function getConnection()
    {
        return $this->connection;
    }

    /**
     * Returns the worker instance which started this worker thread
     *
     * @return \TechDivision\Server\Interfaces\WorkerInterface
     */
    protected function getWorker()
    {
        return $this->worker;
    }
}