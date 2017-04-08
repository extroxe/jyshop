<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =========================================================
 *
 *      Filename: Report_admin.php
 *
 *   Description: 报告管理
 *
 *       Created: 2017-1-12 14:37:01
 *
 *        Author: wuhaohua
 *
 * =========================================================
 */
class Report_admin extends CI_Controller {
    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library(['form_validation', 'Jys_kdniao']);
        $this->load->model(['Report_model']);
    }

    /**
     * 根据子订单ID获取其下所有报告
     */
    public function get_report_list_by_order_commodity_id() {
        $order_commodity_id = $this->input->post('order_commodity_id');

        $result = array('success'=>FALSE, 'msg'=>'获取报告列表失败', 'data'=>array());
        if (intval($order_commodity_id) < 1) {
            $data['msg'] = '子订单ID不正确';
            echo json_encode($data);
            exit;
        }

        $data = $this->Report_model->get_report_list_by_order_commodity_id($order_commodity_id);
        if(!empty($data) && is_array($data)) {
            $result['success'] = TRUE;
            $result['msg'] = '查询成功';
            $result['data'] = $data;
        }else {
            $result['msg'] = '未查询到相关报告';
        }

        echo json_encode($result);
    }


    public function add() {
        $result = array('success'=>FALSE, 'msg'=>'添加报告失败');
        //验证表单信息
        $this->form_validation->set_rules('order_commodity_id', '子订单ID', 'trim|required|is_natural_no_zero');
        $this->form_validation->set_rules('number', '报告编号', 'trim|required');
        $this->form_validation->set_rules('name', '姓名', 'trim|required');
        $this->form_validation->set_rules('age', '年龄', 'trim|required|numeric');
        $this->form_validation->set_rules('gender', '性别', 'trim|required|numeric');
        $this->form_validation->set_rules('phone', '手机号码', 'trim|required|regex_match[/^1(3|4|5|7|8)\d{9}$/]');
        $this->form_validation->set_rules('identity_card', '身份证号', 'trim|required|regex_match[/^(\d{6})(\d{4})(\d{2})(\d{2})(\d{3})([0-9]|X|x)$/]');
        $this->form_validation->set_rules('attachment_id', '报告ID', 'trim|required|is_natural_no_zero');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']) {
            $order_commodity_id = $this->input->post('order_commodity_id', TRUE);
            $number = $this->input->post('number', TRUE);
            $name = $this->input->post('name', TRUE);
            $age = $this->input->post('age', TRUE);
            $gender = $this->input->post('gender', TRUE);
            $phone = $this->input->post('phone', TRUE);
            $identity_card = $this->input->post('identity_card', TRUE);
            $attachment_id = $this->input->post('attachment_id', TRUE);
            $result = $this->Report_model->add($order_commodity_id, $number, $name, $age, $gender, $phone, $identity_card, $attachment_id);
        }else {
            $result['error'] = $res['msg'];
            $result['msg'] = '参数错误';
        }

        echo json_encode($result);
    }


    public function update() {
        $result = array('success'=>FALSE, 'msg'=>'更新报告失败');
        //验证表单信息
        $this->form_validation->set_rules('id', '报告id', 'trim|required');
        $this->form_validation->set_rules('number', '报告编号', 'trim|required');
        $this->form_validation->set_rules('name', '姓名', 'trim|required');
        $this->form_validation->set_rules('phone', '手机号码', 'trim|required|regex_match[/^1(3|4|5|7|8)\d{9}$/]');
        $this->form_validation->set_rules('identity_card', '身份证号', 'trim|required|regex_match[/^(\d{6})(\d{4})(\d{2})(\d{2})(\d{3})([0-9]|X|x)$/]');
        $this->form_validation->set_rules('attachment_id', '报告ID', 'trim|required|is_natural_no_zero');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']) {
            $report_id = $this->input->post('id', TRUE);
            $number = $this->input->post('number', TRUE);
            $name = $this->input->post('name', TRUE);
            $phone = $this->input->post('phone', TRUE);
            $identity_card = $this->input->post('identity_card', TRUE);
            $attachment_id = $this->input->post('attachment_id', TRUE);
            $result = $this->Report_model->update($report_id, $number, $name, $phone, $identity_card, $attachment_id);
        }else {
            $result['error'] = $res['msg'];
            $result['msg'] = '参数错误';
        }

        echo json_encode($result);
    }

    public function delete()
    {
        $report_id = $this->input->post('id', TRUE);
        if (empty($report_id) || intval($report_id) < 1){
            $data['success'] = FALSE;
            $data['msg'] = '参数错误';
        }

        $result = $this->Report_model->delete_report_by_id($report_id);
        if ($result['success']){
            $data['success'] = TRUE;
            $data['msg'] = '删除报告成功';
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '删除报告失败';
        }

        echo json_encode($data);
    }
}