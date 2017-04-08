<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =========================================================
 *
 *      Filename: Banner_model.php
 *
 *   Description: Banner模型
 *
 *       Created: 2016-11-18 16:41:22
 *
 *        Author: wuhaohua
 *
 * =========================================================
 */

class Banner_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * 根据页数获取数据
     * @param $page 页数
     */
    public function get_page_info($page = 1, $pagesize = 10) {
        $result = array('success'=>FALSE, 'msg'=>'查询失败');
        if (intval($page) < 1 || intval($pagesize) < 1) {
            $result['msg'] = "页数或页内数据个数不得小于1";
            return $result;
        }
        $this->db->select("banner.*, system_code.name as position, attachment.path");
        $this->db->join('system_code', 'system_code.value = banner.position_id');
        $this->db->join('attachment', 'attachment.id = banner.attachment_id');
        $this->db->where('system_code.type', 'banner_position');
        $this->db->limit($pagesize, ($page - 1) * $pagesize);
        $data = $this->db->get('banner');
        if ($data && $data->num_rows() > 0) {
            $result['total_page'] = $this->jys_db_helper->get_total_page('banner', $pagesize);
            $result['success'] = TRUE;
            $result['msg'] = "查询成功";
            $result['data'] = $data->result_array();
            $result['msg'] = "未查询到相关数据";
        }else {
            $result['msg'] = "未查询到相关数据";
        }

        return $result;
    }

    /**
     * 添加广告
     * @param $position_id 位置ID
     * @param $attachment_id 附件ID
     * @param $url 链接
     */
    public function add($position_id, $attachment_id, $url) {
        $result = array('success'=>FALSE, 'msg'=>'添加失败');
        if (intval($position_id) < 1 || intval($attachment_id) < 1) {
            $result['msg'] = '请填写正确的信息';
            return $result;
        }

        $insert = array('position_id'=>$position_id, 'attachment_id'=>$attachment_id, 'create_time'=>date("Y-m-d H:i:s"));
		if (!empty($url)) {
			$insert['url'] = $url; 
		}
        $data = $this->jys_db_helper->add('banner', $insert, TRUE);
        if ($data['success']) {
            $result['success'] = TRUE;
            $result['msg'] = '添加成功';
            $result['data'] = $data['insert_id'];
        }else {
            $result['msg'] = $data['msg'];
        }
        return $result;
    }

    /**
     * 更新广告
     * @param $id 广告ID
     * @param $position_id 位置ID
     * @param $attachment_id 附件ID
     * @param $url 链接
     */
    public function update($id, $position_id, $attachment_id, $url) {
        $result = array('success'=>FALSE, 'msg'=>'添加失败');
        if(intval($id) < 1) {
            $result['msg'] = '请选择要更新的广告';
            return $result;
        }

        $update = array();
        if (!empty($position_id)) {
            $update['position_id'] = $position_id;
        }
        if (!empty($attachment_id)) {
            $update['attachment_id'] = $attachment_id;
        }
        if (!empty($url)) {
            $update['url'] = $url;
        }

        if (empty($update)) {
            $result['msg'] = '未对广告信息进行更新';
            return $result;
        }

        if ($this->jys_db_helper->update('banner', $id, $update)) {
            $result['success'] = TRUE;
            $result['msg'] = '更新成功';
        }else {
            $result['msg'] = '更新失败，请检查是否有数据进行了变更';
        }
        return $result;
    }

    public function delete($id) {
        $result = array('success'=>FALSE, 'msg'=>'添加失败');
        if (intval($id) < 1) {
            $result['msg'] = '请选择要删除的广告';
            return $result;
        }

        if ($this->jys_db_helper->delete('banner', $id)) {
            $result['success'] = TRUE;
            $result['msg'] = '删除成功';
        }else {
            $result['msg'] = '删除失败，请检查要删除的数据是否存在';
        }

        return $result;
    }

    /**
     * 获取首页banner，默认是5条数据
     *
     * @param int $banner_num 广告数量
     * @return array
     */
    public function get_home_banner($banner_num = 5, $position = 1){
        switch ($position){
            case 1:
                $position_id = jys_system_code::BANNER_POSITION_PC_HOME;
                break;
            case 2:
                $position_id = jys_system_code::BANNER_POSITION_WEIXIN_HOME;
                break;
            case 3:
                $position_id = jys_system_code::BANNER_POSITION_WEIXIN_INTEGRAL_HOME;
                break;
            case 4:
                $position_id = jys_system_code::BANNER_POSITION_WEIXIN_INTEGRAL_EXCHANGE;
                break;
            case 5:
                $position_id = jys_system_code::BANNER_POSITION_WEIXIN_INTEGRAL_SWEEPSTAKES;
                break;
            default:
                return [];
        }
        $this->db->select('banner.id,
                           banner.position_id,
                           banner.attachment_id,
                           attachment.path,
                           banner.url,
                           banner.create_time');
        $this->db->join('attachment', 'attachment.id = banner.attachment_id', 'left');
        $this->db->where('banner.position_id', $position_id);
        $this->db->order_by('banner.create_time', 'DESC');
        if (!empty($banner_num)){
            $this->db->limit($banner_num, 0);
        }

        $result = $this->db->get('banner');

        if($result && $result->num_rows() > 0){
            return $result->result_array();
        }else{
            return [];
        }
    }
}