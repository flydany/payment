<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Url;
use yii\helpers\Html;
use common\helpers\Render;
use common\models\Navigator;

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
    body {
        background-color:#2A3F54;
    }
    #left-panel {
        position:absolute;
        display:flex;
        padding:0;
        width:230px;
        min-height:100%;
    }
    #right-panel {
        margin-left:230px;
        padding:10px 20px 0;
        min-height:3200px;
        background-color:#FFF;
    }
    .breadcrumb {
        padding:8px 0;
        background:none;
        margin-bottom:10px;
    }
</style>
<div class="col-md-3" id="left-panel">
    <?= $this->render('left') ?>
</div>

<div id="right-panel">
    <div class="wrap">
        <ol class="breadcrumb">
            <li><i class="fa fa-home fa-fw"></i><a href="<?= Url::to('@web/welcome/index') ?>"><?= $this->context->module->name ?></a></li>
            <?php if( ! empty($this->crumbs)) {
                foreach($this->crumbs as $crumbs) {
                    if($crumbs['url']) {
                        echo "<li><a href=\"{$crumbs['url']}\">{$crumbs['title']}</a></li>";
                    }
                    else {
                        echo "<li>{$crumbs['title']}</li>";
                    }
                }
            } ?>
            <li><?= $this->title ?></li>
        </ol>
    </div>
    <div class="wrap">
        <?= $content ?>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
