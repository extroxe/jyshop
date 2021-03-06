<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Commodity_model.php
 *
 *     Description: 商品模型
 *
 *         Created: 2016-11-21 14:22:43
 *
 *          Author: sunzuosheng
 *
 * =====================================================================================
 */

class Commodity_model extends CI_Model{
    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * 添加商品
     *
     * @param array $commodity 商品信息
     * @param null $attachment_ids 缩略图IDS
     * @return mixed
     */
    public function add($commodity = [], $attachment_ids = NULL){
        $data['success'] = FALSE;
        $data['msg'] = '添加失败';

        if (empty($commodity) || empty($attachment_ids)){
            $data['msg'] = '参数错误';
            return $data;
        }

        $this->db->trans_begin();
        $data = $this->jys_db_helper->add('commodity', $commodity, TRUE);

        if ($data['success']){
            $commodity_id = $data['insert_id'];
            $thumbnail_fail_flag = false;
            $thumbnail_arr = [];

            if (is_array($attachment_ids)){
                foreach ($attachment_ids as $attachment_id){
                    $thumbnail_arr[] = [
                        'attachment_id' => $attachment_id,
                        'commodity_id' => $commodity_id,
                        'create_time' => date('Y-m-d H:i:s')
                    ];
                }

                $_data = $this->jys_db_helper->add_batch('commodity_thumbnail', $thumbnail_arr);
            }else{
                $thumbnail_arr = [
                    'attachment_id' => $attachment_ids,
                    'commodity_id' => $commodity_id,
                    'create_time' => date('Y-m-d H:i:s')
                ];
                $_data = $this->jys_db_helper->add('commodity_thumbnail', $thumbnail_arr);
            }

            if (!$_data['success']){
                $thumbnail_fail_flag = true;
            }

            if ($thumbnail_fail_flag){
                $data['success'] = FALSE;
                $data['msg'] = '添加失败，缩略图错误';
                $this->db->trans_rollback();
            }else{
                $this->db->trans_commit();
            }
        }

        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
        }
        else{
            $this->db->trans_commit();
        }

