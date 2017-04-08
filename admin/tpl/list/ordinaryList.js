/**
 * Created by sailwish001 on 2016/11/18.
 */
app.controller('orderCtrl', ['$scope', '_jiyin', 'dataToURL', '$stateParams', '$state', function ($scope, _jiyin, dataToURL, $stateParams, $state) {
    $scope.orderList = {};
    $scope.inputPage = 1;
    $scope.keyword = '';

    /*
     搜索
     */
    $scope.search = function () {
        $scope.inputPage = 1;
        _jiyin.dataPost('admin/order_admin/paginate/'+$scope.inputPage+'/10', dataToURL({
            is_point: 0,
            keyword: $scope.keyword
        }))
            .then(function(result){
                $scope.orderList = result.data;
                $scope.totalPage = result.total_page;
            });
    };

    $("#search").keydown(function (e) {
        if(e.keyCode==13) {
            $scope.search();
        }
    });
    /**
     * 物流信息
     */
    $scope.exinfo = function (data) {
        _jiyin.dataGet('admin/order_admin/show_express_info_by_order_id/'+data.id+'')
            .then(function (result) {
                if(result.success == true){
                    $("#list").modal('show');
                    $scope.exinfo = result.data.Traces;
                }else{
                    _jiyin.msg('e',result.msg);
                }
            });
    };
    $scope.cancel = function () {
        $("#list").modal('hide');
    };
    /**
     * 获取数据
     */
    $scope.getData = function(){
        _jiyin.dataPost('admin/order_admin/paginate/'+$scope.inputPage+'/10', dataToURL({
            is_point: 0,
            keyword: $scope.keyword
        }))
            .then(function(result){
                $scope.orderList = result.data;
                $scope.totalPage = result.total_page;
            });
    };
    $scope.getData();
    /**
     * 编辑
     */
    $scope.editList = function (data) {
        $scope.title = '编辑数据';
        _jiyin.modal({
            tempUrl : '/source/admin/tpl/modal/modal-orderList.html',
            tempCtrl : 'modalOrderCtrl',
            ok : $scope.edit,
            size : 'lg',
            params : {
                title: $scope.title,
                infoList: data,
                ael: 'edit',
                isPoint: false
            }
        });
    };
    $scope.edit = function (list) {
        _jiyin.dataPost('admin/order_admin/update', dataToURL(list))
            .then(function (result) {
                if(result.success == true){
                    _jiyin.msg('s',result.msg);
                    $scope.getData();
                }else{
                    _jiyin.msg('e',result.msg);
                }
            })
    };
    $scope.lookSub = function (data) {
        $state.go('app.subOrderList', {id: data.id, type: 'ord'});
    };
    /**
     * 下一页
     */
    $scope.nextPage = function(){
        if($scope.inputPage < $scope.totalPage){
            $scope.inputPage++;
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
            $scope.inputPage--;
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