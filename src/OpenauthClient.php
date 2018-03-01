<?php
/**
 * OpenauthClient.php
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


/**
 * Class OpenauthClient
 * @package Es
 * @method ServiceGetToken(array $args = [])
 * @method CompanyGetAuthStatus(array $args = [])
 * @method Platform(array $args = [])
 */
class OpenauthClient extends BaseClient
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
        $middlewareHandler->appendMiddlewareBeforeReq('REQUEST_BUILD', 'addTimestamp');
        $middlewareHandler->appendMiddlewareBeforeReq('REQUEST_BUILD', 'md5EncryptRequest');
        $middlewareHandler->appendMiddlewareBeforeReq('VALIDATE', 'paramsValidate');
        $middlewareHandler->appendMiddlewareAfterReq('HANDLE_RESPONSE', 'jsonDecodeResponse');
    }
}