<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Favorite_model.php
 *
 *     Description: 收藏夹模型
 *
 *         Created: 2017-1-3 17:57:08
 *
 *          Author: wuhaohua
 *
 * =====================================================================================
 */
class Favorite_model extends CI_Model {
    public function __construct(){
        parent::__construct();
        $this->load->library(['Jys_kdniao']);
    }

    /**
     * 分页获取收藏信息
     * @param int $page 页数
     * @param int $page_size 页面大小
     * @param null $condition 其他条件
     */
    public function paginate($page = 1, $page_size = 10, $condition=NULL) {
        $result = array('success'=>FALSE, 'msg'=>'获取收藏列表失败', 'data'=>array(), 'total_page'=>0);
        if (intval($page) < 1 || intval($page_size) < 1) {
            $result['msg'] = '分页信息不正确';
            return $result;
        }

        $this->db->select(
            'commodity.*,
             commodity_path.path,
             favorite.id as favorite_id,
             favorite.user_id,
             favorite.create_time as favorite_create_time'
        );
        $this->db->where('commodity.status_id !=', jys_system_code::COMMODITY_STATUS_DELETE);
        if (!empty($condition) && is_array($condition)) {
            $this->db->where($condition);
        }
        $this->db->join('commodity', 'commodity.id = favorite.commodity_id', 'left');
        $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_id = commodity.id', 'left');
        $this->db->join('attachment as commodity_path', 'commodity_path.id = commodity_thumbnail.attachment_id', 'left');
        $this->db->group_by('commodity.id');
        $this->db->order_by('favorite.create_time', 'DESC');
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $data = $this->db->get('favorite');

        if ($data && $data->num_rows() > 0) {
            $result['msg'] = '查询成功';
            $result['success'] = TRUE;
            $result['data'] = $data->result_array();
            $total_page = $this->get_total_page($page_size, $condition);
            if (intval($total_page) < 1) {
                $result['total_page'] = 1;
            }else {
                $result['total_page'] = intval($total_page);
            }
        }else {
            $result['msg'] = '未查询到符合要求的信息';
        }
        return $result;
    }

    /**
     * 添加收藏
     * @param $commodity_id 商品ID
     * @param $user_id 用户ID
     */
    public function add($commodity_id, $user_id) {
        if (intval($commodity_id) < 1 || intval($user_id) < 1) {
            return FALSE;
        }

        $result = FALSE;
        $this->db->trans_start();
        $data = $this->jys_db_helper->get_where('favorite', ['commodity_id'=>$commodity_id, 'user_id'=>$user_id]);
        if (!empty($data) && intval($data['id']) > 0) {
            // 该商品已经收藏过
            $this->jys_db_helper->update('favorite', $data['id'], ['create_time'=>date('Y-m-d H:i:s')]);
            $result = TRUE;
        }else {
            // 该商品还未收藏
            $insert = array('commodity_id'=>$commodity_id, 'user_id'=>$user_id, 'create_time'=>date('Y-m-d H:i:s'));
            $insert_result = $this->jys_db_helper->add('favorite', $insert);
            if ($insert_result['success']) {
                $result = TRUE;
            }else {
                $result = FALSE;
            }
        }
        $this->db->trans_complete();

        return $result;
    }

    /**
     * 删除收藏
     * @param $commodity_id 商品ID
     * @param $user_id 用户ID
     */
    public function delete_by_condition($condition = array()) {
        if (empty($condition) || !is_array($condition)) {
            return FALSE;
        }

        if ($this->jys_db_helper->delete_by_condition('favorite', $condition)) {
            return TRUE;
        }else {
            return FALSE;
        }
    }

    /**
     * 获取总页数
     * @param int $page_size 页面大小
     * @param null $condition 其他条件
     */
    public function get_total_page($page_size = 10, $condition=NULL) {
        if (intval($page_size) < 1) {
            return FALSE;
        }

        $this->db->select(
            'favorite.id as favorite_id'
        );
        $this->db->where('commodity.status_id !=', jys_system_code::COMMODITY_STATUS_DELETE);
        if (!empty($condition) && is_array($condition)) {
            $this->db->where($condition);
        }
        $this->db->join('commodity', 'commodity.id = favorite.commodity_id');
        $data = $this->db->get('favorite');
        if ($data && $data->num_rows() > 0) {
            $total_number = $data->num_rows();
            return ceil( intval($total_number) / intval($page_size) * 1.0);
        }else {
            return FALSE;
        }
    }
}