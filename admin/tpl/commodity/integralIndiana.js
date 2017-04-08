/**
 * Created by sailwish001 on 2016/11/18.
 */
app.controller('indianaCtrl', ['$scope', '_jiyin', 'dataToURL', '$stateParams', '$state', function ($scope, _jiyin, dataToURL, $stateParams, $state) {
    $scope.indianaList = {};
    $scope.inputPage = 1;

    /**
     * 获取商品
     */
    $scope.getCommo = function(){
        _jiyin.dataPost('admin/commodity_admin/get_all_commodity_by_is_point',dataToURL({
            is_point: 1
        }))
            .then(function(result){
                $scope.commoList = result.data;
            });
    };
    $scope.getCommo();
    /**
     * 获取数据
     */
    $scope.getData = function(){
        _jiyin.dataGet('admin/integral_indiana_admin/get_all_indiana_info/'+$scope.inputPage+'/10')
            .then(function(result){
                $scope.indianaList = result.data;
                $scope.totalPage = result.total_page;
            });
    };
    $scope.getData();
    /**
     * 增加
     */
    $scope.addInfo = function () {
        $scope.title = '增加数据';
        $scope.add = true;
        $scope.list = {};
        $('#partyModal').modal('show');
    };
    /**
     * 编辑
     */
    $scope.editList = function (data) {
        $scope.title = '编辑数据';
        $scope.add = false;
        $scope.list = data;
        $('#partyModal').modal('show');
    };
    $scope.ok = function () {
        if(!$scope.list.commodity_id || !$scope.list.total_points || !$scope.list.amount_bet){
            _jiyin.msg('e','带*号为必填，请先填写必填项');
            return;
        }
        if($scope.add == true){
            _jiyin.dataPost('admin/integral_indiana_admin/add',dataToURL($scope.list))
                .then(function (result) {
                    if(result.success == true){
                        _jiyin.msg('s','添加成功');
                        $scope.getData();
                        $('#partyModal').modal('hide');
                    }else{
                        _jiyin.msg('e', result.msg);
                    }
                });
        }else if($scope.add == false){
            _jiyin.dataPost('admin/integral_indiana_admin/update',dataToURL($scope.list))
                .then(function (result) {
                    if(result.success == true){
                        _jiyin.msg('s','修改成功');
                        $scope.getData();
                        $('#partyModal').modal('hide');
                    }else{
                        _jiyin.msg('e', result.msg);
                    }
                });
        }
    };
    /**
     * 删除信息
     * @param data
     */
    $scope.deleteData = function(data){
        if(confirm('确认删除这条数据吗?')){
            _jiyin.dataPost('admin/integral_indiana_admin/delete',dataToURL({ id: data.id}))
                .then(function(result){
                    if(result.success == true){
                        _jiyin.msg('s', '删除成功');
                        $scope.getData();
                    }else{
                        _jiyin.msg('e', result.msg);
                    }
                });
        }
    };
    /**
     * 查看用户
     */
    $scope.look = function (data) {
        $state.go('app.indianaUser', {id: data.id});
    };
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