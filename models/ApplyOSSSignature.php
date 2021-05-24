<?php
/**
 * File Name: ApplyOSSSignature.php
 * ©2021 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021-02-03 10:06
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\upload\models;


use qh4module\upload\external\ExtAliyunOSS;
use qttx\web\ServiceModel;

class ApplyOSSSignature extends ServiceModel
{
    /**
     * @var ExtAliyunOSS
     */
    protected $external;

    public function run()
    {
        // 用户上传文件时指定的前缀。
        $dir = trim($this->external->directory(), '/') . '/';

        // 有效时间
        $end = time() + $this->external->expire;
        $expiration = $this->gmt_iso8601($end);

        //最大文件大小.用户可以自己设置
        if ($this->external->maxFileSize > 0) {
            $condition = array(0 => 'content-length-range', 1 => 0, 2 => $this->external->maxFileSize);
            $conditions[] = $condition;
        }

        // 表示用户上传的数据，必须是以$dir开始
        $start = array(0 => 'starts-with', 1 => '$key', 2 => $dir);
        $conditions[] = $start;

        $arr = array('expiration' => $expiration, 'conditions' => $conditions);
        $policy = json_encode($arr);
        $base64_policy = base64_encode($policy);
        $string_to_sign = $base64_policy;
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $this->external->accessKeySecret(), true));

        $response = array();
        $response['AccessKeyId'] = $this->external->accessKeyId();
        $response['host'] = $this->external->bucketHost();
        $response['policy'] = $base64_policy;
        $response['signature'] = $signature;
        $response['expire'] = $expiration;
        $response['expire_stamp'] = $end;
        $response['dir'] = $dir;  // 这个参数是设置用户上传文件时指定的前缀。
        return $response;
    }


    function gmt_iso8601($time)
    {
        $d = gmdate('Y-m-d',$time);
        $t = gmdate('H:i:s',$time);
        return $d . 'T' . $t . 'Z';
    }
}
