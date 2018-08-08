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
        padding:15px;
    }
    #admin-info {
        height:50px;
        line-height:50px;
        padding:0 15px;
        cursor:pointer;
    }
    #admin-info img {
        height:30px;
        line-height:30px;
        cursor:pointer;
        margin-right:5px;
    }
    #admin-info:hover,
    #top-panel .dropdown.open #admin-info {
        background-color:#D9DEE4;
    }
    #top-panel .dropdown-menu {
        margin-top:1px;
        border-top:0;
        border-top-left-radius:0;
        border-top-right-radius:0;
    }
    #top-panel .dropdown-menu li i {
        margin-top:3px;
    }
</style>
<div class="col-xs-3" id="left-panel">
    <?= $this->render('left') ?>
</div>

<div id="right-panel">
    <div id="top-panel">
        <div class="dropdown pull-right">
            <div class="dropdown-toggle" id="admin-info" data-toggle="dropdown">
                <img src="<?= $this->admin->picture ?>" class="img-circle">
                <?= $this->admin->username ?>
                <span class="caret"></span>
            </div>
            <ul class="dropdown-menu pull-right" aria-labelledby="admin-info">
                <li><a href="#">Setting</a></li>
                <li><a href="#">Information</a></li>
                <li><a href="#">Reset Password</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="/site/logout"><i class="fa fa-sign-out float-right"></i>Logout</a></li>
            </ul>
        </div>
        <!--span id="sidebar-toggle"><i class="fa fa-bars"></i></span-->
        <ul class="breadcrumb">
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
        </ul>
    </div>
    <div class="wrap">
        <?= $content ?>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
