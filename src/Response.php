<?php
/**
 * Response.php
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


class Response
{
    private $raw = '';

    private $statusCode = 0;

    private $data = array();

    private $headers = array();

    private $result = array();

    public function __toString()
    {
        return strval($this->raw);
    }

    public function setRaw($raw)
    {
        $this->raw = $raw;
    }

    public function getRaw()
    {
        return $this->raw;
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function setResult($result)
    {
        $this->result = $result;
    }

    public function getResult()
    {
        return $this->result;
    }
}