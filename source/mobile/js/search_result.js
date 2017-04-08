angular.module('app')
    .controller('searchResultCtrl', ['$scope', 'ajax', function($scope, ajax){
        $scope.$watch('searchfor.flag', function (nv) {
            if (nv == 'key_words'){
                $scope.search_data = {
                    key_words : $scope.searchfor.key_words,
                    page : 1,
                    page_size : 10
                };
            }else if (nv == 'category'){
                $scope.search_data = {
                    category : $scope.searchfor.category,
                    page : 1,
                    page_size : 10
                };
            }
            ajax.req('POST', 'weixin/index/search', $scope.search_data).then(function(response){
                if (response.success){
                    $scope.commodities = response.data;
                }else{
                    var popToast;
                    if(!popToast || popToast&&!popToast.toastBox){
                        popToast=new Toast("没有此类商品",{
                            "onHid":function(e){
                                e.destroy();
                            }
                        });
                    }
                    popToast.show();
                }
            })
        });
        $scope.search = function(){
            ajax.req('POST', 'weixin/index/search', {
                key_words : $scope.search_words,
                page : 1,
                page_size : 10
            }).then(function(response){
                if (response.success){
                    $scope.commodities = response.data;
                }else{
                    var popToast;
                    if(!popToast || popToast&&!popToast.toastBox){
                        popToast=new Toast("没有此类商品",{
                            "onHid":function(e){
                                e.destroy();
                            }
                        });
                    }
                    popToast.show();
                }
            })
        };
        // 打开商品详情页面
        $scope.open_commodity = function (commodity_id) {
            window.location.href = SITE_URL + 'weixin/index/commodity_detail/' + commodity_id;
        }

        $('#empty_content').click(function () {
            $("#search_box").val("");
        });
    }]);
