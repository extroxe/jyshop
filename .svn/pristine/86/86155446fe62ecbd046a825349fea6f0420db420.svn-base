/**
 * Created by sailwish001 on 2016/12/2.
 */
app.controller('evaluateCtrl', ['$scope', '_jiyin', 'dataToURL', '$stateParams', function ($scope, _jiyin, dataToURL, $stateParams) {
    $scope.evaluateList = {};
    $scope.inputPage = 1;
    console.log($stateParams);
    if($stateParams.type == 'com'){
        $scope.flag = true;
    }else if($stateParams.type == 'int'){
        $scope.flag = false;
    }
    /**
     * 获取数据
     */
    $scope.getData = function(){
        _jiyin.dataGet('admin/commodity_admin/evaluation_paginate/'+$scope.inputPage+'/10/'+$stateParams.commodity_id+'')
            .then(function(result){
                if(result.total_page === false){
                    $scope.totalPage = 1;
                }else{
                    $scope.totalPage = result.total_page;
                }
                $scope.evaluateList = result.data;
            });
    };
    $scope.getData();
    /**
     * 下一页
     */
    $scope.nextPage = function(){
        if($scope.inputPage < $scope.totalPage){
            $scope.inputPage ++;
            $scope.getData();
        }else{
            _jiyin.msg('e', '当前是最后一页');
        }
    };
    /**
     * 上一页
     */
    $scope.previousPage = function(){
        if($scope.inputPage > 1){
            $scope.inputPage --;
            $scope.getData();
        }else{
            _jiyin.msg('e', '当前是第一页');
        }
    };
    /**
     * 第一页
     */
    $scope.firstPage = function () {
        $scope.inputPage = 1;
        $scope.getData();
    };
    /**
     * 最后一页
     */
    $scope.lastPage = function () {
        $scope.inputPage = $scope.totalPage;
        $scope.getData();
    };
    $scope.selectPage = function (page) {
        $scope.inputPage = page;
        $scope.getData();
    }
}]);