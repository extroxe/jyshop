/**
 * Created by sailwish001 on 2016/11/23.
 */
app.controller('modalDisCtrl', ['$scope', '$modalInstance', '_jiyin', 'params', 'FileUploader', 'dataToURL',
    function ($scope, $modalInstance, _jiyin, params, FileUploader, dataToURL) {
        $scope.infoList = params.infoList;
        $scope.title = params.title;
        $scope.ael = params.ael;
        /**
         * 获取商品
         */
        $scope.getCommo = function () {
            _jiyin.dataPost('admin/commodity_admin/get_all_commodity_by_is_point')
                .then(function (result) {
                    $scope.commoList = result.data;
                })
        };
        $scope.getCommo();
        /**
         * 取消关闭
         */
        $scope.cancel = function() {
            $modalInstance.dismiss('cancel');
        };
        $scope.ok = function () {
            if(!$scope.infoList.commodity_id){
                _jiyin.msg('e','商品名称不能为空');
                return ;
            }
            if(!$scope.infoList.price){
                _jiyin.msg('e','商品价格不能为空');
                return ;
            }
            if(!$scope.infoList.start_time){
                _jiyin.msg('e','生效起始时间不能为空');
                return ;
            }
            if(!$scope.infoList.end_time){
                _jiyin.msg('e','生效结束时间不能为空');
                return ;
            }
            $modalInstance.close($scope.infoList);

        }
    }]);