<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Shopping_cart_model.php
 *
 *     Description: 购物车模型
 *
 *         Created: 2016-11-24 16:22:54
 *
 *          Author: sunzuosheng
 *
 * =====================================================================================
 */

class Shopping_cart_model extends CI_Model{
    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * 根据用户ID获取该用户的购物车信息
     *
     * @param array $condition 条件
     * @return mixed
     */
    public function all($user_id, $condition = []){
        if (!is_numeric($user_id) || intval($user_id) < 1) {
            $data['success'] = FALSE;
            $data['data'] = NULL;
            $data['msg'] = '用户ID不正确';
            return $data;
        }

        $this->db->select('shopping_cart.id,
                           shopping_cart.user_id,
                           shopping_cart.commodity_id,
                           attachment.path,
                           commodity.name,
                           commodity.price,
                           commodity.points,
                           commodity.is_point,
                           flash_sale.price as flash_sale_price,
                           shopping_cart.amount,
                           shopping_cart.create_time');
        $current_time = date('Y-m-d H:i:s');
        $this->db->join('flash_sale', "flash_sale.commodity_id = shopping_cart.commodity_id AND `flash_sale`.`start_time` <= '{$current_time}' AND `flash_sale`.`end_time` >= '{$current_time}'", 'left');
        $this->db->join('commodity', 'commodity.id = shopping_cart.commodity_id', 'left');
        $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_id = commodity.id', 'left');
        $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
        $this->db->where('shopping_cart.user_id', $user_id);
        $this->db->where('commodity.is_point !=', 1);

        if (!empty($condition)){
            $this->db->where($condition);
        }

        $this->db->order_by('shopping_cart.create_time', 'DESC');
        $this->db->group_by('shopping_cart.id');
        $result = $this->db->get('shopping_cart');

        if ($result && $result->num_rows() > 0){
            $data['success'] = TRUE;
            $data['data'] = $result->result_array();
            $data['msg'] = '获取成功';
        }else{
            $data['success'] = FALSE;
            $data['data'] = NULL;
            $data['msg'] = '获取失败';
        }

        return $data;
    }

    /**
     * 根据用户ID获取该用户购物车的商品数量
     *
     * @return bool
     */
    public function amount($user_id){
        $this->db->select('sum(shopping_cart.amount) as amount');
        $this->db->join('commodity', 'commodity.id = shopping_cart.commodity_id');
        $this->db->where('shopping_cart.user_id', $user_id);
        $this->db->where('commodity.is_point', 0);
        $this->db->where('commodity.status_id', Jys_system_code::COMMODITY_STATUS_PUTAWAY);
        $result = $this->db->get('shopping_cart');

        if ($result && $result->num_rows() > 0){
            return $result->row_array()['amount'];
        }

        return FALSE;
    }
}