angular.module('app')
    .controller('orderDetailCtrl', ['$scope', 'ajax', function ($scope, ajax) {
        $scope.order_id = 0;
        $scope.order = {};
        $scope.commodity_total_amount = 0;
        $scope.js_api_parameters = {};

        $scope.$watch('order_id', function (nv) {
            if (nv) {
                $scope.order_id = nv;
                ajax.req('POST', 'order/get_order_by_id', {id : nv}, true)
                    .then(function (data) {
                        if (data.success){
                            $scope.order = data.data;
                            for (var i = 0; i < data.data.sub_orders.length; i++){
                                $scope.commodity_total_amount += parseInt(data.data.sub_orders[i].amount);
                            }
                        }
                    });
            }else {
                window.location.href = SITE_URL + '/weixin/index/show_404';
            }
        });

        // 评价晒单按钮响应事件
        $scope.evaluate_order = function () {
            window.location.href = SITE_URL + 'weixin/user/evaluation/'+$scope.order_id;
        };

        //再次兑换
        $scope.exchange_again = function (commodity_id) {
            window.location.href = SITE_URL + 'weixin/index/commodity_detail/' + commodity_id;
        };
        // 再次购买按钮响应事件
        $scope.purchase_again = function () {
            var add_times = 0;
            angular.forEach($scope.order.sub_orders, function (data, index, array) {
                var popToast;
                ajax.req('POST', 'shopping_cart/add', {
                    commodity_id : data.commodity_id,
                    amount : data.amount
                }).then(function(response){
                    if (response.success){
                        add_times++;
                        if (add_times == $scope.order.sub_orders.length) {
                            if(!popToast || popToast&&!popToast.toastBox){
                                popToast=new Toast('添加至购物车成功',{
                                    "onHid":function(e){
                                        e.destroy();
                                    }
                                });
                            }
                            popToast.show();
                            window.location.href = SITE_URL + 'weixin/index/shopping_cart';
                        }
                    }else{
                        if(!popToast || popToast&&!popToast.toastBox){
                            popToast=new Toast(response.msg,{
                                "onHid":function(e){
                                    e.destroy();
                                }
                            });
                        }
                        popToast.show();
                    }
                });
            });
        };

        // 立即支付按钮响应事件
        $scope.pay = function () {
            $scope.get_wechat_pay_js_api_parameters();
        };

        // 获取微信支付参数
        $scope.get_wechat_pay_js_api_parameters = function () {
            ajax.req('POST', 'order/get_wechat_pay_js_api_parameters', {order_id: $scope.order_id})
                .then(function (response) {
                    var popToast;
                    if(response.success) {
                        // 调起微信支付
                        $scope.js_api_parameters = response.js_api_parameters;
                        $scope.callpay();
                    }else {
                        if(!popToast || popToast&&!popToast.toastBox){
                            popToast=new Toast(response.msg,{
                                "onHid":function(e){
                                    e.destroy();
                                }
                            });
                        }
                        popToast.show();
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
                        if(!popToast || popToast&&!popToast.toastBox){
                            popToast=new Toast('您已取消支付，请尽快完成订单支付',{
                                "onHid":function(e){
                                    e.destroy();
                                }
                            });
                        }
                        popToast.show();
                    }else {
                        // 支付失败
                        if(!popToast || popToast&&!popToast.toastBox){
                            popToast=new Toast('系统繁忙，支付失败',{
                                "onHid":function(e){
                                    e.destroy();
                                }
                            });
                        }
                        popToast.show();
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
        $scope.back = function () {
            history.go(-1);
        };
    }]);