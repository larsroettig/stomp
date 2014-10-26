<?php
/**
 * \TechDivision\StompProtocol\StompProtocolHandler
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

use TechDivision\StompProtocol\Authenticator\SimpleAuthenticator;
use TechDivision\StompProtocol\Protocol\CommonValues;
use TechDivision\StompProtocol\Protocol\Headers;
use TechDivision\StompProtocol\Protocol\ServerCommands;
use TechDivision\StompProtocol\Utils\ErrorMessages;

/**
 * Implementation to handle stomp request.
 *
 * @category  Library
 * @package   TechDivision_StompProtocol
 * @author    Lars Roettig <l.roettig@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/TechDivision_StompProtocol
 * @link      https://github.com/stomp/stomp-spec/blob/master/src/stomp-specification-1.1.md
 */
class StompProtocolHandler
{

    /**
     * The supported protocol versions
     *
     * @var array
     */
    protected $supportedProtocolVersions;

    /**
     * Holds the stomp authenticator
     *
     * @var \TechDivision\StompProtocol\Authenticator
     */
    protected $auth;

    /**
     * Init new stomp protocol handler
     *
     * @return void
     */
    public function __construct()
    {
        // set the supported protocol versions to 1.0 ,1.1
        $this->supportedProtocolVersions = array(CommonValues::V1_0 => "", CommonValues::V1_1 => "");
        $this->injectAuthenticator();
    }

    /**
     * Injects the authenticator for teh handler
     *
     * @return void
     */
    public function injectAuthenticator()
    {
        $this->auth = new SimpleAuthenticator();
    }

    /**
     * Handle the connect request.
     *
     * @param \TechDivision\StompProtocol\StompFrame $stompFrame The Stomp frame to handle the connect.
     *
     * @return \TechDivision\StompProtocol\StompFrame The stomp frame Response
     *
     * @throws \TechDivision\StompProtocol\ProtocolException
     */
    public function handleConnect(StompFrame $stompFrame)
    {
        $protocolVersion = $stompFrame->getHeaderValueByKey(Headers::ACCEPT_VERSION);

        if (!array_key_exists($protocolVersion, $this->supportedProtocolVersions)) {
            $supportedVersions = implode(" ", array_keys($this->supportedProtocolVersions));
            throw new ProtocolException(sprintf(ErrorMessages::SUPPORTED_PROTOCOL_VERSIONS, $supportedVersions));
        }

        $login = $stompFrame->getHeaderValueByKey(Headers::LOGIN);
        $passCode = $stompFrame->getHeaderValueByKey(Headers::PASSCODE);

        $token = $this->auth->connect($login, $passCode);

        // create new stomp CONNECTED frame with headers and return
        $command = ServerCommands::CONNECTED;
        $headers = array(
            Headers::SESSION => $token,
            Headers::VERSION => $protocolVersion,
            Headers::SERVER  => "Appserver.io Mq V0.1"
        );
        return new StompFrame($command, $headers);
    }

    /**
     * Handle the send request.
     *
     * @param \TechDivision\StompProtocol\StompFrame $stompFrame The Stomp frame to handle the connect.
     *
     * @return \TechDivision\StompProtocol\StompFrame The stomp frame Response
     *
     * @throws \TechDivision\StompProtocol\ProtocolException
     */
    public function handleSend(StompFrame $stompFrame)
    {
        $headers = array(Headers::MESSAGE_ID, rand());

        return new StompFrame(ServerCommands::MESSAGE, $headers);
    }

    /**
     * Handle the disconnect request.
     *
     * @param \TechDivision\StompProtocol\StompFrame $stompFrame The Stomp frame to handle the connect.
     *
     * @return \TechDivision\StompProtocol\StompFrame The stomp frame Response
     *
     * @throws \TechDivision\StompProtocol\ProtocolException
     */
    public function handleDisConnect(StompFrame $stompFrame)
    {
        return new StompFrame(ServerCommands::RECEIPT);
    }

    /**
     * Returns error stomp frame.
     *
     * @param string $message The message to set
     * @param array  $headers The headers to set
     *
     * @return \TechDivision\StompProtocol\StompFrame
     */
    public function handleError($message, array $headers = array())
    {
        // init new stomp frame and set command headers and message
        $command = ServerCommands::ERROR;
        if (count($headers) == 0) {
            $headers = array(Headers::CONTENT_TYPE => CommonValues::TEXT_PLAIN);
        }
        return new StompFrame($command, $headers, $message);
    }
}
