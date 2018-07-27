<?php

/* @var $this \admin\components\View */

use common\helpers\Render;
use common\models\Platform;
use common\models\Merchant;


$this->addCrumbs('Platform');
$this->addCrumbs('Merchant List', 'platform/merchant-list');
$this->title = (isset($data['id']) ? 'Update' : 'Insert'). ' Platform';
$this->setActiveNavigator('platform/merchant-list');

\admin\assets\CheckerAsset::register($this);
\admin\assets\UploaderFileAsset::register($this);
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
            <div class="form-group col-xs-3 checker">
                <label>platform</label>
                <?= Render::select('platform_id', Platform::$platformSelector, Render::value($data, 'platform_id'), ['class' => 'picker']) ?>
            </div>
            <div class="form-group col-xs-6 checker">
                <label>merchant number</label>
                <input class="form-control" type="text" name="merchant_number" value="<?= Render::value($data, 'merchant_number') ?>" placeholder="merchant number">
            </div>
            <div class="form-group col-xs-3 checker">
                <label>payment type</label>
                <?= Render::select('paytype', Platform::$paytypeSelector, Render::value($data, 'paytype'), ['prompt' => '--', 'class' => 'picker']) ?>
            </div>
        </div>
        <div class="form-group checker">
            <label>api domain</label>
            <input class="form-control" type="text" name="domain" value="<?= Render::value($data, 'domain') ?>" placeholder="api domain">
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">merchant private key</div>
            <div class="panel-body pb-zero">
                <div class="form-row">
                    <div class="form-group col-xs-6 checker">
                        <label>key type</label>
                        <?= Render::select('private_type', Merchant::$privateTypeSelector, Render::value($data, 'private_type'), ['prompt' => '--', 'picker']) ?>
                    </div>
                    <div class="form-group col-xs-6 checker">
                        <label>key password</label>
                        <input class="form-control" type="text" name="private_password" value="<?= Render::value($data, 'private_password') ?>" placeholder="private key password">
                    </div>
                </div>
                <div class="form-group" id="private-file"></div>
                <div class="form-group checker">
                    <label>key string</label>
                    <textarea class="form-control" name="private_key" placeholder="private key."><?= Render::value($data, 'private_key') ?></textarea>
                </div>
            </div>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">platform public key</div>
            <div class="panel-body pb-zero">
                <div class="form-group" id="public-file"></div>
                <div class="form-group checker">
                    <label>key string</label>
                    <textarea class="form-control" name="public_key" placeholder="public key."><?= Render::value($data, 'public_key') ?></textarea>
                </div>
            </div>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">merchant parameters<a class="btn btn-default btn-sm ml-15px" id="insert-parameter"><i class="fa fa-plus fa-fw"></i>insert</a><span class="text-danger"><i class="fa fa-long-arrow-left fa-fw ml-15px"></i>click this button to add a new parameter</span></div>
            <div class="panel-body pb-zero" id="parameters-list"></div>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">fee</div>
            <div class="panel-body pb-zero">
                <div class="form-row">
                    <div class="form-group col-xs-6 checker">
                        <label>rate</label>
                        <input class="form-control" type="text" name="rate" value="<?= Render::value($data, 'rate') ?>" placeholder="rate">
                    </div>
                    <div class="form-group col-xs-6 checker">
                        <label>base fee</label>
                        <input class="form-control" type="text" name="base_fee" value="<?= Render::value($data, 'base_fee') ?>" placeholder="base fee">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-xs-6 checker">
                        <label>min fee</label>
                        <input class="form-control" type="text" name="min" value="<?= Render::value($data, 'min') ?>" placeholder="min fee">
                    </div>
                    <div class="form-group col-xs-6 checker">
                        <label>max fee</label>
                        <input class="form-control" type="text" name="max" value="<?= Render::value($data, 'max') ?>" placeholder="max fee">
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group checker">
            <label>status</label>
            <?= Render::select('status', Merchant::$statusSelector, Render::value($data, 'status'), ['class' => 'picker']) ?>
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

<div id="parameter-template" style="display:none;">
    <div class="form-group form-row parameter">
        <div class="input-group col-xs-3 checker">
            <label class="input-group-addon">name</label>
            <input class="form-control" type="text" name="parameter_name[]" value="" placeholder="parameter name">
        </div>
        <div class="input-group col-xs-8 checker">
            <span class="input-group-addon">value</span>
            <input class="form-control" type="text" name="parameter_value[]" value="" placeholder="parameter value">
        </div>
        <div class="col-xs-1"><a class="label label-danger" onclick="deleteParameter(this);" href="javascript:;"><i class="fa fa-close fa-fw"></i>delete</a></div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // 表单数据验证
        (new checker).init({ ruleDom: '#info-detail-json' });

        <?php
        if( ! empty($data->parameters)) {
        foreach(json_decode($data->parameters, true) as $name => $value) {
        ?>
        insertParameter('<?= $name ?>', '<?= $value ?>');
        <?php
        } }
        ?>
        $('#insert-parameter').bind('click', function() {
            insertParameter();
        });
        $('#insert-parameter').click();

        // 初始化 私钥文件上传插件
        (new loaderFile()).init({
            conter: '#private-file',
            action: '/platform/file-encoder',
            onSuccess: function (file, response) {
                response = $.parseJSON(response);
                $('textarea[name=private_key]').val(response.reader);
                $('select[name=private_type]').val(response.ext).change();
            },
            onFailure: function (file, response) {
                response = $.parseJSON(response);
                BootstrapDialog.alert({ type: BootstrapDialog.TYPE_DANGER, message: 'private file load failure: ' + response.message });
            }
        });
        // 初始化 公钥文件上传插件
        (new loaderFile()).init({
            conter: '#public-file',
            action: '/platform/file-encoder',
            onSuccess: function (file, response) {
                response = $.parseJSON(response);
                $('textarea[name=public_key]').val(response.reader);
            },
            onFailure: function (file, response) {
                response = $.parseJSON(response);
                BootstrapDialog.alert({ type: BootstrapDialog.TYPE_DANGER, message: 'public file load failure: ' + response.message });
            }
        });
    });
    // 添加参数对
    function insertParameter(name, value)
    {
        var parameterWrap = $('#parameter-template .parameter').clone(true);
        if(name) {
            $(parameterWrap).find('input').eq(0).val(name);
            $(parameterWrap).find('input').eq(1).val(value);
        }
        $('#parameters-list').append(parameterWrap);
    }
    // 删除参数对
    function deleteParameter(mthis)
    {
        if ($(mthis).parents('.parameter').siblings().length < 1) {
            return;
        }
        $(mthis).parents('.parameter').remove();
    }
</script>