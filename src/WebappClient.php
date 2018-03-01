<?php

namespace Es;

/**
 * WebappClient.php
 * @method TicketFetch(array $args = [])
 * @method CitiesGetAll(array $args = [])
 */

class WebappClient extends BaseClient
{
    public function __construct(array $args)
    {
        if (empty($args['client_id']) || empty($args['client_secret']) || empty($args['sign_key'])) {
            throw new \InvalidArgumentException('Missing parameters: client_id, client_secret, sign_key required.');
        }
        parent::__construct($args);

        $this->addMiddlewares();
    }

    private function addMiddlewares()
    {
        $middlewareHandler = $this->getMiddlewareHandler();

        $middlewareHandler->appendMiddlewareBeforeReq('REQUEST_BUILD', 'addClientIdSecret');
        $middlewareHandler->appendMiddlewareBeforeReq('REQUEST_BUILD', 'DESEncryptRequest');
        $middlewareHandler->appendMiddlewareBeforeReq('VALIDATE', 'paramsValidate');
        $middlewareHandler->appendMiddlewareAfterReq('HANDLE_RESPONSE', 'jsonDecodeResponse');
    }
}