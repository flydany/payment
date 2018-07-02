<?php

/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\helpers\Render;
use common\models\AdminRole;

$this->addCrumbs('权组列表', 'admin/admin-list');
$this->title = (isset($data['id']) ? '修改' : '添加'). '权限组';
?>

<div class="box-content gap">
    <form class="flyer-form pane" id="flyer-create" action="<?= Url::to('@web/admin/admin-role-'.(isset($data['id']) ? 'update?id='.$data['id'] : 'insert')) ?>" method="post">
        <div class="form-item checker">
            <div class="input-title">名称</div>
            <div class="input-block"><input class="flyer-input" type="text" name="title" value="<?= Render::value($data, 'title') ?>" placeholder="title."></div>
        </div>
        <div class="form-item checker">
            <div class="input-title">标识</div>
            <div class="input-block"><input class="flyer-input" type="text" name="identity" value="<?= Render::value($data, 'identity') ?>" placeholder="identity."></div>
        </div>
        <div class="form-item item-text checker">
            <div class="input-title">备注</div>
            <div class="input-block"><textarea class="flyer-textarea" name="remark" placeholder="remark."><?= Render::value($data, 'remark') ?></textarea></div>
        </div>
        <div class="form-item">
            <div class="input-block tr bdn"><button class="flyer-button normal border-round" id="save-button" type="submit"><i class="icon-save"></i> 保 存</button></div>
            <textarea id="flyer-create-json" data-form="#flyer-create" style="display:none;"><?= AdminRole::checker(isset($data['id']) ? 'update' : 'insert') ?></textarea>
        </div>
        <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken() ?>">
    </form>
</div>

<script src="<?= Render::static('flyer/flyer.class.js') ?>"></script>
<script src="<?= Render::static('flyer/checker.class.js') ?>"></script>
<script>
    $(document).ready(function() {
        // Form 元素初始化
        (new flyer).init({ form: '#flyer-create' });

        // 表单数据验证
        (new checker).init({ ruleDom: '#flyer-create-json' });
    });
</script>