<?php

/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\helpers\Render;

$this->title = '管理员列表';
?>

<style>
    .flyer-choise.parent {margin:0;}
    .flyer-choise {margin-bottom:10px;}
    .input-mid {padding:5px;}
</style>

<div class="mg">设置权组：<i class="icon-group"></i> <?= $role->title ?> 的权限</div>
<form class="flyer-form box-content gap" id="flyer-create" onsubmit="return false;">
    <div class="warn success mb">
        <p><i class="icon-info-sign icon-large"></i> 注意事项</p>
        <p>1、选择一级栏目时，表示当前栏目下的所有权限，包括以后可能会扩展的其他权限</p>
    </div>
    <div class="form-item">
        <div class="auto thin" id="permission-detail">
            <?php foreach($permissions[0] as $id => $title) {?>
                <fieldset class="mb-20px pd" id="permission-role-<?= $id ?>" style="border:1px solid #ddd;padding-bottom:0 !important;">
                    <legend>
                        <input flyer="checkbox" class="parent" type="checkbox" id="permission-detail-<?= $id ?>" name="permission_detail[]" value="<?= $id ?>" title="<?= $title ?>">
                    </legend>
                    <?php if(isset($permissions[$id])) {?>
                        <div class="permission-role-<?= $id ?>">
                            <?php foreach($permissions[$id] as $sid => $sTitle) {?>
                                <input flyer="checkbox" type="checkbox" id="permission-detail-<?= $sid ?>" name="permission_detail[]" value="<?= $sid ?>" title="<?= $sTitle ?>">
                            <?php }?>
                        </div>
                    <?php }?>
                </fieldset>
            <?php }?>
        </div>
    </div>
    <div class="form-item">
        <div class="input-block tr bdn"><button class="flyer-button normal border-round thin" id="save-button"><i class="icon-save"></i> <span>保 存</span></button></div>
    </div>
</form>

<script src="<?= Render::static('flyer/flyer.class.js') ?>"></script>
<script src="<?= Render::static('flyer/tableHandler.class.js') ?>"></script>
<script>
    $(document).ready(function() {
        // init form
        (new flyer).init({ form: '#flyer-create' });
        
        // 点选按钮
        $('.flyer-choise.parent input[type=checkbox]').bind('click', function() {
            if ($(this).is(':checked')) {
                $('.permission-role-' + $(this).val() + ' input[type=checkbox]:not(:checked)').click();
            }
            else {
                $('.permission-role-' + $(this).val() + ' input[type=checkbox]:checked').click();
            }
        });

        // 初始用户已存在权限
        tableHandler.requestSingle({
            isConfirm: false, isAlert: false,
            url: "<?= Url::to('@web/admin/permission-detail') ?>",
            beforeRequest: function() {
                $('#save-button').attr('data-layer-index', layer.load(0, {shade: [0.3, '#000']}));
            },
            beforePost: function(param) {
                param.data = {id: '<?= $role->identity ?>'};
            },
            requestSuccess: function (param) {
                layer.close($('#save-button').attr('data-layer-index'));
                $('#permission-detail input[type=checkbox]:disabled').attr('disabled', false);
                if ('super' in param.response.infos) {
                    fill_checkbox('#permission-detail input[type=checkbox]');
                }
                else {
                    for (var permission in param.response.infos) {
                        if ('super' in param.response.infos[permission]) {
                            fill_checkbox('#permission-detail-' + permission);
                            fill_checkbox('.permission-role-' + permission + ' input[type=checkbox]');
                        }
                        else {
                            for (var detail in param.response.infos[permission]) {
                                fill_checkbox('#permission-detail-' + detail);
                            }
                        }
                    }
                }
            }
        });
        
        // 保存操作
        tableHandler.requestSingle({
            button: '#save-button', isConfirm: false,
            url: "<?= Url::to('@web/admin/admin-role-permission-edit?id='.$role->id) ?>",
            beforeRequest: function() {
                $('#save-button').attr('data-layer-index', layer.load(0, {shade: [0.3, '#000']}));
                $('#save-button').attr('disabled', true).find('span').text('数据提交中.');
            },
            beforePost: function(param) {
                param.data.permission_detail = [];
                var checkbox = $("input[name='permission_detail[]']:checked");
                for(var i = 0; i < checkbox.length; ++i) {
                    param.data.permission_detail.push($(checkbox).eq(i).val());
                }
            },
            requestSuccess: function (param) {
                var check = $('#flyer-create input[name=role]:checked');
                var text;
                if ($(check).val() == 0) {
                    text = '';
                }
                else {
                    text = '<span class="flyer-status blue statue-thin">' + $(check).siblings('label').text() + '</span>';
                }
                parent.$('#info-table tbody #tr-<?= $role->id ?> .role').html(text);
                parent.layer.alert('设置成功', {icon: 6}, function () {
                    parent.layer.closeAll();
                });
            }
        });
    });
    
    function fill_checkbox(checkbox, is_user)
    {
        $.each($(checkbox), function() {
            if( ! $(this).is(':checked')) {
                $(this).click();
            }
        });
        if (is_user) {
            $(checkbox).attr('disabled', true);
        }
    }
</script>