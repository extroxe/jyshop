/**
 * Created by sailwish009 on 2016/12/30.
 */
angular.module('app')
    .controller('orderList', ['$rootScope','$scope', 'ajax', function ($rootScope,$scope, ajax) {
        $scope.orderAll = [];
        $scope.subOrder = [];
        $scope.orderStatus = [];
        $scope.orderAll = [];
        $scope.subOrder = [];
        $scope.status_id = '';
        // 微信支付JSAPI参数
        $scope.js_api_parameters = {};

        /**
         * 加载数据
         * @param status_id
         */
        $scope.loadData = function (url, status_id, me) {
            ajax.req('POST', url + status_id, {
                page: $scope.page,
                page_size: 10
            }, true)
                .then(function (data) {
                    if(data.data){
                        $scope.commodity_total_num = 0; //子订单商品总计
                        $scope.total_price = 0; //子订单商品总价

                        $scope.subOrder = data.data;
                        angular.forEach($scope.subOrder, function (data, index, array) {
                            array[index]['total_amount'] = 0;
                            angular.forEach(data['sub_orders'], function (sub_data, sub_index, sub_arrray) {
                                array[index]['total_amount'] += parseInt(sub_data['amount']);
                                if(data.payment_id == '4'){
                                    data.total_price = parseInt(sub_data['price']);
                                }
                            })
                        });
                        
                        angular.forEach($scope.subOrder, function (orderlist) {
                            if(orderlist.sub_orders != null){
                                $scope.orderAll.push(orderlist);
                            }
                        });
                        if(data.data.length < 10 &&  data.data.length > 0){
                            // 锁定
                            me.lock();

                            setTimeout(function(){
                                $('.dropload-down').css('display', 'none');
                                $('.load_done').css('display', 'block');
                            },1000);
                            // 无数据
                            me.noData();
                        }else if(data.success == false){
                            $('.dropload-down').css('display', 'block');
                            $('.load_done').css('display', 'none');
                            // 无数据
                            me.noData();
                            // 锁定
                            me.lock();
                        }
                        setTimeout(function(){
                            // 每次数据加载完，必须重置
                            me.resetload();
                        },1000);
                    }else{
                        // 锁定
                        me.lock();
                        // 无数据
                        me.noData();
                        // 即使加载出错，也得重置
                        me.resetload();
                    }
                });
            $scope.page++;
        };

        /**
         * 下拉刷新
         */
        $scope.page =  1;
        $scope.init = function (url,status_id) {
            setTimeout(function(){
                // 每次数据加载完，必须重置
                $('.content').dropload({
                    scrollArea : window,
                    loadUpFn: function (me) {
                        // $scope.page = 1;
                        if(url == 'order/get_order_by_page/'){
                            $scope.loadData('order/get_order_by_page/', status_id, me);
                        }else{
                            $scope.loadData('order/get_can_evaluate_order_by_page/', status_id, me);
                        }

                    },
                    loadDownFn : function(me){
                        if(url == 'order/get_order_by_page/'){
                            $scope.loadData('order/get_order_by_page/', status_id, me);
                        }else{
                            $scope.loadData('order/get_can_evaluate_order_by_page/', status_id, me);
                        }
                    }
                });
            },1);

        };
        $scope.init('order/get_order_by_page/', $scope.status_id);

        /**
         * 根据订单状态显示对应订单数量
         */
        
        ajax.req('POST', 'order/get_order_list_nav')
            .then(function (response) {
                if(response){
                    $scope.orderStatus = response;
                }
            });

        $scope.get_order_list = function (status) {
            $scope.orderAll = [];
            $scope.subOrder = [];
            $('.dropload-down').remove();
            $('.load_done').css('display', 'none');
            $scope.page = 1;
            switch (status){
                case 'all':
                    $scope.status_id = '';
                    break;
                case 'not_paid':
                    $scope.status_id = 10;
                    break;
                case 'paid':
                    $scope.status_id = 20;
                    break;
                case 'delivered':
                    $scope.status_id = 30;
                    break;
                case 'sentback':
                    $scope.status_id = 40;
                    break;
                case 'assaying':
                    $scope.status_id = 50;
                    break;
                case 'finished':
                    $scope.status_id = 60;
                    break;
                case 'refunding':
                    $scope.status_id = 70;
                    break;
                case 'refunded':
                    $scope.status_id = 80;
                    break;
                case 'can_evaluate':
                    $scope.status_id = '';
                    break;
            }

            if(status == 'can_evaluate'){
                $scope.init('order/get_can_evaluate_order_by_page/', $scope.status_id);
            }else{
                $scope.init('order/get_order_by_page/', $scope.status_id);
            }
        };
        /**
         * 评价晒单
         */
        $scope.evaluate_order = function (orderlist_id) {
            window.location.href = SITE_URL + 'weixin/user/evaluation/'+orderlist_id;
        };

        /**
         * 再次购买
         */
        $scope.add_shopping_cart = function (orderlist) {
            angular.forEach(orderlist.sub_orders, function (data, index) {
                var popToast;
                ajax.req('POST', 'shopping_cart/add', {
                    commodity_id : data.commodity_id,
                    amount : data.amount
                }).then(function(response){
                        if(!popToast || popToast&&!popToast.toastBox){
                            popToast=new Toast('添加至购物车成功',{
                                "onHid":function(e){
                                    e.destroy();
                                }
                            });
                        }
                        popToast.show();
                        window.location.href = SITE_URL + 'weixin/index/shopping_cart';
                });
            });
        };

        $scope.exchange_again = function(commodity_id) {
            window.location.href = SITE_URL + 'weixin/index/commodity_detail/' + commodity_id;
        };
        /**
         * 去付款
         */
        $scope.ids = [];
        $scope.pay = function (order_id) {
            $scope.order_detail(order_id);
        };
        


        //定义exmobi返回
        $scope.back = function () {
            history.go(-1);
        };

        /**
         * 导航
         */

        [].slice.call(document.querySelectorAll('.tabbar')).forEach(function(tabbar){
            tabbar.onclick=function(e){
                [].slice.call(tabbar.querySelectorAll('.tab')).forEach(function(tab) {
                    tab.classList.remove("active");
                });
                e.target.classList.add("active");
            }
        });

        /**
         * 查看商品详情
         */
        $scope.commodity_detail = function (commodity_id) {
            window.location.href = SITE_URL + 'weixin/index/commodity_detail/'+ commodity_id;
        };

        /**
         * 查看订单详情
         */
        $scope.order_detail = function(order_id){
            window.location.href = SITE_URL + 'weixin/index/order_detail/'+ order_id;
        };

        /**
         * 查看物流信息
         */
        $scope.check_logistics = function(order_id){
            window.location.href = SITE_URL + 'weixin/index/logistics_details/'+ order_id;
        }
    }]);
