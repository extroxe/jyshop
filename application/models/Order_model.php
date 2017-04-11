<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Order_model.php
 *
 *     Description: 订单模型
 *
 *         Created: 2016-11-23 11:28:14
 *
 *          Author: sunzuosheng
 *
 * =====================================================================================
 */

class Order_model extends CI_Model{
    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        $this->load->helper('string');
        $this->load->library(['Jys_weixin_pay', 'Jys_tool', 'Jys_weixin']);
    }

    /**
     * 分页
     *
     * @param int $page 页数
     * @param int $page_size 页大小
     * @param null $condition 条件
     * @param string $keyword 查询关键字
     */
    public function paginate($page = 1, $page_size = 10, $condition = NULL, $keyword = ''){
        $auto_cancel_order = $this->auto_cancel_not_paid_order();
        $data = [
            'success' => FALSE,
            'msg' => '没有订单数据',
            'data' => null
        ];

        if (empty($page) || intval($page) < 1 || empty($page_size) || intval($page_size) < 1) {
            $data['msg'] = '参数错误';
            return $data;
        }

        $this->db->select('order.id,
                           order.number,
                           order.total_price,
                           order.payment_amount,
                           order.payment_order,
                           order.express_number,
                           order.predict_complete_time,
                           order.create_time,
                           order.payment_time,
                           discount_coupon.id as discount_coupon_id,
                           discount_coupon.name as discount_coupon_name,
                           discount_coupon.condition,
                           discount_coupon.privilege,
                           order.user_discount_coupon_id,
                           user_discount_coupon.start_time,
                           user_discount_coupon.end_time,
                           user.id as user_id,
                           user.username as user_username,
                           user.name as user_name,
                           user.phone as user_phone,
                           express_company.id as express_company_id,
                           express_company.name as express_company_name,
                           express_company.code as express_company_code,
                           order.payment_id,
                           payment_type.name as payment_type_name,
                           order.terminal_type,
                           terminal_type.name as terminal_type_name,
                           order.status_id,
                           order_status.name as order_status_name');

        $this->db->join('user_discount_coupon', 'user_discount_coupon.id = order.user_discount_coupon_id', 'left');
        $this->db->join('user', 'user.id = order.user_id', 'left');
        $this->db->join('discount_coupon', 'discount_coupon.id = user_discount_coupon.discount_coupon_id', 'left');
        $this->db->join('express_company', 'express_company.id = order.express_company_id', 'left');
        $this->db->join('system_code as payment_type', 'payment_type.value = order.payment_id and payment_type.type = "'.jys_system_code::PAYMENT.'"', 'left');
        $this->db->join('system_code as terminal_type', 'terminal_type.value = order.terminal_type and terminal_type.type = "'.jys_system_code::TERMINAL_TYPE.'"', 'left');
        $this->db->join('system_code as order_status', 'order_status.value = order.status_id and order_status.type = "'.jys_system_code::ORDER_STATUS.'"', 'left');
        if (!empty($condition)) {
            $this->db->where($condition);
        }

        if (!empty($keyword)) {
            // 关键字模糊查找
            $this->db->group_start();
            $this->db->like('order.number', $keyword);
            $this->db->or_like('order.address', $this->jys_tool->unicode_encode($keyword));
            $this->db->or_like('order_status.name', $keyword);
            $this->db->or_like('user.name', $keyword);
            $this->db->or_like('user.phone', $keyword);
            $this->db->or_like('order.payment_order', $keyword);
            $this->db->or_like('payment_type.name', $keyword);
            $this->db->or_like('express_company.name', $keyword);
            $this->db->or_like('express_company.code', $keyword);
            $this->db->group_end();
        }

        $this->db->order_by('order.create_time', 'DESC');
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $result = $this->db->get('order');

        if ($result && $result->num_rows() > 0){
            $data = [
                'success' => TRUE,
                'msg' => '',
                'data' => $result->result_array()
            ];

            $this->db->select('order.id');
            $this->db->join('user_discount_coupon', 'user_discount_coupon.id = order.user_discount_coupon_id', 'left');
            $this->db->join('user', 'user.id = user_discount_coupon.user_id', 'left');
            $this->db->join('discount_coupon', 'discount_coupon.id = user_discount_coupon.discount_coupon_id', 'left');
            $this->db->join('express_company', 'express_company.id = order.express_company_id', 'left');
            $this->db->join('system_code as payment_type', 'payment_type.value = order.payment_id and payment_type.type = "'.jys_system_code::PAYMENT.'"', 'left');
            $this->db->join('system_code as terminal_type', 'terminal_type.value = order.terminal_type and terminal_type.type = "'.jys_system_code::TERMINAL_TYPE.'"', 'left');
            $this->db->join('system_code as order_status', 'order_status.value = order.status_id and order_status.type = "'.jys_system_code::ORDER_STATUS.'"', 'left');
            if (!empty($condition)) {
                $this->db->where($condition);
            }
            if (!empty($keyword)) {
                // 关键字模糊查找
                $this->db->group_start();
                $this->db->like('order.number', $keyword);
                $this->db->or_like('order.address', $this->jys_tool->unicode_encode($keyword));
                $this->db->or_like('order_status.name', $keyword);
                $this->db->or_like('user.name', $keyword);
                $this->db->or_like('user.phone', $keyword);
                $this->db->or_like('order.payment_order', $keyword);
                $this->db->or_like('payment_type.name', $keyword);
                $this->db->or_like('express_company.name', $keyword);
                $this->db->or_like('express_company.code', $keyword);
                $this->db->group_end();
            }
            $res = $this->db->get('order');

            if ($res && $res->num_rows() > 0){
                $data['total_page'] = ceil($res->num_rows() / $page_size * 1.0);
            }else{
                $data['total_page'] = 1;
            }
        }

        return $data;
    }

    /**
     * 用户分页获取相关订单
     * @param int $page 页数
     * @param int $page_size 页面大小
     * @param int $order_status 订单状态
     * @return array
     */
    public function paginate_for_user($page = 1, $page_size = 10, $user_id = 0,  $order_status = array()){
        $auto_cancel_order = $this->auto_cancel_not_paid_order();
        $data = [
            'success' => FALSE,
            'msg' => '没有订单数据',
            'data' => array(),
            'total_page' => 0
        ];

        if (empty($user_id) ||empty($page) || intval($page) < 1 || empty($page_size) || intval($page_size) < 1 || !is_array($order_status) || count($order_status) < 1) {
            $data['msg'] = '参数错误';
            return $data;
        }

        $this->db->select('order.id,
                           order.number,
                           order.total_price,
                           order.payment_amount,
                           order.payment_order,
                           order.express_number,
                           order.predict_complete_time,
                           order.create_time,
                           order.payment_time,
                           discount_coupon.id as discount_coupon_id,
                           discount_coupon.condition,
                           discount_coupon.privilege,
                           order.user_discount_coupon_id,
                           user_discount_coupon.start_time,
                           user_discount_coupon.end_time,
                           user.id as user_id,
                           user.username as user_username,
                           user.name as user_name,
                           user.phone as user_phone,
                           express_company.id as express_company_id,
                           express_company.name as express_company_name,
                           express_company.code as express_company_code,
                           order.payment_id,
                           payment_type.name as payment_type_name,
                           order.terminal_type,
                           terminal_type.name as terminal_type_name,
                           order.status_id,
                           order_status.name as order_status_name');

        $this->db->join('user_discount_coupon', 'user_discount_coupon.id = order.user_discount_coupon_id', 'left');
        $this->db->join('user', 'user.id = user_discount_coupon.user_id', 'left');
        $this->db->join('discount_coupon', 'discount_coupon.id = user_discount_coupon.discount_coupon_id', 'left');
        $this->db->join('express_company', 'express_company.id = order.express_company_id', 'left');
        $this->db->join('system_code as payment_type', 'payment_type.value = order.payment_id and payment_type.type = "'.jys_system_code::PAYMENT.'"', 'left');
        $this->db->join('system_code as terminal_type', 'terminal_type.value = order.terminal_type and terminal_type.type = "'.jys_system_code::TERMINAL_TYPE.'"', 'left');
        $this->db->join('system_code as order_status', 'order_status.value = order.status_id and order_status.type = "'.jys_system_code::ORDER_STATUS.'"', 'left');
        $this->db->where('order.user_id', $user_id);

        $flag = true;
        foreach ($order_status as $item) {
            if (intval($item) > 0) {
                if ($flag) {
                    $this->db->where('order.status_id', $item);
                    $flag = false;
                }else {
                    $this->db->or_where('order.status_id', $item);
                }
            }
        }

        $this->db->order_by('order.create_time', 'DESC');
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $result = $this->db->get('order');

        if ($result && $result->num_rows() > 0){
            $orders = $result->result_array();
            $orders = $this->push_sub_order($orders);
            $data = [
                'success' => TRUE,
                'msg' => '获取用户订单成功',
                'data' => $orders
            ];
            $this->db->select('order.id');
            $this->db->where('order.user_id', $user_id);
            $flag = true;
            foreach ($order_status as $item) {
                if (intval($item) > 0) {
                    if ($flag) {
                        $this->db->where('order.status_id', $item);
                        $flag = false;
                    }else {
                        $this->db->or_where('order.status_id', $item);
                    }
                }
            }
            $result_page = $this->db->get('order');
            if ($result_page && $result_page->num_rows() > 0) {
                $total = $result_page->num_rows();
                $data['total_page'] = ceil($total / $page_size * 1.0);
            }else {
                $data['total_page'] = 1;
            }
        }

        return $data;
    }

    /**
     * 根据订
     *
     * @param int $order_id 订单ID
     * @return array 子订单数据
     */
    public function show_sub_order($order_id = 0){
        $data = [
            'success' => FALSE,
            'msg' => '没有订单数据',
            'data' => null
        ];

        if (empty($order_id) || intval($order_id) < 1) {
            $data['msg'] = '参数错误';
            return $data;
        }

        $this->db->select('order_commodity.id,
                           order_commodity.number,
                           order_commodity.price,
                           order_commodity.amount,
                           order_commodity.total_price,
                           order_commodity.points,
                           order_commodity.express_number,
                           report.name,
                           report.gender,
                           system_code_gender.name as gender_name,
                           report.age,
                           report.identity_card,
                           order_commodity.create_time,
                           report.create_time as upload_time,
                           order_commodity.commodity_id,
                           commodity.name as commodity_name,
                           commodity.type_id,
                           commodity.is_point,
                           commodity_type.name as type_name,
                           thumbnail_attachment.path as thumbnail_path,
                           order_commodity.express_company_id,
                           commodity_evaluation.id as commodity_evaluation_id,
                           express_company.name as express_company_name,
                           report_attachment.path as order_attachment_path');

        $this->db->join('report', 'report.order_commodity_id = order_commodity.id', 'left');
        $this->db->join('commodity', 'commodity.id = order_commodity.commodity_id', 'left');
        $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_id = commodity.id', 'left');
        $this->db->join('attachment as thumbnail_attachment', 'thumbnail_attachment.id = commodity_thumbnail.attachment_id', 'left');
        $this->db->join('commodity_evaluation', 'commodity_evaluation.commodity_id = commodity.id and commodity_evaluation.order_commodity_id = order_commodity.id', 'left');
        $this->db->join('express_company', 'express_company.id = order_commodity.express_company_id', 'left');
        $this->db->join('attachment as report_attachment', 'report_attachment.id = report.attachment_id', 'left');
        $this->db->join('system_code as system_code_gender', 'system_code_gender.value = report.gender and system_code_gender.type = "'.jys_system_code::GENDER.'"', 'left');
        $this->db->join('system_code as commodity_type', 'commodity_type.value = commodity.type_id and commodity_type.type = "'.jys_system_code::COMMODITY_TYPE.'"', 'left');
        $this->db->where('order_commodity.order_id', $order_id);
        $this->db->group_by('commodity.id');
        $result = $this->db->get('order_commodity');

        if ($result && $result->num_rows() > 0){
            $data = [
                'success' => TRUE,
                'msg' => '',
                'data' => $result->result_array()
            ];
        }

        return $data;
    }

    /**
     * 根据订单ID获取子订单数据
     *
     * @param int $order_id 订单ID
     * @return array 子订单数据
     */
    public function get_sub_order_by_id($order_commodity_id = 0){
        $data = [
            'success' => FALSE,
            'msg' => '没有订单数据',
            'data' => null
        ];

        if (empty($order_commodity_id) || intval($order_commodity_id) < 1) {
            $data['msg'] = '参数错误';
            return $data;
        }

        $this->db->select('order_commodity.id,
                           order_commodity.number as sub_number,
                           order_commodity.price,
                           order_commodity.amount,
                           order_commodity.total_price,
                           order_commodity.points,
                           order_commodity.express_number,
                           report.name,
                           report.gender,
                           system_code_gender.name as gender_name,
                           report.age,
                           report.identity_card,
                           order_commodity.create_time,
                           report.create_time as upload_time,
                           order_commodity.order_id,
                           order_commodity.commodity_id,
                           order.number,
                           commodity.name as commodity_name,
                           commodity.type_id,
                           commodity.is_point,
                           commodity_type.name as type_name,
                           thumbnail_attachment.path as thumbnail_path,
                           order_commodity.express_company_id,
                           commodity_evaluation.id as commodity_evaluation_id,
                           express_company.name as express_company_name,
                           report_attachment.path as order_attachment_path');

        $this->db->join('order', 'order.id = order_commodity.order_id', 'left');
        $this->db->join('report', 'report.order_commodity_id = order_commodity.id', 'left');
        $this->db->join('commodity', 'commodity.id = order_commodity.commodity_id', 'left');
        $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_id = commodity.id', 'left');
        $this->db->join('attachment as thumbnail_attachment', 'thumbnail_attachment.id = commodity_thumbnail.attachment_id', 'left');
        $this->db->join('commodity_evaluation', 'commodity_evaluation.commodity_id = commodity.id and commodity_evaluation.order_commodity_id = order_commodity.id', 'left');
        $this->db->join('express_company', 'express_company.id = order_commodity.express_company_id', 'left');
        $this->db->join('attachment as report_attachment', 'report_attachment.id = report.attachment_id', 'left');
        $this->db->join('system_code as system_code_gender', 'system_code_gender.value = report.gender and system_code_gender.type = "'.jys_system_code::GENDER.'"', 'left');
        $this->db->join('system_code as commodity_type', 'commodity_type.value = commodity.type_id and commodity_type.type = "'.jys_system_code::COMMODITY_TYPE.'"', 'left');
        $this->db->where('order_commodity.id', $order_commodity_id);
        $this->db->group_by('commodity.id');
        $result = $this->db->get('order_commodity');

        if ($result && $result->num_rows() > 0){
            $data = [
                'success' => TRUE,
                'msg' => '',
                'data' => $result->row_array()
            ];
        }

        return $data;
    }

    /**
     * 获取结算页面订单信息
     *
     * @param array $shopping_cart_ids 购物车IDS
     * @return array|bool
     */
    public function get_order_settlement($shopping_cart_ids = [], $user_id = NULL){
        if (empty($shopping_cart_ids) || !is_array($shopping_cart_ids) || empty($user_id) || intval($user_id) < 1){
            return FALSE;
        }
        $date = date('Y-m-d H:i:s');

        $this->db->select('shopping_cart.id,
                           shopping_cart.user_id,
                           shopping_cart.commodity_id,
                           attachment.path,
                           commodity.name,
                           commodity.introduce,
                           commodity.price,
                           commodity.points,
                           commodity.is_point,
                           commodity.category_id,
                           flash_sale.price as flash_sale_price,
                           flash_sale.start_time as flash_sale_start_time,
                           flash_sale.end_time as flash_sale_end_time,
                           category.name as category_name,
                           shopping_cart.amount,
                           shopping_cart.create_time');

        $this->db->join('commodity', 'commodity.id = shopping_cart.commodity_id', 'left');
        $this->db->join('flash_sale', 'flash_sale.commodity_id = commodity.id and "'.$date.'" >= flash_sale.start_time and "'.$date.'" <= flash_sale.end_time', 'left');
        $this->db->join('category', 'category.id = commodity.category_id', 'left');
        $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_id = commodity.id', 'left');
        $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
        $this->db->where('shopping_cart.user_id', $user_id);

        foreach ($shopping_cart_ids as $key => $id){
            if ($key == 0){
                $this->db->where('shopping_cart.id', $id);
            }else{
                $this->db->or_where('shopping_cart.id', $id);
            }
        }

        $this->db->group_by('shopping_cart.id');
        $result = $this->db->get('shopping_cart');

        if ($result && $result->num_rows() > 0){
            return $result->result_array();
        }else{
            return [];
        }
    }

    /**
     * 根据购物车获取订单所需要的数据
     *
     * @param array $shopping_cart_ids 购物车ids
     * @return array|bool
     */
    public function get_order_by_shopping_cart($shopping_cart_ids = []){
        if (empty($shopping_cart_ids) || !is_array($shopping_cart_ids)){
            return FALSE;
        }

        $user_id = $_SESSION['user_id'];
        $date = date('Y-m-d H:i:s');

        $this->db->select('shopping_cart.commodity_id,
                           shopping_cart.amount,
                           commodity.price,
                           flash_sale.price as flash_sale_price,
                           commodity.type_id,
                           commodity.level_id,
                           (commodity.points * shopping_cart.amount) as points,
                           sum(commodity.price * shopping_cart.amount) as total_price');

        $this->db->join('commodity', 'commodity.id = shopping_cart.commodity_id', 'left');
        $this->db->join('flash_sale', 'flash_sale.commodity_id = commodity.id and "'.$date.'" >= flash_sale.start_time and "'.$date.'" <= flash_sale.end_time', 'left');
        $this->db->where('shopping_cart.user_id', $user_id);

        foreach ($shopping_cart_ids as $key => $id){
            if ($key == 0){
                $this->db->where('shopping_cart.id', $id);
            }else{
                $this->db->or_where('shopping_cart.id', $id);
            }
        }

        $this->db->group_by('shopping_cart.id');
        $result = $this->db->get('shopping_cart');

        if ($result && $result->num_rows() > 0){
            return $result->result_array();
        }else{
            return [];
        }
    }

    /**
     * 生成订单编号
     *
     * @return string
     */
    public function generate_order_number(){
        return random_string('numeric', 6).time();
    }

    /**
     * 计算总价
     *
     * @param array $orders 订单数据（分商品）
     * @return bool|int
     */
    public function calculation_total_price($orders = []){
        if (empty($orders) || !is_array($orders)){
            return FALSE;
        }
        $total_price = 0;

        foreach ($orders as $order){
            $total_price += $order['flash_sale_price'] ? $order['flash_sale_price'] * $order['amount'] : $order['total_price'];
        }

        return $total_price;
    }

    /**
     * 根据订单ID获取订单信息
     *
     * @param int $order_id 订单ID
     * @return array|bool 订单数据
     */
    public function get_order_by_condition($condition = array()){
        $auto_cancel_order = $this->auto_cancel_not_paid_order();
        $data = array(
            'success' => FALSE,
            'msg' => '查询条件不能为空',
            'data' => NULL
        );
        if (empty($condition) || count($condition) < 1){
            return $data;
        }

        $this->db->select('order.*,
                           discount_coupon.name as discount_coupon_name,
                           discount_coupon.condition as discount_coupon_condition,
                           discount_coupon.privilege as discount_coupon_privilege,
                           express_company.name as express_company_name,
                           payment_type.name as payment_type_name,
                           terminal_type.name as terminal_type_name,
                           order_status.name as order_status_name');

        $this->db->join('user_discount_coupon', 'user_discount_coupon.id = order.user_discount_coupon_id', 'left');
        $this->db->join('discount_coupon', 'discount_coupon.id = user_discount_coupon.discount_coupon_id', 'left');
        $this->db->join('express_company', 'express_company.id = order.express_company_id', 'left');
        $this->db->join('system_code as payment_type', 'payment_type.value = order.payment_id and payment_type.type = "'.jys_system_code::PAYMENT.'"', 'left');
        $this->db->join('system_code as terminal_type', 'terminal_type.value = order.terminal_type and terminal_type.type = "'.jys_system_code::TERMINAL_TYPE.'"', 'left');
        $this->db->join('system_code as order_status', 'order_status.value = order.status_id and order_status.type = "'.jys_system_code::ORDER_STATUS.'"', 'left');
        $this->db->where($condition);
        $result = $this->db->get('order');

        if ($result && $result->num_rows() > 0){
            $order = $result->row_array();
            $order['address'] = json_decode($order['address'], TRUE);
            $order['sub_orders'] = $this->push_sub_order($order['id']);
            if (floatval($order['payment_amount']) < 0.01) {
                if (floatval($order['discount_coupon_privilege']) >= 0.01) {
                    $order['payment_amount'] = floatval($order['total_price']) - floatval($order['discount_coupon_privilege']);
                }else {
                    $order['payment_amount'] = floatval($order['total_price']);
                }
            }

            $data = [
                'success' => TRUE,
                'msg' => '获取成功',
                'data' => $order
            ];
        }else{
            $data = [
                'success' => FALSE,
                'msg' => '没有数据',
                'data' => NULL
            ];
        }

        return $data;
    }

    /**
     * 填充子订单
     *
     * @param array $orders 订单列表
     * @return array
     */
    public function push_sub_order($orders = []){
        if (empty($orders)){
            return [];
        }

        if (is_array($orders)){
            foreach ($orders as $key => $order){
                $orders[$key]['sub_orders'] = $this->show_sub_order($order['id'])['data'];
            }
        }else if (is_numeric($orders)){
            return $this->show_sub_order($orders)['data'];
        }


        return $orders;
    }

    /**
     * 获取订单列表nav
     *
     * @param array $orders
     * @return array
     */
    public function get_order_list_nav($orders = [], $user_id){
        if (empty($orders)){
            return [];
        }
        $data['all'] = count($orders);
        $data['not_paid'] = 0;
        $data['paid'] = 0;
        $data['delivered'] = 0;
        $data['sentback'] = 0;
        $data['assaying'] = 0;
        $data['finished'] = 0;
        $data['refunding'] = 0;
        $data['refunded'] = 0;

        $data['can_evaluate'] = $this->get_can_evaluate_order_num($user_id);

        foreach ($orders as $order){
            switch (intval($order['status_id'])) {
                case jys_system_code::ORDER_STATUS_NOT_PAID:
                    // 未付款
                    $data['not_paid']++;
                    break;
                case jys_system_code::ORDER_STATUS_PAID:
                    // 已付款
                    $data['paid']++;
                    break;
                case jys_system_code::ORDER_STATUS_DELIVERED:
                    // 已发货
                    $data['delivered']++;
                    break;
                case jys_system_code::ORDER_STATUS_SENT_BACK:
                    // 已寄回
                    $data['sentback']++;
                    break;
                case jys_system_code::ORDER_STATUS_ASSAYING:
                    // 正在检测
                    $data['assaying']++;
                    break;
                case jys_system_code::ORDER_STATUS_FINISHED:
                    // 已完成
                    $data['finished']++;
                    break;
                case jys_system_code::ORDER_STATUS_REFUNDING:
                    // 退款中
                    $data['refunding']++;
                    break;
                case jys_system_code::ORDER_STATUS_REFUNDED:
                case jys_system_code::ORDER_STATUS_UNREFUNDED:
                    // 已退款、拒绝退款
                    $data['refunded']++;
                    break;
            }
        }

        return $data;
    }

    /**
     * 根据user_id获取可评价订单的数量
     */
    public function get_can_evaluate_order_num($user_id = 0) {
        if (intval($user_id) < 1) {
            return FALSE;
        }
        $this->db->select('order.id');
        $this->db->where('order.user_id', $user_id);
        $this->db->where('order.id IS NOT NULL');
        $this->db->where('order_commodity.id IS NOT NULL');
        $this->db->where('commodity_evaluation.id IS NULL');
        $this->db->group_start();
        $this->db->where('order.status_id', jys_system_code::ORDER_STATUS_FINISHED);
        $this->db->or_where('order.status_id', jys_system_code::ORDER_STATUS_REFUNDED);
        $this->db->or_where('order.status_id', jys_system_code::ORDER_STATUS_UNREFUNDED);
        $this->db->group_end();
        $this->db->join('order_commodity', 'order_commodity.order_id = order.id', 'left');
        $this->db->join('commodity_evaluation', 'commodity_evaluation.order_commodity_id = order_commodity.id AND commodity_evaluation.order_id = order.id', 'left');
        $this->db->group_by('order.id');
        $result = $this->db->get('order');

        if ($result && $result->num_rows() > 0) {
            return $result->num_rows();
        }else {
            return FALSE;
        }
    }

    /**
     * 根据user_id获取可评价订单的数量
     */
    public function get_can_evaluate_order($page = 1, $page_size = 10, $user_id = 0) {
        $data = array('success'=>FALSE, 'msg'=>'获取待评价列表失败', 'data'=>array(), 'total_page'=>0);
        if (intval($page) < 1 || intval($page_size) < 1 || intval($user_id) < 1) {
            $data['msg'] = '参数错误';
            return $data;
        }

        $this->db->select('
            order.id,
            order.number,
            order.total_price,
            order.payment_amount,
            order.payment_order,
            order.express_number,
            order.predict_complete_time,
            order.create_time,
            order.payment_time,
            order.payment_id,
            order.terminal_type,
            order.status_id,
            payment.name AS payment_type_name,
            terminal_type.name AS terminal_type_name,
            order_status.name AS order_status_name
        ');
        $this->db->where('order.user_id', $user_id);
        $this->db->where('order.id IS NOT NULL');
        $this->db->where('order_commodity.id IS NOT NULL');
        $this->db->where('commodity_evaluation.id IS NULL');
        $this->db->group_start();
        $this->db->where('order.status_id', jys_system_code::ORDER_STATUS_FINISHED);
        $this->db->or_where('order.status_id', jys_system_code::ORDER_STATUS_REFUNDED);
        $this->db->or_where('order.status_id', jys_system_code::ORDER_STATUS_UNREFUNDED);
        $this->db->group_end();
        $this->db->join('order_commodity', 'order_commodity.order_id = order.id', 'left');
        $this->db->join('commodity_evaluation', 'commodity_evaluation.order_commodity_id = order_commodity.id AND commodity_evaluation.order_id = order.id', 'left');
        $this->db->join('system_code as payment', "payment.value = order.payment_id AND payment.type ='".jys_system_code::PAYMENT."'", 'left');
        $this->db->join('system_code as terminal_type', "terminal_type.value = order.terminal_type AND terminal_type.type = '".jys_system_code::TERMINAL_TYPE."'", 'left');
        $this->db->join('system_code as order_status', "order_status.value = order.status_id AND order_status.type = '".jys_system_code::ORDER_STATUS."'", 'left');
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $this->db->group_by('order.id');
        $this->db->order_by('order.create_time', 'DESC');
        $result = $this->db->get('order');

        if ($result && $result->num_rows() > 0) {
            $data['success'] = TRUE;
            $data['msg'] = '查询成功';
            $orders = $result->result_array();
            $orders = $this->push_sub_order($orders);
            $data['data'] = $orders;
            $total = $this->get_can_evaluate_order_num($user_id);
            if (intval($total) > 0) {
                $data['total_page'] = ceil($total / $page_size * 1.0);
            }else {
                $data['total_page'] = 1;
            }
            return $data;
        }else {
            return FALSE;
        }
    }

    /**
     * 微信统一下单
     * @param $order_id 订单Id
     * @return mixed
     */
    public function wechat_pay_unified_order($order_id, $trade_type="NATIVE", $open_id = ""){
        $result = array('success'=>FALSE, 'msg'=>'');
        $order = $this->get_order_by_condition(array('order.id'=>$order_id));
        if (empty($order) || $order['success'] == false || empty($order['data'])) {
            $result['msg'] = "未找到相关订单信息";
            return $result;
        }
        $order = $order['data'];
        $total_fee = floatval($order['payment_amount']);
        if(empty($order['payment_time'])){   //如果未支付，则执行下面的
            if ($trade_type == "NATIVE") {
                // 扫码支付
                if(!empty($order['wx_code_url'])){ //判断是否存在二维码,这里是存在二维码
                    $current_time = strtotime(date('Y-m-d H:i:s'));
                    $wx_code_url_time = strtotime($order['wx_code_url_time']);
                    if($wx_code_url_time - $current_time > 7200){  //判断二维码是否过期（2小时有效期),这里是已过期
                        //过期后重新生成二维码
                        $wx_order = $this->jys_weixin_pay->getOrderInfo("基因商城".$order['number'], $order, $order['number'], $total_fee, $order_id, $trade_type);
                        $wx_order = $this->jys_weixin_pay->unifiedOrder($wx_order);
                        $wx_info = array(
                            'wx_code_url'=> $wx_order['code_url'],
                            'wx_code_url_time' => strftime('%Y-%m-%d %H:%M:%S',strtotime(date('Y-m-d H:i:s'))+7200) //更新过期时间
                        );
                        $result['success'] = TRUE;
                        $result['code_url'] = $wx_order['code_url'];
                        $result['wx_code_url_time'] = $wx_info['wx_code_url_time'];
                        $this->db->where('id', $order_id)->update('order',$wx_info); //更新二维码和过期时间
                    }else{
                        $result['success'] = TRUE;
                        $result['code_url'] = $order['wx_code_url'];  //未过期就直接使用以前的二维码
                        $result['wx_code_url_time'] = $order['wx_code_url_time'];
                    }
                }else{ //由于是微信支付，不存在二维码，也不会存在商户订单号
                    $wx_order = $this->jys_weixin_pay->getOrderInfo("基因商城".$order['number'], $order, $order['number'], $total_fee, $order_id, $trade_type);
                    $wx_order = $this->jys_weixin_pay->unifiedOrder($wx_order);

                    if ($wx_order['return_code'] == "FAIL") {
                        $result['success'] = FALSE;
                        $result['msg']     = $wx_order['return_msg'];
                    }else {
                        $wx_info = array(
                            'wx_code_url'=> $wx_order['code_url'],
                            'wx_code_url_time' => strftime('%Y-%m-%d %H:%M:%S',strtotime(date('Y-m-d H:i:s'))+7200) //更新过期时间
                        );
                        $result['success'] = TRUE;
                        $result['code_url'] = $wx_order['code_url'];
                        $result['wx_code_url_time'] = $wx_info['wx_code_url_time'];
                        $this->db->where('id', $order_id)->update('order',$wx_info); //更新二维码和过期时间
                    }
                }
            }else if ($trade_type == "JSAPI") {
                // 微信公众号JSAPI支付
                if (empty($open_id)) {
                    $result['msg'] = "用户openid不正确";
                    return $result;
                }
                $wx_order = $this->jys_weixin_pay->getOrderInfo("基因商城".$order['number'], $order, $order['number'], $total_fee, $order_id, $trade_type, $open_id);
                $wx_order = $this->jys_weixin_pay->unifiedOrder($wx_order);
                $js_api_parameters = $this->jys_weixin_pay->GetJsApiParameters($wx_order);
                if (empty($js_api_parameters)) {
                    $result['msg'] = "获取JSAPI支付参数失败";
                }else {
                    $result['success'] = TRUE;
                    $result['msg'] = "获取JSAPI支付参数成功";
                    $result['js_api_parameters'] = $js_api_parameters;
                }
            }else{
                $result['msg'] = "支付方式不正确";
            }
        }else{
            $result['success'] = TRUE;
            $result['code_url'] = $order['wx_code_url'];  //已支付的订单就使用原来的二维码，防止重复支付
            $result['wx_code_url_time'] = $order['wx_code_url_time'];
        }

        return $result;
    }

    /**
     * 支付成功后修改订单状态
     * @param $number 订单编号
     * @param $payment_order 支付平台端生成的支付单号
     * @param $payment_amount 实际支付金额
     * @param $payment_id 支付方式ID
     * @return 成功返回TRUE，失败返回FALSE
     */
    public function get_pay_result_set_status($number, $payment_order, $payment_amount, $payment_id, $payment_time) {
        if (empty($number) || empty($payment_order) || floatval($payment_amount) < 0.01 || intval($payment_id) < 1 || empty($payment_time)) {
            return FALSE;
        }

        //微信推送支付成功消息
        $order = $this->jys_db_helper->get_where('order', ['number'=>$number]);
        $sub_orders = $this->show_sub_order($order['id'])['data'];
        $user = $this->jys_db_helper->get('user', $order['user_id']);
        $url = site_url('weixin/index/order_detail/'.$order['id']);
        $commodity_name_str = '';
        if (!empty($sub_orders)){
            foreach ($sub_orders as $sub_order) {
                $commodity_name_str .= $sub_order['commodity_name'].'*'.$sub_order['amount'].' ';
            }
        }

        $info = [
            'first' => [
                'value' => '您的订单已支付成功',
                'color' => '#000000'
            ],
            'keyword1' => [
                'value' => $user['username'],
                'color' => '#000000'
            ],
            'keyword2' => [
                'value' => $order['number'],
                'color' => '#000000'
            ],
            'keyword3' => [
                'value' => '¥'.$order['payment_amount'],
                'color' => '#000000'
            ],
            'keyword4' => [
                'value' => $commodity_name_str,
                'color' => '#000000'
            ],
            'remark' => [
                'value' => $this->config->item('wx_tm_remarks'),
                'color' => '#000000'
            ]
        ];

        if ($order['status_id'] < Jys_system_code::ORDER_STATUS_PAID){
            $this->jys_weixin->send_template_message($user['openid'], $this->config->item('wx_tm_order_payment_success'), $info, $url);
        }

        $update = array(
            'payment_amount'=>$payment_amount,
            'payment_order'=>$payment_order,
            'payment_id'=>$payment_id,
            'payment_time'=>$payment_time,
            'status_id'=>jys_system_code::ORDER_STATUS_PAID
        );
        $this->jys_db_helper->update_by_condition('order', array('number'=>$number), $update);

        return TRUE;
    }

    /**
     * 创建本地订单
     * @param $user_id 用户ID
     * @param $shopping_cart_ids 购物车id构成的字符串
     * @param $is_point_flag 是否是积分商品订单
     * @param $address_id 地址ID
     * @param $payment_id 支付方式
     * @param $status_id 订单状态
     * @param int $terminal_type
     * @param null $user_discount_coupon
     * @param null $message
     * @return array
     */
    public function add($user_id, $shopping_cart_ids, $is_point_flag, $address_id, $payment_id, $terminal_type = jys_system_code::TERMINAL_TYPE_PC, $user_discount_coupon = NULL, $message = NULL, $user_info = []) {
        $result = array('success'=>FALSE, 'msg'=>'创建订单失败', 'insert_id'=>0);
        if (intval($user_id) < 1 || empty($shopping_cart_ids) || intval($address_id) < 1 || intval($payment_id) < 1 || intval($is_point_flag) < 0) {
            $result['msg'] = '创建订单失败，订单参数不正确';
            return $result;
        }

        $add['user_id'] = $user_id;
        if ($is_point_flag){
            $orders = $this->jys_db_helper->get('commodity', $shopping_cart_ids[0]);
        }else{
            $orders = $this->get_order_by_shopping_cart($shopping_cart_ids);
        }
        $add['number'] = $this->generate_order_number();
        $add['total_price'] = $is_point_flag ? $orders['price'] : $this->calculation_total_price($orders);
        $add['address'] = json_encode($this->jys_db_helper->get('address', $address_id));
        $add['payment_time']               = $is_point_flag ? date('Y-m-d H:i:s') : NULL;
        $add['create_time']                = date('Y-m-d H:i:s');
        $add['terminal_type']              = $terminal_type;
        $add['payment_id']                 = $payment_id;
        $add['status_id']                  = Jys_system_code::ORDER_STATUS_NOT_PAID;
        
        if ($is_point_flag){
            $add['payment_amount'] = $add['total_price'];
            $add['status_id'] = Jys_system_code::ORDER_STATUS_PAID;
        }

        if (!empty($message)) {
            $add['message'] = $message;
        }
        if (!empty($user_discount_coupon) && is_array($user_discount_coupon) && count($user_discount_coupon) && !$is_point_flag && $user_discount_coupon['status_id'] == jys_system_code::USER_DISCOUNT_COUPON_STATUS_UNUSED) {
            // 使用了优惠券
            if (floatval($add['total_price']) >= floatval($user_discount_coupon['condition'])) {
                $add['user_discount_coupon_id'] = $user_discount_coupon['id'];
                $add['payment_amount'] = floatval($add['total_price']) - floatval($user_discount_coupon['privilege']);

                // 将优惠券置为已使用
                $this->jys_db_helper->update('user_discount_coupon', $user_discount_coupon['id'], ['status_id'=>jys_system_code::USER_DISCOUNT_COUPON_STATUS_USED]);
            }else {
                $result['msg'] = '创建订单失败，该订单不符合优惠券条件';
                return $result;
            }
        }else {
            $add['payment_amount'] = floatval($add['total_price']);
        }
        if ($add['payment_amount'] == 0) {
            // 经过优惠券折扣之后，商品价格为0的，直接改订单状态为已支付
            $add['status_id']                  = Jys_system_code::ORDER_STATUS_PAID;
        }else if ($add['payment_amount'] < 0.01) {
            // 如果价格不为0，但又小于0.01元，则置为0.01
            $add['payment_amount'] = 0.01;
        }

        $data = $this->jys_db_helper->add('order', $add, TRUE);
        if ($data['success']){
            if (isset($orders['type_id']) && $orders['type_id'] == 3 && !empty($orders['level_id'])){
                $user_level = $this->jys_db_helper->get('level', $user_info['level']);
                $order_level = $this->jys_db_helper->get('level', $orders['level_id']);
                if ($user_level['rank'] > $order_level['rank']){
                    $result['msg'] = '您当前的会员等级大于购买的会员等级，无法生成订单';
                    return $result;
                }
            }
            if ($is_point_flag){
                $order['commodity_id']   = $orders['id'];
                $order['amount']         = 1;
                $order['price']          = $orders['price'];
                $order['points']         = 0;
                $order['total_price']    = $orders['price'];
                $order['order_id']       = $data['insert_id'];
                $order['number']         = $add['number'].'1';
                $order['create_time']    = date('Y-m-d H:i:s');


                $_data = $this->jys_db_helper->add('order_commodity', $order);
                if ($_data['success']){
                    $this->jys_db_helper->set_update('user', $_SESSION['user_id'], ['current_point'=>'current_point - '.intval($orders['price'])], FALSE);
                }
            }else{
                foreach ($orders as $key => $order){
                    $order_commodities[$key]['amount']         = $order['amount'];
                    $order_commodities[$key]['commodity_id']   = $order['commodity_id'];
                    $order_commodities[$key]['points']         = $order['points'];
                    $order_commodities[$key]['price']          = $order['price'];
                    $order_commodities[$key]['total_price']    = $order['total_price'];
                    $order_commodities[$key]['order_id']       = $data['insert_id'];
                    $order_commodities[$key]['number']         = $add['number'].($key+1);
                    $order_commodities[$key]['create_time']    = date('Y-m-d H:i:s');

                    //修改销量
                    $this->jys_db_helper->set_update('commodity', $order['commodity_id'], ['sales_volume'=>'sales_volume + '.$order['amount']], FALSE);
                }
                $_data = $this->jys_db_helper->add_batch('order_commodity', $order_commodities);
            }

            if ($_data['success'] && ($is_point_flag ? TRUE : $this->jys_db_helper->delete('shopping_cart', $shopping_cart_ids))){
                if (isset($add['user_discount_coupon_id']) && intval($add['user_discount_coupon_id']) > 0) {
                    $this->jys_db_helper->update('user_discount_coupon', $add['user_discount_coupon_id'], ['status_id'=>jys_system_code::USER_DISCOUNT_COUPON_STATUS_USED]);
                }
            }else{
                $data['insert_id'] = NULL;
                $data['success'] = FALSE;
                $data['msg'] = '添加失败';
            }
        }
        return $data;
    }

    /**
     * 用户申请退款
     * @param $order_id 订单ID
     * @param $user_id 用户ID
     */
    public function application_for_refund($order_id, $user_id) {
        $result = array('success'=>FALSE, 'msg'=>'申请退款失败');
        if(intval($order_id) < 1 || intval($user_id) < 1) {
            $result['msg'] = '订单信息不正确';
            return $result;
        }

        $this->db->trans_start();
        $order_info = $this->get_order_by_condition(array('order.id' => $order_id, 'order.user_id'=>$user_id))['data'];
        if (empty($order_info)) {
            $result['msg'] = '未查询到相关订单';
        }else if (intval($order_info['payment_id']) == jys_system_code::PAYMENT_POINTPAY) {
            // 积分订单不能退款
            $result['msg'] = '积分订单无法申请退款';
        }else {
            if ($order_info['status_id'] == jys_system_code::ORDER_STATUS_NOT_PAID) {
                // 订单状态为未支付，直接关闭订单
                if ($this->jys_db_helper->update('order', $order_id, ['status_id'=>jys_system_code::ORDER_STATUS_CANCELED])) {
                    $result['msg'] = '订单取消成功';
                    $result['success'] = TRUE;
                }else {
                    $result['msg'] = '订单取消失败';
                }
            }else if ($order_info['status_id'] == jys_system_code::ORDER_STATUS_PAID) {
                // 订单状态为已支付，提交管理员审核
                $this->jys_db_helper->update('order', $order_id, ['status_id'=>jys_system_code::ORDER_STATUS_REFUNDING]);
                $insert = array(
                    'order_id'=>$order_info['id'],
                    'price'=>$order_info['payment_amount'],
                    'status_id'=>jys_system_code::REFUND_STATUS_APPLYING,
                    'payment_id'=>$order_info['payment_id'],
                    'create_time'=>date('Y-m-d H:i:s')
                );
                $insert['number'] = 'RF'.$this->generate_order_number();

                $insert_result = $this->jys_db_helper->add('refund', $insert);

                if ($insert_result['success']) {
                    $result['msg'] = '申请退款成功';
                    $result['success'] = TRUE;
                }else {
                    $result['msg'] = '申请退款失败';
                }
            }
        }
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $result['msg'] = '申请退款失败';
        }

        return $result;
    }

    /**
     * 分页获取退款信息
     * @param int $page
     * @param int $page_size
     * @param null $condition
     */
    public function paginate_for_refund($payment_id = 0, $page = 1, $page_size = 10, $condition = NULL) {
        $data = [
            'success' => FALSE,
            'msg' => '没有订单数据',
            'data' => null
        ];

        if (intval($page) < 1 || intval($page_size) < 1 || intval($payment_id) < 1) {
            $data['msg'] = '参数错误';
            return $data;
        }

        $this->db->select(
            'refund.*,
            order.user_id,
            order.number as order_number,
            order.total_price,
            order.user_discount_coupon_id,
            order.payment_amount,
            order.payment_order,
            order.address,
            order.terminal_type as terminal_type_id,
            order.status_id as order_status_id,
            order_status.name as order_status_name,
            order.create_time as order_create_time,
            order.payment_time as order_payment_time,
            refund_status.name as refund_status_name,
            payment.name as payment_name,
            discount_coupon.name as discount_coupon_name,
            user.username,
            user.phone,
            terminal_type.name as terminal_type_name'
        );
        $this->db->where('refund.payment_id', $payment_id);
        $this->db->join('order', 'order.id = refund.order_id', 'left');
        $this->db->join('user', 'user.id = order.user_id', 'left');
        $this->db->join('system_code as order_status', "order_status.value = order.status_id AND order_status.type = '".jys_system_code::ORDER_STATUS."'", 'left');
        $this->db->join('system_code as refund_status', "refund_status.value = refund.status_id AND refund_status.type = '".jys_system_code::REFUND_STATUS."'", 'left');
        $this->db->join('system_code as payment', "payment.value = refund.payment_id AND payment.type = '".jys_system_code::PAYMENT."'", 'left');
        $this->db->join('system_code as terminal_type', "terminal_type.value = order.terminal_type AND terminal_type.type = '".jys_system_code::TERMINAL_TYPE."'", 'left');
        $this->db->join('user_discount_coupon', 'user_discount_coupon.id = order.user_discount_coupon_id', 'left');
        $this->db->join('discount_coupon', 'discount_coupon.id = user_discount_coupon.discount_coupon_id', 'left');
        $this->db->order_by('refund.create_time', 'DESC');
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $refund_list = $this->db->get('refund');

        if ($refund_list && $refund_list->num_rows() > 0) {
            $data['msg'] = '查询成功';
            $data['success'] = TRUE;
            $refund_arr = $refund_list->result_array();
            foreach ($refund_arr as $key => $refund){
                $refund_arr[$key]['address'] = json_decode($refund['address']);
            }

            $data['data'] = $refund_arr;
        }else {
            $data['msg'] = '未找到符合要求的订单';
        }
        return $data;
    }

    /**
     * 审核退款申请
     *
     * @param $id
     * @param $audit_result
     */
    public function audit_refund($refund_id = 0, $audit_result = "") {
        $result = array('success'=>FALSE, 'msg'=>'操作失败');
        if (intval($refund_id) < 1 || empty($audit_result)) {
            $result['msg'] = '参数错误';
            return $result;
        }

        $this->db->select(
            'order.number as order_number, 
                order.payment_amount, 
                order.payment_time,
                order.payment_id,
                order.payment_order,
                refund.number as refund_number,
                refund.price,
                refund.order_id'
        );
        $this->db->join('order', 'order.id = refund.order_id', 'left');
        $this->db->where('refund.id', $refund_id);
        $refund = $this->db->get('refund');
        if ($refund && $refund->num_rows() > 0) {
            $refund = $refund->row_array();
            if (strtolower($audit_result) == 'true') {
                // 同意退款
                switch(intval($refund['payment_id'])) {
                    case jys_system_code::PAYMENT_WXPAY:
                        // 微信退款
                        $refund_result = $this->_wechat_pay_refund($refund);
                        break;
                    case jys_system_code::PAYMENT_ALIPAY:
                        // 支付宝退款
                        break;
                    case jys_system_code::PAYMENT_UNIONPAY:
                        // 银联退款
                        break;
                    default:
                        // 其他支付方式不能退款
                        $result['msg'] = '支付方式不支持退款';
                        break;
                }

                if (isset($refund_result) && !empty($refund_result) && is_array($refund_result)) {
                    if ($refund_result['success']) {
                        // 退款成功
                        $this->db->trans_start();

                        $this->jys_db_helper->update('refund', $refund_id, ['status_id'=>jys_system_code::REFUND_STATUS_AGREED, 'refund_order'=>$refund_result['refund_order']]);
                        $this->jys_db_helper->update('order', $refund['order_id'], ['status_id'=>jys_system_code::ORDER_STATUS_REFUNDED]);

                        $this->db->trans_complete();

                        if ($this->db->trans_status() === FALSE) {
                            $result['msg'] = '操作失败';
                        }else {
                            $result['success'] = TRUE;
                            $result['msg'] = '操作成功';
                        }
                    }else {
                        // 退款失败
                        $result['msg'] = $refund_result['msg'];
                    }
                }
            }else {
                // 拒绝退款
                $this->db->trans_start();

                $this->jys_db_helper->update('refund', $refund_id, ['status_id'=>jys_system_code::REFUND_STATUS_REJECTED]);
                $this->jys_db_helper->update('order', $refund['order_id'], ['status_id'=>jys_system_code::ORDER_STATUS_UNREFUNDED]);

                $this->db->trans_complete();

                if ($this->db->trans_status() === FALSE) {
                    $result['msg'] = '操作失败';
                }else {
                    $result['success'] = TRUE;
                    $result['msg'] = '操作成功';
                }
            }
        }else {
            $result['msg'] = '未找到相关退款请求';
        }
        return $result;
    }

    /**
     * 调用微信退款
     * @param $refund
     * @return array
     */
    private function _wechat_pay_refund($refund) {
        $result = array('success'=>FALSE, 'msg'=>'微信退款失败');
        if (empty($refund) || !is_array($refund)) {
            $result['msg'] = '未找到退款信息';
            return $result;
        }
        $refund_result = $this->jys_weixin_pay->refund($refund['payment_order'], $refund['order_number'], $refund['payment_amount'], $refund['price'], $_SESSION['username'], $refund['refund_number']);
        if ($refund_result['return_code'] == 'SUCCESS') {
            $result['success'] = TRUE;
            $result['msg'] = '退款成功';
            $result['refund_order'] = $refund_result['refund_id'];
        }else {
            $result['msg'] = $refund_result['err_code_des'];
        }

        return $result;
    }

    /**
     * 根据订单ID获取订单评价信息
     * @param $order_id 订单ID
     */
    public function get_evaluation_by_order_id($order_id = 0) {
        $result = array('success'=>FALSE, 'msg'=>'获取订单信息失败', 'data'=>NULL);
        if (intval($order_id) < 1) {
            $result['msg'] = '订单ID不正确';
            return $result;
        }

        $this->db->select(
            'commodity_evaluation.id,
            commodity_evaluation.user_id,
            commodity_evaluation.score,
            commodity_evaluation.content,
            commodity_evaluation.create_time,
            commodity_thumbnail.commodity_id,
            commodity.name as commodity_name,
            order_commodity.commodity_id,
            order.number as order_number,
            order_commodity.number as order_commodity_number,
            order_commodity.order_id AS `order_id`,
	        order_commodity.id as order_commodity_id,
	        commodity_pic.path as commodity_thumbnail_path'
        );
        $this->db->where('order_commodity.order_id', $order_id);
        $this->db->join('order', 'order.id = order_commodity.order_id', 'left');
        $this->db->join('commodity', 'commodity.id = order_commodity.commodity_id', 'left');
        $this->db->join('commodity_evaluation', 'order_commodity.id = commodity_evaluation.order_commodity_id', 'left');
        $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_id = order_commodity.commodity_id', 'left');
        $this->db->join('attachment as commodity_pic', 'commodity_pic.id = commodity_thumbnail.attachment_id', 'left');
        $this->db->group_by('order_commodity_id');

        $order_data = $this->db->get('order_commodity');
        if ($order_data && $order_data->num_rows() > 0) {
            // 订单信息存在
            $order_data = $order_data->result_array();
            for ($i = 0; $i < count($order_data); $i++) {
                if (intval($order_data[$i]['id']) > 0) {
                    // 已经评论过了
                    $pic = $this->get_evaluation_pics_by_evaluation_id(intval($order_data[$i]['id']));
                    if ($pic['success']) {
                        // 有图片
                        $order_data[$i]['pic'] = $pic['data'];
                    }else {
                        // 没图片
                        $order_data[$i]['pic'] = array();
                    }
                }else {
                    // 还没评论
                    $order_data[$i]['pic'] = array();
                }
            }
            $result['data'] = $order_data;
            $result['success'] = TRUE;
            $result['msg'] = '获取订单信息成功';
        }else {
            // 订单信息不存在
            $result['msg'] = '未找到相关订单';
        }

        return $result;
    }

    /**
     * 根据评价ID获取评价图片
     * @param int $evaluation_id 评价ID
     */
    public function get_evaluation_pics_by_evaluation_id($evaluation_id = 0) {
        $result = array('success'=>FALSE, 'msg'=>'获取评价图片失败', 'data'=>array());
        if (intval($evaluation_id) < 1) {
            $result['msg'] = '评价ID不正确';
            return $result;
        }

        $this->db->select('commodity_evaluation_pic.*, attachment.path');
        $this->db->join('attachment', 'attachment.id = commodity_evaluation_pic.attachment_id');
        $this->db->where('commodity_evaluation_pic.commodity_evaluation_id', $evaluation_id);
        $data = $this->db->get('commodity_evaluation_pic');

        if ($data && $data->num_rows() > 0) {
            $result['data'] = $data->result_array();
            $result['success'] = TRUE;
            $result['msg'] = '获取评价图片成功';
        }

        return $result;
    }

    /**
     * 用户发表评论
     *
     * @param array $evaluate_info
     * @param array $attachment_ids
     */
    public function evaluate_order($evaluation_info = array(), $user_id = 0, $attachment_ids = array()) {
        $result = array('success'=>FALSE, 'msg'=>'发表评价失败');
        if (empty($evaluation_info) || !is_array($evaluation_info) || intval($user_id) < 1) {
            $result['msg'] = '参数错误';
            return $result;
        }

        $this->db->trans_start();
        $evaluation = $this->jys_db_helper->get_where('commodity_evaluation', ['order_commodity_id'=>$evaluation_info['order_commodity_id'], 'order_id'=>$evaluation_info['order_id'], 'commodity_id'=>$evaluation_info['commodity_id']]);
        if (!empty($evaluation) && is_array($evaluation)) {
            // 已有评论
            $result['msg'] = '该订单已发表过评价，不需要再次评价';
        }else {
            // 没有评论
            $current_time = date('Y-m-d H:i:s');
            $evaluation_info['user_id'] = $user_id;
            $evaluation_info['create_time'] = $current_time;
            $insert_result = $this->jys_db_helper->add('commodity_evaluation', $evaluation_info, TRUE);
            if ($insert_result['success']) {
                if (!empty($attachment_ids) && is_array($attachment_ids)) {
                    $commodity_evaluation_pic = array();
                    foreach ($attachment_ids as $item_id) {
                        if (intval($item_id) > 0) {
                            $commodity_evaluation_pic[] = array(
                                'commodity_id'=>$evaluation_info['commodity_id'],
                                'commodity_evaluation_id'=>$insert_result['insert_id'],
                                'attachment_id'=>$item_id,
                                'create_time'=>$current_time
                            );
                        }
                    }
                    if (!empty($commodity_evaluation_pic)) {
                        $this->db->insert_batch('commodity_evaluation_pic', $commodity_evaluation_pic);
                    }
                }
                $result['success'] = TRUE;
                $result['msg'] = '发表评价成功';
            }else {
                $result['msg'] = '发表评价失败';
            }

        }
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $result['msg'] = '发表评价失败';
            $result['success'] = FALSE;
        }

        return $result;
    }

    /**
     * 根据附件ID获取pdf路径
     *
     * @param null $attachment_id 附件ID
     * @return bool
     */
    public function get_pdf_url($attachment_id = NULL){
        if (empty($attachment_id) || intval($attachment_id) < 1){
            return FALSE;
        }

        $attachment = $this->jys_db_helper->get('attachment', $attachment_id);

        if ($attachment){
            return $attachment['path'];
        }else{
            return FALSE;
        }
    }

    /**
     * 修改订单状态为已完成
     * @param string $order_number 订单编号
     */
    public function finish_order($order_number = "") {
        $result = array('success'=>FALSE, 'msg'=>'修改订单信息失败');
        if (empty($order_number)) {
            $result['msg'] = '订单编号不能为空';
            return $result;
        }

        $this->db->trans_start();
        $order = $this->get_order_by_condition(array('order.number'=>$order_number));
        if ($order['success'] && !empty($order['data']) && isset($order['data']['sub_orders']) && is_array($order['data']['sub_orders']) && count($order['data']['sub_orders']) > 0) {
            if (intval($order['data']['status_id']) != jys_system_code::ORDER_STATUS_FINISHED) {
                $total_points = 0;
                foreach ($order['data']['sub_orders'] as $sub_order) {
                    $total_points += intval($sub_order['points']);
                }
                if ($total_points > 0) {
                    $this->jys_db_helper->set_update('user', $order['data']['user_id'], ['current_point'=>'current_point + '.intval($total_points), 'total_point'=>'total_point + '.intval($total_points)], FALSE);
                }
                $this->jys_db_helper->update('order', $order['data']['id'], ['finnished_time'=>date('Y-m-d H:i:s'), 'status_id'=>jys_system_code::ORDER_STATUS_FINISHED]);
                $result['success'] = TRUE;
                $result['msg'] = '修改成功';
            }else {
                // 当前订单已经是已完成状态，不需要修改
                $result['success'] = TRUE;
                $result['msg'] = '当前订单已经是已完成状态，不需要修改';
            }
        }else {
            // 未找到相关订单信息
            $result['msg'] = '未找到相关订单信息';
        }
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            // 事务失败
            $result['success'] = FALSE;
            $result['msg'] = '修改失败，事务提交失败';
        }
        return $result;
    }

    /**
     * 订阅快递订单信息
     * @param $express_company_id 快递公司ID
     * @param $express_number 快递单号
     * @param string $order_number 系统内部订单编号或子订单编号
     * @param string $callback 自定义回调信息
     */
    public function subscribe_express_info($express_company_id, $express_number, $order_number = "", $callback = "") {
        if (intval($express_company_id) < 1 || empty($express_number)) {
            return FALSE;
        }

        $express_company = $this->jys_db_helper->get('express_company', $express_company_id);
        if (isset($express_company['code']) && !empty($express_company['code'])) {
            // 记录调用订阅快递信息接口的次数
            $subscribe_express_infotimes = 0;
            while (TRUE) {
                $dist_result = $this->jys_kdniao->dist($express_company['code'], $express_number, $order_number, $callback);
                if (!empty($dist_result) && is_array($dist_result) && isset($dist_result['Success']) && $dist_result['Success']) {
                    // 订阅快递信息成功
                    break;
                }else {
                    // 订阅失败，次数增加
                    $subscribe_express_infotimes++;
                }

                if ($subscribe_express_infotimes > 3) {
                    // 当调用次数大于3次时，返回错误信息
                    return FALSE;
                }
            }
        }else {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * 自动取消下单后超过半个小时仍未付款的订单
     */
    public function auto_cancel_not_paid_order() {
        $condition_time = date("Y-m-d H:i:s", strtotime("-30 minute"));

        $this->db->trans_start();
        $this->db->select('
            order.id,
            order.number,
            order.total_price,
            order.user_discount_coupon_id,
            order.user_id,
            user.openid,
            user.username,
            user.phone
        ');
        $this->db->join('user', 'user.id = order.user_id', 'left');
        $this->db->where('order.create_time <=', $condition_time);
        $this->db->where('order.status_id', jys_system_code::ORDER_STATUS_NOT_PAID);
        $order_list = $this->db->get('order');
        if ($order_list && $order_list->num_rows() > 0) {
            // 有符合条件的订单
            if ($this->jys_db_helper->update_by_condition('order', ['create_time <='=>$condition_time, 'status_id'=> jys_system_code::ORDER_STATUS_NOT_PAID], ['status_id'=>jys_system_code::ORDER_STATUS_CANCELED])) {
                // 逐个向用户发送通知，告知订单已被自动取消

            }
            // 恢复用户使用过的优惠券
            $user_discount_coupon_id_list = array();
            foreach ($order_list->result_array() as $order) {
                if (isset($order['user_discount_coupon_id']) && intval($order['user_discount_coupon_id']) > 0) {
                    $user_discount_coupon_id_list[] = intval($order['user_discount_coupon_id']);
                }
            }
            if (!empty($user_discount_coupon_id_list)) {
                $this->db->where_in('id', $user_discount_coupon_id_list);
                $this->db->where('end_time >', date("Y-m-d H:i:s"));
                $this->db->update('user_discount_coupon', ['status_id'=>Jys_system_code::USER_DISCOUNT_COUPON_STATUS_UNUSED]);

                $this->db->where_in('id', $user_discount_coupon_id_list);
                $this->db->where('end_time <=', date("Y-m-d H:i:s"));
                $this->db->update('user_discount_coupon', ['status_id'=>Jys_system_code::USER_DISCOUNT_COUPON_STATUS_EXPIRED]);
            }
        }
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }else {
            return TRUE;
        }
    }

    /**
     * 获取当前用户消费总额
     * @param $user_id
     */
    public function get_user_expenditure($user_id = 0)
    {
        if (empty($user_id)){
            $data = ['success' => FALSE, 'msg' => '参数错误', 'data' => NULL];
            return $data;
        }

        $this->db->select('total_price, user_id');
        $this->db->where('user_id', $user_id);
        $this->db->where('status_id', Jys_system_code::ORDER_STATUS_FINISHED);
        $result = $this->db->get('order');

        if ($result && $result->num_rows() > 0){
            $data = ['success' => TRUE, 'data' => $result->result_array()];
        }else{
            $data = ['success' => FALSE, 'data' => NULL];
        }

        return $data;
    }

    /**
     * 添加奖品订单
     *
     * @param int $user_id 用户ID
     * @param int $commodity_id 商品ID
     * @param int $address_id 地址ID
     * @param int $terminal_type 客户端类型
     * @return mixed
     */
    public function add_prize_order($user_id = 0, $commodity_id = 0, $address_id = 0, $terminal_type = Jys_system_code::TERMINAL_TYPE_PC, $is_indiana = TRUE, $id = 0){
        $result['success'] = FALSE;
        $result['msg'] = '添加失败';
        if(empty($user_id) || intval($user_id) < 1 || empty($commodity_id) || intval($commodity_id) < 1 || empty($address_id) || intval($address_id) < 1){
            $result['msg'] = '参数错误';
            return $result;
        }

        $commodity = $this->Commodity_model->get_commodity_list_by_condition(['commodity.id'=>$commodity_id], FALSE);
        if ($commodity){
            $order['user_id'] = $user_id;
            $order['number'] = $this->generate_order_number();
            $order['total_price'] = $commodity['price'] * 1;
            $order['payment_amount'] = 0;
            if ($is_indiana){
                $order['payment_id'] = Jys_system_code::PAYMENT_INTEGRAL_INDIANA;
            }else{
                $order['payment_id'] = Jys_system_code::PAYMENT_INTEGRAL_SWEEPSTAKES;
            }
            $order['address'] = json_encode($this->jys_db_helper->get('address', $address_id));
            $order['terminal_type'] = $terminal_type;
            $order['status_id'] = Jys_system_code::ORDER_STATUS_PAID;
            $order['create_time'] = date('Y-m-d H:i:s');
            $order['payment_time'] = $order['create_time'];

            $this->db->trans_begin();
            $data = $this->jys_db_helper->add('order', $order, TRUE);
            if ($data['success']){
                $sub_order['order_id'] = $data['insert_id'];
                $sub_order['number'] = $order['number'].'1';
                $sub_order['commodity_id'] = $commodity_id;
                $sub_order['price'] = $commodity['price'];
                $sub_order['amount'] = 1;
                $sub_order['total_price'] = $commodity['price'] * 1;
                $sub_order['points'] = $commodity['points'];
                $sub_order['create_time'] = date('Y-m-d H:i:s');

                $_data = $this->jys_db_helper->add('order_commodity', $sub_order);
                if ($_data['success']){
                    if ($is_indiana){
                        $res = $this->change_indiana($id, $user_id, $data['insert_id']);
                    }else{
                        $res = $this->change_sweepstakes($id, $user_id, $data['insert_id']);
                    }

                    if ($res){
                        $result['success'] = TRUE;
                        $result['msg'] = '添加成功';
                        $this->db->trans_complete();
                    }else{
                        $this->db->trans_rollback();
                    }
                }else{
                    $result['success'] = FALSE;
                    $result['msg'] = '添加失败';
                    $this->db->trans_rollback();
                }
            }else{
                $result['success'] = FALSE;
                $result['msg'] = '添加失败';
            }
        }

        return $result;
    }

    /**
     * 检查奖品名额是否还存在
     *
     * @param int $user_id 用户ID
     * @param int $_id 积分抽奖或积分夺宝的ID
     * @param bool $is_indiana 是积分抽奖还是积分夺宝
     * @return mixed
     */
    public function exist_prize_places($user_id = 0, $id = 0, $is_indiana = 1){
        if (empty($user_id) || intval($user_id) < 1 || empty($id) || intval($id) < 1){
            return FALSE;
        }

        if ($is_indiana == 1){
            $row = $this->jys_db_helper->get_where('integral_indiana_result', ['user_id'=>$user_id, 'integral_indiana_id'=>$id, 'status'=>Jys_system_code::INTEGRAL_INDIANA_RESULT_STATUS_PASS]);
        }else{
            $row = $this->jys_db_helper->get_where('sweepstakes_result', ['user_id'=>$user_id, 'sweepstakes_commodity_id'=>$id, 'status'=>Jys_system_code::SWEEPSTAKES_RESULT_STATUS_NOT_RECEIVE]);
        }

        if ($row){
            return TRUE;
        }
        return FALSE;
    }

    /**
     * 领取奖品后改变积分抽奖数据
     *
     * @param $id 积分抽奖商品ID
     * @param int $user_id 用户ID
     * @return bool
     */
    public function change_sweepstakes($id = 0, $user_id = 0, $order_id = 0){
        if (empty($id) || intval($id) < 1 || empty($user_id) || intval($user_id) < 1 || empty($order_id) || intval($order_id) < 1){
            return FALSE;
        }

        $this->jys_db_helper->update_by_condition('sweepstakes_result', ['user_id'=>$user_id, 'id'=>$id, 'status'=>Jys_system_code::SWEEPSTAKES_RESULT_STATUS_NOT_RECEIVE], ['status'=>Jys_system_code::SWEEPSTAKES_RESULT_STATUS_RECEIVED, 'order_id'=>$order_id]);
        return TRUE;
    }

    /**
     * 领取奖品后改变积分夺宝数据
     *
     * @param int $id 积分夺宝ID
     * @param int $user_id 用户ID
     * @return bool
     */
    public function change_indiana($id = 0, $user_id = 0, $order_id = 0){
        if (empty($id) || intval($id) < 1 || empty($user_id) || intval($user_id) < 1 || empty($order_id) || intval($order_id) < 1){
            return FALSE;
        }

        $this->jys_db_helper->update_by_condition('integral_indiana_result', ['user_id'=>$user_id, 'id'=>$id, 'status'=>Jys_system_code::INTEGRAL_INDIANA_RESULT_STATUS_PASS], ['status'=>Jys_system_code::INTEGRAL_INDIANA_RESULT_STATUS_RECEIVED, 'order_id'=>$order_id]);

        return TRUE;
    }

    /**
     * 向外部系统发送订单信息的异步通知
     */
    public function notify_inform_order_info($order_id = 0, $order_number = "") {
        if (intval($order_id) > 0) {
            $condition['order.id'] = intval($order_id);
        }else if (!empty($order_number)) {
            $condition['order.number'] = $order_number;
        }else {
            return array('success'=>FALSE, 'msg'=>'请输入订单查询条件');
        }

        $auto_cancel_order = $this->auto_cancel_not_paid_order();
        $data = array(
            'success' => FALSE,
            'msg' => '查询条件不能为空',
            'data' => NULL
        );
        if (empty($condition) || count($condition) < 1){
            return $data;
        }

        $this->db->select('order.*,
                           user_agent.uid as openid,
                           express_company.name as express_company_name,
                           payment_type.name as payment_type_name,
                           terminal_type.name as terminal_type_name,
                           order_status.name as order_status_name');

        $this->db->join('user_agent', 'user_agent.user_id = order.user_id', 'left');
        $this->db->join('express_company', 'express_company.id = order.express_company_id', 'left');
        $this->db->join('system_code as payment_type', 'payment_type.value = order.payment_id and payment_type.type = "'.jys_system_code::PAYMENT.'"', 'left');
        $this->db->join('system_code as terminal_type', 'terminal_type.value = order.terminal_type and terminal_type.type = "'.jys_system_code::TERMINAL_TYPE.'"', 'left');
        $this->db->join('system_code as order_status', 'order_status.value = order.status_id and order_status.type = "'.jys_system_code::ORDER_STATUS.'"', 'left');
        $this->db->where($condition);
        $this->db->where('user_agent.uid IS NOT NULL');
        $result = $this->db->get('order');

        if ($result && $result->num_rows() > 0){
            $order = $result->row_array();
            $order['address'] = json_decode($order['address'], TRUE);
            $order['sub_orders'] = $this->push_sub_order($order['id']);
            if (floatval($order['payment_amount']) < 0.01) {
                if (floatval($order['discount_coupon_privilege']) >= 0.01) {
                    $order['payment_amount'] = floatval($order['total_price']) - floatval($order['discount_coupon_privilege']);
                }else {
                    $order['payment_amount'] = floatval($order['total_price']);
                }
            }

            $data = [
                'success' => TRUE,
                'msg' => '获取成功',
                'data' => $order
            ];
        }else{
            $data = [
                'success' => FALSE,
                'msg' => '没有数据',
                'data' => NULL
            ];
        }


        if ($data['success'] && !empty($data['data'])) {
            $url = "http://www.baidu.com/";
            $result = $this->jys_weixin->httpPostRequest($url, ['orderData'=>$data['data']]);
            return TRUE;
        }else {
            return FALSE;
        }
    }
}