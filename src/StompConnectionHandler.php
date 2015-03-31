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

use AppserverIo\Server\Dictionaries\EnvVars;
use AppserverIo\Server\Dictionaries\ModuleHooks;
use AppserverIo\Server\Dictionaries\ServerVars;
use AppserverIo\Server\Interfaces\ConnectionHandlerInterface;
use AppserverIo\Server\Interfaces\ServerContextInterface;
use AppserverIo\Server\Interfaces\RequestContextInterface;
use AppserverIo\Server\Interfaces\WorkerInterface;
use AppserverIo\Stomp\Exception\StompProtocolException;
use AppserverIo\Stomp\Utils\ErrorMessages;
use AppserverIo\WebServer\Interfaces\HttpModuleInterface;
use AppserverIo\Psr\Socket\SocketInterface;
use AppserverIo\Psr\Socket\SocketReadException;
use AppserverIo\Psr\Socket\SocketReadTimeoutException;
use AppserverIo\Psr\Socket\SocketServerException;
use AppserverIo\Psr\HttpMessage\Protocol;
use AppserverIo\Http\HttpRequest;
use AppserverIo\Http\HttpResponse;
use AppserverIo\Http\HttpPart;
use AppserverIo\Http\HttpQueryParser;
use AppserverIo\Http\HttpRequestParser;
use AppserverIo\Http\HttpResponseStates;
use AppserverIo\Stomp\Interfaces\StompProtocolHandlerInterface;
use Psr\Log\LogLevel;

