<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =========================================================
 *
 *      Filename: Refund_admin.php
 *
 *   Description: 退款管理
 *
 *       Created: 2017-1-3 16:18:29
 *
 *        Author: wuhaohua
 *
 * =========================================================
 */
class Refund_admin extends CI_Controller {
    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library(['form_validation']);
        $this->load->model(['Order_model']);
    }

    /**
     * 分页获取退款列表
     * @param int $payment_id 支付方式ID
     * @param int $page 页数
     * @param int $page_size 页面大小
     */
    public function paginate($page = 1, $page_size = 10) {
        $result = array('success'=>FALSE, 'msg'=>'查询失败');
        $payment_id = $this->input->post('payment_id');
        if (intval($payment_id) < 1 || intval($page) < 1 || intval($page_size) < 1) {
            $result['msg'] = '参数不正确';
            echo json_encode($result);
            exit;
        }

        $result = $this->Order_model->paginate_for_refund($payment_id, $page, $page_size);

        echo json_encode($result);
    }

    /**
     * 审核退款接口
     */
    public function audit_refund() {
        //验证表单信息
        $this->form_validation->set_rules('id', '退款ID', 'trim|required|is_natural_no_zero');
        $this->form_validation->set_rules('audit_result', '审核结果', 'trim|required|in_list[true,false]');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']) {
            //处理数据
            $id = $this->input->post('id', TRUE);
            $audit_result = $this->input->post('audit_result', TRUE);

            $data = $this->Order_model->audit_refund($id, $audit_result);
        }else {
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $res['msg'];
        }

        echo json_encode($data);
    }
}