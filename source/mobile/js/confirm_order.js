angular.module('app')
    .controller('confirmOrderCtrl', ['$rootScope', '$scope', 'ajax', '$filter', function($rootScope, $scope, ajax, $filter){
        //初始化
        var singlePage = new Page({
            "onLoad":function(e){
                //var targetPageId;
                if(e.isRoot){

                }else{

                }
            }
        });
        $scope.discount = [];
        $scope.total_price = 0;
        $scope.settlement = [];
        // 微信支付JSAPI参数
        $scope.js_api_parameters = {};
        // 下单成功之后的订单ID
        $scope.order_id = 0;
        $scope.total_price = 0;

        //监听购物车IDS，获取数据
        $scope.$watch('ids', function(nv, ov){
            var url = '';
            if ($scope.is_point == 1){
                url = 'weixin/index/get_point_commodity';
            }else{
                url = 'weixin/index/get_order_settlement';
            }
            ajax.req('POST', url, {ids : nv})
                .then(function(response){
                    if (response.success){
                        $scope.settlement = response.data;
                        $scope.total_price = $filter('sum_price')($scope.settlement);
                    }else{
                        window.location.href = SITE_URL+'weixin/user/order_list';
                    }
                });
        });

        //显示地址
        $scope.show_address = function(id){
            $('#order_header').hide();
            singlePage.open(id);
        };

        //选择地址
        $scope.select_address = function(address_info){
            $scope.default_address = address_info;
            singlePage.close('#page_address');
            $('#order_header').show();
        };

        //显示我的优惠券
        $scope.show_discount = function(id){
            $('#order_header').hide();
            singlePage.open(id);
            document.body.scrollTop = document.documentElement.scrollTop = 0;
        };

        //选择优惠券
        $scope.select_discount = function(discount){
            if (discount.condition <= $scope.total_price){
                $scope.discount = discount;
                singlePage.close('#page_discount');
            }
        };
        //弹窗提示函数
        var popToast;
        $scope.show_popToast = function (msg) {
            if(!popToast || popToast&&!popToast.toastBox){
                popToast=new Toast(msg,{
                    "onHid":function(e){
                        e.destroy();
                    }
                });
            }
            popToast.show();
        };
        //付款
        $scope.pay = function(ids){
            if ($scope.order_id != 0 && $scope.settlement[0].is_point != 1) {
                // 已经下单，直接调用支付
                $scope.get_wechat_pay_js_api_parameters();
            }else {
                // 还未下单
                if(!ids){
                    return false;
                }
                var data = {
                    address_id : $scope.default_address.id,
                    ids : ids,
                    message : $scope.message || '',
                    is_point_flag : $scope.settlement[0].is_point,
                    payment_id: 1,
                    user_discount_coupon_id : $scope.discount.id,
                    terminal_type : 2
                };
                ajax.req('POST', 'order/add', data)
                    .then(function(response){
                        if (response.success){
                            if (data.is_point_flag == 1){
                                $scope.show_popToast("兑换成功");
                                window.location.href = SITE_URL+"weixin/index/pay_status/"+response.insert_id;
                            }else if (data.is_point_flag == 0){
                                $scope.show_popToast("创建订单成功，正在调起微信支付");
                                $scope.order_id = response.insert_id;
                                $scope.get_wechat_pay_js_api_parameters();
                            }else{
                                $scope.show_popToast("服务器异常，请到订单中心完成支付");
                            }
                        }else {
                            $scope.order_id = 0;
                            $scope.show_popToast(response.msg);
                        }
                    });
            }
        };

        //关闭page页面
        $scope.close_page = function(id, animation){
            singlePage.close(id, animation);
            $('#order_header').show();
        };

        // 获取微信支付参数
        $scope.get_wechat_pay_js_api_parameters = function () {
            ajax.req('POST', 'order/get_wechat_pay_js_api_parameters', {order_id: $scope.order_id})
                .then(function (response) {
                    if(response.success) {
                        // 调起微信支付
                        $scope.js_api_parameters = response.js_api_parameters;
                        $scope.callpay();
                    }else {
                        $scope.show_popToast(response.msg);
                    }
                });
        };

        // 调起微信支付
        $scope.js_api_call = function () {
            var js_params = JSON.parse($scope.js_api_parameters);
            WeixinJSBridge.invoke(
                'getBrandWCPayRequest',
                {
                    'appId':js_params.appId,
                    'timeStamp':js_params.timeStamp,
                    'nonceStr':js_params.nonceStr,
                    'package':js_params.package,
                    'signType':js_params.signType,
                    'paySign':js_params.paySign
                },
                function(res){
                    //WeixinJSBridge.log(res);
                    var popToast;
                    if(res.err_msg == "get_brand_wcpay_request:ok") {
                        // 跳转到订单详情页面
                        window.location.href = SITE_URL+"weixin/index/pay_status/"+$scope.order_id;
                    }else if (res.err_msg == "get_brand_wcpay_request:cancel") {
                        // 用户取消支付
                        $scope.show_popToast('您已取消支付，请尽快完成订单支付');
                    }else {
                        // 支付失败
                        $scope.show_popToast('系统繁忙，支付失败');
                        console.log(res);
                    }
                }
            );
        };
        $scope.callpay = function () {
            if (typeof WeixinJSBridge == "undefined"){
                if( document.addEventListener ){
                    document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                }else if (document.attachEvent){
                    document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                    document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                }
            }else{
                $scope.js_api_call();
            }
        };

        // 重写$rootScope中的返回按钮事件
        $scope.back = function () {
            if ($scope.order_id != 0) {
                // 已经下单
                window.location.href = SITE_URL+"weixin/index/order_detail/"+$scope.order_id;
            }else {
                $rootScope.back();
            }
            $('#order_header').show();
        };
        //获取地址信息
        $scope.default_address = [];
        $scope.address_infos = [];
        ajax.req('POST', 'user/show_address')
            .then(function (data) {
                if(data.success){
                    $scope.address_infos = data.data;
                    for (var i = 0; i < data.data.length; i++){
                        if (data.data[i].default === '1'){
                            $scope.default_address = data.data[i];
                            break;
                        }
                    }
                }else{
                    var popConfirm=new Alert("您还没有填写地址信息，请到个人中心-收货地址添加地址信息",{
                        onClickOk:function(e){
                            window.location.href = SITE_URL+"weixin/user/receipt_address";
                        },onClickCancel:function(e){
                            $scope.back()
                            e.hide();
                        }
                    });
                    $('.alert-handler > a:nth-child(1)').text('自己去');
                    $('.alert-handler > a:nth-child(2)').text('直接去');
                    popConfirm.show();
                }
            });
        //获取优惠券
        $scope.discount_coupons = [];
        ajax.req('POST', 'user/get_discount_coupon_by_user_id')
            .then(function (data) {
                if(data.success){
                    $scope.discount_coupons = data.data;
                }
            });
    }]);
