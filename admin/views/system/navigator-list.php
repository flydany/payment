<?php

/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\helpers\Html;
use common\helpers\Render;

$this->addCrumbs('系统设置');
$this->title = '导航栏管理';
?>
<style>
    #info-table td.first i[class^=icon-],
    #info-table td.first i[class*= icon-] {display:inline-block;width:16px;text-align:center;}
</style>

<div class="box-content gap">
    <div class="flyer-page mt right" id="info-page">
        <ul class="handle">
            <li class="prev select-all" data-table="#info-table"><a><i class="icon-check icon-large"></i> 全选</a></li>
            <li class="reverse-all" data-table="#info-table"><a><i class="icon-check-empty icon-large"></i> 反选</a></li>
            <li class="next delete-mult" data-table="#info-table" data-href="<?= Url::to('@web/system/navigator-delete') ?>"><a><i class="icon-trash icon-large"></i> 删除选中</a></li>
        </ul>
    </div>
    <div class="flyer-table flyer-form" id="navigator-list">
        <table id="info-table" cellspacing="0" cellpadding="0">
            <thead>
                <tr data-id="0">
                    <th class="first w-250px">
                        <i class="icon-th-list"></i> 菜单标题
                        <div class="handle fr fs-12px">
                            <a class="cl-orange add-navigator" data-id="0" href="javascript:;"><i class="icon-plus"></i> 添加一级菜单</a>
                        </div>
                    </th>
                    <th class="w-80px"><i class="icon-flag"></i> 菜单类型</th>
                    <th><i class="icon-sitemap"></i> 控制器</th>
                    <th><i class="icon-flag"></i> <a class="cl-orange fs-12px" href="http://www.bootcss.com/p/font-awesome/" target="_blank">Icon 样式</th>
                    <th class="w-80px"><i class="icon-sort"></i> 排序</th>
                    <th class="w-120px"><i class="icon-gear"></i> 操作</th>
                </tr>
            </thead>
            <tbody class="thin">
                <tr><td class="first" colspan="6"><i class="icon-search"></i> 点击查询按钮查找数据.</td></tr>
            </tbody>
            <tfoot style="display:none;">
                <tr id="tr-{id}" data-id="{id}">
                    <td class="first">
                        <input class="list" type="checkbox" name="info[]" value="{id}"><span class="space"></span>
                        <input class="edit" type="hidden" name="parent_id" value="{parent_id}">
                        <input class="edit" type="hidden" name="top_id" value="{top_id}">
                        <i class="icon-double-angle-right fa-class"></i>
                        <input class="edit flyer-input auto" name="title" type="text" value="">
                        <div class="handle fr">
                            <a class="cl-orange add-navigator" href="javascript:;"><i class="icon-plus" title="添加"></i></a>
                        </div>
                    </td>
                    <td>
                        <div class="flyer-switch navigator">
                            <input class="edit" id="type-{id}" name="type" type="checkbox" checked value="1">
                            <label for="type-{id}"></label>
                        </div>
                    </td>
                    <td><input class="edit flyer-input"name="controller" type="text" value=""></td>
                    <td><input class="edit flyer-input" name="icon_class" type="text" value="icon-double-angle-right"></td>
                    <td>
                        <input class="edit flyer-input"name="sort" type="text" value="1000">
                    </td>
                    <td>
                        <a class="save-data flyer-status blue thin" href="javascript:;" data-href="<?= Url::to('@web/system/navigator-insert') ?>"><i class="icon-save icon-large" title="保存"></i>保存</a>
                        <a class="delete-data flyer-status red thin" href="javascript:;"><i class="icon-trash icon-large" title="删除"></i>删除</a>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="flyer-page mt right" id="info-page">
        <ul class="handle">
            <li class="prev select-all" data-table="#info-table"><a><i class="icon-check icon-large"></i> 全选</a></li>
            <li class="reverse-all" data-table="#info-table"><a><i class="icon-check-empty icon-large"></i> 反选</a></li>
            <li class="next delete-mult" data-table="#info-table" data-href="<?= Url::to('@web/system/navigator-delete') ?>"><a><i class="icon-trash icon-large"></i> 删除选中</a></li>
        </ul>
    </div>
</div>

