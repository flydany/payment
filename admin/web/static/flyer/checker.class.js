/**
 * @name Form表单数据格式校验
 * @create by Flydany
 * @date 2017.03.30
 * include dom blur check & dom value change & form submit data check
 * check format contain's single dom & relate dom & or mult dom choise one
 */

var checker = function() {
    ///////// checker 属性定义 ////////////
    this.ruleDom = '';
    // 需要校验的form表单 string
    this.form = '';
    // 数据校验规则 object
    this.rules = {};
    // 数据校验状态 string
    this.status = 'pass';
    // 校验异常描述 array
    this.message = [];
    this.errorDom = [];
    // 数据校验配置
    this.pass = 'pass';
    this.warnCss = '.checker';
    this.warnSpan = '.warn-span';
    this.bindEvent = 'change';
    this.isRecycle = false;
    this.relateDom = {};
    /**
     * @name 获取异常错误描述
     * @param name string 错误规则字段名
     * @param type string 数据异常规则
     * @param message string 默认的异常描述
     * @return string
     */
    this.getNotice = function(name, type, message) {
        // 通过function getNotice 获取错误信息描述
        if(this.rules.param[name] && this.rules.param[name][2] && this.rules.param[name][2][type]) {
            return this.rules.param[name][2][type];
        }
        return message;
    }
    // @name 设置需要校验的规则容器
    this.setRuleDom = function(dom) {
        this.ruleDom = dom;
        return this;
    }
    // @name 设置需要校验的form表单
    this.setForm = function(form) {
        this.form = form;
        return this;
    }
    this.setRelateDom = function(relate) {
        this.relateDom = relate;
        return this;
    }
    // @name 添加关系型数据校验 name与relate数据index的关联
    this.addRelateDom = function(name, index) {
        if(this.relateDom[name] === undefined) {
            this.relateDom[name] = [];
        }
        this.relateDom[name][this.relateDom[name].length] = index;
        return this;
    }
    // @name 获取关系型数据校验中 name 与 relate 数据index的关联
    this.getRelateDomIndex = function(name) {
        return this.relateDom[name];
    }
    // @name 设置校验规则
    this.setRules = function(rules) {
        this.rules = rules;
        return this;
    }
    this.getOriRules = function() {
        return $.parseJSON($(this.ruleDom).val());
    }
    // @name 添加数据校验错误描述
    this.addMessage = function(message) {
        this.message.push(message);
        return this;
    }
    this.getMessage = function() {
        return this.message;
    }
    this.setMessage = function(message) {
        this.message = message;
        return this;
    }
    // @name 添加错误的Dom队列
    this.addErrorDom = function(dom) {
        this.errorDom.push(dom);
        return this;
    }
    this.getErrorDom = function() {
        return this.errorDom;
    }
    this.setErrorDom = function(domList) {
        this.errorDom = domList;
        return this;
    }
    /**
     * @name 设置数据整体校验状态
     * @param status string 数据校验状态
     * @return object checker
     */
    this.setStatus = function(status) {
        this.status = status;
        return this;
    }
    // @name 获取数据整体校验状态
    // @return string this.status
    this.getStatus = function() {
        return this.status;
    }
    this.renderStatus = function(status, message, dom) {
        this.setStatus(status).addMessage(message).addErrorDom(dom);
        return this;
    }
    // @name 返回当前校验是否通过
    // @return boolean true 不通过， false 通过
    this.isError = function() {
        return this.getStatus() === 'pass' ? false : true;
    }
    // @name 是否全部校验(遇错是否直接退出)
    // @return boolean
    this.unRecycle = function() {
        return this.isRecycle ? false : true;
    }
    // @name 获取name对应的dom元素
    // @param string name 校验数据的name值
    // @param boolean gFull 是否获取全部的元素 、 获取选中的元素
    // return dom object
    this.getDom = function(name, gFull) {
        var dom = this.form + ' [name=' + name + ']';
        if( ! $(dom).length) {
            dom = this.form + " [name='" + name + "[]']";
        }
        if( ! gFull && ($(dom).get(0) && $(dom).get(0).tagName.toLowerCase() === 'input') && ($.inArray($(dom).eq(0).attr('type'), ['checkbox', 'radio']) >= 0)) {
            dom = dom + ':checked';
        }
        // 异常输出
        else if( ! $(dom).length) {
            console.log('name = ' + this.form + ' [name=' + name + ']' + ' not found');
        }
        return dom;
    }
    // @name 获取dom的name属性
    // @return string
    this.getDomName = function(dom) {
        return $(dom).attr('name').replace('[', '').replace(']', '');
    }
    // @name 获取dom[name=name]元素的值
    // @param string name 元素name
    // @return array
    this.getValue = function(name) {
        var dom = this.getDom(name, false);
        var domLength = $(dom).length;
        if(domLength <= 1) {
            return $(dom).val();
        }
        var value = [];
        for(var i = 0; i < domLength; ++i) {
            value.push($(dom).eq(i).val());
        }
        return value;
    }
    // @name 重置校验配置参数
    this.reset = function(ruleDom) {
        this.setRuleDom(ruleDom);
        this.setForm('');
        // 设置 rules 避免直接存储之后
        // 避免下次使用原始rule会出现值被改变
        this.setRules(this.getOriRules());
        this.setStatus(this.pass);
        this.setMessage([]);
        this.setErrorDom([]);
        this.setRelateDom({});
        return this;
    }

    ///////// checker 数据校验方法定义 //////////
    this.init = function(param) {
        this.reset(param.ruleDom).setForm($(param.ruleDom).attr('data-form'));
        // 初始化DOM实时验证
        this.initEvent();
        if($(this.form).length && $(this.form)[0] && $(this.form)[0].tagName == 'FORM') {
            // 初始化提交验证
            var checkerClass = this;
            $(this.form).submit(function () {
                return checkerClass.validate();
            });
        }
    }
    this.validate = function() {
        this.setStatus(this.pass);
        this.setMessage([]);
        this.setErrorDom([]);
        return this.initSubmit();
    }

    // @name 初始化 dom blur/change 事件
    this.initEvent = function() {
        // 多选一格式数据校验
        this.initOrEvent();
        // 初始化关系型数据格式关联
        this.initRelateDom();
        // 根据初始值重置关系型数据校验规则
        this.initRelateRule();
        // 关系型数据格式校验
        this.initRelateEvent();
        // 整体预设数据格式校验
        this.initParamEvent();
        return this;
    }
    // @name 初始化rules[param]校验源的事件
    this.initParamEvent = function () {
        // 循环每条数据规则
        for (var name in this.rules.param) {
            // 获取DOM对象
            var dom = this.getDom(name, true);
            // 校验单条数据
            // 绑定每个DOM的blur事件，数据校验触发条件
            var checkerClass = this;
            $(dom).bind(checkerClass.bindEvent, function() {
                // 检测当前DOM的规则
                checkerClass.verifySingle(checkerClass.getDomName(this));
            });
            // 多重验证事件
            $(dom).bind('blur', function() { $(this).change();});
        }
        return this;
    }
    // @name 初始化rules[or]校验源的事件
    this.initOrEvent = function() {
        if(checker_empty(this.rules.or)) {
            return true;
        }
        // TODO...

        return this;
    }
    // @name 监测并初始化relate中相关的name与relate[index]的关系
    this.initRelateDom = function() {
            if(checker_empty(this.rules.relate)) {
            return this;
        }
        // 检测关系型规则验证是否为数组
        if(checker_array(this.rules.relate) === true) {
            // 获取关系型数据校验的总条数
            var relateLength = this.rules.relate.length;
            for(var i = 0; i < relateLength; ++i) {
                var ruleLength = this.rules.relate[i][0].length;
                for(var j = 0; j < ruleLength; ++j) {
                    this.addRelateDom(this.rules.relate[i][0][j][0], i);
                }
            }
        }
        else {
            console.log('checking of relationship rules that are temporarily unsupported');
        }
        return this;
    }
    // @name 初始化rules[relate]校验源的事件
    this.initRelateEvent = function() {
        if(checker_empty(this.relateDom)) {
        // if(this.relateDom === undefined) {
            return this;
        }
        // 循环每个dom
        for(var name in this.relateDom) {
            // 取出一个DOM，绑定事件
            var dom = this.getDom(name, true);
            var checkerClass = this;
            $(dom).bind(checkerClass.bindEvent, function() {
                // console.log('relate event');
                var name = checkerClass.getDomName(this);
                // 当前关联规则校验通过
                var relateIndex = checkerClass.getRelateDomIndex(name),
                    indexLength = checkerClass.relateDom[name].length;
                for(var i = 0; i < indexLength; ++i) {
                    // 循环校验每条dom相关的relate[0]规则，通过则重置对应的relate[1]规则
                    // 开始重置/还原param[name]相关的规则
                    var isPass = checkerClass.checkRelateRule(relateIndex[i]);
                    console.log(isPass);
                    if(isPass) {
                        checkerClass.reBuliderRules(relateIndex[i]);
                    }
                    else {
                        checkerClass.resetRules(relateIndex[i]);
                    }
                }
            });
            // 多重验证事件
            $(dom).bind('blur', function() { $(this).change(); });
        }
        return this;
    }
    // @name 检测relate[0]中的校验规则是否通过
    this.checkRelateRule = function(index) {
        var relateRule = this.rules.relate[index],
            isPass = true;
        var ruleLength = relateRule[0].length;
        // 循环校验relateRule中的每条校验规则
        for(var i = 0; i < ruleLength; ++i) {
            var name = relateRule[0][i][0],
                type = relateRule[0][i][1];
            // type eq 'empty', check value is empty?
            if(type == 'pass') {
                var response = this.verifySingle(name);
                if(response.code !== this.pass) {
                    isPass = false;
                    break;
                }
            }
            // check relate[1]中的所有规则
            else {
                var format = relateRule[0][i][2],
                    value = this.getValue(name);
                // 校验单条规则是否通过
                if(eval('checker_' + type + '(value, format)') !== true) {
                    isPass = false;
                    break;
                }
            }
        }
        return isPass;
    }
    // 重新设置param[name]的校验规则
    this.reBuliderRules = function(index) {
        if(checker_array(this.rules.relate[index][1]) !== true && checker_object(this.rules.relate[index][1]) !== true) {
            params = [this.rules.relate[index][1]];
        }
        else {
            params = this.rules.relate[index][1];
        }
        for(var relateName in params) {
            // 检测relateName的具体属性
            // if(checker_int(relateName) == true) {
            //     relateName = params[relateName];
            //     continue;
            // }
            // 校验通过，重置param[relateName]的规则
            this.reBuliderRule(relateName, params[relateName]);
        }
        return this;
    }
    /**
     * @name 重置param[name]的校验规则
     * --------------------------------------------------------------------------------------------
     * 实现功能 -> 当idType = idcard时，idNo的格式必须是身份证号格式，且必填
     * [['idType', 'eq', idcard], ['idNo' => ['rule' => ['idcard'], 'required']]
     * [['idType', 'eq', idcard], ['idNo' => ['rule' => ['idcard', 'required']]]],
     * [['idType', 'eq', idcard], ['idNo' => ['rule' => 'idcard']], 'idNo'],
     * 以上三种执行效果是一致的
     */
    this.reBuliderRule = function(name, rule) {
        // 如果 name 为 int型数据, name = rule, 并且 required
        if(checker_int(name) === true) {
            if(this.rules.param[rule] === undefined) {
                console.log('unknown associated data: ' + rule);
            }
            if( ! this.isRuleRequired(this.rules.param[rule][1])) {
                if(checker_array(this.rules.param[rule][1]) === true) {
                    this.rules.param[rule][1].push('required');
                }
                else {
                    this.rules.param[rule][1].required = true;
                }
            }
            name = rule;
        }
        // 如果是 name => []
        else {
            if(this.rules.param[name] === undefined) {
                console.log('unknown associated data: ' + name);
            }
            else {
                // 循环每个需要改变的规则
                for(var type in rule) {
                    // 当 type 为 int型数据, rule == 'required'
                    if(checker_int(type) === true) {
                        if(rule[type] === 'required') {
                            if( ! this.isRuleRequired(this.rules.param[name][1])) {
                                this.rules.param[name][1].push("required");
                            }
                        }
                    }
                    else if(type === 'rule') {
                        this.rules.param[name][1] = rule[type];
                    }
                    else if(type === 'required') {
                        if( ! this.isRuleRequired(this.rules.param[name][1])) {
                            this.rules.param[name][1].push("required");
                        }
                    }
                    else if(type === 'message') {
                        this.rules.param[name][2] = rule[type];
                    }
                    else {
                        console.log('change of unknown association data rules: '.type);
                    }
                }
                // console.log(this.rules.param[name]);
            }
        }
        // 规则改变之后, dom表单重新交验
        $(this.getDom(name, true)).blur();
        return this;
    }
    // @name 重置原param[name]的校验规则
    this.resetRules = function(index) {
        if(checker_array(this.rules.relate[index][1]) !== true && checker_object(this.rules.relate[index][1]) !== true) {
            params = [this.rules.relate[index][1]];
        }
        else {
            params = this.rules.relate[index][1];
        }
        for(var relateName in params) {
            // 检测relateName的具体属性
            if(checker_int(relateName) == true) {
                relateName = params[relateName];
                // continue;
            }
            // 校验未通过，还原param[relateName]的规则
            this.resetRule(relateName);
        }
        return this;
    }
    // @name 还原param[name]的校验规则
    this.resetRule = function(name) {
        this.rules.param[name] = this.getOriRules().param[name];
        // 规则改变之后, dom表单重新交验
        $(this.getDom(name, true)).blur();
        return this;
    }

    /**
     * @name 初始化数据提交时整体验证方法
     * @param this.rules object 数据校验规则
     * @param this.relateDom object 关系型数据校验name与relate数据index的关联
     * @param this.message array 异常描述
     * @param this.status string 处理状态
     * @return boolean
     */
    this.initSubmit = function() {
        this.initRelateRule();
        // 循环每条数据规则
        this.verifyParams();
        // console.log(this.getMessage());
        // 判断是否校验通过
        if(this.isError()) {
            // layer.msg(this.getMessage().join('。'), { shift: 6 });
            jQuery.warning(this.getMessage().join('。'));
            $(this.getErrorDom()[0]).focus();
            return false;
        }
        return true;
    }
    /**
     * @name 根据relate重置所有的相关rules
     * @param this.rules.relate object 相关校验规则
     * @return object self
     */
    this.initRelateRule = function() {
        if(checker_empty(this.rules.relate)) {
            return this;
        }
        // 检测关系型规则验证是否为数组
        if(checker_array(this.rules.relate) === true) {
            // 获取关系型数据校验的总条数
            var relateLength = this.rules.relate.length;
            for(var i = 0; i < relateLength; ++i) {
                // 校验通过，重置param[relateName]规则
                if(this.checkRelateRule(i)) {
                    this.reBuliderRules(i);
                }
                // 校验未通过，还原param[relateName]规则
                else {
                    this.resetRules(i);
                }
            }
        }
        else {
            console.log('unknown relational data validation model');
        }
        return this;
    }
    /**
     * @name 校验所有的数据字段
     * @param this.rules.param object 需要校验的数据字段
     * @return boolean
     */
    this.verifyParams = function() {
        for (var name in this.rules.param) {
            // 校验单条数据
            var response = this.verifySingle(name);
            if(response.code !== this.pass) {
                this.renderStatus(response.code, response.message, this.getDom(name, false));
                if(this.unRecycle()) {
                    break;
                }
            }
        }
        return this;
    }
    /**
     * @name 校验单条数据字段
     * @param name string 需要校验的数据字段
     * @return boolean
     */
    this.verifySingle = function(name) {
        var oneStatus = this.pass,
            oneMessage = [];
        // 获取当前数据验证规则
        var rule = this.rules.param[name];
        // 获取dom的值
        var dom = this.getDom(name, false);
        // 获取验证字段的标题
        var title = rule[0];
        var value = this.getValue(name);
        // 获取dom的所有value值
        if( ! $(dom).length) {
            dom = this.getDom(name, true);
        }
        if(checker_string(rule[1]) === true) {
            rule[1] = [rule[1]];
        }
        // 校验值不能为空
        if(checker_empty(value)) {
            if(this.isRuleRequired(rule[1])) {
                oneStatus = 'null';
                oneMessage.push(this.getNotice(name, 'required', 'can\'t be empty'));
            }
        }
        else {
            // 循环验证 rule[1]中的每个具体数据规则
            for(var type in rule[1]) {
                if(this.unRecycle() && oneStatus != this.pass) {
                    break;
                }
                var format = rule[1][type];
                // 必填验证已在上面校验、忽略
                if ( ! checker_array(value) && (type == 'required' || format == 'required')) {
                    continue;
                }
                // 判断校验规则
                if (checker_number(type) === true) {
                    type = format;
                }
                else if(format.toString().indexOf(':') === 0) {
                    var match = this.getDom(format.replace(':', ''), true);
                    if( ! $(match).length) {
                        console.log('unknown associated data key');
                    }
                    format = $(match).val();
                }
                // 检测值是否为数组
                if (checker_array(value) !== true) {
                    // type规则校验
                    var message = eval('checker_' + type + '(value, format)');
                    // 校验失败
                    if(message !== true) {
                        oneStatus = 'error';
                        oneMessage.push(this.getNotice(name, type, message));
                    }
                }
                else {
                    var valueLength = value.length;
                    for(var i = 0; i < valueLength; ++i) {
                        var singleValue = value[i];
                        if(checker_empty(singleValue)) {
                            // continue;
                        }
                        // type规则校验
                        var message = eval('checker_' + type + '(singleValue, format)');
                        // 校验失败
                        if(message !== true) {
                            oneStatus = 'error';
                            oneMessage.push((i + 1) + 'th ' + this.getNotice(name, type, message));
                            if(this.unRecycle()) {
                                break;
                            }
                        }
                    }
                }
            }
        }
        // 获取承载当前异常的父元素
        var parent = $(dom).parents(this.warnCss);
        // 校验未通过
        if(oneStatus !== this.pass) {
            oneMessage = title + '：' + oneMessage.join(', ');
            // 展示错误
            $(parent).addClass('has-error').removeClass('has-success');
            // 获取需要渲染错误描述的dom元素
            $(parent).find(this.warnSpan).text(oneMessage);
        }
        // 校验通过
        else {
            // 取消展示错误、展示通过提示
            $(parent).addClass('has-success').removeClass('has-error');
            $(parent).find(this.warnSpan).text('PASS');
        }
        return { code: oneStatus, message: oneMessage };
    }
    // @name 判断值是否为空
    // @param array value 需要判断的值
    // @return boolean
    this.checker_empty = function(value) {
        return checker_empty(value, null);
    }
    // @name 校验值为空时, 规则是否必填
    // @param rule object/array 规则
    // @return boolean
    this.isRuleRequired = function(rule) {
        if(($.inArray('required', rule) >= 0) || (JSON.stringify(rule) && JSON.stringify(rule).indexOf('"required"') > 0 && (rule['required'] == undefined || rule['required'] == true))) {
            return true;
        }
        return false;
    }
};