/**
 * Class StompConnectionHandler
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
class StompConnectionHandler implements ConnectionHandlerInterface
{


    /**
     * Hold's the server context instance
     *
     * @var \AppserverIo\Server\Interfaces\ServerContextInterface
     */
    protected $serverContext;

    /**
     * Hold's the request's context instance
     *
     * @var \AppserverIo\Server\Interfaces\RequestContextInterface
     */
    protected $requestContext;

    /**
     * Hold's an array of modules to use for connection handler
     *
     * @var array
     */
    protected $modules;

    /**
     * Hold's the connection instance
     *
     * @var \AppserverIo\Server\Sockets\SocketInterface
     */
    protected $connection;

    /**
     * Hold's the worker instance
     *
     * @var \AppserverIo\Server\Interfaces\WorkerInterface
     */
    protected $worker;

    /**
     * Flag if a shutdown function was registered or not
     *
     * @var boolean
     */
    protected $hasRegisteredShutdown = false;

    /**
     * Holds the stomp protocol handler.
     *
     * @var \AppserverIo\Stomp\Interfaces\StompProtocolHandlerInterface
     */
    protected $protocolHandler;

    /**
     * Holds the stomp frame.
     *
     * @var \AppserverIo\Stomp\Interfaces\FrameInterface
     */
    protected $stompFrame;

    /**
     * Holds the stomp parser.
     *
     * @var \AppserverIo\Stomp\Interfaces\StompRequestParserInterface
     */
    protected $stompParser;

    /**
     * The logger for the connection handler
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var int
     */
    protected $maxCommandLength = 20;

    /**
     * @var int
     */
    protected $maxHeaders = 1000;
    /**
     * @var int
     */
    protected $maxHeaderLength = 102410;

    /**
     * @var int
     */
    protected $maxDataLength = 10241024100;

    /**
     * Holds must the connection closed.
     *
     * @var bool
     */
    protected $closeConnection = false;

    /**
     * Inits the connection handler by given context and params
     *
     * @param \AppserverIo\Server\Interfaces\ServerContextInterface $serverContext The server's context
     * @param array                                                 $params        The params for connection handler
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function init(ServerContextInterface $serverContext, array $params = null)
    {
        // set server context
        $this->serverContext = $serverContext;

        // register shutdown handler
        register_shutdown_function(array(&$this, "shutdown"));

        // get the logger for the connection handler
        $this->logger = $serverContext->getLogger();
        $this->setConfigValues($params);


        // injects new stomp handler
        $this->injectProtocolHandler(new StompProtocolHandler());
    }

    /**
     * Init instances for the stompConnection handler
     *
     * @return void
     */
    public function initInstances()
    {
        // init new stomp frame with received command
        $this->stompFrame = new StompFrame();

        // init new stomp frame parser
        $this->stompParser = new StompParser();

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
     * Return's all needed modules as array for connection handler to process
     *
     * @return array An array of Modules
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * Return's a specific module instance by given name
     *
     * @param string $name The modules name to return an instance for
     *
     * @return \AppserverIo\WebServer\Interfaces\HttpModuleInterface|null
     */
    public function getModule($name)
    {
        if (isset($this->modules[$name])) {
            return $this->modules[$name];
        }
    }

    /**
     * Return's the request's context instance
     *
     * @return \AppserverIo\Server\Interfaces\RequestContextInterface
     *
     * @codeCoverageIgnore
     */
    public function getRequestContext()
    {
        return $this->requestContext;
    }

    /**
     * Return's the server's configuration
     *
     * @return \AppserverIo\Server\Interfaces\ServerConfigurationInterface
     *
     * @codeCoverageIgnore
     */
    public function getServerConfig()
    {
        return $this->getServerContext()->getServerConfig();
    }

    /**
     * Return's the server context instance
     *
     * @return \AppserverIo\Server\Interfaces\ServerContextInterface
     *
     * @codeCoverageIgnore
     */
    public function getServerContext()
    {
        return $this->serverContext;
    }

    /**
     * Handles the connection with the connected client in a proper way the given
     * protocol type and version expects for example.
     *
     * @param \AppserverIo\Psr\Socket\SocketInterface        $connection The connection to handle
     * @param \AppserverIo\Server\Interfaces\WorkerInterface $worker     The worker how started this handle
     *
     * @return bool Weather it was responsible to handle the firstLine or not.
     */
    public function handle(SocketInterface $connection, WorkerInterface $worker)
    {
        // add connection ref to self
        $this->connection = $connection;
        $this->worker = $worker;
        $this->protocolHandler->init();

        do {
            try {
                // set the command initial to empty string
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

                // some clients send additional newlines for heart beat
                if ($command === StompFrame::NEWLINE) {
                    continue;
                }

                $this->initInstances();

                // remove the newline from the command
                $command = rtrim($command, StompFrame::NEWLINE);
                $this->stompFrame->setCommand($command);

                if (strlen($command) > $this->maxCommandLength) {
                    throw new StompProtocolException(ErrorMessages::HEADER_COMMAND_LENGTH);
                }

                // handle the stomp frame header
                $this->handleHeader();

                // set the headers for the stomp frame
                $this->stompFrame->setHeaders($this->stompParser->getParsedHeaders());

                // handle the stomp frame body
                $this->handleBody();

                //log for frame receive
                $this->log("FrameReceive", $this->stompFrame, LogLevel::INFO);

                // delegate the frame to a handler and write the response in the stream
                $this->getProtocolHandler()->handle($this->stompFrame);
                $response = $this->getProtocolHandler()->getResponseStompFrame();
                if (isset($response)) {
                    $this->writeFrame($response, $connection);
                }

                // get the state if will the handler close the connection with the client.
                $this->closeConnection = $this->getProtocolHandler()->getMustConnectionClose();
            } catch (\Exception $e) {
                // set the current exception as error to get the error frame for the stream
                $this->getProtocolHandler()->setErrorState($e->getMessage(), array());
                $response = $this->getProtocolHandler()->getResponseStompFrame();
                $this->writeFrame($response, $connection);

                // close the connection
                $this->closeConnection = true;
            }
        } while ($this->closeConnection === false);

        // finally close connection
        $this->connection->close();
    }

    /**
     * Inject the handler stomp handler to handle stomp request.
     *
     * @param StompProtocolHandlerInterface $protocolHandler the protocol handler to inject.
     *
     * @return  void
     */
    public function injectProtocolHandler(StompProtocolHandlerInterface $protocolHandler)
    {
        $this->protocolHandler = $protocolHandler;
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
     * Returns the protocol handler.
     *
     * @return \AppserverIo\Stomp\Interfaces\StompProtocolHandlerInterface
     *
     * @codeCoverageIgnore
     */
    public function getProtocolHandler()
    {
        return $this->protocolHandler;
    }

    /**
     * Write a stomp frame
     *
     * @param \AppserverIo\Stomp\StompFrame           $stompFrame The stomp frame to write
     * @param \AppserverIo\Psr\Socket\SocketInterface $connection The connection to handle
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
     * Registers the shutdown function in this context
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    public function registerShutdown()
    {
        // register shutdown handler once to avoid strange memory consumption problems
        if ($this->hasRegisteredShutdown === false) {
            register_shutdown_function(array(&$this, "shutdown"));
            $this->hasRegisteredShutdown = true;
        }
    }

    /**
     * Does shutdown logic for worker if something breaks in process
     *
     * @return void
     *
     * @codeCoverageIgnore
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
     * Return's the connection used to handle with
     *
     * @return \AppserverIo\Psr\Socket\SocketInterface
     *
     * @codeCoverageIgnore
     */
    protected function getConnection()
    {
        return $this->connection;
    }

    /**
     * Return's the worker instance which starte this worker thread
     *
     * @return \AppserverIo\Server\Interfaces\WorkerInterface
     *
     * @codeCoverageIgnore
     */
    protected function getWorker()
    {
        return $this->worker;
    }

    /**
     * Sets the config values for the connection handler.
     *
     * @param array|null $params config values to set.
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    protected function setConfigValues($params = array())
    {
        if (!isset($params)) {
            return;
        }

        //set config values
        if (is_numeric($params['maxCommandLength'])) {
            $this->maxCommandLength = $params['maxCommandLength'];
        }

        if (is_numeric($params['maxHeaders'])) {
            $this->maxHeaders = $params['maxHeaders'];
        }

        if (is_numeric($params['maxHeaderLength'])) {
            $this->maxHeaders = $params['maxHeaderLength'];
        }

        if (is_numeric($params['maxDataLength'])) {
            $this->maxHeaders = $params['maxDataLength'];
        }
    }


    /**
     * Read the headers from the connection
     *
     * @throws \AppserverIo\Stomp\Exception\StompProtocolException
     *
     * @return void
     */
    protected function handleHeader()
    {
        // read the headers from the connection
        do {
            // read next line
            $line = $this->getConnection()->readLine();

            // stomp header are complete
            if ($line === StompFrame::NEWLINE) {
                break;
            }

            // remove the last line break
            $line = rtrim($line, StompFrame::NEWLINE);

            // check for the max header length
            if (strlen($line) > $this->maxHeaderLength) {
                throw new StompProtocolException(ErrorMessages::HEADER_LENGTH);
            }

            // check for the max header size
            if ($this->stompParser->getHeaderSize() > $this->maxHeaders) {
                throw new StompProtocolException(ErrorMessages::HEADERS_WAS_EXCEEDED);
            }

            // parse a single stomp header line
            $this->stompParser->parseHeaderLine($line);

        } while (true);
    }

    /**
     * Read the stomp body from the connection
     *
     * @throws \AppserverIo\Stomp\Exception\StompProtocolException
     *
     * @return void
     */
    protected function handleBody()
    {
        // read the stomp body
        $stompBody = "";
        do {
            $stompBody .= $this->getConnection()->read(1);

            // check for the max data length
            if (strlen($stompBody) > $this->maxDataLength) {
                throw new StompProtocolException(ErrorMessages::MAX_DATA_LENGTH);
            }
        } while (false === strpos($stompBody, StompFrame::NULL));

        // removes the null frame from the body string
        $stompBody = str_replace(StompFrame::NULL, "", $stompBody);

        // set the body for the stomp frame
        $this->stompFrame->setBody($stompBody);
    }
}
