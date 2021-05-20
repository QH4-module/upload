<?php
/**
 * File Name: ExtAliyunOSS.php
 * ©2021 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021-02-03 09:57
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\upload\external;


use qttx\web\External;

abstract class ExtAliyunOSS extends External
{
    /**
     * @var int 生成policy的有效时间
     * 单位秒,默认1分钟
     */
    public $expire = 60;

    /**
     * @var int 上传文件大小最大限制
     * 默认2MB
     */
    public $maxFileSize = 2097152;

    /**
     * @return string RAM访问控制用户的 AccessKey ID
     */
    abstract public function accessKeyId();

    /**
     * @return string RAM访问控制用户的 AccessKey Secret
     */
    abstract public function accessKeySecret();

    /**
     * @return string 外网访问的Bucket域名
     * 类似于 'test.oss-cn-qingdao.aliyuncs.com' 不带https,前面带有bucket的名字
     */
    abstract public function bucketHost();

    /**
     * @return string Bucket的名称
     */
    abstract public function bucketName();

    /**
     * @return string endPoint
     * 类似于 "https://oss-cn-qingdao.aliyuncs.com" 样子,是一个url
     */
    abstract public function endPoint();

    /**
     * 表示用户上传的数据，必须是以返回的目录开始，不然上传会失败
     * 这一步不是必须项，只是为了安全起见，防止用户通过policy上传到别人的目录。
     * 不需要 '/' 开头
     * @return string|null  返回null不限制
     */
    public function directory()
    {
        return date('Y-m-d');
    }

}
