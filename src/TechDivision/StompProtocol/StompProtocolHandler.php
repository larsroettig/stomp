<?php
/**
 * Created by PhpStorm.
 * User: roettigl
 * Date: 24.10.14
 * Time: 21:16
 */

namespace TechDivision\StompProtocol;

use TechDivision\StompProtocol\Authenticator\SimpleAuthenticator;
use TechDivision\StompProtocol\Utils\CommonValues;
use TechDivision\StompProtocol\Utils\ErrorMessages;
use TechDivision\StompProtocol\Utils\Headers;
use TechDivision\StompProtocol\Utils\ServerCommands;

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
     *
     */
    public function __construct()
    {
        // set the supported protocol versions to 1.0 ,1.1
        $this->supportedProtocolVersions = array(CommonValues::V1_0 => "", CommonValues::V1_1 => "");
        $this->injectAuthenticator();
    }


    public function injectAuthenticator()
    {
        $this->auth = new SimpleAuthenticator();
    }


    /**
     * Handle the connect request.
     *
     * @param \TechDivision\StompProtocol\StompFrame $stompFrame
     *
     * @return \TechDivision\StompProtocol\StompFrame The stomp frame Response
     *
     * @throws \TechDivision\StompProtocol\ProtocolException
     */
    public function handleConnect(StompFrame $stompFrame)
    {
        $protocolVersion = $stompFrame->getHeaderValueByKey(Headers::ACCEPT_VERSION);

        if (!array_key_exists(
            $protocolVersion, $this->supportedProtocolVersions
        )
        ) {
            $supportedProtocolVersions = implode(
                " ", array_keys($this->supportedProtocolVersions)
            );
            throw new ProtocolException(
                sprintf(
                    ErrorMessages::SUPPORTED_PROTOCOL_VERSIONS,
                    $supportedProtocolVersions
                )
            );
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
