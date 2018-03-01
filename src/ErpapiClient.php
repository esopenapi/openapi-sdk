<?php
/**
 * ErpapiClient.php
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
 * Class ErpapiClient
 * @package Es
 * @method AuthAuthorize(array $args = [])
 * @method CompanyDetail(array $args = [])
 * @method MemberAdd(array $args = [])
 * @method MemberEdit(array $args = [])
 * @method MemberDel(array $args = [])
 * @method MemberGet(array $args = [])
 * @method MemberDetail(array $args = [])
 * @method OrderGet(array $args = [])
 * @method OrderDetail(array $args = [])
 * @method InvoiceGetTitleList(array $args = [])
 * @method InvoiceGetQualification(array $args = [])
 * @method InvoiceApply(array $args = [])
 * @method InvoiceCharge(array $args = [])
 * @method InvoiceGet(array $args = [])
 * @method BudgetCenterAdd(array $args = [])
 * @method BudgetCenterEdit(array $args = [])
 * @method BudgetCenterDel(array $args = [])
 * @method BudgetCenterGet(array $args = [])
 * @method BudgetItemGet(array $args = [])
 * @method RegulationGet(array $args = [])
 * @method CompanyGetAuthStatus(array $args = [])
 */
class ErpapiClient extends BaseClient
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