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
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/TechDivision_StompProtocol
 */

namespace TechDivision\StompProtocol;

use TechDivision\Server\Interfaces\ConnectionHandlerInterface;
use TechDivision\Server\Interfaces\RequestContextInterface;
use TechDivision\Server\Interfaces\ServerContextInterface;
use TechDivision\Server\Interfaces\WorkerInterface;
use TechDivision\Server\Sockets\SocketInterface;

/**
 * Stomp connection handler
 *
 * @category  Library
 * @package   TechDivision_StompProtocol
 * @author    Lars Roettig <l.roettig@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/TechDivision_StompProtocol
 * @link      https://github.com/stomp/stomp-spec/blob/master/src/stomp-specification-1.1.md
 */
class StompConnectionHandler implements ConnectionHandlerInterface
{

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
     * Inits the connection handler by given context and params
     *
     * @param \TechDivision\Server\Interfaces\ServerContextInterface $serverContext The servers context
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
    public function injectRequestContext(RequestContextInterface $requestContext)
    {
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
     * Handles the connection with the connected client in a proper way the given
     * protocol type and version expects for example.
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
        $serverContext = $this->getServerContext();
        $serverConfig = $serverContext->getServerConfig();

        // init keep alive settings
        $keepAliveTimeout = (int)$serverConfig->getKeepAliveTimeout();
        $keepAliveConnection = true;

        // push the line into stomp request
        $stompRequest = new StompRequest();

        do {
            // receive a line from the connection
            $line = $connection->readLine(1024, $keepAliveTimeout);

            // add the stomp request a request line
            $stompRequest->push($line);


            if ($stompRequest->isComplete() === true) {

                /** @var \TechDivision\StompProtocol\StompFrame $stompFrame */
                $stompFrame = $stompRequest->getStompParsedFrame();

                switch ($stompFrame->getCommand()) {
                    case Commands::CONNECT:
                        //@todo implement this case
                }
            }
        } while ($keepAliveConnection === true);

        // finally close connection
        $connection->close();
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