<script src="<?= Render::static('flyer/flyer.class.js') ?>"></script>
<script src="<?= Render::static('flyer/checker.class.js') ?>"></script>
<script src="<?= Render::static('flyer/tabler.class.js') ?>"></script>
<script src="<?= Render::static('flyer/tableHandler.class.js') ?>"></script>
<script>
    var add_index = 0;
    $(document).ready(function() {
        (new flyer).init({ form: '#navigator-list' });
        // 批量删除按钮事件
        tableHandler.requestMulti({ button: '.delete-mult', isKeep: false });
        // 删除当前行
        $('#info-table tfoot .delete-data').bind('click', function() {
            $(this).parents('tr').remove();
        });
        var saveLine = { afterAlert: function(param) {
            // 初始化 表格相关事件
            // table_init(param.table_body);
            // 如果添加的是一级菜单 则显示 添加按钮
            if($(param.tr).find('input[name=parent_id]').val() == 0) {
                $(param.tr).find('.first .handle').show();
            }
            // 填充 当前行 信息
            if ($(param.tr).attr('data-type') == 'insert') {
                var id = $(param.tr).attr('data-id');
                $(param.tr).attr('id', 'tr-' + id).find('input[type=checkbox].list').val(id);
                $(param.tr).find('.navigator-type label').attr('for', 'type-' + id);
                $(param.tr).find('.save-data').attr('data-href', '<?= Url::to('@web/system/navigator-update?id=') ?>' + id);
                // 重新定义 删除按钮事件
                $(param.tr).find('.delete-data').unbind('click');
                // 重新初始化 删除按钮事件
                tableHandler.requestSingle({ button: $(param.tabler).find('tbody .delete-data'), url: $('.delete-mult:first').data('href'), isKeep: false });
            }
            // 更改小图标样式
            if ( ! $(param.tr).find('.fa-class').hasClass($(param.tr).find('input[name=icon_class]').val())) {
                $(param.tr).find('.fa-class').removeClass().addClass('fa-class').addClass($(param.tr).find('input[name=icon_class]').val());
            }
        }};
        // 初始化 保存按钮事件
        var tfootSave = saveLine;
        tfootSave.button = $('#info-table tfoot .save-data');
        tableHandler.saveLine(tfootSave);
        // 初始化表格异步加载事件
        (new tabler).init({
            url: '<?= Url::to('@web/system/navigator-list') ?>',
            // 数据渲染配置
            table: '#info-table', page: '#info-page', template: 'info-template', search: '#info-search', button: '#search-button',
            // 全选、反选按钮、页面加载完毕自动loading
            selectButton: '.select-all', reverseButton: '.reverse-all', readyCall: true,
            // param => tabler
            afterPost: function(param) {
                // 初始化 删除按钮事件
                tableHandler.requestSingle({ button: $(param.tabler).find('tbody .delete-data'), url: $('.delete-mult:first').data('href'), isKeep: false });
                // 初始化 添加按钮点击事件
                tableHandler.insertLine({ button: $('.add-navigator'), clone: '#info-table tfoot tr', afterAlert: function(param) {
                    var parent = $(param.mthis).parents('tr');
                    $(param.cloneDom).find('.first .handle').hide();
                    $(param.cloneDom).find('.space').html($('#tr-' + $(parent).attr('data-id')).length ? $(parent).find('.space').text() + '　　' : '');
                    // 隐藏或删除 添加按钮
                    if ($(parent).attr('data-id') == undefined) {
                        var parent_id = 0;
                    }
                    else {
                        var parent_id = $(parent).attr('data-id');
                    }
                    $(param.cloneDom).find('.flyer-switch input').attr('id', 'type-{id}' + add_index);
                    $(param.cloneDom).find('.flyer-switch label').attr('for', 'type-{id}' + add_index);
                    ++add_index;
                    $(param.cloneDom).find('input[name=parent_id].edit, input[name=top_id].edit').val(parent_id);
                }});
                // 初始化 保存按钮事件
                var tbodySave = saveLine;
                tbodySave.button = $(param.tabler).find('tbody .save-data');
                tableHandler.saveLine(tbodySave);
            }
        });
    });
</script>
<script id="info-template" type="text/html">
    {{if infos[start_index] != undefined}}
        {{each infos[start_index] as info key}}
            <tr id="tr-{{info.id}}" data-id="{{info.id}}">
                <td class="first">
                    <input class="list" type="checkbox" name="info[]" value="{{info.id}}"><span class="space">{{if space != undefined}}{{space}}{{/if}}</span>
                    <input class="edit" type="hidden" name="parent_id" value="{{info.parent_id}}">
                    <input class="edit" type="hidden" name="top_id" value="{{info.top_id}}">
                    <i class="{{info.icon_class}} fa-class"></i>
                    <input class="edit flyer-input auto" name="title" type="text" value="{{info.title}}">
                    {{if start_index == 0}}
                    <div class="handle fr">
                        <a class="cl-orange add-navigator" href="javascript:;"><i class="icon-plus" title="添加"></i></a>
                    </div>
                    {{/if}}
                </td>
                <td>
                    <div class="flyer-switch navigator">
                        <input class="edit" id="type-{{info.id}}" name="type" type="checkbox"{{if info.type == 1}} checked{{/if}} value="1">
                        <label for="type-{{info.id}}"></label>
                    </div>
                </td>
                <td><input class="edit flyer-input"name="controller" type="text" value="{{info.controller}}"></td>
                <td><input class="edit flyer-input" name="icon_class" type="text" value="{{info.icon_class}}"></td>
                <td><input class="edit flyer-input"name="sort" type="text" value="{{info.sort}}"></td>
                <td>
                    <a class="save-data flyer-status blue thin" href="javascript:;" data-href="<?= Url::to('@web/system/navigator-update') ?>"><i class="icon-save icon-large" title="保存"></i>保存</a>
                    <a class="delete-data flyer-status red thin" href="javascript:;"><i class="icon-trash icon-large" title="删除"></i>删除</a>
                </td>
            </tr>
            {{if infos[info.id]}}
                {{include 'info-template' {infos:infos,start_index:info.id,space:space!=undefined?space+'　　':'　　'},0}}
            {{/if}}
        {{/each}}
    {{else}}
        <tr>
            <td class="first" colspan="6"><i class="icon-ban-circle"></i> 搜索程序未为您搜索到任何信息！</td>
        </tr>
    {{/if}}
</script>
