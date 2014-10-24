<?php

namespace StompServer;

use TechDivision\StompProtocol\StompFrame;
use TechDivision\StompProtocol\StompRequest;
use TechDivision\StompProtocol\Utils\ClientCommands;
use TechDivision\StompProtocol\Utils\CommonValues;
use TechDivision\StompProtocol\Utils\ErrorMessages;
use TechDivision\StompProtocol\Utils\Headers;
use TechDivision\StompProtocol\Utils\ServerCommands;

require_once 'Autoloader.php';
require_once 'BasicStompDaemon.php';

spl_autoload_register('Autoloader::loader');

$port = 61500;
$StompServer = new StompServer($port);
$StompServer->start();