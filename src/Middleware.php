<?php
/**
 * Middleware.php
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


class Middleware
{

    public static function paramsValidate(Command $command)
    {
        $validator = new Validator($command->getValidations());
        $validator->validate($command->getData());
    }


    /**
     * @param $params
     * @param array $config
     */
    public static function addClientIdSecret(Command $command)
    {
        $params = $command->getData();
        $config = $command->getConfig();

        if (empty($params['client_id'])) {
            $params['client_id'] = $config['client_id'];
        }
        if (empty($params['client_secret'])) {
            $params['client_secret'] = $config['client_secret'];
        }
        $command->setData($params);
    }

    /**
     * @param $params
     * @param array $config
     */
    public static function DESEncryptRequest(Command $command)
    {
        $params = $command->getData();
        $config = $command->getConfig();

        $str = json_encode($params);
        // key是sign_key的前8位
        if (strlen($config['sign_key']) > 8) {
            $key = substr($config['sign_key'], 0, 8);
        } else {
            $key = $config['sign_key'];
        }
        $iv = $key;
        $cipher = mcrypt_module_open(MCRYPT_DES, '', MCRYPT_MODE_CBC, '');
        $size = mcrypt_enc_get_block_size($cipher);
        $str  = self::pkcs5Pad($str, $size);
        mcrypt_generic_init($cipher, $key, $iv);
        $data = mcrypt_generic($cipher,$str);
        mcrypt_generic_deinit($cipher);
        $params = array(
            'client_id' => $params['client_id'],
            'data_encode' => base64_encode($data),
        );

        $command->setData($params);
    }

    public static function addTimestamp(Command $command)
    {
        $params = $command->getData();
        $params['timestamp'] = time();
        $command->setData($params);
    }

    /**
     * @param $response
     */
    public static function jsonDecodeResponse(Response &$response)
    {
        $result = json_decode($response->getRaw(), true);
        $response->setResult($result);
    }


    public static function md5EncryptRequest(Command $command)
    {
        $params = $command->getData();
        $config = $command->getConfig();

        unset($params['sign']);
        $params['sign_key'] = $config['sign_key'];
        ksort($params);

        $str = '';

        foreach ($params as $k => $v) {
            if ('' == $str) {
                $str .= $k . '=' . trim($v);
            } else {
                $str .= '&' . $k . '=' . trim($v);
            }
        }
        $params['sign'] = md5($str);
        unset($params['sign_key']);
        $command->setData($params);
    }

    /**
     * @param $text
     * @param $blocksize
     * @return string
     */
    private static function pkcs5Pad($text, $blocksize)
    {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

}