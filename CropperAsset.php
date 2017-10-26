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
class CropperAsset extends AssetBundle {
	public $sourcePath = '@npm';
	public $css = [
		'cropper/dist/cropper.min.css'
	];
	public $js = [
		'cropper/dist/cropper.min.js'
	];
	public $depends = [
		'yii\web\JqueryAsset'
	];

	/**
	 * @inheritdoc
	 */
	public function init() {
		if (YII_DEBUG) {
			$this->js = [
				'cropper/dist/cropper.js'
			];
			$this->css = [
				'cropper/dist/cropper.css'
			];
		}

		parent::init();
	}
}