        return $data;
    }

    /**
     * 更新商品信息
     *
     * @param int $id 商品ID
     * @param array $commodity 商品信息
     * @param null $attachment_ids 商品缩略图IDS
     * @return mixed
     */
    public function update($id = 0, $commodity = [], $attachment_ids = NULL){
        $data['success'] = FALSE;
        $data['msg'] = '更新失败';

        if (empty($id) || empty($commodity) || intval($id) < 1){
            $data['msg'] = '参数错误';
            return $data;
        }
        $this->db->trans_begin();
        if ($this->jys_db_helper->update('commodity', $id, $commodity)){
            $data['success'] = TRUE;
            $data['msg'] = '更新成功';
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '更新失败';
        }

        if ($data['success']){
            $thumbnail_fail_flag = false;
            $thumbnail_arr = [];

            if (!empty($attachment_ids) && is_array($attachment_ids)){
                foreach ($attachment_ids as $attachment_id){
                    $thumbnail_arr[] = [
                        'attachment_id' => $attachment_id,
                        'commodity_id' => $id,
                        'create_time' => date('Y-m-d H:i:s')
                    ];
                }

                $_data = $this->jys_db_helper->add_batch('commodity_thumbnail', $thumbnail_arr);
            }else if (!empty($attachment_ids) && is_string($attachment_ids)){
                $thumbnail_arr = [
                    'attachment_id' => $attachment_ids,
                    'commodity_id' => $id,
                    'create_time' => date('Y-m-d H:i:s')
                ];
                $_data = $this->jys_db_helper->add('commodity_thumbnail', $thumbnail_arr);
            }else{
                $_data['success'] = TRUE;
            }

            if (!$_data['success']){
                $thumbnail_fail_flag = true;
            }

            if ($thumbnail_fail_flag){
                $data['success'] = FALSE;
                $data['msg'] = '添加失败，缩略图错误';
                $this->db->trans_rollback();
            }else{
                $this->db->trans_commit();
            }
        }

        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
        }
        else{
            $this->db->trans_commit();
        }

        return $data;
    }

    /**
     * 根据条件，获取商品列表
     * @param array $condition 条件数组
     */
    public function get_commodity_list_by_condition($condition = array(), $multiple = TRUE) {
        if (empty($condition) || !is_array($condition) || count($condition) < 1) {
            return FALSE;
        }

        $this->db->select('commodity.id,
                           commodity.name,
                           commodity.number,
                           commodity.price,
                           commodity.points,
                           commodity.introduce,
                           commodity.detail,
                           commodity.sales_volume,
                           commodity.create_time,
                           commodity.update_time,
                           commodity.category_id,
                           flash_sale.price as flash_sale_price,
                           flash_sale.start_time as flash_sale_start_time,
                           flash_sale.end_time as flash_sale_end_time,
                           category.name as category_name,
                           r_commodity.id as recommend_id,
                           r_commodity.name as recommend_name,
                           recommend_commodity.start_time,
                           recommend_commodity.end_time,
                           commodity.status_id,
                           commodity_status.name as status,
                           commodity.type_id,
                           commodity_type.name as type,
                           commodity.is_point,
                           attachment.path');

        $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_id = commodity.id', 'left');
        $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
        $this->db->join('category', 'category.id = commodity.category_id', 'left');
        $this->db->join('recommend_commodity', 'recommend_commodity.id = commodity.recommend_commodity', 'left');
        $this->db->join('flash_sale', 'flash_sale.commodity_id = commodity.id', 'left');
        $this->db->join('commodity as r_commodity', 'r_commodity.id = recommend_commodity.commodity_id', 'left');
        $this->db->join('system_code as commodity_status', 'commodity_status.value = commodity.status_id and commodity_status.type = "'.jys_system_code::COMMODITY_STATUS.'"', 'left');
        $this->db->join('system_code as commodity_type', 'commodity_type.value = commodity.type_id and commodity_type.type = "'.jys_system_code::COMMODITY_TYPE.'"', 'left');
        if (!empty($condition) && is_array($condition)) {
            $this->db->where($condition);
        }
        $this->db->group_by('commodity.id');
        $result = $this->db->get('commodity');

        if ($result && $result->num_rows() > 0){
            return $multiple ? $this->commodity_html_decode($result->result_array()) : $this->commodity_html_decode($result->row_array());
        }else {
            return FALSE;
        }
    }

    /**
     * 分页
     *
     * @param int $page 页数
     * @param int $page_size 页大小
     * @param array $condition 条件
     * @param string $keyword 查询关键字
     */
    public function paginate($page = 1, $page_size = 10, $condition = NULL, $keyword = ''){
        $data = [
            'success' => FALSE,
            'msg' => '没有商品数据',
            'data' => null,
            'total_page' => 0
        ];

        if (empty($page) || intval($page) < 1 || empty($page_size) || intval($page_size) < 1) {
            $data['msg'] = '参数错误';
            return $data;
        }

        $this->db->select('commodity.id,
                           commodity.name,
                           commodity.number,
                           commodity.price,
                           commodity.points,
                           commodity.introduce,
                           commodity.detail,
                           commodity.sales_volume,
                           commodity.create_time,
                           commodity.update_time,
                           commodity.category_id,
                           commodity.level_id,
                           flash_sale.price as flash_sale_price,
                           flash_sale.start_time as flash_sale_start_time,
                           flash_sale.end_time as flash_sale_end_time,
                           category.name as category_name,
                           r_commodity.id as recommend_id,
                           r_commodity.name as recommend_name,
                           recommend_commodity.start_time,
                           recommend_commodity.end_time,
                           commodity.status_id,
                           commodity_status.name as status,
                           commodity.type_id,
                           commodity_type.name as type,
                           commodity.is_point,
                           attachment.path');

        $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_id = commodity.id', 'left');
        $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
        $this->db->join('category', 'category.id = commodity.category_id', 'left');
        $this->db->join('recommend_commodity', 'recommend_commodity.commodity_id = commodity.id', 'left');
        $this->db->join('commodity as r_commodity', 'r_commodity.id = recommend_commodity.commodity_id', 'left');
        $this->db->join('flash_sale', 'flash_sale.commodity_id = commodity.id', 'left');
        $this->db->join('system_code as commodity_status', 'commodity_status.value = commodity.status_id and commodity_status.type = "'.jys_system_code::COMMODITY_STATUS.'"', 'left');
        $this->db->join('system_code as commodity_type', 'commodity_type.value = commodity.type_id and commodity_type.type = "'.jys_system_code::COMMODITY_TYPE.'"', 'left');
        $this->db->where('commodity.status_id !=', jys_system_code::COMMODITY_STATUS_DELETE);
        if (!empty($condition)){
            $this->db->where($condition);
        }

        if (!empty($keyword)) {
            // 关键字模糊查找
            $this->db->group_start();
            $this->db->like('commodity.name', $keyword);
            $this->db->or_like('commodity.introduce', $keyword);
            $this->db->or_like('category.name', $keyword);
            $this->db->group_end();
        }

        $this->db->group_by('commodity.id');
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $result = $this->db->get('commodity');

        if ($result && $result->num_rows() > 0){
            $data = [
                'success' => TRUE,
                'msg' => '',
                'data' => $this->commodity_html_decode($result->result_array())
            ];

            $this->db->select('commodity.id');
            $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_id = commodity.id', 'left');
            $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
            $this->db->join('category', 'category.id = commodity.category_id', 'left');
            $this->db->join('recommend_commodity', 'recommend_commodity.commodity_id = commodity.id', 'left');
            $this->db->join('commodity as r_commodity', 'r_commodity.id = recommend_commodity.commodity_id', 'left');
            $this->db->join('flash_sale', 'flash_sale.commodity_id = commodity.id', 'left');
            $this->db->join('system_code as commodity_status', 'commodity_status.value = commodity.status_id and commodity_status.type = "'.jys_system_code::COMMODITY_STATUS.'"', 'left');
            $this->db->join('system_code as commodity_type', 'commodity_type.value = commodity.type_id and commodity_type.type = "'.jys_system_code::COMMODITY_TYPE.'"', 'left');

            if (!empty($condition)){
                $this->db->where($condition);
            }
            $this->db->where('commodity.status_id !=', jys_system_code::COMMODITY_STATUS_DELETE);

            if (!empty($keyword)) {
                // 关键字模糊查找
                $this->db->group_start();
                $this->db->like('commodity.name', $keyword);
                $this->db->or_like('commodity.introduce', $keyword);
                $this->db->or_like('category.name', $keyword);
                $this->db->group_end();
            }

            $this->db->group_by('commodity.id');
            $res = $this->db->get('commodity');

            if ($res && $res->num_rows() > 0){
                $data['total_page'] = ceil($res->num_rows() / $page_size * 1.0);
            }else{
                $data['total_page'] = 1;
            }
        }

        return $data;
    }

    /**
     * 获取总页数
     *
     * @param int $page_size 页大小
     * @param null $condition 条件
     * @return array|int
     */
    public function get_total_page($page_size = 10, $condition = NULL){
        if (empty($page_size)){
            return FALSE;
        }
        
        $total_page = 0;

        $this->db->select('commodity.id');
        $this->db->join('category', 'category.id = commodity.category_id', 'left');
        $this->db->join('flash_sale', 'flash_sale.commodity_id = commodity.id', 'left');
        $this->db->join('recommend_commodity', 'recommend_commodity.commodity_id = commodity.id', 'left');
        $this->db->where('commodity.status_id !=', jys_system_code::COMMODITY_STATUS_DELETE);
        if (!empty($condition)){
            $this->db->where($condition);
        }



        $result = $this->db->get('commodity');

        if ($result && $result->num_rows() > 0){
            $total_page = ceil($result->num_rows()/$page_size);
        }

        return $total_page;
    }

    /**
     * 根据商品ID获取商品剩余数据(缩略图)
     *
     * @param int $commodity_id
     * @return array
     */
    public function show_thumbnail($commodity_id = 0){
        $data = [
            'success' => FALSE,
            'msg' => '没有商品数据',
            'data' => null
        ];

        if (empty($commodity_id) || intval($commodity_id) < 1) {
            $data['msg'] = '参数错误';
            return $data;
        }

        $this->db->select('commodity_thumbnail.id,
                           attachment.id as attachment_id,
                           attachment.path,
                           user.id as user_id,
                           user.name as user_name');

        $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
        $this->db->join('attachment_user', 'attachment_user.attachment_id = attachment.id', 'left');
        $this->db->join('user', 'user.id = attachment_user.user_id', 'left');
        $this->db->where('commodity_thumbnail.commodity_id', $commodity_id);
        $this->db->group_by('commodity_thumbnail.id');
        $result = $this->db->get('commodity_thumbnail');

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
     * 根据商品ID获取商品评价分页
     *
     * @param int $page 页数
     * @param int $page_size 页大小
     * @param int $commodity_id 商品ID
     * @param int $evaluation_level 评价等级(0=>无,1=>好评,2=>中评,3=>差评)
     * @return array 商品评价分页数据
     */
    public function evaluation_paginate($page = 1, $page_size = 10, $commodity_id = 0, $evaluation_level = 0){
        $data = [
            'success' => FALSE,
            'msg' => '没有商品评价数据',
            'data' => null
        ];

        if (empty($page) || intval($page) < 1 || empty($page_size) || intval($page_size) < 1 || empty($commodity_id) || intval($commodity_id) < 1) {
            $data['msg'] = '参数错误';
            return $data;
        }

        $this->db->select('commodity_evaluation.id,
                           commodity_evaluation.score,
                           commodity_evaluation.content,
                           commodity_evaluation.create_time,
                           user.name as user_name,
                           user.username as user_username,
                           user.nickname as user_nickname,
                           user.phone as user_phone,
                           user.gender as user_gender,
                           attachment.path as user_avatar_path,
                           order_commodity.id as order_commodity_id,
                           order_commodity.number as order_commodity_number,
                           level.id as level_id,
                           level.name as level_name');

        $this->db->join('user', 'user.id = commodity_evaluation.user_id', 'left');
        $this->db->join('attachment', 'attachment.id = user.avatar', 'left');
        $this->db->join('order_commodity', 'order_commodity.id = commodity_evaluation.order_commodity_id', 'left');
        $this->db->join('level', 'level.id = user.level', 'left');
        $this->db->where('commodity_evaluation.commodity_id', $commodity_id);

        if ($evaluation_level == 1){
            $this->db->where('commodity_evaluation.score >=', 4);
        }else if ($evaluation_level == 2){
            $this->db->where('commodity_evaluation.score <', 4);
            $this->db->where('commodity_evaluation.score >=', 2);
        }else if ($evaluation_level == 3){
            $this->db->where('commodity_evaluation.score <=', 1);
        }

        $this->db->order_by('commodity_evaluation.create_time', 'DESC');
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $result = $this->db->get('commodity_evaluation');

        if ($result && $result->num_rows() > 0){
            $evaluation_arr = $result->result_array();

            $evaluation_arr = $this->get_evaluation_pic($evaluation_arr);

            $data = [
                'success' => TRUE,
                'msg' => '',
                'data' => $evaluation_arr
            ];
        }

        return $data;
    }

    /**
     * 统计评价nav
     *
     * @param array $all_evaluation 所有评价
     * @return array 统计结果
     */
    public function evaluation_nav($all_evaluation = []){
        if (empty($all_evaluation)) {
            return [];
        }
        $data['all_eva'] = count($all_evaluation);
        $data['good_eva'] = 0;
        $data['mid_eva'] = 0;
        $data['bad_eva'] = 0;
        foreach ($all_evaluation as $evaluation){
            if ($evaluation['score'] >= 4){
                $data['good_eva']++;
            }else if ($evaluation['score'] >= 2){
                $data['mid_eva']++;
            }else{
                $data['bad_eva']++;
            }
        }

        return $data;
    }

    /**
     * 推荐商品分页
     *
     * @param int $page 页数
     * @param int $page_size 页大小
     * @return array 推荐商品分页数据
     */
    public function recommend_paginate($page = 1, $page_size = 10, $is_point = 0){
        $now_date = date('Y-m-d H:i:s');
        $data = [
            'success' => FALSE,
            'msg' => '没有推荐商品数据',
            'data' => array(),
            'total_page' => 0
        ];
        if (empty($page) || intval($page) < 1 || empty($page_size) || intval($page_size) < 1) {
            $data['msg'] = '参数错误';
            return $data;
        }

        $this->db->select('recommend_commodity.id,
                           recommend_commodity.commodity_id,
                           recommend_commodity.type_id,
                           recommend_commodity.start_time,
                           recommend_commodity.end_time,
                           recommend_commodity.create_time,
                           commodity.name,
                           commodity.price,
                           commodity.sales_volume,
                           commodity_thumbnail.attachment_id,
                           attachment.path,
                           system_code.name as type');

        $this->db->join('commodity', 'commodity.id = recommend_commodity.commodity_id', 'left');
        $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_id = recommend_commodity.commodity_id', 'left');
        $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
        $this->db->join('system_code', 'system_code.value = recommend_commodity.type_id and system_code.type = "'.jys_system_code::COMMODITY_TYPE.'"', 'left');
        /*$this->db->where('recommend_commodity.start_time <=', $now_date);
        $this->db->where('recommend_commodity.end_time >=', $now_date);*/
        $this->db->where('commodity.is_point', $is_point);
        $this->db->where('commodity.status_id !=', '0');
        $this->db->order_by('commodity.sales_volume', 'DESC');
        $this->db->order_by('attachment.create_time', 'DESC');
        $this->db->group_by('recommend_commodity.id');
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $result = $this->db->get('recommend_commodity');

        if ($result && $result->num_rows() > 0){
            $data = [
                'success' => TRUE,
                'msg' => '',
                'data' => $result->result_array()
            ];
        }
        $this->db->select('recommend_commodity.id,recommend_commodity.commodity_id,commodity.name');
        $this->db->join('commodity', 'commodity.id = recommend_commodity.commodity_id', 'left');
        $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_id = recommend_commodity.commodity_id', 'left');
        $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
        $this->db->join('system_code', 'system_code.value = recommend_commodity.type_id and system_code.type = "'.jys_system_code::COMMODITY_TYPE.'"', 'left');
        /*$this->db->where('recommend_commodity.start_time <=', $now_date);
        $this->db->where('recommend_commodity.end_time >=', $now_date);*/
        $this->db->where('commodity.is_point', $is_point);
        $this->db->where('commodity.status_id !=', '0');
        $this->db->group_by('recommend_commodity.id');
        $result = $this->db->get('recommend_commodity');
        if ($result && $result->num_rows() > 0){
            $total_num = $result->num_rows();
            $data['total_page'] = ceil($total_num / $page_size * 1.0);
        }

        return $data;
    }

    /**
     * 添加热换或热卖商品
     * @param int $commodity_id 商品ID
     * @param string $start_time 开始时间
     * @param string $end_time 结束时间
     * @param int $type_id 推荐类型
     */
    public function add_recommend($commodity_id = 0, $start_time = "", $end_time = "", $type_id = 0) {
        $result = array('success'=>FALSE, 'msg'=>'添加失败');
        if (intval($commodity_id) < 0 || empty($start_time) || empty($end_time) || intval($type_id) < 1) {
            $result['msg'] = '添加失败，参数错误';
            return $result;
        }

        $this->db->trans_start();
        $commodity = $this->jys_db_helper->get('commodity', $commodity_id);
        if (!empty($commodity) && isset($commodity['status_id']) && intval($commodity['status_id']) > 0) {
            $recommend = $this->jys_db_helper->get_where('recommend_commodity', ['end_time >'=>$start_time, 'type_id'=>$type_id, 'commodity_id'=>$commodity_id]);
            if (!empty($recommend)) {
                $result['msg'] = '当前商品在当前时间已有推荐，请不要重复添加';
            }else {
                $insert['commodity_id'] = $commodity_id;
                $insert['start_time'] = $start_time;
                $insert['end_time'] = $end_time;
                $insert['type_id'] = $type_id;
                $insert['create_time']    = date('Y-m-d H:i:s');
                $result = $this->jys_db_helper->add('recommend_commodity', $insert);
            }
        }else {
            $result['msg'] = '该商品已被删除，无法添加';
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $result['success'] = FALSE;
            $result['msg'] = '添加失败，事务执行失败';
        }

        return $result;
    }

    /**
     * 管理员端获取获取推荐商品
     *
     * @return array
     */
    public function get_recommend($nums = NULL){
        $date_now = date('Y-m-d H:i:s');
        $data = [
            'success' => FALSE,
            'msg' => '没有推荐商品数据',
            'data' => null
        ];

        $this->db->select('recommend_commodity.id,
                           recommend_commodity.start_time,
                           recommend_commodity.end_time,
                           recommend_commodity.create_time,
                           recommend_commodity.commodity_id,
                           commodity.name,
                           commodity.price,
                           commodity.sales_volume,
                           commodity_thumbnail.attachment_id,
                           attachment.path,
                           recommend_commodity.type_id,
                           system_code.name as type');

        $this->db->join('commodity', 'commodity.id = recommend_commodity.commodity_id', 'left');
        $this->db->join('system_code', 'system_code.value = recommend_commodity.type_id and system_code.type = "'.jys_system_code::RECOMMEND_COMMODITY_STATUS_HOT_SALE.'"', 'left');
        $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_id = recommend_commodity.commodity_id', 'left');
        $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
        $this->db->where('recommend_commodity.start_time <=', $date_now);
        $this->db->where('recommend_commodity.end_time >=', $date_now);
        $this->db->where('commodity.status_id !=', '0');
        $this->db->where('commodity.is_point =', '0');
        $this->db->group_by('commodity.id');
        $this->db->order_by('commodity.sales_volume', 'DESC');
        $this->db->order_by('attachment.create_time', 'ASC');
        if(!empty($nums)){
            $this->db->limit($nums, 0);
        }
        $result = $this->db->get('recommend_commodity');

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
     * 获取热换商品
     *
     * @param int $new_num 热换条数
     * @param int $type_id 推荐类型
     * @param  array $user_info 用户信息
     * @return array
     */
    public function get_home_recommend($new_num = NULL, $type_id = jys_system_code::RECOMMEND_COMMODITY_STATUS_HOT_EXCHANGE, $user_info = []){
        $date_now = date('Y-m-d H:i:s');

        $this->db->select('recommend_commodity.id,
                           recommend_commodity.commodity_id,
                           commodity.name,
                           commodity.price,
                           attachment.path,
                           flash_sale.price as flash_sale_price,
                           flash_sale.start_time as flash_sale_start_time,
                           flash_sale.end_time as flash_sale_end_time,
                           system_code.name as type');

        $this->db->join('commodity', 'commodity.id = recommend_commodity.commodity_id', 'left');
        $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_id = commodity.id', 'left');
        $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
        $this->db->join('flash_sale', 'flash_sale.commodity_id = commodity.id and "'.$date_now.'" >= flash_sale.start_time and "'.$date_now.'" <= flash_sale.end_time', 'left');
        $this->db->join('system_code', 'system_code.value = commodity.type_id and system_code.type = "'.jys_system_code::COMMODITY_TYPE.'"', 'left');
        $this->db->where('recommend_commodity.start_time <=', $date_now);
        $this->db->where('recommend_commodity.end_time >=', $date_now);
        $this->db->where('recommend_commodity.type_id', $type_id);
        $this->db->order_by('recommend_commodity.create_time', 'DESC');
        $this->db->group_by('recommend_commodity.id');

        if(!empty($new_num)){
            $this->db->limit($new_num, 0);
        }

        $result = $this->db->get('recommend_commodity');

        if ($result && $result->num_rows() > 0){
            if ($type_id == Jys_system_code::RECOMMEND_COMMODITY_STATUS_HOT_SALE && isset($user_info['price_discount']) && floatval($user_info['price_discount']) > 0) {
                return $this->calculate_discount_price($result->result_array(), $user_info['price_discount']);
            }else {
                return $result->result_array();
            }
        }else{
            return [];
        }
    }

    /**
     * 获取限时出售商品
     *
     * @return array
     */
    public function get_flash_sale($limit = NULL)
    {
        $date_now = date('Y-m-d, H:i:s');
        $data = [
            'success' => FALSE,
            'msg' => '没有限时折扣商品',
            'data' => NULL
        ];
        $this->db->select('flash_sale.id, 
                           flash_sale.commodity_id,
                           commodity.price,
                           flash_sale.price as flash_sale_price, 
                           flash_sale.start_time, 
                           flash_sale.end_time, 
                           flash_sale.create_time,
                           commodity.name,
                           attachment.path,
                           system_code.name as type');
        $this->db->join('commodity', 'flash_sale.commodity_id = commodity.id', 'left');
        $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_id = commodity.id', 'left');
        $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
        $this->db->join('system_code', 'system_code.value = commodity.type_id and system_code.type = "'.jys_system_code::COMMODITY_TYPE.'"', 'left');
        $this->db->where('flash_sale.start_time <=', $date_now);
        $this->db->where('flash_sale.end_time >=', $date_now);
        $this->db->order_by('flash_sale.create_time', 'DESC');
        $this->db->group_by('flash_sale.id');

        if(!empty($limit)){
            $this->db->limit($limit, 0);
        }

        $result = $this->db->get('flash_sale');

        if ($result && $result->num_rows() > 0)
        {
            $data = [
                'success' => TRUE,
                'msg' => '查找到限时折扣商品',
                'data' => $result->result_array()
            ];
        }
        return $data;
    }
    /**
     * 限时折扣商品分页
     *
     * @param int $page 页数
     * @param int $page_size 页大小
     * @return array 限时折扣商品分页数据
     */
    public function flash_sale_paginate($page = 1, $page_size = 10){
        $data = [
            'success' => FALSE,
            'msg' => '没有限时折扣商品数据',
            'data' => null
        ];

        if (empty($page) || intval($page) < 1 || empty($page_size) || intval($page_size) < 1) {
            $data['msg'] = '参数错误';
            return $data;
        }

        $this->db->select('flash_sale.id,
                           flash_sale.price,
                           flash_sale.start_time,
                           flash_sale.end_time,
                           flash_sale.create_time,
                           flash_sale.commodity_id,
                           commodity.name');

        $this->db->join('commodity', 'commodity.id = flash_sale.commodity_id', 'left');
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $result = $this->db->get('flash_sale');

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
     * 评价数据中添加评价图片
     *
     * @param array $evaluation_arr
     * @return array
     */
    public function get_evaluation_pic($evaluation_arr = []){
        if (!is_array($evaluation_arr) || empty($evaluation_arr)){
            return [];
        }

        foreach ($evaluation_arr as $key => $evaluation){
            $evaluation_arr[$key]['evaluation_pic'] = $this->get_evaluation_pic_path_by_evaluation_id($evaluation['id']);
        }

        return $evaluation_arr;
    }

    /**
     * 根据评价ID获取评价图片
     *
     * @param int $evaluation_id
     * @return null
     */
    public function get_evaluation_pic_path_by_evaluation_id($evaluation_id = 0){
        if (intval($evaluation_id) < 1 || empty($evaluation_id)){
            return null;
        }

        $this->db->select('commodity_evaluation_pic.id,
                           attachment.path');
        $this->db->join('attachment', 'attachment.id = commodity_evaluation_pic.attachment_id', 'left');
        $this->db->where('commodity_evaluation_pic.commodity_evaluation_id', $evaluation_id);
        $result = $this->db->get('commodity_evaluation_pic');

        if ($result && $result->num_rows() > 0){
            return $result->result_array();
        }else{
            return null;
        }
    }

    /**
     * 根据条件获取商品详情
     *
     * @param array $condition 条件（数组）
     * @param bool $multiple 返回多条数据
     * @param bool $thumb 返回缩略图
     * @param bool $limit 数据数量
     * @param array $user_info 用户信息（数组）
     * @return array 商品详情
     */
    public function get_commodity_by_condition($condition = [], $multiple = FALSE, $thumb = FALSE, $limit = FALSE, $user_info = []){
        $data = [
            'success' => FALSE,
            'msg' => '没有商品数据',
            'data' => null
        ];

        $date = date('Y-m-d H:i:s');

        $this->db->select('commodity.id,
                           commodity.name,
                           commodity.number,
                           commodity.price,
                           commodity.points,
                           commodity.introduce,
                           commodity.detail,
                           commodity.sales_volume,
                           commodity.create_time,
                           commodity.update_time,
                           commodity.is_point,
                           commodity.category_id,
                           commodity.level_id,
                           category.name as category_name,
                           r_commodity.id as recommend_id,
                           r_commodity.name as recommend_name,
                           flash_sale.price as flash_sale_price,
                           flash_sale.start_time as flash_sale_start_time,
                           flash_sale.end_time as flash_sale_end_time,
                           recommend_commodity.start_time,
                           recommend_commodity.end_time,
                           commodity.status_id,
                           commodity_status.name as status,
                           commodity.type_id,
                           commodity_type.name as type'.($thumb ? ', attachment.id as attachment_id, attachment.path' : NULL));

        $this->db->join('category', 'category.id = commodity.category_id', 'left');
        $this->db->join('recommend_commodity', 'recommend_commodity.id = commodity.recommend_commodity', 'left');
        $this->db->join('commodity as r_commodity', 'r_commodity.id = recommend_commodity.commodity_id', 'left');
        $this->db->join('flash_sale', 'flash_sale.commodity_id = commodity.id and "'.$date.'" >= flash_sale.start_time and "'.$date.'" <= flash_sale.end_time', 'left');
        $this->db->join('system_code as commodity_status', 'commodity_status.value = commodity.status_id and commodity_status.type = "'.jys_system_code::COMMODITY_STATUS.'"', 'left');
        $this->db->join('system_code as commodity_type', 'commodity_type.value = commodity.type_id and commodity_type.type = "'.jys_system_code::COMMODITY_TYPE.'"', 'left');

        if ($thumb){
            $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_id = commodity.id', 'left');
            $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
        }

        $this->db->where($condition);
        $this->db->where('commodity.status_id !=', Jys_system_code::COMMODITY_STATUS_DELETE);
        $this->db->where('commodity.status_id !=', Jys_system_code::COMMODITY_STATUS_SOLDOUT);
        $this->db->order_by('commodity.create_time', 'DESC');

        if ($thumb){
            $this->db->group_by('commodity.id');
        }

        if ($limit){
            $this->db->limit($limit, 0);
        }
        $result = $this->db->get('commodity');

        if ($result && $result->num_rows() > 0){
            $response_data = array();
            if (isset($user_info['price_discount']) && floatval($user_info['price_discount']) > 0) {
                if ($multiple) {
                    $result_data = $result->result_array();

                }else {
                    $result_data = $result->row_array();
                }
                $result_data = $this->calculate_discount_price($result_data, $user_info['price_discount']);
                $response_data = $this->commodity_html_decode($result_data);
            }else {
                $response_data = $this->commodity_html_decode($multiple ? $result->result_array() : $result->row_array());
            }

            $data = [
                'success' => TRUE,
                'msg' => '',
                'data' => $response_data
            ];
        }

        return $data;
    }

    /**
     * 根据商品Id获取评价数量
     * @param $id
     * @return mixed
     */
    public function get_evaluation_number_by_commodity_id($id){
        $this->db->select('count(*) as num');
        $this->db->where('commodity_id',$id);
        $result = $this->db->get('commodity_evaluation');

        if ($result && $result->num_rows() > 0){
            return $result->row_array();
        }
    }

    /**
     * 更具商品ID获取商品图片
     *
     * @param int $id 商品ID
     * @param bool $multiple 复数选择
     * @return mixed
     */
    public function get_pic_by_commodity_id($id, $multiple = FALSE){
        $data = [
            'success' => FALSE,
            'msg' => '没有该商品的图片',
            'data' => null
        ];
        $this->db->select('attachment.path as pic_path');
        $this->db->from('commodity_thumbnail');
        $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
        $this->db->where('commodity_thumbnail.commodity_id', $id);
        $result = $this->db->get();
        if($result && $result -> num_rows()>0){
            $data = [
                'success' => TRUE,
                'msg' => '成功获取该商品的图片',
                'data' => $multiple ? $result->result_array() :$result->row_array()
            ];
        }
        return $data;
    }
    
    /**
     * 商品类容html解码
     *
     * @param array $commodities 商品数据（解码前）
     * @return array 商品数据（解码后）
     */
    public function commodity_html_decode($commodities = []){
        if (empty($commodities) || !is_array($commodities)){
            return [];
        }

        foreach ($commodities as $key => $commodity){
            if (is_array($commodity)){
                $commodities[$key]['introduce'] = htmlspecialchars_decode($commodity['introduce']);
                $commodities[$key]['detail'] = htmlspecialchars_decode($commodity['detail']);
            }else{
                $commodities['introduce'] = htmlspecialchars_decode($commodities['introduce']);
                $commodities['detail'] = htmlspecialchars_decode($commodities['detail']);
                return $commodities;
            }
        }

        return $commodities;
    }

    /**
     * 过滤用户评论
     *
     * @param array $evaluations 评论
     * @return array|bool
     */
    public function filter_evaluation($evaluations = []){
        if (empty($evaluations)){
            return FALSE;
        }

        foreach ($evaluations as $key => $evaluation){
            $evaluations[$key]['user_username'] = NULL;
            $evaluations[$key]['user_name'] = NULL;
            $evaluations[$key]['user_nickname'] = mb_substr($evaluation['user_nickname'], 0, 1).'***'.mb_substr($evaluation['user_nickname'], -1);
            $evaluations[$key]['user_phone'] = substr($evaluation['user_phone'], 0, 3).'*****'.substr($evaluation['user_phone'], -3);
            $evaluations[$key]['order_commodity_number'] = NULL;
        }

        return $evaluations;
    }

    /**
     * 获取好评数目
     *
     * @param int $commodity_id 商品ID
     * @return bool|int
     */
    public function get_praise_num($commodity_id = 0){
        if (empty($commodity_id) || intval($commodity_id) < 1){
            return FALSE;
        }

        $this->db->where('commodity_id', $commodity_id);
        $this->db->where('score >=', 4);
        $result = $this->db->get('commodity_evaluation');

        if ($result && $result->num_rows() > 0){
            return count($result->result_array());
        }else{
            return 0;
        }
    }

    /**
     * 搜索条件（处理后）
     *
     * @param array $search 搜索条件
     * @return bool
     */
    public function search($search = []){
        if (empty($search)){
            return FALSE;
        }
        $search_str = '';
        $date_now = date('Y-m-d, H:i:s');
        if (isset($search['flash_sale']) && $search['flash_sale'] == true){
            $search_str .= 'flash_sale.start_time <= "'.$date_now.'" and flash_sale.end_time >= "'.$date_now.'"';
        }else if (isset($search['hot_sale']) && $search['hot_sale'] == true){
            $search_str .= 'recommend_commodity.start_time <= "'.$date_now.'" and recommend_commodity.end_time >= "'.$date_now.'" and recommend_commodity.type_id = '.jys_system_code::RECOMMEND_COMMODITY_STATUS_HOT_SALE;
        }else if (isset($search['hot_exchange']) && $search['hot_exchange'] == true){
            $search_str .= 'recommend_commodity.start_time <= "'.$date_now.'" and recommend_commodity.end_time >= "'.$date_now.'" and recommend_commodity.type_id = '.jys_system_code::RECOMMEND_COMMODITY_STATUS_HOT_EXCHANGE;
        }

        if (isset($search['key_words']) && !empty($search['key_words'])){
            $search['result'] = $search['key_words'];
            foreach (explode(' ', $search['key_words']) as $key => $row){
                if (empty($row)){
                    header('Location:'.$_SERVER['HTTP_REFERER']);
                    return;
                }

                if ($key == 0){
                    $search_str .= '(commodity.name like "%'.$row.'%" or category.name like "%'.$row.'%"';
                }else{
                    $search_str .= ' or commodity.name like "%'.$row.'%" or category.name like "%'.$row.'%"';
                }

                if ($key == count(explode(' ', $search['key_words']))- 1){
                    $search_str .= ')';
                }
            }
        }

        if (isset($search['category']) && !empty($search['category'])){
            if (strlen($search_str) > 0){
                $search_str .= ' and ';
            }
            $search_str .= '(category.id = '.$search['category'].' or category.parent_id = '.$search['category'].')';
        }

        if (isset($search['type']) && !empty($search['type'])){
            if (strlen($search_str) > 0){
                $search_str .= ' and ';
            }
            $search_str .= 'commodity.type_id = '.$search['type'];
        }

        if (isset($search['price']) && !empty($search['price'])){
            $price = explode('-', $search['price']);
            if ($price[0] == 'min_p'){
                $search_str = '('.$search_str.') and commodity.price >= '.$price[1];
            }else if ($price[0] == 'max_p'){
                $search_str = '('.$search_str.') and commodity.price <= '.$price[1];
            }else{
                $search_str = '('.$search_str.') and commodity.price >= '.$price[0].' and commodity.price <= '.$price[1];
            }
        }
        
        return $search_str;
    }

    /**
     * 检验用户积分是否足够兑换该商品
     *
     * @param int $user_id 用户ID
     * @param int $commodity_id 商品ID
     * @param int $amount 数量
     * @return bool
     */
    public function check_point_enough($user_id, $commodity_id, $amount){
        if (empty($user_id) || empty($commodity_id) || empty($amount) || intval($user_id) < 1 || intval($commodity_id) < 1 || intval($amount) < 1){
            return FALSE;
        }

        $user = $this->jys_db_helper->get('user', $user_id);
        $commodity = $this->jys_db_helper->get_where('commodity', ['id'=>$commodity_id, 'is_point'=>1]);

        if ($commodity && $user['current_point'] >= ($amount * $commodity['price'])){
            return TRUE;
        }

        return FALSE;

    }

    /**
     * 根据商品ID获取商品评价信息
     *
     * @param null $commodities 商品数据
     * @return array|null
     */
    public function get_commodity_evaluation_info($commodities = NULL){
        if (empty($commodities) || !is_array($commodities)){
            return NULL;
        }

        foreach ($commodities as $key => $commodity){
            $total_num = $this->jys_db_helper->get_total_num('commodity_evaluation', ['commodity_id'=>$commodity['id']]);
            $commodities[$key]['evaluation_num'] = $this->get_evaluation_number_by_commodity_id($commodity['id'])['num'];
            if ($total_num == 0){
                $commodities[$key]['good_evaluation'] = 0;
            }else{
                $commodities[$key]['good_evaluation'] = round($this->get_praise_num($commodity['id']) / $total_num, 2);
            }
        }

        return $commodities;
    }

    /**
     * 获取商品推荐商品
     */
    public function get_commodity_recommend_commodity($commodity_id)
    {
        $this->db->select('managing_suggestions.id,
                           managing_suggestions.commodity_id,
                           managing_suggestions.recommend_commodity_id,
                           commodity.name,
                           commodity.create_time,
                           commodity.sales_volume,
                           commodity.price,
                           commodity_thumbnail.attachment_id,
                           commodity_thumbnail.create_time,
                           attachment.path
                           ');
        $this->db->join('commodity', 'commodity.id = managing_suggestions.recommend_commodity_id', 'left');
        $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_id = commodity.id', 'left');
        $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
        if (!empty($commodity_id)){
            $this->db->where('managing_suggestions.commodity_id', $commodity_id);
        }
        $this->db->group_by('recommend_commodity_id');
        $result = $this->db->get('managing_suggestions');


        if ($result && $result->num_rows() > 0){
            return $result->result_array();
        }else{
            return [];
        }
    }

    /**
     * 检验商品编号是否存在
     *
     * @param null $number 商品编号
     * @return bool
     */
    public function check_number_is_exists($number = NULL){
        if (empty($number)){
            return FALSE;
        }
        
        if ($this->jys_db_helper->get_total_num('commodity', ['number'=>$number])){
            return TRUE;
        }

        return FALSE;
    }

    /**
     * 根据代理商ID分页获取代理商商品
     * @param $agent_id 代理商ID
     * @param $page 页数
     * @param $page_size 页面大小
     */
    public function paginate_by_agent_id($agent_id = 0, $page = 1, $page_size = 10, $condition = NULL, $keyword = '', $user_info = []) {
        $response = array('success'=>FALSE, 'msg'=>'获取商品列表失败', 'data'=>[], 'total_page'=>0);
        if (intval($page) < 1 || intval($page_size) < 1) {
            $response['msg'] = '参数错误，获取商品列表失败';
            return $response;
        }
        $this->db->select('commodity.id,
                           commodity.name,
                           commodity.number,
                           commodity.price,
                           commodity.points,
                           commodity.introduce,
                           commodity.detail,
                           commodity.sales_volume,
                           commodity.create_time,
                           commodity.update_time,
                           commodity.category_id,
                           commodity.level_id,
                           agent_home.agent_id,
                           flash_sale.price as flash_sale_price,
                           flash_sale.start_time as flash_sale_start_time,
                           flash_sale.end_time as flash_sale_end_time,
                           category.name as category_name,
                           r_commodity.id as recommend_id,
                           r_commodity.name as recommend_name,
                           recommend_commodity.start_time,
                           recommend_commodity.end_time,
                           commodity.status_id,
                           commodity_status.name as status,
                           commodity.type_id,
                           commodity_type.name as type,
                           commodity.is_point,
                           attachment.path');

        $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_id = commodity.id', 'left');
        $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
        $this->db->join('category', 'category.id = commodity.category_id', 'left');
        $this->db->join('recommend_commodity', 'recommend_commodity.commodity_id = commodity.id', 'left');
        $this->db->join('commodity as r_commodity', 'r_commodity.id = recommend_commodity.commodity_id', 'left');
        $this->db->join('flash_sale', 'flash_sale.commodity_id = commodity.id', 'left');
        $this->db->join('system_code as commodity_status', 'commodity_status.value = commodity.status_id and commodity_status.type = "'.jys_system_code::COMMODITY_STATUS.'"', 'left');
        $this->db->join('system_code as commodity_type', 'commodity_type.value = commodity.type_id and commodity_type.type = "'.jys_system_code::COMMODITY_TYPE.'"', 'left');
        $this->db->join('agent_home', 'agent_home.commodity_id = commodity.id', 'left');


        $this->db->where('commodity.status_id !=', jys_system_code::COMMODITY_STATUS_DELETE);
        if (intval($agent_id) > 0) {
            $this->db->where('agent_home.agent_id', intval($agent_id));
        }
        if (!empty($condition)){
            $this->db->where($condition);
        }

        if (!empty($keyword)) {
            // 关键字模糊查找
            $this->db->group_start();
            $this->db->like('commodity.name', $keyword);
            $this->db->or_like('commodity.introduce', $keyword);
            $this->db->or_like('category.name', $keyword);
            $this->db->group_end();
        }

        $this->db->group_by('commodity.id');
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $result = $this->db->get('commodity');
        if ($result && $result->num_rows() > 0) {
            $response['success'] = TRUE;
            $commodity_list = $result->result_array();
            if (isset($user_info['price_discount']) && floatval($user_info['price_discount']) > 0) {
                $commodity_list = $this->calculate_discount_price($commodity_list, $user_info['price_discount']);
            }
            $response['data'] = $commodity_list;
            $response['msg'] = '获取成功';

            $this->db->select('COUNT(commodity.id) AS count');
            $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_id = commodity.id', 'left');
            $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
            $this->db->join('category', 'category.id = commodity.category_id', 'left');
            $this->db->join('recommend_commodity', 'recommend_commodity.commodity_id = commodity.id', 'left');
            $this->db->join('commodity as r_commodity', 'r_commodity.id = recommend_commodity.commodity_id', 'left');
            $this->db->join('flash_sale', 'flash_sale.commodity_id = commodity.id', 'left');
            $this->db->join('system_code as commodity_status', 'commodity_status.value = commodity.status_id and commodity_status.type = "'.jys_system_code::COMMODITY_STATUS.'"', 'left');
            $this->db->join('system_code as commodity_type', 'commodity_type.value = commodity.type_id and commodity_type.type = "'.jys_system_code::COMMODITY_TYPE.'"', 'left');
            $this->db->join('agent_home', 'agent_home.commodity_id = commodity.id', 'left');

            $this->db->where('commodity.status_id !=', jys_system_code::COMMODITY_STATUS_DELETE);
            if (intval($agent_id) > 0) {
                $this->db->where('agent_home.agent_id', intval($agent_id));
            }
            if (!empty($condition)){
                $this->db->where($condition);
            }

            if (!empty($keyword)) {
                // 关键字模糊查找
                $this->db->group_start();
                $this->db->like('commodity.name', $keyword);
                $this->db->or_like('commodity.introduce', $keyword);
                $this->db->or_like('category.name', $keyword);
                $this->db->group_end();
            }

            $this->db->group_by('commodity.id');
            $data_result = $this->db->get('commodity');
            if ($data_result && $data_result->num_rows() > 0) {
                $data_result = $data_result->row_array();
                $response['total_page'] = intval($data_result['count']) > 0 ? intval($data_result['count']) : 1;
            }else {
                $response['total_page'] = 1;
            }
        }else {
            $response['msg'] = '未找到符合要求的商品';
        }

        return $response;
    }

    /**
     * 计算折扣价格
     * @param array $commodity_list 商品信息列表
     * @param $price_discount 折扣率
     * @return array 修改价格后的商品信息列表
     */
    public function calculate_discount_price($commodity_list = [], $price_discount) {
        if (empty($commodity_list) || floatval($price_discount) < 0) {
            return $commodity_list;
        }

        if (isset($commodity_list['price']) && floatval($commodity_list['price']) > 0) {
            // 商品信息是一维数组
            $commodity_list['price'] = floatval($price_discount) * floatval($commodity_list['price']);
            if ($commodity_list['price'] < 0.01) {
                $commodity_list['price'] = 0.01;
            }
        }else {
            for ($i = 0; $i < count($commodity_list); $i++) {
                if (isset($commodity_list[$i]['price']) && floatval($commodity_list[$i]['price']) > 0) {
                    $commodity_list[$i]['price'] = floatval($price_discount) * floatval($commodity_list[$i]['price']);
                    if ($commodity_list[$i]['price'] < 0.01) {
                        $commodity_list[$i]['price'] = 0.01;
                    }
                }
            }
        }

        return $commodity_list;
    }
}