// @name 检测值是否为空
function checker_required(value, format) {
    return checker_empty(value, format) ? 'can\'t be empty' : true;
}
// @name 检测值是否为空
function checker_empty(value, format) {
    if(value == null || value == '' || value == [] || value == [''] || value == [""] || value == {}) {
        return true;
    }
    return false;
}
/**
 * @name 校验值是否为特定格式
 * @param {array} value 需要交验的值
 * @param {string} format 数据格式
 * @return boolean
 */
function checker_format(value, format) {
    return Object.prototype.toString.call(value).toLowerCase() === format ? true : 'formatting error';
}
/**
 * @name 校验数据是否为int型
 * @param value int 需要校验的值
 * @param format string 此处为空
 * @return boolean
 */
function checker_int(value, format) {
    return /^[+\-]?\d+$/.test(value) ? true : 'formatting error';
}
// @name 验证值是否为数字格式
function checker_number(value, format) {
    return /^\d+$/.test(value) ? true : 'formatting error';
}
// @name 校验数字格式是否为float类型
function checker_float(value, format) {
    return /^\d+(\.\d{0,2})?$/.test(value) ? true : 'formatting error';
}
// @name 验证值是否为字符串格式
function checker_string(value, format) {
    return checker_format(value, "[object string]");
}
// @name 验证值是否为数组格式
function checker_array(value, format) {
    return checker_format(value, '[object array]');
}
// @name 验证值是否为object格式
function checker_object(value, format) {
    return checker_format(value, '[object object]');
}
/**
 * @name 校验字符串的最大长度是否超出限值
 * @param value string 需要校验的值
 * @param format int 最大长度限值
 * @return boolean
 */
