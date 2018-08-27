<?php

/* @var $this \admin\components\View */
/* @var $content string */

use yii\helpers\Url;
use yii\helpers\Html;
use common\helpers\Render;

\admin\assets\AppAsset::register($this);
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
    <?php $this->head() ?>
    <link rel="stylesheet" href="<?= Render::static('css/document.css') ?>">
    <link rel="stylesheet" href="<?= Render::static('flyer/flyer.css') ?>">
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
