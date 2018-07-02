<?php

/* @var $this \yii\web\View */
use yii\helpers\Url;
use common\helpers\Render;

$this->title = '首页';
?>
<style>
    .flyer-form .warn-span {top:1px;right:1px;}
</style>


<div id="pg-lefter">
    <ul id="left-navigator">
        <li><a href="#introduct"><i class="icon-double-angle-right"></i> 一、需求背景</a></li>
        <li><a href="#scene"><i class="icon-double-angle-right"></i> 二、使用场景</a></li>
        <li><a href="#file-structure"><i class="icon-double-angle-right"></i> 三、文件结构</a></li>
        <li><a href="#php-validate"><i class="icon-double-angle-right"></i> 四、PHP校验方式</a></li>
        <li><a href="#javascript-validate"><i class="icon-double-angle-right"></i> 五、JavaScript校验方式</a></li>
        <li><a href="#rule-define"><i class="icon-double-angle-right"></i> 六、规则定义</a></li>
        <li><a href="#demo"><i class="icon-double-angle-right"></i> 七、数据校验示例</a></li>
        <li>　　<a href="#demo-int"><i class="icon-double-angle-right"></i> 1、整形</a></li>
        <li>　　<a href="#demo-float"><i class="icon-double-angle-right"></i> 2、浮点型</a></li>
        <li>　　<a href="#demo-chinese"><i class="icon-double-angle-right"></i> 3、中文</a></li>
        <li>　　<a href="#demo-email"><i class="icon-double-angle-right"></i> 4、邮箱</a></li>
        <li>　　<a href="#demo-eq"><i class="icon-double-angle-right"></i> 5、值相等</a></li>
        <li>　　<a href="#demo-follow"><i class="icon-double-angle-right"></i> 6、跟随必填</a></li>
        <li>　　<a href="#demo-value-rule"><i class="icon-double-angle-right"></i> 7、按值重置类型</a></li>
        <li>　　<a href="#demo-multi-one"><i class="icon-double-angle-right"></i> 8、二选一</a></li>
        <li><a href="#php-remind"><i class="icon-double-angle-right"></i> 八、PHP校验思路</a></li>
        <li><a href="#javascript-remind"><i class="icon-double-angle-right"></i> 九、JavaScript校验思路</a></li>
    </ul>
