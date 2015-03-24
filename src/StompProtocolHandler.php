<?php
/**
 * \AppserverIo\Stomp\StompProtocolHandler
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   AppserverIo
 * @package    Appserver
 * @subpackage Stomp
 * @author     Lars Roettig <l.roettig@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 */

namespace AppserverIo\Stomp;

use AppserverIo\Messaging\MessageQueue;
use AppserverIo\Messaging\QueueConnectionFactory;
use AppserverIo\Messaging\StringMessage;
use AppserverIo\Stomp\Interfaces\AuthenticatorInterface;
use AppserverIo\Stomp\Authenticator\SimpleAuthenticator;
use AppserverIo\Stomp\Exception\StompProtocolException;
use AppserverIo\Stomp\Interfaces\StompProtocolHandlerInterface;
use AppserverIo\Stomp\Protocol\ClientCommands;
use AppserverIo\Stomp\Protocol\CommonValues;
use AppserverIo\Stomp\Protocol\Headers;
use AppserverIo\Stomp\Protocol\ServerCommands;
use AppserverIo\Stomp\Utils\ErrorMessages;

/**
 * Implementation to handle stomp request.
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
class StompProtocolHandler implements StompProtocolHandlerInterface
{

    /**
     * The supported protocol versions.
     *
     * @var array
     */
    protected $supportedProtocolVersions;

    /**
     * Holds the stomp authenticator.
     *
     * @var \AppserverIo\Stomp\Interfaces\AuthenticatorInterface
     */
    protected $authenticator;

    /**
     * Holds the response as stomp frame.
     *
     * @var \AppserverIo\Stomp\StompFrame
     */
    protected $response;

    /**
     * Holds the state to close parent connection.
     *
     * @var bool
     */
    protected $mustConnectionClose;

    /**
     * Holds the queue session.
     *
     * @var \AppserverIo\Messaging\QueueSession
     */
    protected $session;

    /**
     * Init new stomp protocol handler.
     */
    public function __construct()
    {
        // set the supported protocol versions to 1.0,1.1,1.2
        $this->injectSupportedProtocolVersions(array(
            CommonValues::V1_0 => "",
            CommonValues::V1_1 => "",
            CommonValues::V1_2 => ""
        ));
        $this->init();
    }

    /**
     * Init the StompProtocolHandler
     *
     * @return void
     */
    public function init()
    {
        $this->injectAuthenticator(new SimpleAuthenticator());
        $this->mustConnectionClose = false;
    }

    /**
     * Injects the supported protocol versions for the handler.
     *
     * @param array $supportedProtocolVersion Array with supported protocol versions
     *
     * @return void
     */
    public function injectSupportedProtocolVersions(array $supportedProtocolVersion)
    {
        $this->supportedProtocolVersions = $supportedProtocolVersion;
    }

    /**
     * Injects the authenticator for the handler.
     *
     * @param \AppserverIo\Stomp\Interfaces\AuthenticatorInterface $authenticator The $authenticator
     *
     * @return void
     */
    public function injectAuthenticator(AuthenticatorInterface $authenticator)
    {
        $this->authenticator = $authenticator;
    }

    /**
     * Returns must the parent handler the connection close
     *
     * @return bool
     */
    public function getMustConnectionClose()
    {
        return $this->mustConnectionClose;
    }

    /**
     * Handle the connect request.
     *
     * @param \AppserverIo\Stomp\StompFrame $stompFrame The Stomp frame to handle the connect.
     *
     * @return void
     *
     * throws \AppserverIo\Stomp\Exception\StompProtocolException
     */
    public function handle(StompFrame $stompFrame)
    {
        switch ($stompFrame->getCommand()) {
            case ClientCommands::CONNECT:
            case ClientCommands::STOMP: // case for client connect
                $this->response = $this->handleConnect($stompFrame);
                break;

            case ClientCommands::SEND:// case for client send message
                $this->handleSend($stompFrame);
                $this->response = null;
                break;

            case ClientCommands::DISCONNECT:// case for client disconnect
                $this->response = $this->handleDisConnect($stompFrame);
                break;
        }
    }

    /**
     * Detects the protocol version by given string
     *
     * @param string $protocolVersion The version string to detect the version
     *
     * @return string
     *
     * @throws \AppserverIo\Stomp\Exception\StompProtocolException
     */
    public function detectProtocolVersion($protocolVersion)
    {
        // is not required to detect the version
        if (strpos($protocolVersion, ',') === false) {
            return $protocolVersion;
        }

        $supportedProtocolVersions = array_keys($this->supportedProtocolVersions);
        $acceptsVersions = explode(",", $protocolVersion);
        $acceptsVersions = array_intersect($acceptsVersions, $supportedProtocolVersions);

        if (count($acceptsVersions) == 0) {
            $supportedVersions = implode(" ", array_keys($this->supportedProtocolVersions));
            throw new StompProtocolException(sprintf(ErrorMessages::SUPPORTED_PROTOCOL_VERSIONS, $supportedVersions));
        }

        return max($acceptsVersions);
    }

    /**
     * Handle the connect request.
     *
     * @param \AppserverIo\Stomp\StompFrame $stompFrame The Stomp frame to handle the connect.
     *
     * @return \AppserverIo\Stomp\StompFrame The stomp frame Response
     *
     * @throws \AppserverIo\Stomp\Exception\StompProtocolException
     */
    protected function handleConnect(StompFrame $stompFrame)
    {
        $protocolVersion = $stompFrame->getHeaderValueByKey(Headers::ACCEPT_VERSION);

        // detects the protocol version by given string
        $protocolVersion = $this->detectProtocolVersion($protocolVersion);

        $login = $stompFrame->getHeaderValueByKey(Headers::LOGIN);
        $passCode = $stompFrame->getHeaderValueByKey(Headers::PASSCODE);

        $this->getAuthenticator()->connect($login, $passCode);

        // create new session
        $connection = QueueConnectionFactory::createQueueConnection("stomp");
        $this->setSession($connection->createQueueSession());

        // create new stomp CONNECTED frame with headers and return
        $command = ServerCommands::CONNECTED;
        $headers = array(
            Headers::SESSION => $this->getSession()->getId(),
            Headers::VERSION => $protocolVersion,
            Headers::SERVER => CommonValues::SERVER_NAME,
            Headers::HEART_BEAT => CommonValues::DEFAULT_HEART_BEAT
        );

        // returns the response frame
        return new StompFrame($command, $headers);
    }

    /**
     * Returns the authenticator,
     *
     * @return \AppserverIo\Stomp\Interfaces\AuthenticatorInterface
     */
    public function getAuthenticator()
    {
        return $this->authenticator;
    }

    /**
     * Returns the message queue session.
     *
     * @return \AppserverIo\Messaging\QueueSession
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Set the message queue session.
     *
     * @param \AppserverIo\Messaging\QueueSession $session The  message queue session to set.
     *
     * @return void
     */
    public function setSession($session)
    {
        $this->session = $session;
    }

    /**
     * Handle the send request.
     *
     * @param \AppserverIo\Stomp\StompFrame $stompFrame The Stomp frame to handle the connect.
     *
     * @return void
     *
     * @throws \AppserverIo\Stomp\Exception\StompProtocolException
     */
    protected function handleSend(StompFrame $stompFrame)
    {
        // checks ist the client authenticated
        if ($this->getAuthenticator()->getIsAuthenticated() == false) {
            throw new StompProtocolException(sprintf(ErrorMessages::FAILED_AUTH, ""));
        }

        // set the destination from the header
        $destination = $stompFrame->getHeaderValueByKey(Headers::DESTINATION);

        // initialize the connection and the session
        $queue = MessageQueue::createQueue($destination);

        // create the sender and send a simple string message
        $sender = $this->getSession()->createSender($queue);

        // push the message in the que
        $sender->send(new StringMessage($stompFrame->getBody()));
    }

    /**
     * Handle the disconnect request.
     *
     * @param \AppserverIo\Stomp\StompFrame $stompFrame The Stomp frame to handle the connect.
     *
     * @return \AppserverIo\Stomp\StompFrame The stomp frame Response
     */
    protected function handleDisConnect($stompFrame)
    {
        // set state to close the client connection
        $this->mustConnectionClose = true;
        $headers = array();

        // set the client a receiptId than must server must add this to response header
        $receiptId = $stompFrame->getHeaderValueByKey(Headers::RECEIPT_REQUESTED);
        if (is_string($receiptId)) {
            $headers = array(Headers::RECEIPT_ID => $receiptId);
        }

        // returns the response frame
        return new StompFrame(ServerCommands::RECEIPT,$headers);
    }

    /**
     * Returns the response stomp frame.
     *
     * @return \AppserverIo\Stomp\StompFrame
     */
    public function getResponseStompFrame()
    {
        return $this->response;
    }

    /**
     * Sets the state from handler to error
     *
     * @param string $message The message to set in the error frame.
     * @param array  $headers The header to set in the error frame.
     *
     * @return mixed
     */
    public function setErrorState($message = "", $headers = array())
    {
        $this->response = $this->handleError($message, $headers);
    }

    /**
     * Returns error stomp frame.
     *
     * @param string $message The message to set
     * @param array  $headers The headers to set
     *
     * @return \AppserverIo\Stomp\StompFrame
     */
    protected function handleError($message, array $headers = array())
    {
        // init new stomp frame and set command headers and message
        $command = ServerCommands::ERROR;

        // set the default header
        if (count($headers) == 0) {
            $headers = array(Headers::CONTENT_TYPE => CommonValues::TEXT_PLAIN);
        }

        // set state to close the client connection
        $this->mustConnectionClose = true;

        // returns the response frame
        return new StompFrame($command, $headers, $message);
    }
}
