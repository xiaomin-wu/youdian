<?php
defined('IN_IA') or exit('Access Denied');
class AliPay
{
    public $alipay;
    public function __construct()
    {
        global $_W;
        $setting = uni_setting($_W['uniacid']);
        $this->alipay = array('app_id' => $_W['we7_wmall']['config']['payment']['alipay_cert']['app_id']);
    }
    public function array2url($params, $force = false)
    {
        $str = '';
        foreach ($params as $key => $val) {
            if ($force && empty($val)) {
                continue;
            }
            $str .= "{$key}={$val}&";
        }
        $str = trim($str, '&');
        return $str;
    }
    public function bulidSign($params)
    {
        unset($params['sign']);
        ksort($params);
        $string = $this->array2url($params, true);
        $priKey = file_get_contents(MODULE_ROOT . '/cert/rsa_private_key.pem');
        $res = openssl_get_privatekey($priKey);
        openssl_sign($string, $sign, $res);
        openssl_free_key($res);
        $sign = base64_encode($sign);
        return $sign;
    }
    public function checkCert()
    {
        global $_W;
        $cert = $_W['we7_wmall']['config']['payment']['alipay_cert'];
        if (empty($cert['private_key']) || empty($cert['private_key'])) {
            return error(-1, '支付宝支付证书不完整');
        }
        return true;
    }
    public function payRefund_build($params)
    {
        global $_W;
        $status = $this->checkCert();
        if (is_error($status)) {
            return $status;
        }
        $elements = array('refund_fee', 'out_trade_no', 'out_refund_no', 'refund_reason');
        $params = array_elements($elements, $params);
        if (empty($params['refund_fee'])) {
            return error(-1, '退款金额不能为空');
        }
        if (empty($params['out_trade_no'])) {
            return error(-1, '商户订单号不能为空');
        }
        $set['app_id'] = $this->alipay['app_id'];
        $set['method'] = 'alipay.trade.refund';
        $set['charset'] = 'utf8';
        $set['sign_type'] = 'RSA';
        $set['timestamp'] = date('Y-m-d H:i:s');
        $set['version'] = '1.0';
        $other = array('out_trade_no' => $params['out_trade_no'], 'refund_amount' => $params['refund_fee'], 'refund_reason' => $params['refund_reason'] ? $params['refund_reason'] : '正常退款');
        $set['biz_content'] = json_encode($other);
        $set['sign'] = $this->bulidSign($set);
        load()->func('communication');
        $result = ihttp_post('https://openapi.alipay.com/gateway.do', $set);
        if (is_error($result)) {
            return $result;
        }
        $result['content'] = iconv("GBK", "UTF-8//IGNORE", $result['content']);
        $result = json_decode($result['content'], true);
        if (!is_array($result)) {
            return error(-1, '返回数据错误');
        }
        if ($result['alipay_trade_refund_response']['code'] != 10000) {
            return error(-1, $result['alipay_trade_refund_response']['sub_msg']);
        }
        return $result['alipay_trade_refund_response'];
    }
}