function checker_maxlength(value, format) {
    return (value + '').length > format ? 'too long' : true;
}
// @name 校验字符串的最大长度是否低于限值
function checker_minlength(value, format) {
    return (value + '').length < format ? 'too short' : true;
}
// @name 校验字符串的长度是否定长
function checker_length(value, format) {
    return (value + '').length === format ? true : 'length error';
}
// @name 校验数字是否 <= 特定值
function checker_let(value, format) {
    return parseInt(value) <= parseInt(format) ? true : 'too large';
}
// @name 校验数字是否 < 特定值
function checker_lt(value, format) {
    return parseInt(value) < parseInt(format) ? true : 'too large';
}
// @name 校验数字是否 >= 特定值
function checker_get(value, format) {
    return parseInt(value) >= parseInt(format) ? true : 'too small';
}
// @name 校验数字是否 > 特定值
function checker_gt(value, format) {
    return parseInt(value) > parseInt(format) ? true : 'too small';
}
// @name 校验两字符串/数字是否 = 特定值
function checker_eq(value, format) {
    return value == format ? true : 'value error';
}
// @name 校验两字符串/数字是否 = 特定值
function checker_in(value, format) {
    if(format) {
        if(format.indexOf(value) >= 0) {
            return true;
        }
        if(checker_int(value) && format.indexOf(parseInt(value)) >= 0) {
            return true;
        }
    }
    return 'value error';
}
// @name 校验两字符串/数字是否 = 特定值
function checker_inkey(value, format) {
    return (format[value] !== undefined) ? true : 'value error';
}
// @name 校验两字符串/数字是否 = 特定值
function checker_preg(value, format) {
    var pregs = new RegExp(format.substring(1, format.length - 1));
    return pregs.test(value) ? true : 'formatting error';
}
// @name 校验标签组格式
function checker_tags(value, format) {
    return /^([^;]{1,4};){0,4}([^;]{1,4})?$/.test(value) ? true : 'formatting error';
}
/**
 * @name 校验字符串是否url格式
 * @param value string 需要校验的值
 * @param format string 此处为空
 * @returns boolean
 */
