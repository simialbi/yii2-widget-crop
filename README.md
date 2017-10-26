# yii2-widget-crop
Wrapper for [Image Cropper](http://fengyuanchen.github.io/cropper/) javascript library based on 
[demisang/yii2-cropper](https://github.com/demisang/yii2-cropper).

## Resources
 * [yii2](https://github.com/yiisoft/yii2) framework
 * [demisang/yii2-cropper](https://github.com/demisang/yii2-cropper).
 * Image [Cropper](http://fengyuanchen.github.io/cropper/)

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```sh
$ php composer.phar require --prefer-dist "simialbi/yii2-crop"
```
or add

```json
{
	"require": {
  		"simialbi/yii2-crop": "~1.0"
	}
}
```

to the `require` section of your `composer.json`


## Example Usage

```php
<?php
/* @var $this yii\web\View */
/* @var $image stdClass */

use simialbi\yii2\crop\Cropper;

$this->title = 'my example';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="my-example">
<?php
echo Cropper::widget([
	'type' => Cropper::TYPE_MODAL,
	'cropUrl' => ['my-module/image/crop', 'id' => $image->id],
	'image' => $image->src,
	'aspectRatio' => 16 / 9,
	'clientOptions' => [
		//see https://github.com/fengyuanchen/cropper/blob/master/README.md#options
		'minCropBoxWidth' => 1600,
		'minCropBoxHeight' => 900
	],
	'options' => [],
	'imageOptions' => [],
	'modalOptions' => [],
	'buttonOptions' => [
		'class' => ['btn', 'btn-default']
	],
	'buttonContent' => 'Crop {icon}',
	'buttonIcon' => '<span class="glyphicon glyphicon-scissors"></span>',
	'ajaxOptions' => [] //$.ajax properties
]);
?>
</div>
```

## License
**yii2-widget-crop** is released under MIT license. See bundled [LICENSE](LICENSE) for details.