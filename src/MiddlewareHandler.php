<?php

namespace Es;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

/**
 * MiddlewareHandler.php
 */
class MiddlewareHandler
{
    private $middlewareListBeforeReq = array();

    private $middlewareListAfterReq = array();

    private $stageBeforeReq = array(
        'INIT',
        'VALIDATE',
        'REQUEST_BUILD',
        'SIGN',
    );

    private $stageAfterReq = array(
        'HANDLE_RESPONSE',
    );

    public function __construct()
    {
        foreach ($this->stageBeforeReq as $stage) {
            $this->middlewareListBeforeReq[$stage] = array();
        }

        foreach ($this->stageAfterReq as $stage) {
            $this->middlewareListAfterReq[$stage] = array();
        }
    }

    public function execute(Command $command)
    {
        $response = new Response();

        foreach ($this->middlewareListBeforeReq as $stage => $middlewareList) {
            foreach ($middlewareList as $middleware) {
                Middleware::$middleware($command);
            }
        }

        // http请求
        if (!empty($command->getConfig()['http'])) {
            $client = new Client();
            $uri = $command->getConfig()['end_point'] . $command->getConfig()['http']['requestUri'];

            switch ($command->getConfig()['http']['method']) {
                case 'POST':
                    $promise = $client->requestAsync('POST', $uri, [RequestOptions::JSON => $command->getData()]);
                    break;
                case 'GET':
                    $uri .= (strpos($uri,'?')?  '&': "?") . http_build_query($command->getData());
                    $promise = $client->requestAsync('GET', $uri);
                    break;
            }
            try{
                $ret = $promise->wait();
                $response->setStatusCode($ret->getStatusCode());
                $response->setRaw($ret->getBody());
                $response->setHeaders($ret->getHeaders());
            } catch (RequestException $e) {
                $err = $this->parseError($e, $command);
                if ($err->isConnectionError()) {
                    throw $err;
                }
                $errorResponse = $e->getResponse();
                $statusCode = $errorResponse && $errorResponse instanceof ResponseInterface ? $errorResponse->getStatusCode() : 0;
                $raw = $errorResponse && $errorResponse instanceof ResponseInterface ? $errorResponse->getBody() : '';
                $headers = $errorResponse && $errorResponse instanceof ResponseInterface ? $errorResponse->getHeaders() : array();
                $response->setStatusCode($statusCode);
                $response->setRaw($raw);
                $response->setHeaders($headers);
            }
        }

        foreach ($this->middlewareListAfterReq as $stage => $middlewareList) {
            foreach ($middlewareList as $middleware) {
                Middleware::$middleware($response);
            }
        }
        return $response;
    }

    public function parseError($err, Command $command)
    {
        $parts = array();
        $parts['message'] = $err->getMessage();
        $parts['request'] = $err->getRequest();
        $parts['response'] = $err->getResponse();
        $parts['connection_error'] = $err instanceof ConnectException;
        $serviceError = 'HTTP error: '.$parts['message'];
        return new EsException(
            sprintf("Error executing on %s; %s", $command->getName(), $serviceError),
            $command,
            $parts,
            $err
        );
    }


    public function appendMiddlewareBeforeReq($stage, $middlewareName)
    {
        $this->middlewareListBeforeReq[$stage][] = $middlewareName;
    }

    public function prependMiddlewareBeforeReq($stage, $middlewareName)
    {
        array_unshift($this->middlewareListBeforeReq[$stage], $middlewareName);
    }

    public function appendMiddlewareAfterReq($stage, $middlewareName)
    {
        $this->middlewareListAfterReq[$stage][] = $middlewareName;
    }

    public function prependMiddlewareAfterReq($stage, $middlewareName)
    {
        array_unshift($this->middlewareListAfterReq[$stage], $middlewareName);
    }

    public function getMiddlewareListBeforeReq()
    {
        return $this->middlewareListBeforeReq;
    }

    public function getMiddlewareListAfterReq()
    {
        return $this->middlewareListAfterReq;
    }
}