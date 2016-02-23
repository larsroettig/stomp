Appserver.io Message Queue StompProtocol Adapter
==============================================================================
[![Build Status](https://scrutinizer-ci.com/g/larsroettig/stomp/badges/build.png?b=master)](https://scrutinizer-ci.com/g/larsroettig/stomp/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/larsroettig/stomp/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/larsroettig/stomp/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/larsroettig/stomp/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/larsroettig/stomp/?branch=master)

Can receive stomp messages and will send it to "Appserver.io message Queue".

### Installation Mac

* Clone repository or Download the ZIP
* Add the followings lines to /opt/appserver/composer.json

```json
    "repositories": [
        {
            "type":"vcs",
            "url":"/YOUR/PATH/stomp"
        }
    ],
    "require": {
    "appserver-io/stomp" : "dev-master"
    }
```

* Run the command "sudo bin/php bin/composer.phar update" in /opt/appserver
* Add the new server node to /opt/appserver/etc/appserver/appserver.xml  

```xml
                <server
                        name="stomp"
                        type="\AppserverIo\Server\Servers\MultiThreadedServer"
                        worker="\AppserverIo\Server\Workers\ThreadWorker"
                        socket="\AppserverIo\Server\Sockets\StreamSocket"
                        requestContext="\AppserverIo\Server\Contexts\RequestContext"
                        serverContext="\AppserverIo\Server\Contexts\ServerContext"
                        loggerName="System">

                    <params>
                        <param name="admin" type="string">info@appserver.io</param>
                        <param name="transport" type="string">tcp</param>
                        <param name="address" type="string">127.0.0.1</param>
                        <param name="port" type="integer">61613</param>
                        <param name="workerNumber" type="integer">64</param>
                        <param name="workerAcceptMin" type="integer">3</param>
                        <param name="workerAcceptMax" type="integer">8</param>
                        <param name="documentRoot" type="string">webapps</param>
                        <param name="directoryIndex" type="string">index.pc</param>
                        <param name="keepAliveMax" type="integer">64</param>
                        <param name="keepAliveTimeout" type="integer">5</param>
                        <param name="errorsPageTemplatePath" type="string">var/www/errors/error.phtml</param>
                    </params>

                    <environmentVariables>
                        <environmentVariable condition="" definition="LOGGER_ACCESS=Access" />
                    </environmentVariables>

                    <connectionHandlers>
                        <connectionHandler type="\AppserverIo\Stomp\StompConnectionHandler" />
                    </connectionHandlers>

                    <accesses>
                        <!-- per default allow everything -->
                        <access type="allow">
                            <params>
                                <param name="X_REQUEST_URI" type="string">.*</param>
                            </params>
                        </access>
                    </accesses>

                    <!-- include of virtual host configurations -->
                    <xi:include href="conf.d/virtual-hosts.xml"/>

                    <modules>
                        <!-- REQUEST_POST hook -->
                        <module type="\AppserverIo\WebServer\Modules\AuthenticationModule"/>
                        <module type="\AppserverIo\WebServer\Modules\VirtualHostModule"/>
                        <module type="\AppserverIo\WebServer\Modules\EnvironmentVariableModule" />
                        <module type="\AppserverIo\WebServer\Modules\RewriteModule"/>
                        <module type="\AppserverIo\WebServer\Modules\DirectoryModule"/>
                        <module type="\AppserverIo\WebServer\Modules\AccessModule"/>
                        <module type="\AppserverIo\WebServer\Modules\CoreModule"/>
                        <module type="\AppserverIo\Appserver\PersistenceContainer\PersistenceContainerModule" />
                        <!-- RESPONSE_PRE hook -->
                        <module type="\AppserverIo\WebServer\Modules\DeflateModule"/>
                        <!-- RESPONSE_POST hook -->
                        <module type="\AppserverIo\Appserver\Core\Modules\ProfileModule"/>
                    </modules>
                </server>
```

* Restart the Apserver

## Development/Deployment 
* run unit test => ant run-tests

TODO
==========

* Better documentation 
* Example(tutorial)
 


