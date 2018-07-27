<?php

/* @var $this yii\web\View */

use yii\helpers\Url;
use common\helpers\Render;
use common\models\Project;
use common\models\Recharge;
use common\models\RechargeLog;
use common\models\ProjectMerchant;

$this->addCrumbs('Project');
$this->addCrumbs('Project List', 'project/list');
$this->title = 'Update Project';
$this->setActiveNavigator('project/list');

\admin\assets\CheckerAsset::register($this);
\admin\assets\TablerAsset::register($this);
?>

<div class="contenter">
    <div class="alert alert-info" role="alert">
        <p><strong>Heads up!</strong></p>
        <p><?= $this->modifyNotice($recharge->id) ?></p>
        <p>1. the account will be disabled after the expiration date.</p>
    </div>
    <form id="info-detail" method="post" action="/recharge/update?id=<?= $recharge->id ?>">
        <div class="form-group checker">
            <label>order number</label>
            <input class="form-control" type="text" name="order_number" value="<?= $recharge->order_number ?>" placeholder="order number" readonly>
        </div>
        <div class="form-row">
            <div class="form-group col-xs-6 checker">
                <label>source order number</label>
                <input class="form-control" type="text" name="source_order_number" value="<?= $recharge->source_order_number ?>" placeholder="source order number">
            </div>
            <div class="form-group col-xs-6 checker">
                <label>outer order number</label>
                <input class="form-control" type="text" name="outer_order_number" value="<?= $recharge->outer_order_number ?>" placeholder="outer order number">
            </div>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">recharge configuration</div>
            <div class="panel-body pb-zero">
                <div class="form-row">
                    <div class="form-group col-xs-6 checker">
                        <label>project</label>
                        <?= Render::select('project_id', Project::selector(), $recharge->project_id, ['prompt' => '--', 'class' => 'picker', 'disabled' => 'disabled']) ?>
                    </div>
                    <div class="form-group col-xs-6 checker">
                        <label>project merchant</label>
                        <?= Render::select('project_merchant_id', ProjectMerchant::selector($recharge->project_id), $recharge->project_merchant_id, ['class' => 'picker', 'data-live-search' => 'true']) ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-xs-6 checker">
                        <label>user</label>
                        <input class="form-control" type="text" name="user_id" value="<?= $recharge->user_id ?>" placeholder="user id">
                    </div>
                    <div class="form-group col-xs-6 checker">
                        <label>bind card</label>
                        <input class="form-control" type="text" name="bind_card_id" value="<?= $recharge->bind_card_id ?>" placeholder="bind card id">
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">recharge parameter</div>
            <div class="panel-body pb-zero">
                <div class="form-row">
                    <div class="form-group col-xs-6 checker">
                        <label>amount</label>
                        <input class="form-control" type="text" name="amount" value="<?= $recharge->amount ?>" placeholder="amount">
                    </div>
                    <div class="form-group col-xs-6 checker">
                        <label>fee</label>
                        <input class="form-control" type="text" name="fee" value="<?= $recharge->fee ?>" placeholder="fee">
                    </div>
                </div>
                <div class="form-group checker">
                    <label>postscript</label>
                    <textarea class="form-control" name="postscript" placeholder="postscript"><?= $recharge->postscript ?></textarea>
                </div>
            </div>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">recharge result</div>
            <div class="panel-body pb-zero">
                <div class="form-row">
                    <div class="form-group col-xs-4 checker">
                        <label>success date</label>
                        <input class="form-control" type="text" name="success_date" value="<?= $recharge->success_date ?>" placeholder="success date">
                    </div>
                    <div class="form-group col-xs-4 checker">
                        <label>success at</label>
                        <input class="form-control" type="text" name="success_at" value="<?= $recharge->success_at ? date('Y-m-d H:i:s', $recharge->success_at) : '' ?>" placeholder="success at">
                    </div>
                    <div class="form-group col-xs-4 checker">
                        <label>error code</label>
                        <input class="form-control" type="text" name="error_code" value="<?= $recharge->error_code ?>" placeholder="error code">
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label>recharge status</label>
            <?= Render::select('status', Recharge::$statusSelector, $recharge->status, ['class' => 'picker']) ?>
        </div>
        <div class="form-group checker">
            <label>remark</label>
            <textarea class="form-control" name="remark" placeholder="remark"><?= $recharge->remark ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary" id="save-button"><i class="fa fa-save fa-fw"></i>save</button>
        <textarea id="info-detail-json" data-form="#info-detail" style="display:none;"><?= Recharge::checker() ?></textarea>
        <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken() ?>">
    </form>
    <div class="panel panel-primary mt-15px">
        <div class="panel-heading">recharge logs</div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="info-table">
                <thead>
                <tr>
                    <th class="first"><i class="fa fa-user fa-fw"></i>operator</th>
                    <th><i class="fa fa-gear fa-fw"></i>event</th>
                    <th><i class="fa fa-location-arrow fa-fw"></i>IP</th>
                    <th><i class="fa fa-gavel fa-fw"></i>operation</th>
                    <th><i class="fa fa-clock-o fa-fw"></i>time</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="5"><i class="fa fa-search fa-fw"></i>click on the search button to search data.</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="btn-toolbar" id="info-page">
        <div class="btn-group render" role="group"></div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // 提交按钮绑定事件
        $('#success-button, #refuse-button').bind('click', function() {
            var mthis = this;
            tableHandler.confirm({ functionName: function() {
                    $('#flyer-create').attr('action', $(mthis).attr('data-href')).submit();
                }
            });
        });
        // 初始化表格异步加载事件
        (new tabler).init({
            // 请求地址
            url: '<?= Url::to('@web/recharge/logs?id='.$recharge->id) ?>',
            // 数据渲染配置
            table: '#info-table', page: '#info-page', template: 'info-template', search: '#info-search',
            // 页面加载完毕自动loading
            readyCall: true,
            // param => tabler
            afterPost: function(param) {
                // 充值方式名称显示
                tableHandler.renderCategory({ category: $(param.tabler).find('.handler'), select: 'select[name=handler]' });
            }
        });
    });
</script>
<script id="info-template" type="text/html">
    {{each infos as info key}}
    <tr id="tr-{{info.id}}" data-id="{{info.id}}">
        <td>{{info.operator.username}}</td>
        <td>{{info.event}}</td>
        <td>{{info.ip}}</td>
        <td>{{info.operation}}</td>
        <td>{{info.created_at | dateShow}}</td>
    </tr>
    {{/each}}
</script>