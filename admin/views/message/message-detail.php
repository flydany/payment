<?php

/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use admin\helpers\Render;
use common\models\Message;

$this->addCrumbs('站内信列表', 'message/message-list');
$this->title = (isset($data['id']) ? '修改' : '添加'). '站内信';
?>

<form class="box-content flyer-form pane gap" id="flyer-create" flyer="tabs" action="<?= Url::to('@web/message/message-'.(isset($data['id']) ? 'update?id='.$data['id'] : 'insert')) ?>" method="post">
    <div class="form-item checker">
        <div class="input-title">标题</div>
        <div class="input-block"><input class="flyer-input" type="text" name="title" value="<?= Render::value($data, 'title') ?>" placeholder="title."></div>
    </div>
    <div class="form-item">
        <div class="item-inline checker">
            <div class="input-title">收件人</div>
            <div class="input-inline"><input class="flyer-input" type="text" name="receiver_id" value="<?= Render::value($data, 'receiver_id') ?>" placeholder="receiver id."></div>
        </div>
    </div>
    <div class="form-item">
        <div class="item-inline checker">
            <div class="input-title">类型</div>
            <div class="input-inline"><?= Render::select('type', Message::$typeSelector, Render::value($data, 'type'), ['flyer' => 'select']) ?></div>
        </div>
        <div class="item-inline checker">
            <div class="input-title">读取状态</div>
            <div class="input-inline"><?= Render::select('status', Message::$statusSelector, Render::value($data, 'status'), ['flyer' => 'select']) ?></div>
        </div>
    </div>
    <div class="form-item item-text checker">
        <div class="input-title">消息体</div>
        <div class="input-block"><textarea class="flyer-textarea" name="content" placeholder="content."><?= Render::value($data, 'content') ?></textarea></div>
    </div>
    <div class="form-item">
        <div class="input-block tr bdn"><button class="flyer-button normal border-round" id="save-button" type="submit"><i class="icon-save"></i> 保 存</button></div>
        <textarea id="flyer-create-json" data-form="#flyer-create" style="display:none;"><?= Message::checker(isset($data['id']) ? 'update' : 'insert') ?></textarea>
    </div>
    <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken() ?>">
</form>

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