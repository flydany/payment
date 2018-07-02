<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;

?>

<div id="pg-contenter">
    <div class="box-content gap">
        <div class="warn notice mb" style="text-align:center;">
            <p><i class="icon-warning-sign icon-large"></i> Select Skip Request</p>
            <p><?= Html::encode($message); ?></p>
            <?php 
            if($skip) {
                echo '<p>';
                if(is_array($skip)) {
                    foreach($skip as $k => $web) {
                        if($k) {
                            echo '&nbsp;&nbsp;<i class="icon-double-angle-right icon-large"></i>&nbsp;';
                        }
                        echo '<a class="cl-white" href="'.$web['url'].'"><i class="icon-fighter-jet"></i> '.$web['title'].'</a>';
                    }
                }
                else {
                    echo '请点击左侧A标签，进行页面跳转，<a class="cl-white" href="'.$skip.'"><i class="icon-fighter-jet"></i> 点击跳转</a>';
                }
                echo '</p>';
            } ?>
        </div>
    </div>
</div>