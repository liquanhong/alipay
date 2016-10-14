<?php
use ....../Alipay;
use frontend\components\BaseController;
use yii;

class AlipayController extends BaseController
{

    public $enableCsrfValidation=false;

    public function actionNotify()
    {
        $sn = Yii::$app->request->post('out_trade_no');
        //根据订单编号获取$order
        if ($order){
            $alipay = new Alipay();
            $verify_result = $alipay->verifyNotify(Yii::$app->request);
            if(!$verify_result) {
                return "fail";
            }
          	//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
		    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
			//商户订单号
			$out_trade_no = $_GET['out_trade_no'];
			//支付宝交易号
			$trade_no = $_GET['trade_no'];
			//交易状态
			$trade_status = $_GET['trade_status'];

            if($trade_status === 'TRADE_FINISHED' || $trade_status === 'TRADE_SUCCESS') {
                if ($seller_id == $school->alipay_partner && $order->amount == $total_fee){
                	//判断该笔订单是否在商户网站中已经做过处理
					//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
					//如果有做过处理，不执行商户的业务程序
                }
            }
            echo "success";
        }
    }

    public function actionReturn()
    {
        $sn = Yii::$app->request->get('out_trade_no');
        //根据订单编号获取$order
        if ($order){
            $alipay = new Alipay();
            $verify_result = $alipay->verifyReturn(Yii::$app->request);
            if($verify_result) {
                //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
			    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
				//商户订单号
				$out_trade_no = $_GET['out_trade_no'];
				//支付宝交易号
				$trade_no = $_GET['trade_no'];
				//交易状态
				$trade_status = $_GET['trade_status'];
                if($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
                    $order = $this->_orderService->getOrderBysn($sn);
                    if ($order && $seller_id == $school->alipay_partner && $order->amount == $total_fee){
                        //判断该笔订单是否在商户网站中已经做过处理
						//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
						//如果有做过处理，不执行商户的业务程序
                    }
                }
            }
//            return $this->render('return', ['verify_return' => $verify_result]);
        }
    }

}