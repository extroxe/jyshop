angular.module('app')
    .controller('commodityDetailCtrl', ['$scope', 'ajax', function($scope, ajax){
        //初始化轮播图
        var s1=new Slider("#carousel1",{
            "pagination":".slider-pagination",
            "loop":true,
            "autoplay": 5000
        });
        //popToast弹窗函数
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

        var img_height = parseFloat(screen.width)*0.8;
        $('.slider-container, .slider-container div, .slider-container img').css('height', img_height+'px');

        $scope.favorite_flag = false;

        //获取商品详情
        $scope.commodity = [];
        $scope.$watch('commodity.id', function(nv, ov){
            if (nv){
                $scope.commodity.id = nv;
                ajax.req('POST', 'index/get_commodity_by_id', {commodity_id : nv})
                    .then(function(response){
                        if(response){
                            $scope.commodity = response;
                        }
                    });

                ajax.req('POST', 'favorite/check_favorite_by_commodity_id', {commodity_id : nv})
                    .then(function(response){
                        if (response.success){
                            $scope.favorite_flag = true;
                        }else{
                            $scope.favorite_flag = false;
                        }
                    });
            }
        });

        //收藏
        $scope.favorite = function($event){
            if ($scope.favorite_flag){
                ajax.req('POST', 'favorite/delete_by_commodity_id', { commodity_id : $scope.commodity.id })
                    .then(function(response){
                        if(response.success){
                            $($event.target).removeClass('icon-heart-fill').addClass('icon-heart');
                            $scope.favorite_flag = !$scope.favorite_flag;
                            $scope.show_popToast("取消收藏成功");
                        }else {
                            $scope.show_popToast(response.msg);
                        }
                    });
            }else{
                ajax.req('POST', 'favorite/add', { commodity_id : $scope.commodity.id })
                    .then(function(response){
                        if(response.success){
                            $($event.target).removeClass('icon-heart').addClass('icon-heart-fill');
                            $scope.favorite_flag = !$scope.favorite_flag;
                            $scope.show_popToast("收藏成功");
                        }else {
                            $scope.show_popToast(response.msg);
                            if (response.timeout) {
                                setTimeout("location.href = SITE_URL + 'weixin/index/sign_in'", 1000);
                            }
                        }
                    });
            }
        };

        //获取购物车数量
        $scope.cart_num = 0;
        ajax.req('GET', 'shopping_cart/amount')
            .then(function(response){
                if (!response.success && response.timeout) {
                    // 购物车为空或未登录
                }else if (response){
                    $scope.cart_num = response;
                }
            });
        $scope.$watch('cart_num', function(nv, ov){
            if (nv > 99){
                $('.tip.shopping-cart').text('99+');
            }
        });

        //商品数量
        $scope.num = 1;
        $scope.$watch('num', function(nv, ov){
            if (nv < 1){
                $scope.num = 1;
            }else if (nv > 99) {
                $scope.num = 99;
            }
            if (nv > 1 && $scope.commodity.is_point == 1){
                $scope.num = 1;
                $scope.show_popToast("积分商品每次只能兑换一个");
            }
        });

        //商品数量加1
        $scope.add_num = function(){
            $scope.num += 1;
        };

        //商品数量减1
        $scope.sub_num = function(){
            $scope.num -= 1;
        };

        //加入购物车
        $scope.add_cart = function(){
            ajax.req('POST', 'shopping_cart/add', {
                commodity_id : $scope.commodity.id,
                amount : $scope.num
            }).then(function(response){
                if (response.success){
                    $scope.show_popToast(response.msg);
                    $scope.cart_num = parseInt($scope.cart_num) + $scope.num;
                }else {
                    $scope.show_popToast(response.msg);
                    if (response.timeout) {
                        setTimeout("location.href = SITE_URL + 'weixin/index/sign_in'", 1000);
                    }
                }
            });
        };

        //立即购买
        $scope.buy_directly = function(){
            ajax.req('POST', 'shopping_cart/add', {
                commodity_id : $scope.commodity.id,
                amount : $scope.num
            }).then(function(response){
                if (response.success){
                    window.location.href = SITE_URL+'weixin/index/confirm_order/'+response.insert_id;
                }else{
                    $scope.show_popToast(response.msg);
                    if (response.timeout) {
                        setTimeout("location.href = SITE_URL + 'weixin/index/sign_in'", 1000);
                    }
                }
            });
        };

        //兑换积分商品
        $scope.exchange = function () {
            ajax.req('GET', 'user/get_personal_info')
                .then(function(response){
                    if (response.success){
                        window.location.href = SITE_URL+'weixin/index/confirm_order/'+$scope.commodity.id+'/1';
                    }else{
                        $scope.show_popToast(response.msg);
                        if (response.timeout) {
                            setTimeout("location.href = SITE_URL + 'weixin/index/sign_in'", 1000);
                        }
                    }
                });
        };

        $scope.goShoppingCart = function () {
            ajax.req('GET', 'user/get_personal_info')
                .then(function(response){
                    if (response.success){
                        window.location.href = SITE_URL + 'weixin/index/shopping_cart';
                    }else{
                        $scope.show_popToast(response.msg);
                        if (response.timeout) {
                            setTimeout("location.href = SITE_URL + 'weixin/index/sign_in'", 1000);
                        }
                    }
            });
        };
        //查看商品评价
        $scope.commodity_evaluation = function () {
            window.location.href = SITE_URL + 'weixin/user/evaluation_list/'+ $scope.commodity.id;
        };

        //定义exmobi返回
        function back(){
            history.go(-1);
        }

    }]);
