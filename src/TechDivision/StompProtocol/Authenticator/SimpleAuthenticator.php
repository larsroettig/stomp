<?php
/**
 * \TechDivision\StompProtocol\Authenticator
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


namespace TechDivision\StompProtocol\Authenticator;

use TechDivision\StompProtocol\Exception\StompProtocolException;
use TechDivision\StompProtocol\Interfaces\Authenticator;
use TechDivision\StompProtocol\Utils\ErrorMessages;

/**
 * Stomp protocol authenticator class.
 *
 * @category  Library
 * @package   TechDivision_StompProtocol
 * @author    Lars Roettig <l.roettig@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/TechDivision_StompProtocol
 * @link      https://github.com/stomp/stomp-spec/blob/master/src/stomp-specification-1.1.md
 */
class SimpleAuthenticator implements Authenticator
{
    /**
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

        throw new StompProtocolException(sprintf(ErrorMessages::FAILED_AUTH, $login));
    }

    /**
     * Returns is authenticated user.
     *
     * @return bool
     */
    public function getIsAuthenticated()
    {
        $this->isAuthenticated;
    }
}
