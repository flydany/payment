<?php

/* @var $this \admin\components\View */

use common\helpers\Render;
use common\models\AdminRole;

$this->addCrumbs('Manager');
$this->addCrumbs('Administrator Group List', 'admin/group-list');
$this->title = (isset($data['id']) ? 'Update' : 'Insert'). ' Administrator Group';
$this->setActiveNavigator('admin/group-list');

\admin\assets\CheckerAsset::register($this);
?>

<div class="contenter">
    <div class="alert alert-info" role="alert">
        <p><strong>Heads up!</strong></p>
        <p><?= $this->modifyNotice(Render::value($data, 'id')) ?></p>
    </div>
    <form id="info-detail" method="post" action="/admin/group-<?= isset($data['id']) ? 'update?id='.$data['id'] : 'insert' ?>">
        <div class="form-group checker">
            <label>permission group title</label>
            <input class="form-control" type="text" name="title" value="<?= Render::value($data, 'title') ?>" placeholder="identity.">
        </div>
        <div class="form-group checker">
            <label>identity</label>
            <input class="form-control" type="text" name="identity" value="<?= Render::value($data, 'identity') ?>" placeholder="identity.">
        </div>
        <div class="form-group checker">
            <label>remark</label>
            <textarea class="form-control" name="remark" placeholder="email."><?= Render::value($data, 'remark') ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary" id="save-button"><i class="fa fa-save fa-fw"></i>save</button>
        <textarea id="info-detail-json" data-form="#info-detail" style="display:none;"><?= AdminRole::checker() ?></textarea>
        <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken() ?>">
    </form>
</div>

<script>
    $(document).ready(function() {
        // 表单数据验证
        (new checker).init({ ruleDom: '#info-detail-json' });
    });
</script>