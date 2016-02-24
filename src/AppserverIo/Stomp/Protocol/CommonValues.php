<?php

/**
 * \AppserverIo\Stomp\CommonValues
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

namespace AppserverIo\Stomp\Protocol;

/**
 * Holds the common values.
 *
 * @author     Lars Roettig <lr@appserver.io>
 * @copyright  2016 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 */
class CommonValues
{
    /**
     * Holds the server name
     *
     * @var string
     */
    const SERVER_NAME = "Appserver.io Mq Stomp V0.1";

    /**
     * Defines the Stomp protocol 1.0 version identifier
     *
     * @var string
     */
    const V1_0 = "1.0";

    /**
     * Defines the Stomp protocol 1.1 version identifier
     *
     * @var string
     */
    const V1_1 = "1.1";

    /**
     * Defines the Stomp protocol 1.2 version identifier
     *
     * @var string
     */
    const V1_2 = "1.2";

    /**
     *  Defines default heart beat value.
     */
    const DEFAULT_HEART_BEAT = "0,0";

    /**
     * Defines text plain type for the stomp body.
     *
     * @var string
     */
    const TEXT_PLAIN = "text/plain";
}
