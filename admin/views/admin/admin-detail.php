<?php

/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\helpers\Html;
use common\helpers\Render;
use common\models\Admin;

$this->addCrumbs('管理员列表', 'admin/admin-list');
$this->title = (isset($data['id']) ? '修改' : '添加'). '管理员';
?>

<div class="box-content gap">
    <div class="warn success mb">
        <p><i class="icon-info-sign icon-large"></i> 注意事项</p>
        <p>1、过期时间到了之后账号将处于禁用状态</p>
        <p>2、修改账号时填写表示修改密码</p>
    </div>
    <form class="flyer-form pane" id="flyer-create" method="post" action="<?= Url::to('@web/admin/admin-') ?><?= isset($data['id']) ? 'update?id='.$data['id'] : 'insert' ?>">
        <div class="form-item checker">
            <div class="input-title">过期时间</div>
            <div class="input-inline"><input class="flyer-input" flyer="date" type="text" name="end_date" value="<?= Render::value($data, 'end_date', date('Y-m-d', strtotime('+1 year'))) ?>" placeholder="end date."></div>
        </div>
        <div class="form-item">
            <div class="item-inline checker">
                <div class="input-title">登录帐号</div>
                <div class="input-inline"><input class="flyer-input" type="text" name="username" value="<?= Render::value($data, 'username') ?>" placeholder="login name." <?= isset($data['id']) ? 'readonly' : '' ?>></div>
            </div>
            <div class="item-inline checker">
                <div class="input-title">密码</div>
                <div class="input-inline"><input class="flyer-input" type="password" name="password_digest" value="" placeholder="password."></div>
            </div>
        </div>
        <div class="form-item checker">
            <div class="input-title">权限</div>
            <div class="input-block"><?= Render::select('role_id', $roles, Render::value($data, 'role_id'), ['prompt' => '不设所属组--', 'flyer' => 'select']) ?></div>
        </div>
        <div class="form-item">
            <div class="item-inline checker">
                <div class="input-title">姓名</div>
                <div class="input-inline"><input class="flyer-input" type="text" name="realname" value="<?= Render::value($data, 'realname') ?>" placeholder="realname."></div>
            </div>
            <div class="item-inline checker">
                <div class="input-title">手机</div>
                <div class="input-inline"><input class="flyer-input" type="text" name="mobile" value="<?= Render::value($data, 'mobile') ?>" placeholder="mobile."></div>
            </div>
        </div>
        <div class="form-item checker">
            <div class="input-title">邮箱</div>
            <div class="input-block"><input class="flyer-input" type="text" name="email" value="<?= Render::value($data, 'email') ?>" placeholder="email."></div>
        </div>
        <div class="form-item">
            <div class="input-block tr bdn"><button class="flyer-button normal border-round" id="save-button" type="submit"><i class="icon-save"></i> 保 存</button></div>
            <textarea id="flyer-create-json" data-form="#flyer-create" style="display:none;"><?= Admin::checker(isset($data['id']) ? 'update' : 'insert') ?></textarea>
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