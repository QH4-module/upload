<?php
/**
 * File Name: HpUpload.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/5/11 5:30 下午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\upload;


use qh4module\upload\external\ExtAliyunOSS;
use QTTX;
use qttx\helper\StringHelper;
use OSS\OssClient;
use OSS\Core\OssException;

class HpUpload
{
    /**
     * 从远程url下载文件保存到本地
     * @param $url string
     * @param $file string 文件的完整路径
     * @return bool
     */
    public static function downloadFileFormUrl($url, $file)
    {
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    // https请求 不验证证书和hosts
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            $source = curl_exec($ch);
            curl_close($ch);
            $fp = fopen($file, 'w');
            fwrite($fp, $source);
            fclose($fp);
            return true;
        } catch (\Exception $exception) {
            QTTX::$app->log->error($exception);
            return false;
        }
    }

    /**
     * 通过文件url将文件上传阿里云oss
     * @param string $url 文件url
     * @param string $target 存到阿里云的路径,注意不要以 `/` 开头
     *               类似于 2001-01-01/xxxxxx/file.jpeg
     * @param ExtAliyunOSS $external
     * @return bool
     */
    public static function uploadOssByUrl($url, $target, ExtAliyunOSS $external)
    {
        $filename = StringHelper::random(32) . '.jpg';
        $file = \QTTX::$app->runtimePath . '/' . $filename;
        if (!self::downloadFileFormUrl($url, $file)) {
            return false;
        }
        $ret = self::uploadFileOss($file, $target, $external);
        @unlink($file);
        return $ret;
    }

    /**
     * 将本地文件上传到阿里云
     * @param string $file 本地文件路径
     * @param string $target 存到阿里云的路径,注意不要以 `/` 开头
     *               类似于 2001-01-01/xxxxxx/file.jpeg
     * @param ExtAliyunOSS $external
     * @return bool
     */
    public static function uploadFileOss($file, $target, ExtAliyunOSS $external)
    {
        // 阿里云主账号AccessKey拥有所有API的访问权限，风险很高。强烈建议您创建并使用RAM账号进行API访问或日常运维，请登录RAM控制台创建RAM账号。
        $accessKeyId = $external->accessKeyId();
        $accessKeySecret = $external->accessKeySecret();
        $endpoint = $external->endPoint();
        // 设置存储空间名称。
        $bucket = $external->bucketName();
        // 设置文件名称。
        $object = $target;
        // <yourLocalFile>由本地文件路径加文件名包括后缀组成，例如/users/local/myfile.txt。
        $filePath = $file;

        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);

            $ossClient->uploadFile($bucket, $object, $filePath);

            return true;
        } catch (OssException $e) {
            \QTTX::$app->log->error($e);
            return false;
        }
    }

}