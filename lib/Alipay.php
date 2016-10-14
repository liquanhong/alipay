<?php
use yii/web/Request;
class Alipay{
	//合作身份者ID，签约账号，以2088开头由16位纯数字组成的字符串，查看地址：https://b.alipay.com/order/pidAndKey.htm
	private $_partner=Yii::$app->param['partner'];
	//收款支付宝账号，以2088开头由16位纯数字组成的字符串，一般情况下收款账号就是签约账号
	private $_seller_id;
	// MD5密钥，安全检验码，由数字和字母组成的32位字符串，查看地址：https://b.alipay.com/order/pidAndKey.htm
	private $_key=Yii::$app->param['key'];
	// 服务器异步通知页面路径  需http://格式的完整路径，不能加?id=123这类自定义参数，必须外网可以正常访问
	private $_notifyUrl;
	// 页面跳转同步通知页面路径 需http://格式的完整路径，不能加?id=123这类自定义参数，必须外网可以正常访问
	private $_returnUrl;
	//配置参数
	private $_config;
	//防钓鱼参数,默认是有防钓鱼设置,如果不需要下面函数参数设置为false或者传递参数时给false.
	private $_isAntiPhishing;

	public function __construct($notifyUrl=null,$returnUrl=null,$isAntiPhishing=true)
    {
        $this->_partner=//合作身份者ID
        $this->_key=;//MD5密钥，安全检验码，由数字和字母组成的32位字符串
        $this->_notifyUrl=$notifyUrl;
        $this->_returnUrl=$returnUrl;
        $this->_isAntiPhishing=$isAntiPhishing;
        $this->_config=$this->buildConfig();
    }
    /**
     * 构建配置参数
     * @return array
     */
    private function buildConfig(){
        return [
            'partner'=>$this->_partner,
            'key'=>$this->_key,
            'sign_type'=>strtoupper('MD5'),
            'input_charset'=>strtolower('utf-8'),
		    'cacert'=>\Yii::getAlias('....../cacert.pem'),//ca证书路径地址，用于curl中ssl校验.请保证cacert.pem文件在当前文件夹目录中
            'transport'=>'http',
        ];
    }


    /**
     * 构造请求参数数组
     * @return array
     */
    private function buildParams($trade_no,$total_fee,$subject,$body,$show_url){
        return [
            "service" => "create_direct_pay_by_user",
            "partner" => $this->_partner,
            "seller_id" => $this->_partner,
            "payment_type"	=> 1,
            "notify_url"	=> $this->_notifyUrl,
            "return_url"	=> $this->_returnUrl,
            "out_trade_no"	=> $trade_no,
            "subject"	=> $subject,
            "total_fee"	=> $total_fee,
            "body"	=> $body,
            "show_url"	=> $show_url,
            "anti_phishing_key"	=> $this->_isAntiPhishing?(new AlipaySubmit($this->_config))->query_timestamp():'',
            "exter_invoke_ip"	=> '',
            "_input_charset"	=> trim(strtolower($this->_config['input_charset']))
        ];
    }

    /**
     * 发送支付请求
     * @param string $trade_no 订单号
     * @param string $total_fee 总费用
     * @param string $subject 订单名称
     * @param string $body 产品内容
     * @param string $show_url 产品链接
     * @return string
     */
    public function sendRequest($trade_no,$total_fee,$subject,$body,$show_url){
        $alipaySubmit=new AlipaySubmit($this->_config);
        return $alipaySubmit->buildRequestForm($this->buildParams($trade_no,$total_fee,$subject,$body,$show_url),"get", "确认");
    }

    /**
     * 验证异步通知
     * @param Request $request
     * @return boolean
     */
    public function verifyNotify(Request $request){
        $alipayNotify=new AlipayNotify($this->_config);
        return $alipayNotify->verifyNotify($request);
    }

    /**
     * 验证同步跳转
     * @param Request $request
     * @return boolean
     */
    public function verifyReturn(Request $request){
        $alipayNotify=new AlipayNotify($this->_config);
        return $alipayNotify->verifyReturn($request);
    }
}