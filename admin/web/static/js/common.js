
// 公用js
jQuery(document).ready(function() {
    // 初始化select样式
    jQuery('select.select-picker').selectpicker();
    // 初始化date样式
    jQuery('input.datetime-picker').datetimepicker({ format: 'yyyy-mm-dd hh:ii:ss', autoclose: true });
    jQuery('input.date-picker').datetimepicker({ format: 'yyyy-mm-dd', autoclose: true, minView : 2 });
    jQuery('input.time-picker').datetimepicker({ format: 'hh:ii:ss', autoclose: true, maxView: 0 });
});