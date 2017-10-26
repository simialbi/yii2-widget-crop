<?php

namespace simialbi\yii2\crop;

use yii\base\Widget;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use Yii;

/**
 * This widget renders an image cropper either in a model with TYPE_MODAL or inline with TYPE_INLINE or as collapsible
 * (TYPE_BUTTON)
 *
 * ```php
 * Cropper::widget([
 *      'type' => Cropper::TYPE_MODAL,
 *      'cropUrl' => ['my-module/image/crop', 'id' => $image->id],
 *      'image' => $image->src,
 *      'aspectRatio' => 16 / 9,
 *      'clientOptions' => [
 *          //see https://github.com/fengyuanchen/cropper/blob/master/README.md#options
 *          'minCropBoxWidth' => 1600,
 *          'minCropBoxHeight' => 900
 *      ],
 *      'options' => [],
 *      'imageOptions' => [],
 *      'modalOptions' => [],
 *      'buttonOptions' => [
 *          'class' => ['btn', 'btn-default']
 *      ],
 *      'buttonContent' => 'Crop {icon}',
 *      'buttonIcon' => '<span class="glyphicon glyphicon-scissors"></span>',
 *      'ajaxOptions' => [] //$.ajax properties
 * ]);
 * ```
 *
 * @author Ivan Orlov <orlov_mail@mail.ru>
 * @author Simon Karlen <simi.albi@gmail.com>
 * @since 1.0
 */
class Cropper extends Widget {
	const TYPE_MODAL = 'modal';
	const TYPE_INLINE = 'inline';
	const TYPE_BUTTON = 'button';

	/**
	 * @var string URL for send crop data
	 */
	public $cropUrl;

	/**
	 * @var string Original image URL
	 */
	public $image;

	/**
	 * @var float Aspect ratio for crop box. If not set(null) - it means free aspect ratio
	 */
	public $aspectRatio;

	/**
	 * @var string display type of cropper
	 */
	public $type = self::TYPE_MODAL;

	/**
	 * @var array Modal view options
	 */
	public $modalOptions = [];

	/**
	 * @var array Button view options
	 */
	public $buttonOptions = [];

	/**
	 * @var array HTML widget options
	 */
	public $options = [];

	/**
	 * @var array HTML-options for image tag
	 */
	public $imageOptions = [];

	/**
	 * @var string Text to show in button
	 */
	public $buttonContent = 'Crop {icon}';

	/**
	 * @var string Icon to show in button
	 */
	public $buttonIcon = '<i class="glyphicon glyphicon-scissors"></i>';

	/**
	 * @var array Additional cropper options
	 * @see https://github.com/fengyuanchen/cropper/blob/master/README.md#options
	 */
	public $clientOptions = [];

	/**
	 * @var array Ajax options for send crop-requests
	 */
	public $ajaxOptions = [];

	/**
	 * @var array Default HTML-options for image tag
	 */
	private $_defaultImageOptions = [
		'class' => 'cropper-image img-responsive',
		'alt'   => 'crop-image'
	];

	/**
	 * @var array Default cropper options
	 * @see https://github.com/fengyuanchen/cropper/blob/master/README.md#options
	 */
	private $_defaultClientOptions = [
		'strict'           => true,
		'autoCropArea'     => 1,
		'checkImageOrigin' => false,
		'checkCrossOrigin' => false,
		'checkOrientation' => false,
		'zoomable'         => false
	];

	/**
	 * @inheritdoc
	 */
	public function init() {
		if (!isset($this->options['id'])) {
			$this->options['id'] = $this->getId();
		}
		if (empty($this->modalOptions)) {
			$this->modalOptions = [
				'header' => Html::tag('h3', Yii::t('simialbi/crop/cropper', 'Select crop area and click "crop" button'), [
					'class' => 'modal-title'
				])
			];
		}

		$this->registerTranslations();

		parent::init();
	}

	/**
	 * @inheritdoc
	 */
	public function run() {
		$imageOptions = ArrayHelper::merge($this->_defaultImageOptions, $this->imageOptions);

		ob_start();

		switch ($this->type) {
			case self::TYPE_MODAL:
			case self::TYPE_BUTTON:
				$buttonOptions = ArrayHelper::merge([
					'class' => 'btn btn-primary'
				], $this->buttonOptions, [
					'id'   => '#'.$this->options['id'].'-btn',
					'data' => [
						'toggle' => ($this->type === self::TYPE_MODAL) ? 'modal' : 'collapse',
						'target' => '#'.$this->options['id'].'-target'
					]
				]);

				if ($this->type === self::TYPE_BUTTON) {
					$buttonOptions['aria-expanded'] = 'false';
					$buttonOptions['aria-controls'] = $this->options['id'].'-target';
				}

				echo Html::a(
					Yii::t('simialbi/crop/cropper', $this->buttonContent, ['icon' => $this->buttonIcon]),
					['#'.$this->options['id']],
					$buttonOptions
				);

				if ($this->type === self::TYPE_MODAL) {
					$modalOptions       = $this->modalOptions;
					$modalOptions['id'] = $this->options['id'].'-target';
					Modal::begin($modalOptions);
				} elseif ($this->type === self::TYPE_BUTTON) {
					echo Html::beginTag('div', [
						'id'    => $this->options['id'].'-target',
						'class' => 'collapse'
					]);
				}

			case self::TYPE_INLINE:
				echo Html::beginTag('div', ['class' => 'crop-image-container']);
				echo Html::img($this->image, $imageOptions);
				echo Html::endTag('div');
				if ($this->type === self::TYPE_INLINE) {
					break;
				}

			case self::TYPE_MODAL:
				Modal::end();
				break;
			case self::TYPE_BUTTON:
				echo Html::endTag('div');
				break;
		}
		$content = ob_get_clean();

		$this->registerPlugin();

		return $content;
	}

	/**
	 * Init translations
	 */
	protected function registerTranslations() {
		Yii::$app->i18n->translations['simialbi/crop*'] = [
			'class'          => 'yii\i18n\GettextMessageSource',
			'sourceLanguage' => 'en-US',
			'basePath'       => __DIR__.'/messages'
		];
	}

	/**
	 * Registers the assets and builds the required js for the widget
	 */
	protected function registerPlugin() {
		$id   = $this->options['id'];
		$view = $this->getView();

		CropperAsset::register($view);

		$clientOptions = ArrayHelper::merge($this->_defaultClientOptions, $this->clientOptions);
		$selector      = "#$id .crop-image-container > img";
		$js            = '';

		if (!empty($this->aspectRatio)) {
			$clientOptions['aspectRatio'] = $this->aspectRatio;
		}

		switch ($this->type) {
			case self::TYPE_MODAL:
				$js = <<<JS
var modal = jQuery('#$id'),
	image = jQuery('#$selector');

modal.on({
	'shown.bs.modal': function () {
		image.cropper($clientOptions);
	},
	'hidden.bs.modal': function () {
		image.cropper(destroy);
	}
});
JS;
				break;
			case self::TYPE_BUTTON:
			case self::TYPE_INLINE:
				$js = <<<JS
var image = jQuery('#$selector');
image.cropper($clientOptions);
JS;

				break;
		}

		if ($this->cropUrl) {
			$ajaxOptions = Json::encode(ArrayHelper::merge([
				'url'      => Url::to($this->cropUrl),
				'method'   => 'POST',
				'data'     => new JsExpression("image.cropper('getData')"),
				'dataType' => 'JSON'
			], $this->ajaxOptions));
			$js          .= <<<JS
image.on('crop', function() {
	jQuery.ajax($ajaxOptions);
});
JS;
		}

		$view->registerJs($js);
	}
}