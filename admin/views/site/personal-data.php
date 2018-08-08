<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model common\models\ContactForm */

use yii\helpers\Url;
use yii\helpers\Html;
use admin\helpers\Render;

$this->addCrumbs('个人中心');
$this->title = '详细资料';
?>
<div class="box-content gap">
    <div class="flyer-form pane">
        <div class="warn success mb-10px">
            <p><i class="icon-info-sign"></i> 备注</p>
            <p>1、账号到期时间：<span class="flyer-status red">0000-00-00</span> 表示为 <span class="flyer-status green">永不过期</span></p>
            <p class="mt-10px">2、权限列表：<span class="flyer-status gray">灰色部分</span> -> 表示当前没有权限操作，<span class="flyer-status blue">蓝色部分</span> -> 表示当前有权限操作</p>
        </div>
        <div class="form-item">
            <div class="item-inline">
                <div class="input-title">登录名</div>
                <div class="input-inline input-mid"><?= Html::encode(Yii::$app->admin['username']) ?></div>
            </div>
            <div class="item-inline">
                <div class="input-title">帐号到期时间</div>
                <div class="input-inline input-mid cl-red"><?= Html::encode(Yii::$app->admin['end_date']) ?></div>
            </div>
        </div>
        <div class="form-item">
            <div class="item-inline">
                <div class="input-title">手机号</div>
                <div class="input-inline input-mid"><?= Html::encode(Yii::$app->admin['mobile']) ?></div>
            </div>
            <div class="item-inline">
                <div class="input-title">邮箱</div>
                <div class="input-inline input-mid"><?= Html::encode(Yii::$app->admin['email']) ?></div>
            </div>
        </div>
        <div class="form-item">
            <div class="item-inline">
                <div class="input-title">权组</div>
                <div class="input-inline input-mid cl-blue"><?= Yii::$app->admin['adminRole'] ? Html::encode(Yii::$app->admin['adminRole']['title']) : '未设' ?></div>
            </div>
        </div>
        <div class="form-item item-text">
            <div class="input-title">权限列表</div>
            <div class="input-block pd" id="permissions">
                <?php foreach($permissions[0] as $id => $title) {?>
                <fieldset class="mb-20px pt-10px pl-10px pr-10px" id="permission-group-<?= $id ?>" style="border:1px solid #ddd;">
                    <legend>
                        <font class="flyer-status gray thin" id="permission-<?= $id ?>"><?= $title ?></font>
                    </legend>
                    <?php if(isset($permissions[$id])) {?>
                    <div class="permission-group-<?= $id ?>">
                        <?php foreach($permissions[$id] as $pi => $pt) {?>
                        <font class="flyer-status mb-10px gray thin" id="permission-<?= $pi ?>"><?= $pt ?></font>
                        <?php }?>
                    </div>
                    <?php }?>
                </fieldset>
                <?php }?>
            </div>
        </div>
        <div class="form-item item-text">
            <div class="input-title">登录记录</div>
            <div class="input-block pd">
            </div>
        </div>
    </div>
</div>

<script src="<?= Render::static('flyer/tableHandler.class.js') ?>"></script>
<script>
    $(document).ready(function() {
        // 初始用户直属权限
        permission_detail('<?= Yii::$app->admin['id'] ?>');
        // 初始用户权组所属权限
        permission_detail('<?= Yii::$app->admin['adminRole']['identity'] ?>');
    });

    function permission_detail(id)
    {
        if(id == '') {
            return true;
        }
        // 初始用户已存在权限
        tableHandler.requestSingle({
            isConfirm: false, isAlert: false,
            url: "<?= Url::to('@web/admin/permission-detail') ?>",
            beforePost: function(param) {
                param.data = {id: id};
            },
            requestSuccess: function (param) {
                if ('super' in param.response.infos) {
                    fill_checkbox('#permissions font');
                }
                else {
                    for (var permission in param.response.infos) {
                        if ('super' in param.response.infos[permission]) {
                            fill_checkbox('#permissio-group-' + permission);
                            fill_checkbox('.permission-group-' + permission + ' font');
                        }
                        else {
                            for (var detail in param.response.infos[permission]) {
                                fill_checkbox('#permission-' + detail);
                            }
                        }
                    }
                }
            }
        });
    }
    function fill_checkbox(checkbox)
    {
        $(checkbox).removeClass('gray').addClass('blue');
    }
</script>