function checker_url(value, format) {
    return /^(http(s)?:\/\/)?[A-Za-z0-9-_]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/.test(value) ? true : 'formatting error';
}
/**
 * @name 校验字符串是否为手机号格式
 * @param {string} value 手机号字符串
 * @param {string} format 此处为空
 */
function checker_mobile(value, format) {
    return /^1\d{10}$/.test(value) ? true : 'formatting error';
}
// @name 校验普通电话、传真号码：可以“+”开头，除数字外，可含有“-”
function checker_phone(value, format) {
    return /^\+?(\d){1,3}[ ]?([-]?((\d)|[ ]){1,12})+$/.test(value) ? true : 'formatting error';
}
// @name 校验邮箱格式是否正确
function checker_email(value, format) {
    return /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/.test(value) ? true : 'formatting error';
}
// @name 校验用户名格式是否正确
function checker_username(value, format) {
    return /^[\w-_@\.]{4,}$/.test(value) ? true : 'formatting error';
}
// @name 校验密码格式是否正确
function checker_password(value, format) {
    return /^.{4,}$/.test(value) ? true : 'formatting error';
}
// @name 校验状态格式是否正确
function checker_status(value, format) {
    return /^[\w-_@\.]+$/.test(value) ? true : 'formatting error';
}
// @name 校验控制器格式是否正确
function checker_controller(value, format) {
    return /^[\w-_\/]+$/.test(value) ? true : 'formatting error';
}
// @name 校验身份证格式是否正确
function checker_idcard(value, format) {
    return (
            /^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$/.test(value) ||
            /^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)$/.test(value)
        )
        ? true : 'formatting error';
}
// @name 校验日期格式是否正确
function checker_date(value, format) {
    if (format === 'Y-m-d H:i:s') {
        var result = value.match(/^(\d{4})(-|\/)(\d{1,2})\2(\d{1,2})\s(\d{1,2}):(\d{1,2}):(\d{1,2})$/);
        if (result == null) {
            return 'formatting error';
        }
        var d = new Date(result[1], result[3] - 1, result[4], result[5], result[6], result[7]);
        return (d.getFullYear() == result[1] && d.getMonth() + 1 == result[3] && d.getDate() == result[4]) ? true : 'formatting error';
    }
    else {
        var result = value.match(/^(\d{4})(-|\/)(\d{1,2})\2(\d{1,2})$/);
        if (result == null) {
            return 'formatting error';
        }
        var d = new Date(result[1], result[3] - 1, result[4]);
        return (d.getFullYear() == result[1] && d.getMonth() + 1 == result[3] && d.getDate() == result[4]) ? true : 'formatting error';
    }
}
// @name 校验时间格式是否正确
function checker_time(value, format) {
    var result = value.match(/^(\d{4})(-|\/)(\d{1,2})\2(\d{1,2})$/);
    if (result == null) {
        return 'formatting error';
    }
    var d = new Date(result[1], result[3] - 1, result[4]);
    return (d.getFullYear() == result[1] && d.getMonth() + 1 == result[3] && d.getDate() == result[4]) ? true : 'formatting error';
}
// @name 校验字符串格式是否为英文
function checker_english(value, format) {
    return /^[a-zA-Z]+$/.test(value) ? true : 'formatting error';
}
// @name 校验字符串格式是否为中文
function checker_chinese(value, format) {
    return /^[\u4E00-\uFA29]+$/.test(value) ? true : 'formatting error';
}
// IP 类型
function checker_ip(value, format)
{
    return /^(([01]?\d?\d|2[0-4]\d|25[0-5])\.){3}([01]?\d?\d|2[0-4]\d|25[0-5])$/.test(value) ? true : 'formatting error';
}
// @name 校验字符串格式是否为JSON
function checker_json(value, format) {
    try {
        value = $.parseJSON(value);
        return (
            typeof(value) === "object" && (checker_object(value) || checker_array(value))
        ) ? true : 'formatting error';
    }
    catch (e) {
        return 'formatting error';
    }
}
