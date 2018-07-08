<?php

/* @var $this \admin\components\View */

use common\helpers\Render;
use common\models\Navigator;

$this->title = 'Navigator List';
$this->addCrumbs('System');

$this->registerJavascript('@static/flyer/checker.class.js');
$this->registerJavascript('@static/flyer/tableHandler.class.js');

$this->registerCss('
    tbody .edit {
        border:1px solid #eee;
        border-radius:3px;
        height:25px;
    }
');
?>
    
<div class="alert alert-info" role="alert">
    <p><strong>Heads up!</strong></p>
    <p>navigation editor</p>
    <p>1. at present, the navigation system only supports three level navigation, and one level of navigation is not allowed to add.</p>
    <p>2. the function menu will not be displayed in the navigation bar.</p>
</div>
<table class="table table-bordered table-condensed" id="info-table">
    <thead>
    <tr data-id="0">
        <th><i class="fa fa-list fa-fw"></i>title</th>
        <th><i class="fa fa-flag fa-fw"></i>navigator type</th>
        <th><i class="fa fa-sitemap fa-fw"></i> controller</th>
        <th><i class="fa fa-image fa-fw"></i>icon class</th>
        <th><i class="fa fa-sort fa-fw"></i>sort</th>
        <th><i class="fa fa-gear fa-fw"></i>gear</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $controllers = Navigator::controllers();
    echo implode('', array_map(function($controller) use ($controllers) {
        $base = builder($controller);
        if(isset($controllers[$controller['id']])) {
            $base .= implode('', array_map(function($subController) use ($controllers) {
                $sub = builder($subController, '　　');
                if(isset($controllers[$subController['id']])) {
                    $sub .= implode('', array_map(function($tController) use ($controllers) {
                        return builder($tController, '　　　　');
                    }, $controllers[$subController['id']]));
                }
                return $sub;
            }, $controllers[$controller['id']]));
        }
        return $base;
    }, $controllers[0]));
    ?>
    </tbody>
</table>
<div class="btn-group">
    <a class="btn btn-default"><i class="fa fa-check-square fa-fw"></i>check all</a>
    <a class="btn btn-default"><i class="fa fa-minus-square fa-fw"></i>inverse</a>
    <a class="btn btn-default"><i class="fa fa-trash fa-fw"></i>batch delete</a>
</div>

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
                    $(param.tr).find('.save-data').attr('data-href', '/navigator/update?id=' + id);
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
    });
</script>

<?php
/**
 * 组织导航HTML
 * @param Navigator $controller 导航
 * @param string $space 预留空格
 * @return string
 */
function builder($controller, $space = '')
{
    return <<<BUILDER
    <tr id="tr-{$controller['id']}" data-id="{$controller['id']}">
        <td>
            <label class="input-group">
                <span class="input-group-addon">{$space}<i class="{$controller['icon_class']} fa-fw icon-class"></i></span>
                <input class="form-control" name="title" type="text" value="{$controller['title']}">
                <span class="input-group-addon add-navigator"><i class="fa fa-plus fa-fw"></i></span>
            </label>
            <input class="tabler" type="hidden" name="parent_id" value="{$controller['parent_id']}">
            <input class="tabler" type="hidden" name="top_id" value="{$controller['top_id']}">
        </td>
        <td>
            <div class="flyer-switch navigator">
                <input class="tabler" id="type-{$controller['id']}" name="type" type="checkbox" checked value="1">
                <label for="type-{$controller['id']}"></label>
            </div>
        </td>
        <td><input class="form-control tabler"name="controller" type="text" value="{$controller['controller']}"></td>
        <td><input class="form-control tabler" name="icon_class" type="text" value="{$controller['icon_class']}"></td>
        <td>
            <input class="form-control tabler"name="sort" type="text" value="{$controller['sort']}">
        </td>
        <td>
            <a class="save-data label label-primary" href="javascript:;" data-href="/navigator/insert"><i class="fa fa-save fa-fw"></i>save</a>
            <a class="delete-data label label-danger" href="javascript:;"><i class="fa fa-trash fa-fw"></i>delete</a>
        </td>
    </tr>
BUILDER;
}
