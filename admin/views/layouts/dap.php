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
    <title><?= $this->context->module->name.' '.Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<style>
    .alert-danger {
        color: #73879C;
        background-color: #2A3F54;
        border-color: #2A3F54;
    }
</style>
<div id="wrap">
    <?= $content ?>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
