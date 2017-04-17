<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename:  Shopping_cart.php
 *
 *     Description:  购物车控制器
 *
 *         Created:  2016-11-24 16:10:56
 *
 *          Author:  sunzuosheng
 *
 * =====================================================================================
 */

Class Shopping_cart extends CI_Controller {
    /**
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
        $this->load->library(['form_validation', 'Jys_db_helper']);
        $this->load->model(['Common_model', 'Shopping_cart_model', 'Commodity_model', 'User_model']);
    }

    /**
     * 购物车页面
     */
    public function index(){
        $data['title'] = "塞安生物-购物车";
        $data['js'] = array('template', 'cart');
        $data['css'] = array('cart');
        $data['main_content'] = 'cart';
        $data['isset_search'] = TRUE;
        $data['isset_nav'] = FALSE;
        $data['is_cart'] = TRUE;
        $user_info = $this->User_model->get_user_detail_by_condition(['user.id' => $_SESSION['user_id']]);
        $shopping_carts = $this->Shopping_cart_model->all($_SESSION['user_id'])['data'];
        $data['shopping_carts'] = $this->Commodity_model->calculate_discount_price($shopping_carts, $user_info['price_discount']);

        $data['commodity_type_id'] = $this->Commodity_model->get_commodity_list_by_condition(['commodity.id'=>$data['shopping_carts'][0]['commodity_id']], FALSE)['type_id'];
        $this->load->view('includes/template_view', $data);
    }

    /**
     * 立即购买购物车页面
     */
    public function buy_now($shopping_cart_id = NULL){
        if (empty($shopping_cart_id)){
            return FALSE;
        }

        $data['title'] = "塞安生物-购物车";
        $data['js'] = array('cart');
        $data['css'] = array('cart');
        $data['main_content'] = 'cart';
        $data['shopping_carts'] = $this->Shopping_cart_model->all($_SESSION['user_id'], ['shopping_cart.id'=>$shopping_cart_id])['data'];

        if (empty($data['shopping_carts']) || $data['shopping_carts'][0]['is_point']){
            show_404();
        }

        $data['commodity_type_id'] = $this->Commodity_model->get_commodity_list_by_condition(['commodity.id'=>$data['shopping_carts'][0]['commodity_id']], FALSE)['type_id'];

        $data['isset_search'] = TRUE;
        $data['isset_nav'] = FALSE;
        $data['is_cart'] = TRUE;
        $this->load->view('includes/template_view', $data);
    }

    /**
     * 获取该用户的全部购物车信息
     */
    public function all(){
        if (!empty($_SESSION['user_id']) && !empty($_SESSION['role_id']) && intval($_SESSION['role_id']) == jys_system_code::ROLE_USER) {
            $user_info = $this->User_model->get_user_detail_by_condition(['user.id'=>$_SESSION['user_id']]);
            $data = $this->Shopping_cart_model->all($_SESSION['user_id'], ['commodity.type_id !='=>jys_system_code::COMMODITY_TYPE_MEMBER]);
            $data['data'] = $this->Commodity_model->calculate_discount_price($data['data'], $user_info['price_discount']);
        }else {
            $data = $this->Shopping_cart_model->all(0, ['commodity.type_id !='=>jys_system_code::COMMODITY_TYPE_MEMBER]);
        }

        echo json_encode($data);
    }

    /**
     * 获取购物车商品数量
     */
    public function amount(){
        $user_id = $_SESSION['user_id'];
        $amount = $this->Shopping_cart_model->amount($user_id);

        echo json_encode(intval($amount));
    }

    /**
     * 添加购物车
     */
    public function add(){
        //验证表单信息
        $this->form_validation->set_rules('commodity_id', '商品ID', 'trim|required|numeric');
        $this->form_validation->set_rules('amount', '商品数量', 'trim|required|numeric');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            //处理数据
            if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])){
                echo json_encode([
                    'success' => FALSE,
                    'msg' => '请先登录'
                ]);
                exit;
            }
            $post['user_id']      = $_SESSION['user_id'];
            $post['commodity_id'] = intval($this->input->post('commodity_id', TRUE));
            $post['amount']       = intval($this->input->post('amount', TRUE));
            $post['create_time']  = date('Y-m-d H:i:s');

            $shopping_cart_id = $this->jys_db_helper->is_exist('shopping_cart', ['user_id'=>$post['user_id'], 'commodity_id'=>$post['commodity_id']]);
            $commodity = $this->Commodity_model->get_commodity_list_by_condition(['commodity.id'=>$post['commodity_id']], FALSE);
            if ($commodity['is_point']){
                echo json_encode([
                    'success' => FALSE,
                    'msg' => '积分商品不能添加到购物车'
                ]);
                exit;
            }

            if ($shopping_cart_id){
                if ($commodity['type_id'] == jys_system_code::COMMODITY_TYPE_MEMBER){
                    if ($this->jys_db_helper->set_update('shopping_cart', $shopping_cart_id, ['amount'=>1], FALSE)){
                        $data['success'] = TRUE;
                        $data['msg'] = '购物车中已存在该商品';
                        $data['insert_id'] = $shopping_cart_id;
                    }else{
                        $data['success'] = FALSE;
                        $data['msg'] = '';
                    }
                }else{
                    if ($this->jys_db_helper->set_update('shopping_cart', $shopping_cart_id, ['amount'=>'amount+'.$post['amount'] ], FALSE)){
                        $data['success'] = TRUE;
                        $data['msg'] = '购物车中已存在该商品';
                        $data['insert_id'] = $shopping_cart_id;
                    }else{
                        $data['success'] = FALSE;
                        $data['msg'] = '';
                    }
                }
            }else{
                $data = $this->jys_db_helper->add('shopping_cart', $post, TRUE);
            }
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $res['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 修改购物车
     */
    public function update(){
        //验证表单信息
        $this->form_validation->set_rules('id', '购物车ID', 'trim|required|numeric');
        $this->form_validation->set_rules('amount', '商品数量', 'trim|required|numeric');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            //处理数据
            $id                   = intval($this->input->post('id', TRUE));
            $post['user_id']      = $_SESSION['user_id'];
            $post['amount']       = intval($this->input->post('amount', TRUE));

            $data = $this->jys_db_helper->update('shopping_cart', $id, $post);
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $res['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 删除购物车
     */
    public function delete(){
        $id = explode(',', $this->input->post('id', TRUE));

        $data = $this->jys_db_helper->delete('shopping_cart',$id);

        echo json_encode($data);
    }

    /**
     * 购物车数量增量1
     */
    public function increment_num(){
        //验证表单信息
        $this->form_validation->set_rules('id', '购物车ID', 'trim|required|numeric');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            //处理数据
            $id = intval($this->input->post('id', TRUE));

            $data = $this->jys_db_helper->set_update('shopping_cart', $id, ['amount' => 'amount+1'], FALSE);
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $res['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 购物车数量减量1
     */
    public function decrement_num(){
        //验证表单信息
        $this->form_validation->set_rules('id', '购物车ID', 'trim|required|numeric');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            //处理数据
            $id = intval($this->input->post('id', TRUE));

            if ($this->jys_db_helper->set_update('shopping_cart', $id, ['amount' => 'amount-1'], FALSE)) {
                $data['success'] = TRUE;
                $data['msg'] = '减少购物车商品成功';
            }else {
                $data['success'] = FALSE;
                $data['msg'] = '减少购物车商品失败';
            }
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $res['msg'];
        }

        echo json_encode($data);
    }
}