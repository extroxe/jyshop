<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: User_model.php
 *
 *     Description: 用户模型
 *
 *         Created: 2016-11-15 20:20:57
 *
 *          Author: huazhiqiang
 *
 * =====================================================================================
 */
Class User_model extends CI_Model {
    /**
     * User_model 构造函数
     */
    public function __construct(){
        parent::__construct();
    }
    
    /**
     * 分页获取会员信息
     * @param string $id 用户Id
     * @return mix  用户信息或者bool信息
     */
    public function get_page_info($page = 1, $page_size = 10, $is_show = 1, $keyword = ""){
        if (intval($page) < 1 || intval($page_size) < 1) {
            $data['success'] = FALSE;
            $data['msg']     = '请求数据错误，无法获取数据';
            $data['data']    = array();
            $data['total_page'] = 0;
            return $data;
        }
        $this->db->select('user.*,  system_code.name as role, attachment.path, level.name as level_name');
        $this->db->from('user');
        $this->db->where('is_show', $is_show);
        $this->db->where('system_code.type', Jys_system_code::ROLE);
        if (!empty($keyword)) {
            // 关键字模糊查找
            $this->db->group_start();
            $this->db->like('user.username', $keyword);
            $this->db->or_like('user.name', $keyword);
            $this->db->or_like('user.phone', $keyword);
            $this->db->or_like('user.email', $keyword);
            $this->db->group_end();
        }

        $this->db->join('attachment', 'attachment.id = user.avatar', 'left');
        $this->db->join('level', 'level.id = user.level', 'left');
        $this->db->join('system_code', 'system_code.value = user.role_id', 'left');
        $this->db->limit($page_size, ($page-1)*$page_size);
        $this->db->order_by('user.create_time', 'DESC');
        $result = $this->db->get();
        if($result && $result->num_rows() > 0){
            $data['success'] = TRUE;
            $data['msg']     = '获取成功';
            $user_list = $result->result_array();
            foreach ($user_list as $value) {
                unset($value['password']);
            }
            $data['data']    = $user_list;
            $this->db->select('user.id');
            $this->db->from('user');
            $this->db->where('is_show', $is_show);
            $this->db->where('system_code.type', Jys_system_code::ROLE);
            if (!empty($keyword)) {
                // 关键字模糊查找
                $this->db->group_start();
                $this->db->like('user.username', $keyword);
                $this->db->or_like('user.name', $keyword);
                $this->db->or_like('user.phone', $keyword);
                $this->db->or_like('user.email', $keyword);
                $this->db->group_end();
            }
            $this->db->join('attachment', 'attachment.id = user.avatar', 'left');
            $this->db->join('level', 'level.id = user.level', 'left');
            $this->db->join('system_code', 'system_code.value = user.role_id', 'left');
            $page_result = $this->db->get();
            if ($page_result && $page_result->num_rows() > 0) {
                $total = $page_result->num_rows();
                $data['total_page'] = ceil($total / $page_size * 1.0);
            }else {
                $data['total_page'] = 1;
            }
        }else{
            $data['success'] = FALSE;
            $data['msg']     = '当前系统中没有用户';
            $data['total_page'] = 0;
        }
        return $data;
    }

    /**
     * 根据条件获取用户详细信息
     * @param $condition 查询条件
     * @return 返回用户信息或FALSE
     */
    public function get_user_detail_by_condition($condition = [], $show_psd = FALSE) {
        if (!is_array($condition) || empty($condition)) {
            return FALSE;
        }

        $this->db->select('user.*,
                           avatar_attachment.path as avatar_path,
                           role.name as role_name,
                           level.name as level_name,
                           level_attachment.path as level_icon_path');
        $this->db->join('level', 'level.id = user.level', 'left');
        $this->db->join('attachment as level_attachment', 'level_attachment.id = level.icon_id', 'left');
        $this->db->join('attachment as avatar_attachment', 'avatar_attachment.id = user.avatar', 'left');
        $this->db->join('system_code as role', 'role.value = user.role_id and role.type = "'.jys_system_code::ROLE.'"', 'left');
        $this->db->where($condition);
        $result = $this->db->get('user');

        if ($result && $result->num_rows() > 0) {
            $user_info = $result->row_array();
            if (!$show_psd){
                $user_info['password'] = NULL;
            }

            return $user_info;
        }

        return FALSE;
    }

    /**
     * 校验用户信息
     *
     * @param null $username 用户账号
     * @param null $password 用户密码
     * @return mixed
     */
    public function check_user($username = NULL, $password = NULL){
        $data['success'] = FALSE;
        $data['msg'] = '账号或密码错误';

        if (empty($username) || empty($password)){
            $data['msg'] = '账号、密码不能为空...';
            return $data;
        }

        $user_info = $this->get_user_detail_by_condition(['user.username'=>$username], TRUE);
        
        if ($user_info && $user_info['is_show'] && $user_info['role_id'] != Jys_system_code::ROLE_ADMINISTRATOR){
            if (password_verify($password, $user_info['password'])){
                if (isset($user_info['openid']) && !empty($user_info['openid'])) {
                    // 当前用户已绑定微信
                    $_SESSION['openid'] = $user_info['openid'];
                }else {
                    // 当前用户未绑定微信
                    if (isset($_SESSION['openid']) && !empty($_SESSION['openid'])) {
                        // 当前session中已有openid（用户微信访问）
                        $update_data = array('openid'=>$_SESSION['openid']);
                        $this->jys_db_helper->update('user', $user_info['id'], $update_data);
                    }
                }
                if (isset($user_info['unionid']) && !empty($user_info['unionid'])) {
                    // 当前用户已设置unionid
                    $_SESSION['unionid'] = $user_info['unionid'];
                }else {
                    // 当前用户未设置unionid
                    if (isset($_SESSION['unionid']) && !empty($_SESSION['unionid'])) {
                        $update_data = array('unionid'=>$_SESSION['unionid']);
                        $this->jys_db_helper->update('user', $user_info['id'], $update_data);
                    }
                }

                $_SESSION['user_id'] = $user_info['id'];
                $_SESSION['username'] = $user_info['username'];
                $_SESSION['name'] = $user_info['name'];
                $_SESSION['nickname'] = $user_info['nickname'];
                $_SESSION['role_id'] = $user_info['role_id'];
                $_SESSION['avatar_path'] = $user_info['avatar_path'];

                $data['msg'] = '登陆成功';
                $data['success'] = TRUE;
            }else{
                $data['msg'] = '密码错误';
            }
        }else{
            $data['msg'] = '账号不存在';
        }
        $this->db->trans_complete();

        return $data;
    }

    /**
     * 根据微信接口上返回的用户信息获取用户信息，填入session
     * 如果当前openid在系统中不对应任何用户，则只在session中填入openid，当用户在微信端登录或者注册后，马上将openid填入对应用户的信息中，实现绑定
     * @param $userinfo 微信端返回的用户信息
     */
    public function get_user_from_weixin($userinfo) {
        if (!is_array($userinfo) || empty($userinfo)) {
            return FALSE;
        }

        $user = $this->get_user_detail_by_condition(['user.openid'=>$userinfo['openid']]);
        $_SESSION['openid'] = $userinfo['openid'];
        if (isset($userinfo['unionid'])) {
            $_SESSION['unionid'] = $userinfo['unionid'];
        }
        if (!empty($user) && is_array($user) && count($user) > 0 && $user['is_show']) {
            // 当前openid对应系统中的某个用户
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['nickname'] = $user['nickname'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['avatar_path'] = $user['avatar_path'];
            return TRUE;
        }else if (!empty($user) && is_array($user) && count($user) > 0 && !$user['is_show']){
            //当前openid对应系统用户，该用户已经被软删除
            return FALSE;
        }else {
            // 当前openid不对应系统中的任何用户
            return FALSE;
        }
    }

    /**
     * 验证密码
     *
     * @param string $password 密码
     * @return bool
     */
    public function check_psd($password = ''){
        if (empty($password)){
            return FALSE;
        }

        $user_info = $this->get_user_detail_by_condition(['user.id'=>$_SESSION['user_id']], TRUE);

        if (password_verify($password, $user_info['password'])){
            return TRUE;
        }

        return FALSE;
    }

    /**
     * 过滤手机号码
     *
     * @param string $phone 手机号码
     * @return string|bool
     */
    public function filter_phone($phone = ''){
        if (empty($phone)){
            return FALSE;
        }

        $phone = substr($phone, 0, 3).'****'.substr($phone, -4);

        return $phone;
    }

    /**
     * 用户注册
     * @param $username 用户名
     * @param $password 密码
     * @param $phone 手机号码
     * @param $role_id 角色ID
     */
    public function register($username, $password, $phone, $role_id = Jys_system_code::ROLE_USER) {
        $result = array('success'=>FALSE, 'msg'=>'注册失败');
        if (empty($username) || empty($password) || empty($phone) || empty($role_id)) {
            $result['msg'] = '用户信息缺失，注册失败';
            return $result;
        }

        $this->db->trans_start();

        if ($this->username_is_available($username) && $this->phone_is_available($phone)) {
            $userinfo['username'] = $username;
            $userinfo['password'] = password_hash($password, PASSWORD_DEFAULT);
            $userinfo['phone'] = $phone;
            $userinfo['nickname'] = $username;
            $userinfo['create_time']    = date('Y-m-d H:i:s');
            $userinfo['role_id'] = Jys_system_code::ROLE_USER;
            $userinfo['update_time'] = $userinfo['create_time'];
            if (isset($_SESSION['openid']) && !empty($_SESSION['openid'])) {
                if ($this->openid_is_available($_SESSION['openid'])){
                    $userinfo['openid'] = $_SESSION['openid'];
                }else{
                    $this->db->trans_rollback();
                    $result['success'] = FALSE;
                    $result['msg'] = '该微信已经注册过，请更换微信重新尝试';
                    return $result;
                }
            }

            $res_status = $this->jys_db_helper->set('user',$userinfo);
            if($res_status){
                $result['success'] = true;
                $result['msg'] = '用户注册成功';
            }
        }else {
            $result['msg'] = '用户名或手机号已被注册';
        }

        $this->db->trans_complete();

        return $result;
    }

    /**
     * 检测用户名是否可用
     * @param $username 用户名
     * @return bool 可用返回TRUE，不可用返回FALSE
     */
    public function username_is_available($username) {
        if (empty($username)) {
            return FALSE;
        }
        $num = $this->jys_db_helper->get_total_num('user', array('username'=>$username));
        if (intval($num) > 0) {
            return FALSE;
        }else {
            return TRUE;
        }
    }

    /**
     * 检测手机号是否可用
     * @param $phone 手机号
     * @return bool 可用返回TRUE，不可用返回FALSE
     */
    public function phone_is_available($phone) {
        if (empty($phone)) {
            return FALSE;
        }
        $num = $this->jys_db_helper->get_total_num('user', array('phone'=>$phone));
        if (intval($num) > 0) {
            return FALSE;
        }else {
            return TRUE;
        }
    }

    /**
     * 验证手机号与用户是否绑定
     *
     * @param $user_id 用户ID
     * @param $phone 手机号
     * return arrar
     */
    public function check_user_phone_valid($user_id, $phone){
        $data['success'] = FALSE;
        $data['msg'] = '';

        if (empty($user_id) || empty($phone) || intval($user_id) < 1){
            return $data;
        }

        if ($this->jys_db_helper->get_where('user', ['id'=>$user_id, 'phone'=>$phone])){
            $data['success'] = TRUE;
        }else{
            $data['success'] = FALSE;
        }

        return $data;
    }

    /**
     * 检查微信端注册时，该openid是否已经注册
     */
    public function openid_is_available($openid)
    {
        $result = $this->jys_db_helper->get_where('user', array('openid' => $openid));

        if ($result){
            return FALSE;
        }else{
            return TRUE;
        }
    }

    /**
     * 关注某个贴吧用户
     *
     * @param $user_id 关注用户ID
     * @param $focus_id 被关注用户ID
     */
    public function focus_user($user_id, $focus_id) {
        $result = array('success'=>TRUE, 'msg'=>'关注失败');
        if (intval($user_id) < 1 || intval($focus_id) < 1) {
            $result['msg'] = '参数错误';
            return $result;
        }

        $this->db->trans_start();
        $this->jys_db_helper->delete_by_condition('focus_user', ['user_id'=>$user_id, 'focus_id'=>$focus_id]);
        $add_result = $this->jys_db_helper->add('focus_user', ['user_id'=>$user_id, 'focus_id'=>$focus_id, 'create_time'=>date('Y-m-d H:i:s')]);
        if ($add_result['success']) {
            $result['success'] = TRUE;
            $result['msg'] = '关注成功';
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $result['success'] = FALSE;
            $result['msg'] = '关注失败，事务提交失败';
        }

        return $result;
    }

    /**
     * 分页获取当前用户关注的论坛用户
     *
     * @param int $page 页数
     * @param int $page_size 页面大小
     * @param int $user_id 当前用户ID
     */
    public function paginate_focus_user($page = 1, $page_size = 10, $user_id = 0) {
        $result = array('success'=>FALSE, 'msg'=>'获取关注用户失败', 'data'=>array(), 'total_page'=>0);
        if (intval($page) < 1 || intval($page_size) < 1 || intval($user_id) < 1) {
            $result['msg'] = '参数错误';
            return $result;
        }

        $this->db->select('
            focus_user.*,
            user.username,
            user.gender,
            user.phone,
            user.email,
            user.nickname,
            user.avatar,
            attachment.path as avatar_path
        ');
        $this->db->join('user', 'user.id = focus_user.focus_id', 'left');
        $this->db->join('attachment', 'attachment.id = user.avatar', 'left');
        $this->db->where('user.is_show', '1');
        $this->db->where('focus_user.user_id', $user_id);
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $this->db->order_by('focus_user.create_time', 'DESC');
        $user = $this->db->get('focus_user');
        if ($user && $user->num_rows() > 0) {
            $result['success'] = TRUE;
            $result['msg'] = '获取关注用户成功';
            $result['data'] = $user->result_array();

            $this->db->select('focus_user.id');
            $this->db->join('user', 'user.id = focus_user.focus_id', 'left');
            $this->db->join('attachment', 'attachment.id = user.avatar', 'left');
            $this->db->where('user.is_show', '1');
            $this->db->where('focus_user.user_id', $user_id);
            $user_total = $this->db->get('focus_user');
            if ($user_total && $user_total->num_rows() > 0) {
                $total = $user_total->num_rows();
                $result['total_page'] = ceil($total / $page_size * 1.0);
            }else {
                $result['total_page'] = 1;
            }
        }else {
            $result['msg'] = '您当前未关注任何用户';
        }
        return $result;
    }

    /**
     * 分页获取当前用户的粉丝
     * @param int $page
     * @param int $page_size
     * @param int $user_id
     */
    public function paginate_fans($page = 1, $page_size = 10, $user_id = 0) {
        $result = array('success'=>FALSE, 'msg'=>'获取关注用户失败', 'data'=>array(), 'total_page'=>0);
        if (intval($page) < 1 || intval($page_size) < 1 || intval($user_id) < 1) {
            $result['msg'] = '参数错误';
            return $result;
        }

        $this->db->select('
            focus_user.*,
            user.username,
            user.gender,
            user.phone,
            user.email,
            user.nickname,
            user.avatar,
            attachment.path as avatar_path
        ');
        $this->db->join('user', 'user.id = focus_user.user_id', 'left');
        $this->db->join('attachment', 'attachment.id = user.avatar', 'left');
        $this->db->where('user.is_show', '1');
        $this->db->where('focus_user.focus_id', $user_id);
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $this->db->order_by('focus_user.create_time', 'DESC');
        $user = $this->db->get('focus_user');
        if ($user && $user->num_rows() > 0) {
            $result['success'] = TRUE;
            $result['msg'] = '获取粉丝成功';
            $result['data'] = $user->result_array();

            $this->db->select('focus_user.id');
            $this->db->join('user', 'user.id = focus_user.user_id', 'left');
            $this->db->join('attachment', 'attachment.id = user.avatar', 'left');
            $this->db->where('user.is_show', '1');
            $this->db->where('focus_user.focus_id', $user_id);
            $user_total = $this->db->get('focus_user');
            if ($user_total && $user_total->num_rows() > 0) {
                $total = $user_total->num_rows();
                $result['total_page'] = ceil($total / $page_size * 1.0);
            }else {
                $result['total_page'] = 1;
            }
        }else {
            $result['msg'] = "您当前没有任何粉丝";
        }

        return $result;
    }

    /**
     * 获取指定用户的关注数量
     *
     * @param int $user_id
     * @return array
     */
    public function get_focus_num($user_id = 0){
        $result = array('success'=>FALSE, 'msg'=>'获取用户关注数量失败', 'data'=>array());
        if (intval($user_id) < 1) {
            $result['msg'] = '该用户不存在';
            return $result;
        }
        $this->db->select('focus_user.id');
        $this->db->join('user', 'user.id = focus_user.focus_id', 'left');
        $this->db->where('user.is_show', '1');
        $this->db->where('focus_user.user_id', $user_id);
        $user_total = $this->db->get('focus_user');

        return $user_total->num_rows();
    }

    /**
     * 获取指定用户的粉丝数量
     *
     * @param int $user_id
     * @return array
     */
    public function get_fans_num($user_id = 0){
        $result = array('success'=>FALSE, 'msg'=>'获取用户粉丝数量失败', 'data'=>array());
        if (intval($user_id) < 1) {
            $result['msg'] = '该用户不存在';
            return $result;
        }
        $this->db->select('focus_user.id');
        $this->db->join('user', 'user.id = focus_user.user_id', 'left');
        $this->db->where('user.is_show', '1');
        $this->db->where('focus_user.focus_id', $user_id);
        $user_total = $this->db->get('focus_user');

        return $user_total->num_rows();
    }
}