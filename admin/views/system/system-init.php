<?php

/* @var $this yii\web\View */

use yii\helpers\Url;

$this->addCrumbs('系统设置');
$this->title = '分类管理';
?>

<div class="box-content gap">
    <!-- div class="warn notice mb-10px">备注：123</div -->
    <div class="box-content">
        <div class="data-title mt">分类配置</div>
        <div class="data-content">
            <button class="flyer-button normal auto ml-20px init-loading" data-href="<?= Url::to('@web/system/article-category-init') ?>"><i class="fa fa-map-signs fa-fw"></i><span>文章 - rebuild</span></button>
            <button class="flyer-button normal auto ml-20px init-loading" data-href="<?= Url::to('@web/system/design-category-init') ?>"><i class="fa fa-map-signs fa-fw"></i><span>作品 - rebuild</span></button>
            <button class="flyer-button normal auto ml-20px init-loading" data-href="<?= Url::to('@web/system/design-type-init') ?>"><i class="fa fa-map-signs fa-fw"></i><span>作品类型 - rebuild</span></button>
        </div>
        <div class="data-title mt">城市配置</div>
        <div class="data-content">
            <button class="flyer-button normal auto ml-20px init-loading" data-href="<?= Url::to('@web/system/district-init') ?>"><i class="fa fa-map-signs fa-fw"></i><span>关系 - rebuild</span></button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // 绑定 设置操作
        $('.init-loading').bind('click', function() {
            var mthis = this, text = $(this).find('span').text();
            $(this).attr('data-layer-index', layer.load(0, { shade: [0.3, '#000'] }));
            $(this).attr('disabled', true).find('span').text('设置中...');
            $.post($(this).attr('data-href'), {submit: 'json', _csrf: $('meta[name=csrf-token]').attr('content')}, function(ret_data) {
                layer.close($(mthis).attr('data-layer-index'));
                $(mthis).attr('disabled', false).find('span').text(text);
                var data = ret_data; // $.parseJSON(ret_data);
                // console.log(data);
                if (data.code == 200) {
                    layer.alert(text + ' 成功', {icon: 1});
                }
                else {
                    layer.msg(data.message, {shift: 6});
                }
            });
        });
    });
</script>