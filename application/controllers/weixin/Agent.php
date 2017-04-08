<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename:  Agent.php
 *
 *     Description:  代理商控制器
 *
 *         Created:  2017-3-28 15:03:21
 *
 *          Author:  sunzuosheng
 *
 * =====================================================================================
 */

Class Agent extends CI_Controller {

    private $_default_password = 123456;
    //太平代理ID
    private $_default_agent_id = 470;

    /**
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
        $this->load->model(['User_model']);
    }

    /**
     * 登录入口
     */
    public function entrance(){
        $uid = $this->input->get('uid', TRUE);
        $res = $this->_auth_exist($uid);
        if ($res){
            $user = $this->User_model->get_user_detail_by_condition(['user_id'=>$res]);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['nickname'] = $user['nickname'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['avatar_path'] = $user['avatar_path'];

            redirect('/weixin/index');
        }else{
            echo json_encode([
                'success' => FALSE,
                'msg' => '登录失败，认证失败'
            ]);exit;
        }
    }

    //用户认证
    private function _auth_exist($uid = NULL){
        if (empty($uid)){
            return FALSE;
        }

        $agent_user = $this->jys_db_helper->get_where('user_agent', ['uid'=>$uid, 'agent_id'=>$this->_default_agent_id]);

        if ($agent_user){
            return $agent_user['user_id'];
        }else{
            //添加用户数据
            $add_user['username'] = $this->_generate_username();

            //用户名生成失败
            if (!$add_user['username']){
                return FALSE;
            }

            $add_user['password'] = password_hash($this->_default_password, PASSWORD_DEFAULT);
            $add_user['name'] = $add_user['username'];
            $add_user['nickname'] = $add_user['username'];
            $add_user['role_id'] = Jys_system_code::ROLE_USER;
            $add_user['create_time'] = date('Y-m-d H:i:s');
            $add_user['update_time'] = date('Y-m-d H:i:s');

            $this->db->trans_start();
            $user = $this->jys_db_helper->add('user', $add_user, TRUE);

            if ($user['success']){
                //添加代理用户数据
                $add_agent_user['uid'] = $uid;
                $add_agent_user['user_id'] = $user['insert_id'];
                $add_agent_user['agent_id'] = $this->_default_agent_id;
                $add_agent_user['create_time'] = date('Y-m-d H:i:s');

                $agent_user = $this->jys_db_helper->add('user_agent', $add_agent_user);

                if ($agent_user['success']){
                    $this->db->trans_complete();
                    return $user['insert_id'];
                }else{
                    $this->db->trans_rollback();
                }
            }
        }

        return FALSE;
    }

    /**
     * 生成用户名
     *
     * @return bool|string
     */
    private function _generate_username(){
        $this->db->select('user_agent.id, user.username');
        $this->db->join('user', 'user.id = user_agent.user_id', 'left');
        $this->db->where('user_agent.agent_id', $this->_default_agent_id);
        $result = $this->db->get('user_agent');

        if ($result && $result->num_rows() > 0){
            $last_agent_user = $result->last_row();
            if (preg_match('/[0-9]+$/', $last_agent_user->username, $result)){
                $length = strlen($result[0]);
                $number = intval($result[0]) + 1;
                $num_length = strlen(''.$number);

                for($i = 0; $i < $length - $num_length; $i++){
                    $number = '0'.$number;
                }

                $username = substr($last_agent_user->username, 0, strlen($last_agent_user->username) - $length).$number;
            }else{
                $username = 'taiping00001';
            }
        }else{
            $username = 'taiping00001';
        }

        return $username;
    }
}