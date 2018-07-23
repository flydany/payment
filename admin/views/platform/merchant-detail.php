<?php

/* @var $this \admin\components\View */

use common\helpers\Render;
use common\models\Platform;
use common\models\Merchant;


$this->addCrumbs('Platform');
$this->addCrumbs('Merchant List', 'platform/merchant-list');
$this->title = (isset($data['id']) ? 'Update' : 'Insert'). ' Platform';
$this->setActiveNavigator('platform/merchant-list');

$this->registerJavascript('@static/flyer/checker.class.js');
?>

<div class="contenter">
    <div class="alert alert-info" role="alert">
        <p><strong>Heads up!</strong></p>
        <p><?= $this->modifyNotice(Render::value($data, 'id')) ?></p>
        <p>1. the account will be disabled after the expiration date.</p>
    </div>
    <form id="info-detail" method="post" action="/platform/merchant-<?= isset($data['id']) ? 'update?id='.$data['id'] : 'insert' ?>">
        <div class="form-group checker">
            <label>title</label>
            <input class="form-control" type="text" name="title" value="<?= Render::value($data, 'title') ?>" placeholder="title">
        </div>
        <div class="form-row">
            <div class="form-group col-md-6 checker">
                <label>status</label>
                <input class="form-control" type="text" name="merchant_number" value="<?= Render::value($data, 'merchant_number') ?>" placeholder="merchant number">
            </div>
            <div class="form-group col-md-6 checker">
                <label>status</label>
                <input class="form-control" type="text" name="title" value="<?= Render::value($data, 'title') ?>" placeholder="platform name.">
            </div>
        </div>
        <div class="form-group checker">
            <label>title</label>
            <input class="form-control" type="text" name="domain" value="<?= Render::value($data, 'domain') ?>" placeholder="api domain.">
        </div>
        <div class="form-group checker">
            <label>public key</label>
            <textarea class="form-control" name="public_key" placeholder="public key."><?= Render::value($data, 'public_key') ?></textarea>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6 checker">
                <label>status</label>
                <?= Render::select('status', Merchant::$statusSelector, Render::value($data, 'status')) ?>
            </div>
            <div class="form-group col-md-6 checker">
                <label>status</label>
                <?= Render::select('status', Merchant::$statusSelector, Render::value($data, 'status')) ?>
            </div>
        </div>
        <div class="form-group checker">
            <label>remark</label>
            <textarea class="form-control" name="remark" placeholder="remark."><?= Render::value($data, 'remark') ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary" id="save-button"><i class="fa fa-save fa-fw"></i>save</button>
        <textarea id="info-detail-json" data-form="#info-detail" style="display:none;"><?= Merchant::checker() ?></textarea>
        <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken() ?>">
    </form>
</div>

<script>
    $(document).ready(function() {
        // 表单数据验证
        (new checker).init({ ruleDom: '#info-detail-json' });
    });
</script>