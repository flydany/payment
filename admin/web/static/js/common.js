
// 公用js
$(document).ready(function() {
    // 初始 日期选择框
    $.each($('.flyer-date'), function() {
        if($(this).attr('disabled')) {
            return true;
        }
        $(this).attr('readonly', true);
        var pikaday = new Pikaday({
            field: $(this)[0],
        });
        return true;
    });
});