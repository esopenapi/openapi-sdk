<?php
/**
 * EsException.php
 *
 * @category
 * @package
 * @author zhangqian <zhangqiantina@didichuxing.com>
 * @copyright 2017 滴滴出行
 * @license
 * @version GIT: $Id$
 * @link http://www.xiaojukeji.com/website/index.html
 */

namespace Es;


class EsException extends \Exception
{
    private $response;
    private $request;
    private $command;
    private $connectionError;
    private $errorMessage;

    public function __construct(
        $message,
        Command $command,
        array $context = [],
        \Exception $previous = null
    ) {
        $this->command = $command;
        $this->response = isset($context['response']) ? $context['response'] : null;
        $this->request = isset($context['request']) ? $context['request'] : null;
        $this->connectionError = !empty($context['connection_error']);
        $this->errorMessage = isset($context['message']) ? $context['message'] : null;
        parent::__construct($message, 0, $previous);
    }

    public function getCommand()
    {
        return $this->command;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function isConnectionError()
    {
        return $this->connectionError;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
}