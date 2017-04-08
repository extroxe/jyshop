<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Jys_message.php
 *
 *     Description: 短信发送类库
 *
 *         Created: 2017-1-17 11:20
 *
 *          Author: tangyu
 *
 * =====================================================================================
 */
class Jys_message
{
    private $_CI;
    private $_id;
    private $_name;
    private $_psw;
    public function __construct()
    {
        $this->_CI = & get_instance();
        $this->_id = $this->_CI->config->item('bdt360_id');
        $this->_name = $this->_CI->config->item('bdt360_name');
        $this->_psw = $this->_CI->config->item('bdt360_psw');
    }

    /**
     * 短信发送接口
     * @param $phone
     * @param $message
     * @return mixed
     */
    public function send_message($phone, $message)
    {
        $url = 'http://sms.bdt360.com:8180/Service.asmx/SendMessage?Id='.$this->_id.'&Name='.$this->_name.'&Psw='.$this->_psw.'&Message='.$message.'&Phone='.$phone.'&Timestamp=0';

        $ch = curl_init();
        //设置请求URL
        curl_setopt($ch, CURLOPT_URL, $url);
        //设置不显示头部消息
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        // 将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        // 设置本地不检测SSL证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        $result = curl_exec($ch);
        if ($error = curl_error($ch)){
            die($error);
        }
        curl_close($ch);
        $result = $this->xml_to_array($result);
        $result = $this->deal_plalindrome($result);

        return $result;
    }

    /**
     * @param $xml_data
     * 将xml格式转化为数组
     */
    public function xml_to_array($xml_data)
    {
        $xml = json_decode(json_encode((array)simplexml_load_string($xml_data)), TRUE);

        return $xml;
    }

    /**
     * 短信发送回文处理
     */
    public function deal_plalindrome($data_array)
    {
        switch ($data_array['State']){
            case 1:
                $result['success'] = TRUE;
                break;
            case -1:
                $result['success'] = FALSE;
                $result['error'] = '定时失败';
                break;
            case -5:
                $result['success'] = FALSE;
                $result['error'] = '定时发送短信内容为空';
                break;
            case -6:
                $result['success'] = FALSE;
                $result['error'] = '短信内容过长';
                break;
            case -7:
                $result['success'] = FALSE;
                $result['error'] = '发送号码为空';
                break;
            case -8:
                $result['success'] = FALSE;
                $result['error'] = '余额不足';
                break;
            case -9:
                $result['success'] = FALSE;
                $result['error'] = '接收数据失败';
                break;
            case -10:
                $result['success'] = FALSE;
                $result['error'] = '定时发送失败';
                break;
            case -11:
                $result['success'] = FALSE;
                $result['error'] = '定时发送时间或格式错误';
                break;
            case -12:
                $result['success'] = FALSE;
                $result['error'] = '定时发送时间失败';
                break;
            case -13:
                $result['success'] = FALSE;
                $result['error'] = '内容信息含关键字';
                break;
            case -14:
                $result['success'] = FALSE;
                $result['error'] = '信息内容格式与限定格式不符';
                break;
            case -15:
                $result['success'] = FALSE;
                $result['error'] = '信息没带签名';
                break;
            case -16:
                $result['success'] = FALSE;
                $result['error'] = '黑名单号码';
                break;
            case -100:
                $result['success'] = FALSE;
                $result['error'] = '客户端获取状态失败(系统预留)';
                break;
            default:
                $result['success'] = FALSE;
                $result['error'] = '系统配置参数错误';
                break;
        }
        $result['FailPhone'] = $data_array['FailPhone'];
        $result['Id'] = $data_array['Id'];

        return $result;
    }
}