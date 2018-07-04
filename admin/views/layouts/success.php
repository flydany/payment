<?php

/* @var $this \admin\components\View */

use yii\helpers\Html;

$this->title = 'Successful Operation';
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
        margin: 10px 0;
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
    <div class="alert alert-success">
        <div class="text-center">
            <h1 class="success-number">SUCCESS</h1>
            <p><?= Html::encode($message) ?> Congratulations!</p>
            <?php
            if($skip) {
                echo '<p>';
                if(is_array($skip)) {
                    foreach($skip as $k => $web) {
                        echo $k ? '　　' : '';
                        echo '<a href="'.$web['url'].'"><i class="fa fa-hand-pointer-o fa-fw"></i>'.$web['title'].'</a>';
                    }
                }
                else {
                    echo 'the page will automatically jump after 3 seconds, <a href="'.$skip.'"><i class="fa fa-hand-pointer-o fa-fw"></i>click here</a>';
                }
                echo '</p>';
            } ?>
        </div>
    </div>
    <!-- /page content -->
</div>