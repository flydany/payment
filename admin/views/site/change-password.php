<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Url;
use common\helpers\Render;

$this->addCrumbs('个人中心');
$this->title = '修改密码';
?>

<div class="box-content gap">
    <form class="flyer-form pane" id="flyer-create" action="<?= Url::to('@web/welcome/change-password') ?>" method="post">
        <div class="form-item checker">
            <div class="input-title">旧密码</div>
            <div class="input-block"><input class="flyer-input" type="password" name="old" placeholder="旧密码."><span class="warn-span"></span></div>
        </div>
        <div class="form-item checker">
            <div class="input-title">新密码</div>
            <div class="input-block"><input class="flyer-input" type="password" name="new" placeholder="新密码."><span class="warn-span"></span></div>
        </div>
        <div class="form-item checker">
            <div class="input-title">重复密码</div>
            <div class="input-block"><input class="flyer-input" type="password" name="renew" placeholder="重复密码."><span class="warn-span"></span></div>
        </div>
        <div class="form-item">
            <div class="input-block bdn"><button class="flyer-button normal" id="save-button" type="submit"><i class="icon-save"></i> 保存</button></div>
            <textarea id="flyer-create-json" data-form="#flyer-create" style="display:none;"><?= $relate ?></textarea>
        </div>
        <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken() ?>">
    </form>
</div>

<script src="<?= Render::static('flyer/checker.class.js') ?>"></script>
<script>
    $(document).ready(function() {
        // 表单数据验证
        (new checker).init({ ruleDom: '#flyer-create-json' });
    });
</script>