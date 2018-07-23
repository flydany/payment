<?php

/* @var $this \admin\components\View */

use common\helpers\Render;
use common\models\Admin;
use common\models\AdminRole;

$this->addCrumbs('Manager');
$this->addCrumbs('Administrator List', 'admin/list');
$this->title = (isset($data['id']) ? 'Update' : 'Insert'). ' Administrator';
$this->setActiveNavigator('admin/list');

\admin\assets\CheckerAsset::register($this);
?>

<div class="contenter">
    <div class="alert alert-info" role="alert">
        <p><strong>Heads up!</strong></p>
        <p><?= $this->modifyNotice(Render::value($data, 'id')) ?></p>
        <p>1. the account will be disabled after the expiration date.</p>
        <p>2. fill in the password to modify the password.</p>
    </div>
    <form id="info-detail" method="post" action="/admin/<?= isset($data['id']) ? 'update?id='.$data['id'] : 'insert' ?>">
        <div class="form-group checker">
            <label>username</label>
            <input class="form-control" type="text" name="username" value="<?= Render::value($data, 'username') ?>" placeholder="login name." <?= isset($data['id']) ? 'readonly' : '' ?>>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6 checker">
                <label>password</label>
                <input class="form-control" type="password" name="password_digest" value="" placeholder="password.">
            </div>
            <div class="form-group col-md-6 checker">
                <label>effect date</label>
                <input class="form-control" type="text" name="effect_date" value="<?= Render::value($data, 'effect_date', date('Y-m-d', strtotime('+1 year'))) ?>" placeholder="end date.">
            </div>
        </div>
        <!--div class="form-group checker selecter">
            <label>permission group</label>
            <?//= Render::select('role_id', AdminRole::identitySelector(), Render::value($data, 'role_id'), ['prompt' => '--']) ?>
        </div-->
        <div class="form-row">
            <div class="form-group col-md-6 checker">
                <label>realname</label>
                <input class="form-control" type="text" name="realname" value="<?= Render::value($data, 'realname') ?>" placeholder="realname.">
            </div>
            <div class="form-group col-md-6 checker">
                <label>mobile</label>
                <input class="form-control" type="text" name="mobile" value="<?= Render::value($data, 'mobile') ?>" placeholder="mobile.">
            </div>
        </div>
        <div class="form-group checker">
            <label>email</label>
            <input class="form-control" type="text" name="email" value="<?= Render::value($data, 'email') ?>" placeholder="email.">
        </div>
        <button type="submit" class="btn btn-primary" id="save-button"><i class="fa fa-save fa-fw"></i>save</button>
        <textarea id="info-detail-json" data-form="#info-detail" style="display:none;"><?= Admin::checker(isset($data['id']) ? 'update' : 'insert') ?></textarea>
        <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken() ?>">
    </form>
</div>

<script>
    $(document).ready(function() {
        // 表单数据验证
        (new checker).init({ ruleDom: '#info-detail-json' });
    });
</script>