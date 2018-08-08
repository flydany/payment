<?php

/* @var $this \admin\components\View */
/* @var $content string */

use yii\helpers\Html;

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
    #left-panel {
        position:absolute;
        display:flex;
        padding:0;
        width:230px;
        min-height:100%;
    }
    #right-panel {
        margin-left:230px;
        min-height:1000px;
        padding-bottom:35px;
        background-color:#FFF;
    }
    #right-panel .contenter {
        padding:0 15px;
    }
    #top-panel {
        background: #EDEDED;
        border-bottom: 1px solid #D9DEE4;
        margin-bottom: 15px;
        padding:10px 0;
        width: 100%;
    }
    #sidebar-toggle {
        float:left;
        font-size:26px;
        padding:4px 15px 0 15px;
        cursor:pointer;
    }
    .breadcrumb {
        background-color:#EDEDED;
        margin-bottom:0;
    }
</style>
<div class="col-xs-3" id="left-panel">
    <?= $this->render('left') ?>
</div>

<div id="right-panel">
    <div class="wrap" id="top-panel">
        <!--span id="sidebar-toggle"><i class="fa fa-bars"></i></span-->
        <ol class="breadcrumb">
            <li><i class="fa fa-home fa-fw"></i><a href="/site/index"><?= $this->context->module->name ?></a></li>
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
