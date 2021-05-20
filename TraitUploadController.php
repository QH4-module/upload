<?php
/**
 * File Name: TraitUploadController.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/4/23 4:53 下午
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
use qh4module\upload\external\ExtUpload;
use qh4module\upload\models\ApplyOSSSignature;
use qh4module\upload\models\UploadModel;

trait TraitUploadController
{

    /**
     * @return ExtAliyunOSS
     */
    protected function ext_aliyun_oss()
    {
        //todo 该方法必须重写实现
    }


    protected function ext_upload()
    {
        return new ExtUpload();
    }

    /**
     * 上传单个文件到本地服务器
     * 该接口没有进行任何文件相关验证,不建议直接使用该接口上传,应该针对具体业务, 单独写 `ExtUpload` 控制类
     * @return array
     */
    public function actionUploadFile()
    {
        $model = new UploadModel([
            'external'=>$this->ext_upload()
        ]);

        return $this->runModel($model);
    }


    /**
     * 申请前端直传oss用的签名
     * @return array
     */
    public function actionApplyOSSSignature()
    {
        $model = new ApplyOSSSignature([
            'external' => $this->ext_aliyun_oss(),
        ]);

        return $this->runModel($model);
    }
}