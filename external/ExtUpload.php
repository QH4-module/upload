<?php
/**
 * File Name: ExtUpload.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021-01-14 10:47 上午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\upload\external;


use QTTX;
use qttx\helper\FileHelper;
use qttx\helper\StringHelper;
use qttx\web\External;
use qttx\web\UploadedFile;

class ExtUpload extends External
{
    /**
     * @var string 上传的字段名
     */
    public $field = 'file';

    /**
     * @var bool 是否允许多文件上传
     */
    public $allowMultipleFiles = false;

    /**
     * @var bool 是否对文件重命名保存
     * @see generateNewName()
     */
    public $renameFile = true;

    /**
     * 上传单个文件接收字段应该翻译 'file'
     * 上传多个文件接收字段应该翻译 'files'
     * 和 `$field`属性无关
     * eg:
     * return [
     *      'zh_cn'=>[
     *          'file' => '头像',
     *          'files' => '头像'
     *      ]
     * ];
     * @return array
     */
    public function attributeLangs()
    {
        return array();
    }

    /**
     * 文件保存的目录,可以使用别名
     * @param UploadedFile $file
     * @return string
     */
    public function saveFolder($file)
    {
        $m = date('Y-m');

        $d = date('d');

        $dir =  StringHelper::combPath(APP_PATH, 'uploads', $m, $d);

        FileHelper::mkdir($dir);

        return $dir;
    }


    /**
     * 对返回结果的路径进行格式化
     * 默认从路径中去掉根目录前的部分
     * @param string $path 实际路径
     * @return string 要返回的路径
     */
    public function formatResultPath($path)
    {
        return str_replace(APP_PATH, '', $path);
    }


    /**
     * @return string[] 允许的扩展名
     */
    public function enableExtensions()
    {
        return array();
    }

    /**
     * @return string[] 允许的mine类型
     */
    public function enableMineType()
    {
        return array();
    }

    /**
     * @return int[] 允许的单个文件大小
     * 第一个元素为最小限制,第二个元素为最大限制,其它元素忽略
     * 默认不限制
     * eg [10000, 2048000] 最小约10K ,最大约2M
     * 注意: 文件最大限制,首先受限于php.ini配置文件,如果配置文件中的设置比返回值小,则返回值无效
     */
    public function sizeRange()
    {
        return [];
    }

    /**
     * 最多允许的文件数量
     * 默认不限制
     * @return int
     */
    public function maxNumber()
    {
        return 0;
    }

    /**
     * 生成新的文件名
     * 当参数 `$renameFile` 为true的时候生效
     * @param UploadedFile $value
     * @return string
     */
    public function generateNewName($value)
    {
        $a = QTTX::$app->snowflake->id();
        $b = $value->extension;
        return $a . '.' . $b;
    }

    /**
     * 自定义校验函数
     * @param UploadedFile $value
     * @return bool
     */
    public function customRule($value)
    {
        return true;
    }
}
