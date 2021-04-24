<?php
/**
 * File Name: UploadModel.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021-01-14 10:46 上午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\upload\models;


use QTTX;
use qttx\basic\Loader;
use qttx\helper\FileHelper;
use qttx\web\ServiceModel;
use qttx\web\UploadedFile;
use qh4module\upload\external\ExtUpload;

class UploadModel extends ServiceModel
{
    /**
     * @inheritDoc
     */
    protected $autoAssign = false;

    /**
     * @inheritDoc
     */
    protected $autoParams = false;

    /**
     * 配置扩展
     * @var ExtUpload
     */
    protected $external;

    /**
     * @var UploadedFile
     */
    public $file;

    /**
     * @var UploadedFile[]
     */
    public $files;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        if ($this->external->allowMultipleFiles) {
            $this->files = QTTX::$request->getFilesByName($this->external->field);
        } else {
            $this->file = QTTX::$request->getFileByName($this->external->field);
        }
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        if ($this->external->allowMultipleFiles) {
            $rules = [
                [['files'], 'required', ['message' => '{attribute}必须上传']],
                [['files'], 'array', ['type' => 'fileRequiredRule', 'message' => '{attribute}必须上传']],
                [['files'], 'fileNumMaxRule'],
                [['files'], 'array', ['type' => 'extensionRule']],
                [['files'], 'array', ['type' => 'mineTypeRule']],
                [['files'], 'array', ['type' => 'sizeRule']],
                [['files'], 'array', ['type' => 'customRule']],
            ];
        } else {
            $rules = [
                [['file'], 'required', ['message' => '{attribute}必须上传']],
                [['file'], 'fileRequiredRule', ['message' => '{attribute}必须上传']],
                [['file'], 'extensionRule'],
                [['file'], 'mineTypeRule'],
                [['file'], 'sizeRule'],
                [['file'], 'customRule'],
            ];
        }

        return $this->mergeRules($rules, $this->external->rules());
    }

    /**
     * @inheritDoc
     */
    public function attributeLangs()
    {
        return $this->mergeLanguages(['zh_cn' => [
            'file' => '文件',
            'files' => '文件',
        ]], $this->external->attributeLangs());
    }

    /**
     * 自定义校验
     * @param UploadedFile $value
     * @return bool
     */
    public function customRule($value)
    {
        return $this->external->customRule($value);
    }

    /**
     * 验证文件必须上传
     * @param UploadedFile $value
     * @return bool
     */
    public function fileRequiredRule($value)
    {
        if (!$value || !$value instanceof UploadedFile || $value->error == UPLOAD_ERR_NO_FILE) {
            return false;
        }

        switch ($value->error) {
            case UPLOAD_ERR_OK:
                return true;
            case UPLOAD_ERR_INI_SIZE:
                return '{attribute}文件太大，不能超过' . FileHelper::convertSuitableUnit(ini_get('upload_max_filesize'));
            case UPLOAD_ERR_FORM_SIZE:
                return '{attribute}文件太大';
            case UPLOAD_ERR_PARTIAL:
                return '{attribute}文件上传不完整';
            case UPLOAD_ERR_NO_TMP_DIR:
                return '上传失败，找不临时文件';
            case UPLOAD_ERR_CANT_WRITE:
                return '上传失败，文件写入失败';
            default:
                return '文件上传失败';
        }
    }

    /**
     * 验证上传文件数量,只对多上传有效
     * @param $value
     * @return bool|string
     */
    public function fileNumMaxRule($value)
    {
        if ($this->external->allowMultipleFiles) {
            $max = $this->external->maxNumber();
            if ($max > 0 && sizeof($this->files) > $max) {
                return "{attribute}最多上传{$max}个文件";
            }
        }
        return true;
    }

    /**
     * 验证文件后缀名
     * @param UploadedFile $value
     * @return bool
     */
    public function extensionRule($value)
    {
        if ($es = $this->external->enableExtensions()) {
            $es = array_map(function ($item) {
                return strtolower($item);
            }, $es);
            if (!empty($es) && !in_array(strtolower($value->extension), $es)) {
                return '{attribute}的文件类型无效，仅支持' . implode('、',$es) . '后缀';
            }
        }
        return true;
    }

    /**
     * 验证文件mine类型
     * @param UploadedFile $value
     * @return bool
     */
    public function mineTypeRule($value)
    {
        if ($emt = $this->external->enableMineType()) {
            $emt = array_map(function ($item) {
                return strtolower($item);
            }, $emt);
            if (!empty($emt) && !in_array(strtolower($value->mime), $emt)) {
                return '{attribute}的文件类型无效，仅支持' . implode('、',$emt) . '类型';
            }
        }
        return true;
    }

    /**
     * 验证文件大小
     * @param UploadedFile $value
     * @return bool
     */
    public function sizeRule($value)
    {
        $size = $this->external->sizeRange();
        $min = isset($size[0]) ? intval($size[0]) : 0;
        $max = isset($size[1]) ? intval($size[1]) : 0;
        if ($min > 0 && $value->size < $min) {
            return '{attribute}文件太小，不能小于' . FileHelper::convertSuitableUnit($min);
        }
        if ($max > 0 && $value->size > $max) {
            return '{attribute}文件太大，不能超过' . FileHelper::convertSuitableUnit($max);
        }
        return true;
    }


    public function run()
    {
        if ($this->external->allowMultipleFiles) {
            return $this->saveMultipleFiles();
        } else {
            return $this->saveSingleFile($this->file);
        }
    }

    /**
     * 保存单个文件
     * @param UploadedFile $file
     * @return string
     */
    private function saveSingleFile($file)
    {
        // 格式化路径
        $path = $this->external->saveFolder($this->file);
        $path = Loader::getAlias($path);
        $path = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        FileHelper::mkdir($path);
        // 格式化文件名
        $filename = $file->name;
        if ($this->external->renameFile) {
            $filename = $this->external->generateNewName($file);
        }
        // 文件全路径
        $filepath = $path . $filename;

        $file->safeSaveAs($filepath, true);

        return $this->external->formatResultPath($filepath);
    }

    private function saveMultipleFiles()
    {
        $resp = [];
        foreach ($this->files as $file) {
            $resp[] = $this->saveSingleFile($file);
        }
        return $resp;
    }
}
