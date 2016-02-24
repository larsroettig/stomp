<?php

/**
 * \AppserverIo\Stomp\Authenticator
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author     Lars Roettig <lr@appserver.io>
 * @copyright  2016 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 */

namespace AppserverIo\Stomp\Authenticator;

use AppserverIo\Stomp\Exception\ProtocolException;
use AppserverIo\Stomp\Interfaces\AuthenticatorInterface;
use AppserverIo\Stomp\Utils\ErrorMessages;

/**
 * Stomp protocol authenticator class.
 *
 * @author     Lars Roettig <lr@appserver.io>
 * @copyright  2016 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       https://github.com/stomp/stomp-spec/blob/master/src/stomp-specification-1.1.md
 */
class SimpleAuthenticator implements AuthenticatorInterface
{
    /**
     * Holds the information is the client authenticated.
     *
     * @var bool
     */
    protected $isAuthenticated = false;

    /**
     * Authenticate user by connect command.
     *
     * @param string $login    The login name
     * @param string $passCode The password
     *
     * @return string token which will be used for authorization requests (though it isn't actually used yet)
     *
     * @throws \Exception
     */
    public function connect($login, $passCode)
    {
        if ($login === "system" && $passCode === "manager") {
            $this->isAuthenticated = true;
            return md5(rand());
        }

        throw new ProtocolException(sprintf(ErrorMessages::FAILED_AUTH, $login));
    }

    /**
     * Returns is authenticated user.
     *
     * @return bool
     */
    public function getIsAuthenticated()
    {
        return $this->isAuthenticated;
    }
}
