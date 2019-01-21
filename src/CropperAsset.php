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
	public $sourcePath = '@npm/cropper/dist';
	public $css = [
		'cropper.min.css'
	];
	public $js = [
		'cropper.min.js'
	];
	public $depends = [
		'yii\web\JqueryAsset'
	];

	/**
	 * {@inheritdoc}
	 */
	public function init() {
		if (YII_DEBUG) {
			$this->js = [
				'cropper.js'
			];
			$this->css = [
				'cropper.css'
			];
		}

		parent::init();
	}
}
