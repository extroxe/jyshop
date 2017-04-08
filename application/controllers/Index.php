<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename:  index.php
 *
 *     Description:  网站前台默认控制器
 *
 *         Created:  2016-06-10 20:41:45
 *
 *          Author:  sunzuosheng
 *
 * =====================================================================================
 */

Class Index extends CI_Controller {
    /**
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
		$this->load->library(['form_validation', 'encryption']);
		$this->load->helper('cookie');
		$this->load->model(['User_model', 'Category_model', 'Banner_model', 'Commodity_model', 'Common_model', 'Express_model', 'Report_model', 'Verification_code_model', 'System_setting_model']);
    }

	/**
	 * 商城首页
	 */
    public function index(){
		$data['title'] = "塞安生物";
		$data['js'] = array('index');
		$data['css'] = array('index');
		$data['main_content'] = 'index';
		$data['collection'] = $this->Category_model->get_category();
		$data['member_category'] = $this->Category_model->get_member_category();
		$data['banner'] = $this->Banner_model->get_home_banner(5, 1);
		$data['new_recent'] = $this->Commodity_model->get_commodity_by_condition(['commodity.is_point'=>0], TRUE, TRUE, 4)['data'];
		$data['flash_sale'] = $this->Commodity_model->get_flash_sale(3)['data'];
		$data['recommend_hot_sale'] = $this->Commodity_model->get_home_recommend(4, 1);
		$data['recommend_hot_sale_cover'] = $this->System_setting_model->get_hot_sale_cover()['data'];
		$data['recommend_hot_exchange'] = $this->Commodity_model->get_home_recommend(4, 2);
		$data['recommend_hot_exchange_cover'] = $this->System_setting_model->get_hot_exchange_cover()['data'];
		$data['isset_search'] = TRUE;
		$data['isset_nav'] = TRUE;
		$data['is_home'] = TRUE;
		$this->load->view('includes/template_view', $data);
    }

	/**
	 * 搜索页
	 */
	public function search(){
		$search = $this->input->get();
		if (empty($search)){
			show_404();
		}

		$search_str = $this->Commodity_model->search($search);

		$page_size = isset($search['page_size']) ? $search['page_size'] : 9;
		if (isset($search['page']) && intval($search['page']) > 0){
			$page = $search['page'];
		}else{
			$page = 1;
		}

		$data['title'] = "塞安生物-搜索";
		$data['js'] = array('search');
		$data['css'] = array('search');
		$data['main_content'] = 'search';
		$data['search'] = $search;
		$data['collection'] = $this->Category_model->get_category();
		$commodity = $this->Commodity_model->paginate($page, $page_size, $search_str);
		$data['search_commodity'] = $commodity['data'];
		$data['render'] = $this->Common_model->render($page, $commodity['total_page']);
		$data['category'] = $this->Common_model->unique_queue($data['search_commodity'], ['category_id', 'category_name']);
		$data['type'] = $this->Common_model->unique_queue($data['search_commodity'], ['type_id', 'type']);

		$all_point_flag = TRUE;
		if (!empty($data['search_commodity'])){
			foreach ($data['search_commodity'] as $row){
				if (!$row['is_point']){
					$all_point_flag = FALSE;
				}
			}
		}

		if ($all_point_flag){
			$data['recommend'] = $this->Commodity_model->get_home_recommend(3, 2);
		}else{
			$data['recommend'] = $this->Commodity_model->get_home_recommend(3, 1);
		}

		$data['all_point_flag'] = $all_point_flag;

		$data['isset_search'] = TRUE;
		$data['isset_nav'] = TRUE;
		$this->load->view('includes/template_view', $data);
	}

	/**
	 * 注册页面
	 */
	public function sign_up(){
		$data['title'] = "塞安生物-注册";
		$data['js'] = array('sign_up', 'jquery.validate.min');
		$data['css'] = array('sign_up');
		$data['main_content'] = 'sign_up';
		$data['isset_search'] = FALSE;
		$data['isset_nav'] = FALSE;
		$data['sign_up_flag'] = TRUE;
		$data['simple_footer'] = TRUE;
		$this->load->view('includes/template_view', $data);
	}

	/**
	 * 登录页面
	 */
	public function sign_in(){
		if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])){
			redirect('user/user_center');
		}
		$data['title'] = "塞安生物-登录";
		$data['js'] = array('sign_in');
		$data['css'] = array('sign_in');
		$data['main_content'] = 'sign_in';
		$data['isset_search'] = FALSE;
		$data['isset_nav'] = FALSE;
		$data['sign_in_flag'] = TRUE;
		$data['simple_footer'] = TRUE;
		$this->load->view('includes/template_view', $data);
	}

	/**
	 * 找回密码
	 */
	public function find_password(){

		$data['title'] = "塞安生物-找回密码";
		$data['js'] = array('template','find_password');
		$data['css'] = array('find_password');
		$data['main_content'] = 'find_password';
		$data['isset_search'] = FALSE;
		$data['isset_nav'] = FALSE;
		$this->load->view('includes/template_view', $data);
	}


	/**
	 * 健康服务页面
	 */
	public function service(){
		$data['title'] = "塞安生物-健康服务";
		$data['js'] = array('template','service');
		$data['css'] = array('service');
		$data['main_content'] = 'service';
		$data['isset_search'] = FALSE;
		$data['isset_nav'] = FALSE;
		$this->load->view('includes/template_view', $data);
	}

	/**
	 * 查询报告
	 */
	public function search_report(){
		$data['title'] = "塞安生物-查询报告";
		$data['js'] = array('template', 'search_report');
		$data['css'] = array('search_report');
		$data['main_content'] = 'search_report';
		$data['isset_search'] = TRUE;
		$data['isset_nav'] = TRUE;
		$data['collection'] = $this->Category_model->get_category();
		$this->load->view('includes/template_view', $data);
	}

	/**
	 * 登录验证
	 */
	public function do_login(){
		//验证表单信息
		$this->form_validation->set_rules('username', '用户名', 'trim|required|min_length[3]|max_length[50]');
		$this->form_validation->set_rules('password', '密码', 'trim|required|min_length[6]|max_length[256]');
		$this->form_validation->set_rules('auto_login', '自动登录', 'trim|numeric');

		//表单验证是否通过, 若不通过 返回表单错误信息，停止执行
		$res = $this->Common_model->deal_validation_errors();

		if ($res['success']){
			//处理数据
			$username = $this->input->post('username', TRUE);
			$password = $this->input->post('password', TRUE);
			$auto_login = intval($this->input->post('auto_login', TRUE));

			if (!empty(get_cookie('username')) && !empty(get_cookie('password'))){
				if ($password == get_cookie('password')){
					$password = $this->encryption->decrypt(get_cookie('password'));
				}
			}

			$data = $this->User_model->check_user($username, $password);

			if ($auto_login === 1 && $data['success'] && empty(get_cookie('username')) && empty(get_cookie('password'))){
				set_cookie('username', $username, 3600*24*7);
				set_cookie('password', $this->encryption->encrypt($password), 3600*24*7);
			}
		}else{
			$data['success'] = FALSE;
			$data['msg'] = '输入有错误';
			$data['error'] = $res['msg'];
		}

		echo json_encode($data);
	}

	/**
	 * 登出
	 */
	public function sign_out(){
		session_unset();
		header('Location:'.site_url());
	}

	/**
	 * 根据商品ID获取商品
	 */
	public function get_commodity_by_id(){
		$commodity_id = $this->input->post('commodity_id', TRUE);
		$data = $this->Commodity_model->get_commodity_list_by_condition(['commodity.id'=>$commodity_id], FALSE);

		echo json_encode($data);
	}

	/**
	 * 更新快递物流信息（异步毁掉接口）
	 */
	public function update_logistics_info(){
		$request = $this->input->post();
		$request_data = json_decode($request['RequestData'], TRUE);
		$logistics_arrs = $request_data['Data'];

		$response = [
			'EBusinessID' => $request_data['EBusinessID'],
			'UpdateTime' => date('Y-m-d H:i:s'),
			'Success' => false,
			'Reason' => ''
		];

		$response['Success'] = $this->Express_model->update_logistics_info($logistics_arrs);

		echo json_encode($response);
	}

	/**
	 * 根据条件获取报告
	 */
	public function get_report_by_condition(){
		$identity_card = $this->input->post('identity_card');
		$phone = $this->input->post('phone');
		$verification_code = $this->input->post('verification_code');
		if ($this->Verification_code_model->check_code($phone, $verification_code, Jys_system_code::VERIFICATION_CODE_PURPOSE_SEARCH_REPORT)){
			$result = $this->Report_model->get_report_by_condition($identity_card, $phone);
		}else{
			$result['success'] = FALSE;
			$result['msg'] = '验证码错误';
		}

		echo json_encode($result);
	}

	/**
	 * 找回密码
	 */
	public function update_password_by_verified_code(){
		$this->form_validation->set_rules('phone', '手机号码', 'trim|required|regex_match[/^1(3|4|5|7|8)\d{9}$/]',['regex_match'=>'请填写正确的手机号']);
		$this->form_validation->set_rules('code', '验证码', 'trim|required');
		$this->form_validation->set_rules('password', '密码', 'trim|required|min_length[6]|max_length[256]');

		$res = $this->Common_model->deal_validation_errors();

		if ($res['success']){
			//处理数据
			$phone      = $this->input->post('phone', TRUE);
			$code       = $this->input->post('code', TRUE);
			$password   = $this->input->post('password', TRUE);

			if ($this->Verification_code_model->check_code($phone, $code, Jys_system_code::VERIFICATION_CODE_PURPOSE_PHONE)){
				$data['success'] = $this->jys_db_helper->update_by_condition('user', ['phone'=>$phone], ['password'=>password_hash($password, PASSWORD_DEFAULT)]);
					$data['msg'] = '修改成功';
			}else{
				$data['success'] = FALSE;
				$data['msg'] = '修改失败';
			}
		}else{
			$data['success'] = FALSE;
			$data['msg'] = '输入有错误';
			$data['error'] = $res['msg'];
		}

		echo json_encode($data);
	}
}