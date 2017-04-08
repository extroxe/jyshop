angular.module('app')
    .controller('evaluationListCtrl', ['$rootScope','$scope', 'ajax', function ($rootScope,$scope, ajax) {
        $scope.img_src = []; //图片路径
        $scope.flag = true;
        $scope.star = [{num:1, star_src:SITE_URL + 'source/mobile/img/icon/collect.png'},
            {num:2, star_src:SITE_URL + 'source/mobile/img/icon/collect.png'},
            {num:3, star_src:SITE_URL + 'source/mobile/img/icon/collect.png'},
            {num:4, star_src:SITE_URL + 'source/mobile/img/icon/collect.png'},
            {num:5, star_src:SITE_URL + 'source/mobile/img/icon/collect.png'}
        ];
        /**
         * 获取商品id
         */
        $scope.$watch('commodity_id', function (nv, ov) {
            $scope.commodity_id = nv;
            if (nv) {
                $scope.order_id = nv;
                ajax.req('POST', 'commodity/evaluation_paginate/1/999/'+$scope.commodity_id)
                    .then(function (data) {

                        if (data.success) {
                            $scope.flag =false;
                            $scope.commodity = data.data;
                            angular.forEach($scope.commodity, function (commodity) {
                                if(commodity.id != null) {
                                    $scope.score = parseInt(commodity.score);
                                    // commodity.star = $scope.star.slice(0);
                                    commodity.star = angular.copy($scope.star);
                                    for(var i = 0; i < $scope.score; i++){
                                        commodity.star[i].star_src = SITE_URL + 'source/mobile/img/icon/collected.png'
                                    }
                                }
                            });
                        }
                    });
            }
        });
    }]);
/**
 * Created by sailwish009 on 2017/1/6.
 */
