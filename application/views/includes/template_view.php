<?php
/*
 * =====================================================================================
 *
 *       Filename:  template_view.php
 *
 *    Description:  视图模板文件，自动加载必要视图以及参数视图
 *
 *        Created:  2015-03-17 11:47:48
 *
 *         Author:  wuhaohua
 *        Company:  uvge
 *
 * =====================================================================================
 */

$this -> load -> view('includes/header_view');
$this -> load -> view('includes/css_view');
$this -> load -> view($main_content);
$this -> load -> view('includes/footer_view');
$this -> load -> view('includes/js_view');
?>
