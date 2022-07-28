<?php

namespace simialbi\yii2\crop;

use yii\web\AssetBundle;

/**
 * CropperAsset
 *
 * @author Ivan Orlov <orlov_mail@mail.ru>
 * @author Simon Karlen <simi.albi@gmail.com>
 * @since 1.0
 */
class CropperAsset extends AssetBundle
{
    public $sourcePath = '@bower';
    public $css = [
        'cropperjs/dist/cropper.min.css'
    ];
    public $js = [
        'cropperjs/dist/cropper.min.js',
        'jquery-cropper/dist/jquery-cropper.min.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset'
    ];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if (YII_DEBUG) {
            foreach ($this->js as $k => $js) {
                $this->js[$k] = str_replace('.min', '', $js);
            }
            foreach ($this->css as $k => $css) {
                $this->css[$k] = str_replace('.min', '', $css);
            }
        }

        parent::init();
    }
}