</div>
<div id="pg-righter">
    <div class="box-title" id="introduce">Checker 使用文档</div>
    <div class="data-title btn" id="introduce">一、需求背景</div>
    <div class="box-content">
        <p>　　在日常开发工作中，我们需要对收集到的数据进行格式准确性的校验，但是由于前端、后端的差异，导致我们需要分别进行校验，So，本例就是为了实现数据校验的统一配置，以减少工作量。</p>
    </div>
    <div class="box-content cl-red">
        <p>　　最终目的：为了懒！！！懒！！！懒！！！</p>
    </div>
    <div class="data-title" id="scene">二、使用场景</div>
    <div class="box-content">
        <p>　　所有数据校验场景</p>
    </div>
    <div class="data-title" id="file-structure">三、文件结构</div>
    <div class="box-content">
        <div class="warn success">本例仅使用两个文件对数据进行校验，PHP端验证类：Checker.php，JS端验证类：checker.class.js</div>
        <div class="data-code mt-10px">
            <h3>PHP 校验主函数：</h3>
            <ul>
                <li>public static function authentication($rules, $params, $recycle = false)</li>
                <li>{</li>
                <li>　　/* @var $checker Checker */</li>
                <li>　　$checker = static::getInstance();</li>
                <li>　　$checker->reset();</li>
                <li>　　$checker->setRecycle($recycle);</li>
                <li>　　$checker->setParams($params);</li>
                <li>　　$checker->setRules($rules);</li>
                <li>　　try {</li>
                <li>　　　　// 多选一 规则重置</li>
                <li>　　　　$checker->initOrRelateRule();</li>
                <li>　　　　// 关系数据 规则重置</li>
                <li>　　　　$checker->initRelateRule();</li>
                <li>　　　　// 开始校验每条数据规则</li>
                <li class="cl-red">　　　　return $checker->verifyData();</li>
                <li>　　}</li>
                <li>　　catch(\Exception $exp) {</li>
                <li>　　　　return ['code' => 'System.Error', 'message' => $exp->getMessage()];</li>
                <li>　　}</li>
                <li>}</li>
            </ul>
        </div>
        <div class="data-code mt-10px">
            <h3>JavaScript 校验主函数：</h3>
            <ul>
                <li>this.init = function(param) {</li>
                <li>　　this.reset(param.ruleDom).setForm($(param.ruleDom).attr('data-form'));</li>
                <li>　　// 初始化DOM实时验证</li>
                <li>　　this.initEvent();</li>
                <li>　　if($(this.form).length) {</li>
                <li>　　　　// 初始化提交验证</li>
                <li>　　　　var checkerClass = this;</li>
                <li>　　　　$(this.form).submit(function () {</li>
                <li class="cl-red">　　　　　　return checkerClass.validate();</li>
                <li>　　　　});</li>
                <li>　　}</li>
                <li>}</li>
            </ul>
        </div>
    </div>
    <div class="box-title">校验方式</div>
    <div class="data-title btn" id="php-validate">四、PHP校验方式</div>
    <div class="box-content">
        <div class="data-code">
            <h3>PHP 校验实现代码：</h3>
            <ul>
                <li>// 引入Checker</li>
                <li>use common\helpers\Checker;</li>
                <li>// 规则定义</li>
                <li>$rule = [</li>
                <li>　　'param' => [</li>
                <li>　　　　'role' => ['所属组', ['int', 'required']],</li>
                <li>　　],</li>
                <li>];</li>
                <li>$param = ['role' => '1'];</li>
                <li>// 开始校验数据</li>
                <li class="cl-red">$checker = Checker::authentication($rule, $param, false);</li>
                <li>if($checker['code'] != Checker::SuccessCode) {</li>
                <li>　　echo '数据异常：'.$checker['message'];</li>
                <li>}</li>
                <li>else {</li>
                <li>　　echo '数据校验通过';</li>
                <li>}</li>
            </ul>
        </div>
        <div class="sub-title-sub mt-10px">代码解读</div>
        <div class="data-content">
            <p class="mt-10px">1、Checker::authentication( <span class="flyer-status thin blue">$rule</span>, <span class="flyer-status thin blue">$param</span>, <span class="flyer-status thin blue">$recycle</span> );</p>
            <p>2、<span class="flyer-status thin purple">authentication</span> 接收三个参数，<span class="flyer-status thin blue">$rule</span>：校验规则数组，<span class="flyer-status thin blue">$param</span>：校验数据数组，<span class="flyer-status thin blue">$recycle</span>：遇错是否继续（true:继续，false:退出，默认值:false）</p>
            <p>3、返回值：<span class="flyer-status thin red">['code' => 'string', 'message' => '数据异常提示']</span>，<span class="flyer-status thin red">code</span> eq <span class="flyer-status thin red">Checker::SuccessCode</span> 表示校验成功，其他值均为失败！</p>
        </div>
    </div>
    <div class="data-title" id="javascript-validate">五、JavaScript校验方式</div>
    <div class="box-content">
        <div class="data-code">
            <h3>JavaScript 校验实现代码：</h3>
            <ul>
                <li>&lt;form id="<span class="flyer-status thin red">flyer-login</span>" method="post" onsubmit="return false;"&gt;</li>
                <li>　　&lt;div class="form-item <span class="flyer-status thin blue">checker</span>"&gt;</li>
                <li>　　　　&lt;div class="input-block"&gt;&lt;input class="flyer-input" name="username" type="text" placeholder="username."&gt;&lt;/div&gt;</li>
                <li>　　&lt;/div&gt;</li>
                <li>　　&lt;div class="form-item <span class="flyer-status thin blue">checker</span>"&gt;</li>
                <li>　　　　&lt;div class="input-block"&gt;&lt;input class="flyer-input" name="password" type="password" placeholder="password."&gt;&lt;/div&gt;</li>
                <li>　　　　<span class="flyer-status thin orange">&lt;span class="warn-span"&gt;&lt;/span&gt;</span></li>
                <li>　　&lt;/div&gt;</li>
                <li>　　&lt;div class="form-item mt-10px"&gt;</li>
                <li>　　　　&lt;div class="inline-block w-p100"&gt;</li>
                <li>　　　　　　&lt;button class="flyer-button normal" id="login-button" type="submit"&gt;Sign In&lt;/button&gt;</li>
                <li>　　　　&lt;/div&gt;</li>
                <li>　　&lt;/div&gt;</li>
                <li>　　&lt;textarea id="flyer-login-json" <span class="flyer-status thin red">data-form="#flyer-login" style="display:none;"</span>&gt;{"param":{"username":["\u767b\u5f55\u540d",["username","required"]],"password":["\u5bc6\u7801",["password","required"]]}}&lt;/textarea&gt;</li>
                <li>&lt;/form&gt;</li>
                <li>&lt;scrript&gt;</li>
                <li>　　$(document).ready({</li>
                <li>　　　　// 表单数据验证</li>
                <li>　　　　(new checker).init({ ruleDom: '<span class="flyer-status thin red">#flyer-login-json</span>' });</li>
                <li>　　});</li>
                <li>&lt;/script&gt;</li>
            </ul>
        </div>
        <div class="sub-title mt-10px">用户登录数据校验示例：</div>
        <div class="data-content">
            <div class="data-code mb-20px">
                <h3>规则定义：</h3>
                <ul>
                    <li>// @name 登陆数据校验</li>
                    <li>// @param 用户名 username格式、必填</li>
                    <li>// @param 密码 password格式、必填</li>
                    <li>$rule = [</li>
                    <li>　　'param' => [</li>
                    <li>　　　　'username' => ['用户名', ['username', 'required']],</li>
                    <li>　　　　'password' => ['密码', ['password', 'required']],</li>
                    <li>　　],</li>
                    <li>];</li>
                </ul>
            </div>
            <form class="flyer-form pane" id="flyer-login" method="post" onsubmit="return false;">
                <div class="form-item checker">
                    <div class="input-title">用户名</div>
                    <div class="input-block"><input class="flyer-input" name="username" type="text" placeholder="username."></div>
                </div>
                <div class="form-item checker">
                    <div class="input-title">密码</div>
                    <div class="input-block"><input class="flyer-input" name="password" type="text" placeholder="password."></div>
                    <span class="warn-span"></span>
                </div>
                <div class="form-item mt-10px">
                    <div class="inline-block w-p100">
                        <button class="flyer-button normal border-round w-p100" id="login-button" type="submit">Sign In</button>
                    </div>
                </div>
                <textarea id="flyer-login-json" data-form="#flyer-login" style="display:none;">{"param":{"username":["\u767b\u5f55\u540d",["username","required"]],"password":["\u5bc6\u7801",["password","required"]]}}</textarea>
            </form>
            <div class="sub-title-sub mt-10px">代码解读</div>
            <div class="data-content">
                <p>1、(new checker).init({ ruleDom: '<span class="flyer-status thin blue">#textarea-id</span>' });</p>
                <p>2、实例化一个checker类，使用<span class="flyer-status thin purple">init</span>函数初始化配置参数，目前仅接收一个对象参数，<span class="flyer-status thin blue">ruleDom</span>：rule规则元素，rule具体的规则存放在<span class="flyer-status thin green">$(ruleDome).val()</span>中</p>
                <p>3、init函数无返回值</p>
                <p>4、如果<span class="flyer-status thin blue">ruleDom</span>设置了属性data-form时，程序会自动为该Form表单绑定<span class="flyer-status thin purple">onSubmit</span>事件，当Form中的数据格式校验通过时，Form表单会提交，否则程序会阻止Form表单的提交</p>
                <p>5、如果需要对输入框的合法性进行<span class="flyer-status thin orange">颜色变化</span>提示，需要为当前输入框的容器赋予<span class="flyer-status thin red">class="checker"</span>样式类</p>
                <p>6、如果需要对输入框的合法性进行<span class="flyer-status thin orange">文字描述</span>提示，需要在当前输入框的容器添加<span class="flyer-status thin red">&lt;span class="warn-span"&gt;&lt;/span&gt;</span>节点</p>
            </div>
        </div>
    </div>
    <div class="data-title" id="rule-define">六、规则定义</div>
    <div class="box-content">
        <div class="data-code mb-20px">
            <h3>参数格式：</h3>
            <ul>
                <li>$rule = [</li>
                <li>　　'param' => [</li>
                <li>　　　　键1 => [名称<span class="cl-orange">（, <span class="flyer-status thin blue">规则1</span> | <span class="flyer-status thin blue">[规则1，规则2 => 格式]</span><span class="cl-purple">（，[规则1 => 异常提示, 规则2 => 异常提示]）</span>）</span>]，</li>
                <li class="cl-orange">　　　　键2 => [名称]，</li>
                <li class="cl-orange">　　　　键3 => [名称，规则31]，</li>
                <li class="cl-orange">　　　　键4 => [名称，[规则41]]，</li>
                <li class="cl-orange">　　　　键5 => [名称，[规则51，规则52 => 格式]]，</li>
                <li class="cl-orange">　　　　键6 => [名称，[规则61，规则62 => 格式]，[规则61 => 异常提示]]，</li>
                <li>　　]<span class="cl-orange">（，</span></li>
                <li class="cl-orange">　　'relate' => [</li>
                <li class="cl-orange">　　　　[[[键1，规则3, 格式], [键2，规则4, 格式]，<span class="flyer-status thin blue">键</span> | <span class="flyer-status thin blue">[键，键 => ['rule' => [规则集合]，'message' => [提示集合]]]</span>]，</li>
                <li class="cl-orange">　　　　[[[键3，规则r1, 格式]]，键1]，</li>
                <li class="cl-orange">　　　　[[[键4，规则r3, 格式]，[键5，规则r2, 格式]]，[键1，键2]]，</li>
                <li class="cl-orange">　　　　[[[键5，规则r4, 格式]]，[键1 => ['rule' => [规则2]]]]，</li>
                <li class="cl-orange">　　　　[[[键6，规则r5, 格式]]，[键1 => ['rule' => [规则2], 'message' => [规则2 => 异常提示]]]]，</li>
                <li class="cl-orange">　　]），</li>
                <li>];</li>
            </ul>
        </div>
        <div class="sub-title-sub mt-10px">规则解读</div>
        <div class="data-content">
            <p>名词解释：param => 固定值，校验规则的集合</p>
            <p>　　　　　relate => 固定值，关联校验规则的集合</p>
            <p>　　　　　rule => 固定值，用于关联校验中重置规则的数据集合key</p>
            <p>　　　　　message => 固定值，用于关联校验中重置提示的数据集合key</p>
            <p>　　　　　键 => 数据的key</p>
            <p>　　　　　名称 => 数据的名称</p>
            <p>　　　　　规则 => 校验的类型（int, required, let, eq, in, email, chinese等）</p>
            <p>　　　　　格式 => 规则的校验限制（let => 10，in => ['1', '2']等）</p>
            <p>　　　　　异常提示 => 当规则校验不通过时的提示信息</p>
            <p></p>
            <p>1、<span class="flyer-status thin orange">　</span> / <span class="flyer-status thin purple">　</span> 颜色文字标识的参数可不传</p>
            <p>2、<span class="flyer-status thin blue">　</span> | <span class="flyer-status thin blue">　</span> 表示两种数据表示格式任选一种</p>
        </div>
    </div>
    <div class="data-title" id="demo">七、数据校验示例</div>
    <div class="box-content">
        <div class="sub-title" id="demo-int">整形（int）、可空定义</div>
        <div class="data-content">
            <div class="data-code mb-20px">
                <h3>规则定义：</h3>
                <ul>
                    <li>// @name 规则描述</li>
                    <li>// @param input 整形类型，可空，特殊定制异常提示</li>
                    <li>$rule = [</li>
                    <li>　　'param' => [</li>
                    <li>　　　　'input' => ['input', 'int', ['int' => '必须是整数类型：-1+1|1']],</li>
                    <li>　　],</li>
                    <li>];</li>
                </ul>
            </div>
            <div class="flyer-form pane" id="flyer-check01">
                <div class="form-item checker">
                    <div class="input-title">int校验</div>
                    <div class="input-block"><input class="flyer-input" name="input" type="text" placeholder="'input' => ['input', 'int', ['int' => '必须是整数类型：-1+1|1']]"></div>
                    <span class="warn-span"></span>
                </div>
                <div class="form-item mt-10px">
                    <div class="inline-block w-p100">
                        <button class="flyer-button normal border-round w-p100" id="check-button01" type="submit">校验</button>
                    </div>
                </div>
                <textarea id="flyer-check01-json" data-form="#flyer-check01" style="display:none;">{"param":{"input":["\u8f93\u5165\u6846","int",{"int":"\u5fc5\u987b\u662f\u6574\u6570\u7c7b\u578b\uff1a-1|+1|1"}]}}</textarea>
            </div>
            <div class="data-code mt-10px">
                <h3>实现代码：</h3>
                <ul>
                    <li>&lt;div id="<span class="flyer-status thin red">flyer-check01</span>"&gt;</li>
                    <li>　　&lt;div class="form-item <span class="flyer-status thin blue">checker</span>"&gt;</li>
                    <li>　　　　&lt;div class="input-block"&gt;&lt;input class="flyer-input" name="username" type="text" placeholder="'input' => ['input', 'int', ['int' => '必须是整数类型：-1+1|1']]"&gt;&lt;/div&gt;</li>
                    <li>　　&lt;/div&gt;</li>
                    <li>　　&lt;div class="form-item mt-10px"&gt;</li>
                    <li>　　　　&lt;div class="inline-block w-p100"&gt;</li>
                    <li>　　　　　　&lt;button class="flyer-button normal" id="check-button01" type="submit"&gt;校验&lt;/button&gt;</li>
                    <li>　　　　&lt;/div&gt;</li>
                    <li>　　&lt;/div&gt;</li>
                    <li>　　&lt;textarea id="flyer-check01-json" <span class="flyer-status thin red">data-form="#flyer-check01" style="display:none;"</span>&gt;{"param":{"input":["\u8f93\u5165\u6846","int",{"int":"\u5fc5\u987b\u662f\u6574\u6570\u7c7b\u578b\uff1a-1|+1|1"}]}}&lt;/textarea&gt;</li>
                    <li>&lt;/div&gt;</li>
                    <li>&lt;scrript&gt;</li>
                    <li>　　$(document).ready({</li>
                    <li>　　　　// int 整数类型数据验证</li>
                    <li>　　　　checker01 = new checker();</li>
                    <li>　　　　checker01.init({ ruleDom: '<span class="flyer-status thin red">#flyer-check01-json</span>' });</li>
                    <li>　　　　// 校验按钮点击事件绑定</li>
                    <li>　　　　$('#check-button01').bind('click', function() {</li>
                    <li>　　　　　　if(checker01.validate()) {</li>
                    <li>　　　　　　　　layer.alert('数据校验通过', { icon: 6 });</li>
                    <li>　　　　　　}</li>
                    <li>　　　　　　else {</li>
                    <li>　　　　　　　　layer.alert('格式异常', { icon: 5 });</li>
                    <li>　　　　　　}</li>
                    <li>　　　　});</li>
                    <li>　　});</li>
                    <li>&lt;/script&gt;</li>
                </ul>
            </div>
            <div class="warn notice mt-10px">注：不可为空校验需加入rquired规则（此处不演示），例：'input' => ['输入框', ['int', <span class="flyer-status thin red">'required'</span>], ['int' => '必须是整数类型：-1|+1|1']]</div>
        </div>
    </div>
    <div class="box-content">
        <div class="sub-title" id="demo-float">浮点型类型（float）、最小值（get）、最大值（let）、必填定义</div>
        <div class="data-content">
            <div class="data-code mb-20px">
                <h3>规则定义：</h3>
                <ul>
                    <li>// @name 规则描述</li>
                    <li>// @param money 浮点型类型，10 >= money >= 100，必填，特殊定制异常提示</li>
                    <li>$rule = [</li>
                    <li>　　'param' => [</li>
                    <li>　　　　'money' => ['人民币', ['int', 'get' => 10, 'let' => 100, 'required'], ['int' => '你家的钱不是用数字表示的？', 'get' => '你只有这点钱？', 'let' => '你这么穷，哪来的超过100块钱？', 'required' => '我操，去要饭吧！！！']],</li>
                    <li>　　],</li>
                    <li>];</li>
                </ul>
            </div>
            <div class="flyer-form pane" id="flyer-check07">
                <div class="form-item checker">
                    <div class="input-title">人民币</div>
                    <div class="input-block"><input class="flyer-input" name="money" type="text" placeholder="'money' => ['人民币', ['float', 'get' => 10, 'let' => 100, 'required'], ['float' => '你家的钱不是用数字表示的？', 'get' => '你只有这点钱？', 'let' => '你这么穷，哪来的超过100块钱？', 'required' => '我操，去要饭吧！！！']]"></div>
                    <span class="warn-span"></span>
                </div>
                <div class="form-item mt-10px">
                    <div class="inline-block w-p100">
                        <button class="flyer-button normal border-round w-p100" id="check-button07" type="submit">校验</button>
                    </div>
                </div>
                <textarea id="flyer-check07-json" data-form="#flyer-check07" style="display:none;">{"param":{"money":["\u4eba\u6c11\u5e01",{"0":"float","get":10,"let":100,"1":"required"},{"float":"\u4f60\u5bb6\u7684\u94b1\u4e0d\u662f\u7528\u6570\u5b57\u8868\u793a\u7684\uff1f","get":"\u4f60\u53ea\u6709\u8fd9\u70b9\u94b1\uff1f","let":"\u4f60\u8fd9\u4e48\u7a77\uff0c\u54ea\u6765\u7684\u8d85\u8fc7100\u5757\u94b1\uff1f","required":"\u6211\u64cd\uff0c\u53bb\u8981\u996d\u5427\uff01\uff01\uff01"}]}}</textarea>
            </div>
        </div>
    </div>
    <div class="box-content">
        <div class="sub-title" id="demo-chinese">中文类型（chinese）、最小长度（minlength）、最大长度（maxlength）、必填定义</div>
        <div class="data-content">
            <div class="data-code mb-20px">
                <h3>规则定义：</h3>
                <ul>
                    <li>// @name 规则描述</li>
                    <li>// @param title 中文格式，4 - 16位长度，必填，特殊定制非中文异常提示</li>
                    <li>$rule = [</li>
                    <li>　　'param' => [</li>
                    <li>　　　　'title' => ['文章标题', ['chinese', 'minlength' => 4, 'maxlength' => 16, 'required'], ['chinese' => '不用中文，卖国贼！']],</li>
                    <li>　　],</li>
                    <li>];</li>
                </ul>
            </div>
            <div class="flyer-form pane" id="flyer-check08">
                <div class="form-item checker">
                    <div class="input-title">文章标题</div>
                    <div class="input-block"><input class="flyer-input" name="title" type="text" placeholder="'title' => ['文章标题', ['chinese', 'minlength' => 4, 'maxlength' => 16, 'required'], ['chinese' => '不用中文，卖国贼！']]"></div>
                    <span class="warn-span"></span>
                </div>
                <div class="form-item mt-10px">
                    <div class="inline-block w-p100">
                        <button class="flyer-button normal border-round w-p100" id="check-button08" type="submit">校验</button>
                    </div>
                </div>
                <textarea id="flyer-check08-json" data-form="#flyer-check08" style="display:none;">{"param":{"title":["\u6587\u7ae0\u6807\u9898",{"0":"chinese","minlength":4,"maxlength":16,"1":"required"},{"chinese":"\u4e0d\u7528\u4e2d\u6587\uff0c\u5356\u56fd\u8d3c\uff01"}]}}</textarea>
            </div>
        </div>
    </div>
    <div class="box-content">
        <div class="sub-title" id="demo-email">邮箱（email）、必填定义，特殊定制异常提示</div>
        <div class="data-content">
            <div class="data-code mb-20px">
                <h3>规则定义：</h3>
                <ul>
                    <li>// @name 规则描述</li>
                    <li>// @param input 邮箱格式、必填，特殊定制异常提示</li>
                    <li>$rule = [</li>
                    <li>　　'param' => [</li>
                    <li>　　　　'input' => ['input', ['email', 'required'], ['email' => '怎么可以是错误的？', 'required' => '你无权留空！']],</li>
                    <li>　　],</li>
                    <li>];</li>
                </ul>
            </div>
            <div class="flyer-form pane" id="flyer-check02">
                <div class="form-item checker">
                    <div class="input-title">邮箱地址</div>
                    <div class="input-block"><input class="flyer-input" name="input" type="text" placeholder="'input' => ['input', ['email', 'required'], ['email' => '怎么可以是错误的？', 'required' => '你无权留空！']]"></div>
                    <span class="warn-span"></span>
                </div>
                <div class="form-item mt-10px">
                    <div class="inline-block w-p100">
                        <button class="flyer-button normal border-round w-p100" id="check-button02" type="submit">校验</button>
                    </div>
                </div>
                <textarea id="flyer-check02-json" data-form="#flyer-check02" style="display:none;">{"param":{"input":["\u8f93\u5165\u6846",["email","required"],{"email":"\u600e\u4e48\u53ef\u4ee5\u662f\u9519\u8bef\u7684\uff1f","required":"\u4f60\u65e0\u6743\u7559\u7a7a\uff01"}]}}</textarea>
            </div>
        </div>
    </div>
    <div class="box-content">
        <div class="sub-title" id="demo-eq">关联校验之值相等</div>
        <div class="data-content">
            <div class="data-code mb-20px">
                <h3>规则定义：</h3>
                <ul>
                    <li>// @name 规则描述</li>
                    <li>// @param password 密码格式，必填</li>
                    <li>// @param repassword 密码格式，必填，必须与password保持一致</li>
                    <li>$rule = [</li>
                    <li>　　'param' => [</li>
                    <li>　　　　'password' => ['密码', ['password', 'required']],</li>
                    <li>　　　　'repassword' => ['重复密码', ['password', 'eq' => ':password', 'required'], ['eq' => '两次密码输入不一致']],</li>
                    <li>　　],</li>
                    <li>　　'relate' => [</li>
                    <li>　　　　[[['password', 'required']], 'repassword'],</li>
                    <li>　　],</li>
                    <li>];</li>
                </ul>
            </div>
            <div class="flyer-form pane" id="flyer-check06">
                <div class="form-item checker">
                    <div class="input-title">密码</div>
                    <div class="input-block"><input class="flyer-input" name="password" type="text" placeholder="'password' => ['密码', ['password', 'required']]"></div>
                    <span class="warn-span"></span>
                </div>
                <div class="form-item checker">
                    <div class="input-title">重复密码</div>
                    <div class="input-block"><input class="flyer-input" name="repassword" type="text" placeholder="'repassword' => ['重复密码', ['password', 'eq' => ':password', 'required']]"></div>
                    <span class="warn-span"></span>
                </div>
                <div class="form-item mt-10px">
                    <div class="inline-block w-p100">
                        <button class="flyer-button normal border-round w-p100" id="check-button06" type="submit">校验</button>
                    </div>
                </div>
                <textarea id="flyer-check06-json" data-form="#flyer-check06" style="display:none;">{"param":{"password":["\u5bc6\u7801",["password","required"]],"repassword":["\u91cd\u590d\u5bc6\u7801",{"0":"password","eq":":password","1":"required"},{"eq":"\u4e24\u6b21\u5bc6\u7801\u8f93\u5165\u4e0d\u4e00\u81f4"}]},"relate":[[[["password","required"]],"repassword"]]}</textarea>
            </div>
        </div>
    </div>
    <div class="box-content">
        <div class="sub-title" id="demo-follow">关联校验之跟随必填</div>
        <div class="data-content">
            <div class="data-code mb-20px">
                <h3>规则定义：</h3>
                <ul>
                    <li>// @name 规则描述</li>
                    <li>// @param ip IP格式，可为空</li>
                    <li>// @param port 数字格式，当IP填写时，port不可为空，否则可为空</li>
                    <li>$rule = [</li>
                    <li>　　'param' => [</li>
                    <li>　　　　'ip' => ['IP地址', ['ip']],</li>
                    <li>　　　　'port' => ['端口', ['number'], ['required' => '填写IP地址时，端口不能为空！']],</li>
                    <li>　　],</li>
                    <li>　　'relate' => [</li>
                    <li>　　　　[[['ip', 'required']], ['port']],</li>
                    <li>　　],</li>
                    <li>];</li>
                </ul>
            </div>
            <div class="flyer-form pane" id="flyer-check03">
                <div class="form-item checker">
                    <div class="input-title">IP地址</div>
                    <div class="input-block"><input class="flyer-input" name="ip" type="text" placeholder="'ip' => ['IP地址', ['ip']]"></div>
                    <span class="warn-span"></span>
                </div>
                <div class="form-item checker">
                    <div class="input-title">端口</div>
                    <div class="input-block"><input class="flyer-input" name="port" type="text" placeholder="'port' => ['端口', ['number'], ['required' => '填写IP地址时，端口不能为空！']]"></div>
                    <span class="warn-span"></span>
                </div>
                <div class="form-item mt-10px">
                    <div class="inline-block w-p100">
                        <button class="flyer-button normal border-round w-p100" id="check-button03" type="submit">校验</button>
                    </div>
                </div>
                <textarea id="flyer-check03-json" data-form="#flyer-check03" style="display:none;">{"param":{"ip":["IP\u5730\u5740",["ip"]],"port":["\u7aef\u53e3",["number"],{"required":"\u586b\u5199IP\u5730\u5740\u65f6\uff0c\u7aef\u53e3\u4e0d\u80fd\u4e3a\u7a7a\uff01"}]},"relate":[[[["ip","required"]],["port"]]]}</textarea>
            </div>
        </div>
    </div>
    <div class="box-content">
        <div class="sub-title" id="demo-value-rule">关联校验之按值重置关联键数据格式</div>
        <div class="data-content">
            <div class="data-code mb-20px">
                <h3>规则定义：</h3>
                <ul>
                    <li>// @name 规则描述</li>
                    <li>// @param idtype 仅限两个值：身份证、其他，必填</li>
                    <li>// @param idcard 必填，当idtype=身份证时，idcard必须是身份证格式，否自可任意填写</li>
                    <li>$rule = [</li>
                    <li>　　'param' => [</li>
                    <li>　　　　'idtype' => ['证件类型', ['in' => ['身份证', '其他'], 'required'], ['in' => '未知的证件类型！']],</li>
                    <li>　　　　'idcard' => ['证件号码', ['required']],</li>
                    <li>　　],</li>
                    <li>　　'relate' => [</li>
                    <li>　　　　[[['idtype', 'eq', '身份证']], ['idcard' => ['rule' => ['idcard', 'required']]]],</li>
                    <li>　　],</li>
                    <li>];</li>
                </ul>
            </div>
            <div class="flyer-form pane" id="flyer-check04">
                <div class="form-item checker">
                    <div class="input-title">证件类型</div>
                    <div class="input-block"><input class="flyer-input" name="idtype" type="text" placeholder="'idtype' => ['证件类型', ['in' => ['身份证', '其他'], 'required'], ['in' => '未知的证件类型！']]"></div>
                    <span class="warn-span"></span>
                </div>
                <div class="form-item checker">
                    <div class="input-title">证件号码</div>
                    <div class="input-block"><input class="flyer-input" name="idcard" type="text" placeholder="'idcard' => ['证件号码', ['required']]"></div>
                    <span class="warn-span"></span>
                </div>
                <div class="form-item mt-10px">
                    <div class="inline-block w-p100">
                        <button class="flyer-button normal border-round w-p100" id="check-button04" type="submit">校验</button>
                    </div>
                </div>
                <textarea id="flyer-check04-json" data-form="#flyer-check04" style="display:none;">{"param":{"idtype":["\u8bc1\u4ef6\u7c7b\u578b",{"in":["\u8eab\u4efd\u8bc1","\u5176\u4ed6"],"0":"required"},{"in":"\u672a\u77e5\u7684\u8bc1\u4ef6\u7c7b\u578b\uff01"}],"idcard":["\u8bc1\u4ef6\u53f7\u7801",["required"]]},"relate":[[[["idtype","eq","\u8eab\u4efd\u8bc1"]],{"idcard":{"rule":["idcard","required"]}}]]}</textarea>
            </div>
        </div>
    </div>
    <div class="box-content">
        <div class="sub-title" id="demo-multi-one">关联校验之二选一</div>
        <div class="data-content">
            <div class="data-code mb-20px">
                <h3>规则定义：</h3>
                <ul>
                    <li>// @name 规则描述</li>
                    <li>// @param mobile 手机号格式，可为空，不能与电话同时为空</li>
                    <li>// @param phone 电话格式，可为空，不能与手机号同时为空</li>
                    <li>$rule = [</li>
                    <li>　　'param' => [</li>
                    <li>　　　　'mobile' => ['手机号', ['mobile'], ['required' => '手机号与电话不能同时为空！']],</li>
                    <li>　　　　'phone' => ['电话', ['phone'], ['required' => '手机号与电话不能同时为空！']],</li>
                    <li>　　],</li>
                    <li>　　'relate' => [</li>
                    <li>　　　　[[['phone', 'empty']], ['mobile']],</li>
                    <li>　　],</li>
                    <li>];</li>
                </ul>
            </div>
            <div class="flyer-form pane" id="flyer-check05">
                <div class="form-item checker">
                    <div class="input-title">手机号</div>
                    <div class="input-block"><input class="flyer-input" name="mobile" type="text" placeholder="'mobile' => ['手机号', ['mobile'], ['required' => '手机号与电话不能同时为空！']]"></div>
                    <span class="warn-span"></span>
                </div>
                <div class="form-item checker">
                    <div class="input-title">电话</div>
                    <div class="input-block"><input class="flyer-input" name="phone" type="text" placeholder="'phone' => ['电话', ['phone'], ['required' => '手机号与电话不能同时为空！']]"></div>
                    <span class="warn-span"></span>
                </div>
                <div class="form-item mt-10px">
                    <div class="inline-block w-p100">
                        <button class="flyer-button normal border-round w-p100" id="check-button05" type="submit">校验</button>
                    </div>
                </div>
                <textarea id="flyer-check05-json" data-form="#flyer-check05" style="display:none;">{"param":{"mobile":["\u624b\u673a\u53f7",["mobile"],{"required":"\u624b\u673a\u53f7\u4e0e\u7535\u8bdd\u4e0d\u80fd\u540c\u65f6\u4e3a\u7a7a\uff01"}],"phone":["\u7535\u8bdd",["phone"],{"required":"\u624b\u673a\u53f7\u4e0e\u7535\u8bdd\u4e0d\u80fd\u540c\u65f6\u4e3a\u7a7a\uff01"}]},"relate":[[[["phone","empty"]],["mobile"]]]}</textarea>
            </div>
        </div>
    </div>
    <div class="data-title" id="introduce">八、PHP校验思路</div>
    <div class="box-content">
        <h3>PHP校验思路</h3>
        <p>PHP校验时，数据值都是已经确定的，所以相对容易对关联数据进行关联校验</p>
        <p>1、初始化Checker类，配置相关参数</p>
        <p>2、重置关联数据的规则、代码如下</p>
        <div class="data-code">
            <h3>关联数据规则重新定义</h3>
            <ul>
                <li>// 值相关的对应关系验证 【'rules(array)', 'params required(array)'】</li>
                <li>// 如：[[['type', 'eq', 1], ['option', 'in', [1, 2]]], ['reqTxDate', 'reqTxTime', 'reqSeqNo']],</li>
                <li>public function reBuliderRelateRule($relate)</li>
                <li>{</li>
                <li>　　$required = true;</li>
                <li>　　foreach($relate[0] as $rule) {</li>
                <li>　　　　if( ! is_array($rule)) {</li>
                <li>　　　　　　throw new \Exception('未知的数据值关联验证规则', 101005);</li>
                <li>　　　　}</li>
                <li>　　　　// 解析$rule数组</li>
                <li>　　　　$name = $rule[0];</li>
                <li>　　　　$type = $rule[1];</li>
                <li>　　　　// 获取关联键的值</li>
                <li>　　　　$value = isset($this->params[$name]) ? $this->params[$name] : null;</li>
                <li>　　　　switch($type) {</li>
                <li>　　　　　　// 通过校验验证</li>
                <li>　　　　　　case 'pass': {</li>
                <li>　　　　　　　　// 验证当前数据</li>
                <li>　　　　　　　　$checker = $this->singleCheck($this->rules['param'][$name], $name, $value);</li>
                <li>　　　　　　　　if($checker['code'] != 'pass') {</li>
                <li>　　　　　　　　　　$required = false;</li>
                <li>　　　　　　　　}</li>
                <li>　　　　　　} break;</li>
                <li>　　　　　　// 其他校验验证</li>
                <li>　　　　　　default: {</li>
                <li>　　　　　　　　$func = "checker_{$type}";</li>
                <li>　　　　　　　　// 未定义的验证规则，直接退出返回错误</li>
                <li>　　　　　　　　if( ! method_exists($this, $func)) {</li>
                <li>　　　　　　　　　　throw new \Exception("未定义数据值关联验证方法：{$func}", 101006);</li>
                <li>　　　　　　　　}</li>
                <li>　　　　　　　　// 获取数据校验format值</li>
                <li>　　　　　　　　$format = isset($rule[2]) ? $rule[2] : '';</li>
                <li>　　　　　　　　if( ! call_user_func_array([$this, $func], [$value, $format]) === true) {</li>
                <li>　　　　　　　　　　$required = false;</li>
                <li>　　　　　　　　}</li>
                <li>　　　　　　}</li>
                <li>　　　　}</li>
                <li>　　}</li>
                <li>　　if($required) {</li>
                <li>　　　　/**</li>
                <li>　　　　 * 实现功能 -> 当idType = static::$IdTypeIDCard时，idNo的格式必须是身份证号格式，且必填</li>
                <li>　　　　 * [['idType', 'eq', static::$IdTypeIDCard], ['idNo' => ['rule' => ['idcard', 'required']]]</li>
                <li>　　　　 * [['idType', 'eq', static::$IdTypeIDCard], ['idNo' => ['rule' => ['idcard'], 'required']]],</li>
                <li>　　　　 * [['idType', 'eq', static::$IdTypeIDCard], ['idNo' => ['rule' => 'idcard']], 'idNo'],</li>
                <li>　　　　 * 以上三种执行效果是一致的</li>
                <li>　　　　 */</li>
                <li>　　　　if( ! is_array($relate[1])) {</li>
                <li>　　　　　　$relate[1] = [$relate[1]];</li>
                <li>　　　　}</li>
                <li>　　　　foreach($relate[1] as $name => $rule) {</li>
                <li>　　　　　　if(static::checker_int($name) === true) {</li>
                <li>　　　　　　　　if( ! isset($this->rules['param'][$rule])) {</li>
                <li>　　　　　　　　　　throw new \Exception("未知关联数据：{$rule}", 101007);</li>
                <li>　　　　　　　　}</li>
                <li>　　　　　　　　$this->rules['param'][$name][1]['required'] = true;</li>
                <li>　　　　　　}</li>
                <li>　　　　　　else {</li>
                <li>　　　　　　　　if( ! isset($this->rules['param'][$name])) {</li>
                <li>　　　　　　　　　　throw new \Exception("未知关联数据：{$name}", 101007);</li>
                <li>　　　　　　　　}</li>
                <li>　　　　　　　　foreach($rule as $type => $value) {</li>
                <li>　　　　　　　　　　if(static::checker_int($type) === true) {</li>
                <li>　　　　　　　　　　　　if($value == 'required') {</li>
                <li>　　　　　　　　　　　　　　$this->rules['param'][$name][1]['required'] = true;</li>
                <li>　　　　　　　　　　　　　　continue;</li>
                <li>　　　　　　　　　　　　}</li>
                <li>　　　　　　　　　　}</li>
                <li>　　　　　　　　　　switch($type) {</li>
                <li>　　　　　　　　　　　　case 'rule': {</li>
                <li>　　　　　　　　　　　　　　$this->rules['param'][$name][1] = $value;</li>
                <li>　　　　　　　　　　　　} break;</li>
                <li>　　　　　　　　　　　　case 'required': {</li>
                <li>　　　　　　　　　　　　　　$this->rules['param'][$name][1]['required'] = true;</li>
                <li>　　　　　　　　　　　　} break;</li>
                <li>　　　　　　　　　　　　case 'message': {</li>
                <li>　　　　　　　　　　　　　　$this->rules['param'][$name][2] = $value;</li>
                <li>　　　　　　　　　　　　} break;</li>
                <li>　　　　　　　　　　　　default : {</li>
                <li>　　　　　　　　　　　　　　throw new \Exception("未知关联数据规则改变：{$type}", 101008);</li>
                <li>　　　　　　　　　　　　}</li>
                <li>　　　　　　　　　　}</li>
                <li>　　　　　　　　}</li>
                <li>　　　　　　}</li>
                <li>　　　　}</li>
                <li>　　}</li>
                <li>}</li>
            </ul>
        </div>
        <p>3、循环校验每个数据的规则，代码如下</p>
        <div class="data-code">
            <h3>校验单条数据格式是否异常</h3>
            <ul>
                <li>// @name  验证单条数据</li>
                <li>// @param $rule array 验证规则</li>
                <li>// @param $name string 验证字段名称</li>
                <li>// @param $value string / array 字段数据</li>
                <li>// @return array['code', 'message']</li>
                <li>public function singleCheck($rule, $name, $value)</li>
                <li>{</li>
                <li>　　if($this->_echo) {</li>
                <li>　　　　echo 'check: name->', $name, ', value->', print_r($value), "\n";</li>
                <li>　　　　print_r($rule);</li>
                <li>　　　　echo "\n";</li>
                <li>　　}</li>
                <li>　　$oneStatus = 'pass'; $oneMsg = [];</li>
                <li>　　if( ! isset($rule[1])) {</li>
                <li>　　　　return ['code' => $oneStatus, 'message' => $oneMsg];</li>
                <li>　　}</li>
                <li>　　// 获取 参数值</li>
                <li>　　if($value === '' || $value === null) {</li>
                <li>　　　　if($rule[1] == 'required' || isset($rule[1]['required']) || (is_array($rule[1]) && in_array('required', $rule[1]))) {</li>
                <li>　　　　　　$oneStatus = 'null';</li>
                <li>　　　　　　$oneMsg[] = $this->getNotice($name, 'required', '不能为空');</li>
                <li>　　　　}</li>
                <li>　　}</li>
                <li>　　else {</li>
                <li>　　　　if( ! is_array($rule[1])) {</li>
                <li>　　　　　　$rule[1] = [$rule[1]];</li>
                <li>　　　　}</li>
                <li>　　　　// 对 check 数组中的验证类型进行验证</li>
                <li>　　　　foreach($rule[1] as $type => $format) {</li>
                <li>　　　　　　if($this->unRecycle() && $oneStatus != 'pass') {</li>
                <li>　　　　　　　　break;</li>
                <li>　　　　　　}</li>
                <li>　　　　　　// 忽略单数据必填验证</li>
                <li>　　　　　　if( ! is_array($value) && ($type === 'required' || $format === 'required')) {</li>
                <li>　　　　　　　　continue;</li>
                <li>　　　　　　}</li>
                <li>　　　　　　if(static::checker_int($type) === true) {</li>
                <li>　　　　　　　　$type = $format;</li>
                <li>　　　　　　}</li>
                <li>　　　　　　else {</li>
                <li>　　　　　　　　// if $rule 's value format is :*** 则使用$this->params[$format] 的值替代</li>
                <li>　　　　　　　　if(static::checker_string($format) && substr($format, 0, 1) === ':') {</li>
                <li>　　　　　　　　　　$format = str_replace(':', '', $format);</li>
                <li>　　　　　　　　　　if( ! isset($this->params[$format])) {</li>
                <li>　　　　　　　　　　　　throw new \Exception('未知的关联数据键');</li>
                <li>　　　　　　　　　　}</li>
                <li>　　　　　　　　　　$format = $this->params[$format];</li>
                <li>　　　　　　　　}</li>
                <li>　　　　　　}</li>
                <li>　　　　　　$func = 'checker_'.$type;</li>
                <li>　　　　　　if( ! method_exists($this, $func)) {</li>
                <li>　　　　　　　　throw new \Exception("未定义数据值关联验证方法：{$func}", 101006);</li>
                <li>　　　　　　}</li>
                <li>　　　　　　if( ! is_array($value)) {</li>
                <li>　　　　　　　　if(call_user_func_array([$this, $func], [$value, $format]) !== true) {</li>
                <li>　　　　　　　　　　$oneStatus = 'error';</li>
                <li>　　　　　　　　　　$oneMsg[] = $this->getNotice($name, $type, static::getWarn($func));</li>
                <li>　　　　　　　　}</li>
                <li>　　　　　　}</li>
                <li>　　　　　　else {</li>
                <li>　　　　　　　　foreach($value as $k => $v) {</li>
                <li>　　　　　　　　　　if(($msg = call_user_func_array([$this, $func], [$v, $format])) !== true) {</li>
                <li>　　　　　　　　　　　　$oneStatus = 'error';</li>
                <li>　　　　　　　　　　　　$oneMsg[] = '第'.($k + 1).'条'.$this->getNotice($name, $type, $msg);</li>
                <li>　　　　　　　　　　　　if($this->unRecycle()) {</li>
                <li>　　　　　　　　　　　　　　break 2;</li>
                <li>　　　　　　　　　　　　}</li>
                <li>　　　　　　　　　　}</li>
                <li>　　　　　　　　}</li>
                <li>　　　　　　}</li>
                <li>　　　　}</li>
                <li>　　}</li>
                <li>　　// echo str_pad($name, 30, ' '), '：', str_pad($oneStatus, 10, ' '), ' - ', $oneMsg, "\n";</li>
                <li>　　if($oneStatus != 'pass') {</li>
                <li>　　　　$oneMsg = $rule[0] .'：'. implode(', ', $oneMsg);</li>
                <li>　　}</li>
                <li>　　if($this->_echo) {</li>
                <li>　　　　echo 'verify: status->', $oneStatus, ', message->', print_r($oneMsg), "\n";</li>
                <li>　　}</li>
                <li>　　return ['code' => $oneStatus, 'message' => $oneMsg];</li>
                <li>}</li>
            </ul>
        </div>
    </div>
    <div class="data-title" id="javascript-remind">九、JavaScript校验思路</div>
    <div class="box-content">
        <p></p>
    </div>
</div>
<script src="<?= Render::static('flyer/checker.class.js') ?>"></script>
<script>
    $(document).ready(function() {
        // 登陆数据校验
        (new checker).init({ ruleDom: '#flyer-login-json' });

        // int 整数类型数据校验
        var checker01 = new checker();
        checker01.init({ ruleDom: '#flyer-check01-json' });
        $('#check-button01').bind('click', function() {
            if(checker01.validate()) {
                layer.alert('数据校验通过', { icon: 6 });
            }
            else {
                layer.alert('数据格式异常<br>' + checker01.getMessage(), { icon: 5 });
            }
        });

        // email 邮件类型数据校验
        var checker02 = new checker();
        checker02.init({ ruleDom: '#flyer-check02-json' });
        $('#check-button02').bind('click', function() {
            if(checker02.validate()) {
                layer.alert('数据校验通过', { icon: 6 });
            }
            else {
                layer.alert('数据格式异常<br>' + checker02.getMessage(), { icon: 5 });
            }
        });

        // 关联校验之跟随必填
        var checker03 = new checker();
        checker03.init({ ruleDom: '#flyer-check03-json' });
        $('#check-button03').bind('click', function() {
            if(checker03.validate()) {
                layer.alert('数据校验通过', { icon: 6 });
            }
            else {
                layer.alert('数据格式异常<br>' + checker03.getMessage(), { icon: 5 });
            }
        });

        // 关联校验之按值重新规划关联键数据格式
        var checker04 = new checker();
        checker04.init({ ruleDom: '#flyer-check04-json' });
        $('#check-button04').bind('click', function() {
            if(checker04.validate()) {
                layer.alert('数据校验通过', { icon: 6 });
            }
            else {
                layer.alert('数据格式异常<br>' + checker04.getMessage(), { icon: 5 });
            }
        });

        // 关联校验之二选一数据校验
        var checker05 = new checker();
        checker05.init({ ruleDom: '#flyer-check05-json' });
        $('#check-button05').bind('click', function() {
            if(checker05.validate()) {
                layer.alert('数据校验通过', { icon: 6 });
            }
            else {
                layer.alert('数据格式异常<br>' + checker05.getMessage(), { icon: 5 });
            }
        });

        // 关联校验之值相等数据校验
        var checker06 = new checker();
        checker06.init({ ruleDom: '#flyer-check06-json' });
        $('#check-button06').bind('click', function() {
            if(checker06.validate()) {
                layer.alert('数据校验通过', { icon: 6 });
            }
            else {
                layer.alert('数据格式异常<br>' + checker06.getMessage(), { icon: 5 });
            }
        });

        // 浮点型类型数据校验
        var checker07 = new checker();
        checker07.init({ ruleDom: '#flyer-check07-json' });
        $('#check-button07').bind('click', function() {
            if(checker07.validate()) {
                layer.alert('数据校验通过', { icon: 6 });
            }
            else {
                layer.alert('数据格式异常<br>' + checker07.getMessage(), { icon: 5 });
            }
        });

        // 中文类型数据校验
        var checker08 = new checker();
        checker08.init({ ruleDom: '#flyer-check08-json' });
        $('#check-button08').bind('click', function() {
            if(checker08.validate()) {
                layer.alert('数据校验通过', { icon: 6 });
            }
            else {
                layer.alert('数据格式异常<br>' + checker08.getMessage(), { icon: 5 });
            }
        });
    });
</script>