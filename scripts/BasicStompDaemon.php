<?php

namespace StompServer;


use TechDivision\StompProtocol\StompFrame;
use TechDivision\StompProtocol\StompParser;
use TechDivision\StompProtocol\StompProtocolHandler;
use TechDivision\StompProtocol\Utils\ClientCommands;
use TechDivision\StompProtocol\Utils\ServerCommands;

class StompServer
{
    /**
     *
     */
    const HASHTAGLINE = "#######################################################";

    /**
     * @var
     */
    protected $conn;

    /**
     * @var
     */
    protected $closeConnection;

    /**
     * @var
     */
    protected $debugMode;

    /**
     * @var int
     */
    protected $port;

    /**
     * @var \TechDivision\StompProtocol\StompParser
     */
    protected $parser;

    /**
     * @param int $port
     */
    public function __construct($port)
    {
        $this->port = $port;
        $this->parser = new StompParser();
        $this->handler = new StompProtocolHandler();
    }

    /**
     *
     */
    public function start()
    {
        $this->out("Server started on Port: ", $this->port);
        $this->out(self::HASHTAGLINE);

        $socket = stream_socket_server("tcp://0.0.0.0:" . $this->port, $errNo, $errStr);

        do {

            $conn = @stream_socket_accept($socket);

            $buffer = "";

            do {

                $buffer .= fread($conn, 1024);

                if (strlen($buffer) == 0) {
                    continue;
                }

                if (strpos($buffer, StompFrame::NULL) == false) {
                    continue;
                }

                $stompFrame = $this->parser->getStompParsedFrame($buffer);
                $this->out("", $stompFrame);
                $this->out(self::HASHTAGLINE);

                try {
                    switch ($stompFrame->getCommand()) {

                        case ClientCommands::CONNECT:
                        case ClientCommands::STOMP:
                            $response = $this->handler->handleConnect($stompFrame);
                            fwrite($conn, $response);
                            break;

                        case ClientCommands::DISCONNECT:
                            $response = new StompFrame(ServerCommands::RECEIPT);
                            fwrite($conn, $response);



                    }
                } catch (\Exception $e) {
                    $response = $this->handler->handleError($e->getMessage());
                    fwrite($conn, $response);
                    $this->closeConnection = true;
                }

                $buffer = "";

            } while ($this->closeConnection == false);


            fclose($conn);
        } while (true);
    }

    /**
     * @param $message
     * @param $params
     */
    protected function out($message, $params = null)
    {
        if (!isset($params)) {
            echo PHP_EOL . $message . PHP_EOL;
        } else {
            echo PHP_EOL . $message . var_export($params, true) . PHP_EOL;
        }
    }

}