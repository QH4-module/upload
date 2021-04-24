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
use qh4module\upload\models\ApplyOSSSignature;

trait TraitUploadController
{

    /**
     * @return ExtAliyunOSS
     */
    protected function ext_aliyun_oss()
    {
        //todo 该方法必须重写实现
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