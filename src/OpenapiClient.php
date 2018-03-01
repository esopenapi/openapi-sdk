<?php
/**
 * OpenapiClient.php
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
 * Client used to interact with openapi service.
 * @doc http://open.es.xiaojukeji.com/doc/openapi/index.html
 *
 * @method AuthAuthorize(array $args = [])
 * @method CommonCitiesGetAll(array $args = [])
 * @method CommonCitiesGetPrice(array $args = [])
 * @method CommonEstimatePriceCoupon(array $args = [])
 * @method CommonEstimateGetFeature(array $args = [])
 * @method CommonAddressGetAddress(array $args = [])
 * @method CommonCitiesGetCityIdByPoi(array $args = [])
 * @method CommonMinduration(array $args = [])
 * @method OrderCreateOrderId(array $args = [])
 * @method OrderCreateRequest(array $args = [])
 * @method OrderDetailGetOrderDetail(array $args = [])
 * @method OrderCancel(array $args = [])
 * @method OrderHistory(array $args = [])
 * @method OrderFeeConfirm(array $args = [])
 * @method CommonComplaintGetReasonList(array $args = [])
 * @method CommonComplaintSubmit(array $args = [])
 * @method CommonComment(array $args = [])
 * @method UserInfoGetUserInfo(array $args = [])
 */

class OpenapiClient extends BaseClient
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