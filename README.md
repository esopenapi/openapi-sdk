#滴滴企业版SDK（PHP版）
**滴滴企业版SDK**提供了PHP接入滴滴企业版open API的工具包。

##相关资源
* [滴滴企业版][es]
* [开放平台][open-es]
* 接口文档：
	* [用车API接口文档][openapi-doc] 
	* [管理API接口文档][erpapi-doc] 
* [问答社区][es-forum]


##快速入门
###1 安装

*1.	**通过git下载sdk**。

```
git clone git@git.xiaojukeji.com:zhangqiantina/openapi-sdk.git
```
*2.	**引入autoloader.php文件**。

```
<?php
require 'path/to/autoloader.php';
```

###2 一个完整的Demo
**一个完整的用车api价格预估demo**

```
<?php

require 'autoloader.php';

$openapi = new Es\OpenapiClient(array(
    'client_id' => '***',
    'client_secret' => '***',
    'sign_key' => '***'
));

$authResponse = $openapi->AuthAuthorize(array(
    'grant_type' => 'client_credentials',
    'phone' => '***',
));

// Notice: access_token有效期是半个小时, 不要每次请求都申请access_token!
$accessToken = $authResponse->getResult()['access_token'];

$response = $openapi->CommonEstimatePriceCoupon(array(
    'access_token' => $accessToken,
    'flat' => '40.044648',
    'flng' => '116.321149',
    'tlat' => '40.039534',
    'tlng' => '116.323408',
    'require_level' => '600',
    'rule' => 301,
    'city' => 1,
    'type' => 1,
    'departure_time' => '2018-01-30 16:00:00',
));

$statusCode = $response->getStatusCode();
$result = $response->getResult();
```

> 注意这只是一个示例！！

> 正式环境使用时，要**缓存access_token，做异常处理和错误判断**等。

##使用说明
###1 Create a Client

用车api **OpenapiClient**

```
<?php

require 'openapi-sdk/autoloader.php';

$openapi = new Es\OpenapiClient(array(
    'client_id' => '***',
    'client_secret' => '***',
    'sign_key' => '***'
));

```

管理api **ErpapiClient**

```
<?php

require 'openapi-sdk/autoloader.php';

$erpapi = new Es\ErpapiClient(array(
    'client_id' => '***',
    'client_secret' => '***',
    'sign_key' => '***'
));

```

webapp api **WebappClient**

```
<?php

require 'openapi-sdk/autoloader.php';

$webapp = new Es\WebappClient(array(
    'client_id' => '***',
    'client_secret' => '***',
    'sign_key' => '***'
));

```



###2 请求示例
**Webapp获取一次性发单ticket**

```
try {
    $response = $webapp->ticketFetch(array(
        'passenger_phone' => '130********',
        'callback_info' => array(),
        'require_level_list' => '600,900',
        'auth_type' => 1,
        'master_phone' => '130********',
    ));

} catch (\Es\EsException $e) {
    echo $e->getMessage();
    echo $e->isConnectionError();
}
```
**用车api授权**

```
try{
    $response = $openapi->AuthAuthorize(array(
        'grant_type' => 'client_credentials',
        'phone' => '11000000999',
    ));
} catch (\Es\EsException $e) {
    echo $e->getMessage();
    echo $e->isConnectionError();
}
```

###3 处理结果
**获取状态码**

```
$response->getStatusCode();
```

**获取header**

```
$response->getHeaders();
```

**获取结果**

```
// 获取result
$response->getResult();

// 比如说对AuthAuthorize的结果取access_token
$result = $response->getResult();
$accessToken = $result['access_token'];
```

**获取未加工结果**

```
$response->getRaw();

```

> getResult和getRaw的区别是：
> getRaw获得的是调用api接口请求的原始结果， getResult获得的是对原始结果经过json_decode等一系列处理以后的结果。

###4 异常处理

**检查是否是连接错误**

```
// 0-不是连接错误 1-是连接错误
$exception->isConnectionError();
```

**获取错误信息**

```
$exception->getErrorMessage();

```

**获取调用response**

```
$exception->getResponse();

```

###5 查看所有操作
**通过下面的命令查看这个client支持的接口**

```
$openapi->help();
```
**以用车api为例**


```
<?php

require 'autoloader.php';

$openapi = new Es\OpenapiClient(array(
    'client_id' => '***',
    'client_secret' => '***',
    'sign_key' => '***'
));

$openapi->help();
```


##FAQ
#####Q ： WebApp使用时有异常Call to undefined function Es\mcrypt_module_open()
PHP安装mcrypt扩展。[安装方法点击][mcrypt-setup]

[es]:https://es.xiaojukeji.com/
[open-es]:https://es.xiaojukeji.com/
[openapi-doc]:http://open.es.xiaojukeji.com/doc/openapi/index.html
[erpapi-doc]:http://open.es.xiaojukeji.com/doc/erpapi/index.html
[es-forum]:http://qa.es.xiaojukeji.com/index.php?qa=questions
[mcrypt-setup]:http://php.net/manual/zh/mcrypt.setup.php

