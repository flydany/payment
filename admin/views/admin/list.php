<?php

/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\helpers\Html;
use common\helpers\Render;

$this->title = '管理员列表';
?>

<div class="box-content gap">
    <div class="search flyer-form pane" id="info-search">
        <div class="form-item">
            <div class="item-inline">
                <div class="input-title">登录名</div>
                <div class="input-inline w-100px"><input class="tabler flyer-input" name="username" placeholder="login name."></div>
            </div>
            <div class="item-inline">
                <div class="input-title">手机号</div>
                <div class="input-inline w-100px"><input class="tabler flyer-input" name="mobile" placeholder="mobile."></div>
            </div>
            <div class="item-inline">
                <div class="input-title">权组</div>
                <div class="input-inline w-100px"><?= Render::select('role_id', $roles, null, ['prompt' => '全部--', 'flyer' => 'select', 'class' => 'tabler']) ?></div>
            </div>
            <div class="item-inline">
                <div class="input-inline bdn"><button class="flyer-button normal narrow border-round" id="search-button"><i class="icon-search"></i> <span>查 询</span></button></div>
            </div>
        </div>
    </div>
    <div class="flyer-table mt-10px">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Username</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th scope="row">1</th>
                <td>Mark</td>
                <td>Otto</td>
                <td>@mdo</td>
            </tr>
            <tr>
                <th scope="row">2</th>
                <td>Jacob</td>
                <td>Thornton</td>
                <td>@fat</td>
            </tr>
            <tr>
                <th scope="row">3</th>
                <td>Larry</td>
                <td>the Bird</td>
                <td>@twitter</td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="flyer-page mt right" id="info-page">
        <ul class="handle">
            <li class="prev"><a href="<?= Url::to('@web/admin/admin-detail') ?>"><i class="icon-plus icon-large"></i> 添加管理员</a></li>
            <li class="select-all" data-table="#info-table"><a><i class="icon-check icon-large"></i> 全选</a></li>
            <li class="reverse-all" data-table="#info-table"><a><i class="icon-check-empty icon-large"></i> 反选</a></li>
            <li class="next delete-mult" data-table="#info-table" data-href="<?= Url::to('@web/admin/admin-delete') ?>"><a><i class="icon-trash icon-large"></i> 删除选中</a></li>
        </ul>
        <div class="html"></div>
    </div>
</div>

<script src="<?= Render::static('flyer/flyer.class.js') ?>"></script>
<script src="<?= Render::static('flyer/checker.class.js') ?>"></script>
<script src="<?= Render::static('flyer/tabler.class.js') ?>"></script>
<script src="<?= Render::static('flyer/tableHandler.class.js') ?>"></script>
<script>
    $(document).ready(function() {
        (new flyer).init({ form: '#info-search' });
        // 批量删除按钮事件
         tableHandler.requestMulti({ button: '.delete-mult', isKeep: false });
        // 初始化表格异步加载事件
        (new tabler).init({
            // 请求地址
            url: '<?= Url::to('@web/admin/admin-list') ?>',
            // 数据渲染配置
            table: '#info-table', page: '#info-page', template: 'info-template', search: '#info-search', button: '#search-button',
            // 全选、反选按钮、页面加载完毕自动loading
            selectButton: '.select-all', reverseButton: '.reverse-all', readyCall: true,
            // param => tabler
            afterPost: function(param) {
                // 所属权组名称显示
                tableHandler.renderCategory({ category: $(param.tabler).find('.role'), select: 'role_id' });
                // 初始化 删除按钮事件
                tableHandler.requestSingle({ button: $(param.tabler).find('.delete-data'), url: $('.delete-mult:first').data('href'), isKeep: false });
                // 初始化 权限修改按钮事件
                tableHandler.alertDialog({
                    button: $(param.tabler).find('.edit-permission'),
                    title: '<i class="icon-bell-alt"></i> 设置用户权限', area: ['90%', '90%'],
                    src: '<?= Url::to('@web/admin/admin-permission-edit') ?>'
                });
            }
        });
    });
</script>
<script id="info-template" type="text/html">
    {{if infos != undefined && infos.length}}
        {{each infos as info key}}
            <tr id="tr-{{info.id}}" data-id="{{info.id}}">
                <td class="first">
                    {{if info.id != 1}}<input class="list" type="checkbox" name="info[]" value="{{info.id}}">{{/if}}
                    {{info.username}}
                </td>
                <td class="role">{{info.role_id}}</td>
                <td>{{info.realname}}</td>
                <td>{{info.mobile}}</td>
                <td>{{info.end_date}}</td>
                <td>{{info.created_at | dateShow: 'minute'}}</td>
                <td>
                    <a class="flyer-status blue thin" href="<?= Url::to('@web/admin/admin-detail?id=') ?>{{info.id}}"><i class="icon-edit icon-large" title="修改"></i>修改</a>
                    <a class="edit-permission flyer-status green thin" href="javascript:;"><i class="icon-bell-alt icon-large" title="设置权限"></i>设置权限</a>
                    <a class="delete-data flyer-status red thin" href="javascript:;"><i class="icon-trash icon-large" title="删除"></i>删除</a>
                </td>
            </tr>
        {{/each}}
    {{/if}}
</script>