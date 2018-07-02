<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = '异常操作';
?>
<style>
    body {
        background-color:#2A3F54;
    }
    h1 {
        font-size: 80px;
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

<div class="container">
    <!-- page content -->
    <div class="col-md-12">
        <div class="col-middle">
            <div class="text-center text-center">
                <h1 class="error-number">ERROR</h1>
                <h2><?= Html::encode($message) ?> <a href="#">Report this?</a></h2>
            </div>
        </div>
    </div>
    <!-- /page content -->
</div>