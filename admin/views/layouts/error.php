<?php

/* @var $this \admin\components\View */

use yii\helpers\Html;

$this->title = 'Operation Failed';
?>
<style>
    h1 {
        font-size: 60px;
        line-height: 90px;
        margin: 20px 0;
    }
    h2 {
        font-size: 16px;
        font-weight: 400;
    }
    p {
        margin: 0 0 10px;
    }
    a {
        color:#5A738E;
    }
</style>

<div class="container-fluid alert alert-danger" role="alert">
    <!-- page content -->
    <div class="text-center">
        <h1 class="error-number">ERROR</h1>
        <h2><?= Html::encode($message) ?></h2>
        <p>the page will automatically jump after 3 seconds, <a href="<?= $skip ?>"><i class="fa fa-hand-pointer-o fa-fw"></i>click here?</a></p>
    </div>
    <!-- /page content -->
</div>