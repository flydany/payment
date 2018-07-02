<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Url;
use yii\helpers\Html;
use common\helpers\Render;

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <?= Html::csrfMetaTags() ?>
    <title><?= 'Checker 使用文档 '.Html::encode($this->title) ?></title>
    <?php // $this->head() ?>
    <link rel="stylesheet" href="<?= Url::to('@web/static/Font-Awesome-3.2.1/css/font-awesome.css') ?>">
    <link rel="stylesheet" href="<?= Url::to('@web/static/css/document.css') ?>">
    <link rel="stylesheet" href="<?= Render::static('flyer/flyer.css') ?>">
    <script src="<?= Render::static('jquery/jquery-2.0.3.min.js') ?>"></script>
    <script src="<?= Render::static('layer/layer.js') ?>"></script>
</head>
<body style="background-color:#e5e5e5;">
<?php $this->beginBody() ?>

<div class=“content” id="pg-contenter">
    <?= $content ?>
    <div class="clear"></div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
