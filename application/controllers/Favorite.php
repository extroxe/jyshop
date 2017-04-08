<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename:  Favorite.php
 *
 *     Description:  收藏控制器
 *
 *         Created:  2017-1-3 17:45:44
 *
 *          Author:  wuhaohua
 *
 * =====================================================================================
 */
class Favorite extends CI_Controller {
    /**
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
        $this->load->helper('form');
        $this->load->library(['form_validation']);
        $this->load->model(['Favorite_model']);
    }

    /**
     * 分页获取用户收藏信息
     */
    public function get_favorite_by_page() {
        //验证表单信息
        $this->form_validation->set_rules('page', '页数', 'trim|is_natural_no_zero');
        $this->form_validation->set_rules('page_size', '页面大小', 'trim|is_natural_no_zero');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        $result = array('success'=>FALSE, 'msg'=>'查询失败');
        if ($res['success']){
            $page = $this->input->post('page');
            $page_size = $this->input->post('page_size');
            $result = $this->Favorite_model->paginate($page, $page_size, ['favorite.user_id'=>$_SESSION['user_id']]);
        }else {
            $result['msg'] = '收藏列表查询失败';
            $result['error'] = $res['msg'];
        }

        echo json_encode($result);
    }

    /**
     * 添加收藏
     */
    public function add() {
        $commodity_id = $this->input->post('commodity_id');
        $result = array('success'=>FALSE, 'msg'=>'收藏失败');

        if (intval($commodity_id) < 1) {
            $result['msg'] = '商品信息不正确';
            echo json_encode($result);
            exit;
        }

        if ($this->Favorite_model->add($commodity_id, $_SESSION['user_id'])) {
            $result['success'] = TRUE;
            $result['msg'] = '收藏成功';
        }else {
            $result['success'] = FALSE;
            $result['msg'] = '系统繁忙，收藏失败';
        }
        echo json_encode($result);
    }

    /**
     * 根据收藏ID取消收藏
     */
    public function delete_by_id() {
        $id = $this->input->post('id');
        $result = array('success'=>FALSE, 'msg'=>'取消收藏失败');
        if (intval($id) < 1) {
            $result['msg'] = '请选择要取消的收藏商品';
            echo json_encode($result);
            exit;
        }

        if ($this->Favorite_model->delete_by_condition(['id'=>$id])) {
            $result['success'] = TRUE;
            $result['msg'] = '取消收藏成功';
        }else {
            $result['success'] = FALSE;
            $result['msg'] = '系统繁忙，取消收藏失败';
        }
        echo json_encode($result);
    }

    /**
     * 根据商品ID取消收藏
     */
    public function delete_by_commodity_id() {
        $commodity_id = $this->input->post('commodity_id');
        $user_id = $this->session->userdata('user_id');
        $result = array('success'=>FALSE, 'msg'=>'取消收藏失败');
        if (intval($commodity_id) < 1 || intval($user_id) < 1) {
            $result['msg'] = '参数错误';
            echo json_encode($result);
            exit;
        }

        if ($this->Favorite_model->delete_by_condition(['commodity_id'=>$commodity_id, 'user_id'=>$user_id])) {
            $result['success'] = TRUE;
            $result['msg'] = '取消收藏成功';
        }else {
            $result['success'] = FALSE;
            $result['msg'] = '系统繁忙，取消收藏失败';
        }
        echo json_encode($result);
    }

    /**
     * 根据商品ID，判断当前用户是否已经收藏了该商品
     */
    public function check_favorite_by_commodity_id() {
        $commodity_id = $this->input->post('commodity_id');
        $user_id = $this->session->userdata('user_id');
        $result = array('success'=>FALSE, 'msg'=>'当前用户未收藏当前商品');
        if (intval($commodity_id) < 1 || intval($user_id) < 1) {
            $result['msg'] = '当前用户未收藏当前商品';
            echo json_encode($result);
            exit;
        }

        $favorite = $this->jys_db_helper->get_where('favorite', ['commodity_id'=>$commodity_id, 'user_id'=>$user_id]);
        if (!empty($favorite)) {
            $result['success'] = TRUE;
            $result['msg'] = '当前用户收藏了该商品';
            $result['favorite_id'] = $favorite['id'];
        }
        echo json_encode($result);
    }
}