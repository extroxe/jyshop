/**
 * Created by sailwish001 on 2016/11/18.
 */
app.controller('integraCtrl', ['$scope', '_jiyin', 'dataToURL', '$stateParams', '$state', function ($scope, _jiyin, dataToURL, $stateParams, $state) {
    $scope.integraList = {};
    $scope.inputPage = 1;
    $scope.keyword = '';

    /*
     搜索
     */
    $scope.search = function () {
        $scope.inputPage = 1;
        _jiyin.dataPost('admin/order_admin/paginate/'+$scope.inputPage+'/10', dataToURL({
            is_point: 1,
            keyword: $scope.keyword
        }))
            .then(function(result){
                $scope.integraList = result.data;
                $scope.totalPage = result.total_page;
            });
    };

    $("#search").keydown(function (e) {
        if(e.keyCode==13) {
            $scope.search();
        }
    });
    /**
     * 获取数据
     */
    $scope.getData = function(){
        _jiyin.dataPost('admin/order_admin/paginate/'+$scope.inputPage+'/10', dataToURL({
            is_point: 1,
            keyword: $scope.keyword
        }))
            .then(function(result){
                $scope.integraList = result.data;
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
                isPoint: true
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
        $state.go('app.subOrderList', {id: data.id, type: 'int'});
    };
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
     * 删除信息
     * @param data
     */
    $scope.deleteData = function(data){
        if(confirm('确认删除这条数据吗?')){
            _jiyin.data('',dataToURL({ id: data.id}))
                .then(function(result){
                    _jiyin.msg('s', '删除成功');
                    $scope.getData();
                });
        }
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