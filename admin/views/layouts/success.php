<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = '操作成功';
?>

<div class="box-content gap">
    <div class="warn success mb" style="text-align:center;">
        <p><i class="icon-ok icon-large"></i> Request Success</p>
        <p><?php echo $message ? $message : '操作成功（Request Success）！'; ?></p>
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
                echo '浏览器会在3秒后自动跳转，<a class="cl-white" href="'.$skip.'"><i class="icon-fighter-jet"></i> 点击跳转</a>';
            }
            echo '</p>';
        } ?>
    </div>
</div>

<?php if($skip && ! is_array($skip)) { ?>
<script>
    $(document).ready(function() {
        $('head').append('<meta http-equiv="refresh" content="3;url=<?= Html::encode($skip) ?>">');
    });
</script>
<?php } ?>