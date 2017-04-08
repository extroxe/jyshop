/**
 * Created by sailwish001 on 2016/11/18.
 */
app.controller('integralCtrl', ['$scope', '_jiyin', 'dataToURL', '$stateParams', '$state', function ($scope, _jiyin, dataToURL, $stateParams, $state) {
    $scope.integralList = {};
    $scope.inputPage = 1;
    $scope.reList = {};
    $scope.list = {};
    $scope.isPoint = true;
    $scope.picList = [];
    $scope.keyword = '';
    $scope.urlPC = '';
    $scope.urlWC = '';
    $scope.open = false;

    /*
     搜索
     */
    $scope.search = function () {
        $scope.inputPage = 1;
        _jiyin.dataPost('admin/commodity_admin/paginate/'+$scope.inputPage+'/10', dataToURL({
            keyword: $scope.keyword,
            is_point: 1
        }))
            .then(function(result){
                $scope.integralList = result.data;
                $scope.totalPage = result.total_page;
            });
    };

    $("#search").keydown(function (e) {
        if(e.keyCode==13) {
            $scope.search();
        }
    });

    $scope.getCo = function () {
        _jiyin.dataPost('admin/commodity_admin/get_all_commodity_by_is_point', dataToURL({
            is_point: 1,
            keyword: $scope.keyword
        }))
            .then(function (result) {
                $scope.coList = result.data;
            })
    };
    $scope.getList = function () {
        _jiyin.dataPost('commodity/get_commodity_recommend_commodity', dataToURL({commodity_id: $scope.reList.commodity_id}))
            .then(function(result){
                if(result.success = true){
                    $scope.cList = result.data;
                }
            });
    };
    $scope.recommon = function (data) {
        $scope.reList.commodity_id = data.id;
        $scope.getCo();
        $scope.getList();
        $("#recommon").modal('show');
    };
    $scope.addRe = function () {
        $scope.showRe = true;
    };
    $scope.sure = function () {
        $scope.showRe = false;
        _jiyin.dataPost('admin/commodity_admin/add_commodity_recommend_commodity', dataToURL({
            commodity_id: $scope.reList.commodity_id,
            recommend_commodity_id: $scope.reList.recommend_commodity_id
        }))
            .then(function (result){
                if(result.success == true){
                    _jiyin.msg('s', '增加成功');
                    $scope.getList();
                    $("#recommon").modal('hide');
                }else {
                    _jiyin.msg('e', result.msg);
                }
            });

    };
    $scope.remove = function (data) {
        if(confirm('确认删除此相关推荐吗?')){
            _jiyin.dataPost('admin/commodity_admin/delete_commodity_recommend_commodity', dataToURL({id: data.id}))
                .then(function(result){
                    if(result.success = true){
                        _jiyin.msg('s', '删除成功');
                        $scope.getList();
                    }else{
                        _jiyin.msg('e', result.msg);
                    }
                });
        }
    };

    $scope.show=function(){
        $scope.title = '增加';
        $scope.ael = 'add';
        $scope.list = {};
        $scope.url = false;
        $scope.getThumbnail();
        $scope.getStatus();
        $scope.getType();
        $scope.getCate();
        $scope.getLevel();
        $scope.open = true;
        $scope.$broadcast('open', {
            open: $scope.open
        });
        $("#add").modal('show');
    };
    $scope.edit=function(data){
        $scope.title = '编辑';
        $scope.ael = 'edit';
        $scope.url = true;
        $scope.urlPC = 'http://jyshop-dev.sailwish.com/commodity/index/' + data.id;
        $scope.urlWC = 'http://jyshop-dev.sailwish.com/weixin/index/commodity_detail/' + data.id;
        $scope.list = data;
        $scope.list.points = parseInt($scope.list.points);
        $scope.list.sales_volume = parseInt($scope.list.sales_volume);
        $scope.getThumbnail();
        $scope.getStatus();
        $scope.getType();
        $scope.getCate();
        $scope.getLevel();
        $scope.open = true;
        $scope.$broadcast('open', {
            open: $scope.open
        });
        $("#add").modal('show');
    };

    /**
     * 获取缩略图
     */
    $scope.getThumbnail = function(){
        if($scope.list.id){
            _jiyin.dataPost('admin/commodity_admin/show_thumbnail', dataToURL({commodity_id: $scope.list.id}))
                .then(function (result) {
                    $scope.picList = result.data;
                });
        }
    };
    /**
     * 获取商品状态
     */
    $scope.getStatus = function () {
        _jiyin.dataPost('admin/system_code_admin/get_by_type/commodity_status')
            .then(function (result) {
                $scope.statusList = result;
            })
    };
    /**
     * 获取商品类型
     */
    $scope.getType = function () {
        _jiyin.dataPost('admin/system_code_admin/get_by_type/commodity_type')
            .then(function (result) {
                $scope.typeList = result;
            });
    };
    /**
     * 获取等级
     */
    $scope.getLevel = function () {
        _jiyin.dataGet('admin/level_admin/get_level')
            .then(function(result){
                $scope.levelList = result.data;
            });
    };
    /**
     * 获取商品分类
     */
    $scope.getCate = function () {
        _jiyin.dataPost('admin/category_admin/get_categories')
            .then(function (result) {
                $scope.cateList = result.data;
            });
    };

    $scope.$on('attachment_ids', function(event, attachment_ids) {
        if (!$scope.list.attachment_ids){
            $scope.list.attachment_ids = [];
        }
        $scope.list.attachment_ids.push(attachment_ids);
    });
    $scope.$on('path', function(event, path) {
        if ($scope.picList == null){
            $scope.picList = [];
        }
        var thumb = [];
        thumb['path'] = path;
        $scope.picList.push(thumb);
    });
    /**
     * 删除图片
     */
    $scope.removePic = function (id, index) {
        if(confirm('确定删除该图片吗?')){
            if(!id){
                $scope.picList.splice(index, 1);
            }else{
                _jiyin.dataPost('admin/commodity_admin/delete_thumbnail',dataToURL({id: id}))
                    .then(function (result) {
                        $scope.picList.splice(index, 1);
                        $scope.getThumbnail();
                    });
            }
        }
    };

    $scope.ok = function () {
        if(!$scope.list.name){
            _jiyin.msg('e','商品名称不能为空');
            return ;
        }
        if(!$scope.list.number){
            _jiyin.msg('e','商品编号不能为空');
            return ;
        }
        if(!$scope.list.price){
            _jiyin.msg('e','商品价格不能为空');
            return ;
        }
        if($scope.isPoint == false){
            if(!$scope.list.points){
                _jiyin.msg('e','商品购买获得积分不能为空');
                return ;
            }
        }
        if(!$scope.list.category_id){
            _jiyin.msg('e','商品分类不能为空');
            return ;
        }
        if(!$scope.list.sales_volume){
            _jiyin.msg('e','商品销量不能为空');
            return ;
        }
        if(!$scope.list.status_id){
            _jiyin.msg('e','商品状态不能为空');
            return ;
        }
        if(!$scope.list.type_id){
            _jiyin.msg('e','商品类型不能为空');
            return ;
        }
        if(!$scope.list.detail){
            _jiyin.msg('e','商品详情不能为空');
            return ;
        }
        if(!$scope.list.attachment_ids && $scope.ael == 'add'){
            _jiyin.msg('e','还没有上传图片');
            return ;
        }
        if ($scope.list.attachment_ids){
            $scope.list.attachment_ids = $scope.list.attachment_ids.toString();
        }
        if($scope.ael == 'add'){
            $scope.list.is_point = 1;
            _jiyin.dataPost('admin/commodity_admin/add',dataToURL($scope.list))
                .then(function (result) {
                    if(result.success == true){
                        _jiyin.msg('s','添加成功');
                        $scope.getData();
                        $("#add").modal('hide');
                    }else{
                        _jiyin.msg('e', result.msg);
                    }
                });
        }else{
            _jiyin.dataPost('admin/commodity_admin/update',dataToURL($scope.list))
                .then(function (result) {
                    if(result.success == true){
                        _jiyin.msg('s','修改成功');
                        $scope.getData();
                        $("#add").modal('hide');
                    }else{
                        _jiyin.msg('e', result.msg);
                    }
                });
        }
    };

    /**
     * 获取数据
     */
    $scope.getData = function(){
        _jiyin.dataPost('admin/commodity_admin/paginate/'+$scope.inputPage+'/10',dataToURL({
            is_point: 1,
            keyword: $scope.keyword
        }))
            .then(function(result){
                $scope.integralList = result.data;
                $scope.totalPage = result.total_page;
            });
    };
    $scope.getData();
    /**
     * 增加
     */
    /*$scope.addList = function () {
        $scope.title = '增加数据';
        _jiyin.modal({
            tempUrl : '/source/admin/tpl/modal/modal-commoClass.html',
            tempCtrl : 'modalCommoClassCtrl',
            ok : $scope.add,
            size : 'lg',
            params : {
                title: $scope.title,
                commonclassList: {},
                ael: 'add',
                isPoint: true
            }
        });
    };
    $scope.add = function (infoList) {
        infoList.is_point = 1;
        _jiyin.dataPost('admin/commodity_admin/add',dataToURL(infoList))
            .then(function (result) {
                if(result.success == true){
                    _jiyin.msg('s','添加成功');
                    $scope.getData();
                }else{
                    _jiyin.msg('e','添加失败');
                }
            });
    };
    /!**
     * 编辑
     *!/
    $scope.editList = function (data) {
        $scope.title = '编辑数据';
        _jiyin.modal({
            tempUrl : '/source/admin/tpl/modal/modal-commoClass.html',
            tempCtrl : 'modalCommoClassCtrl',
            ok : $scope.edit,
            size : 'lg',
            params : {
                title: $scope.title,
                commonclassList: data,
                ael: 'edit',
                isPoint: true
            }
        });
    };
    $scope.edit = function (infoList) {
        _jiyin.dataPost('admin/commodity_admin/update',dataToURL(infoList))
            .then(function (result) {
                if(result.success == true){
                    _jiyin.msg('s','修改成功');
                    $scope.getData();
                }else{
                    _jiyin.msg('e','修改失败');
                }
            });
    };*/

    /**
     * 删除信息
     * @param data
     */
    $scope.deleteData = function(data){
        if(confirm('确认删除这条数据吗?')){
            _jiyin.dataPost('admin/commodity_admin/delete',dataToURL({ id: data.id}))
                .then(function(result){
                    if(result.success == true){
                        _jiyin.msg('s', '删除成功');
                        $scope.getData();
                    }else {
                        _jiyin.msg('e', result.msg);
                    }
                });
        }
    };

    $scope.lookEva = function (data) {
        $state.go('app.evaluate', {commodity_id: data.id,type: 'int'});
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

app.controller('intFileUploadCtrl', ['$scope', 'FileUploader', '_jiyin', 'dataToURL', function($scope, FileUploader, _jiyin, dataToURL) {
    $scope.attachment_ids = [];
    var uploader = $scope.uploader = new FileUploader({
        url: SITE_URL + 'attachment/up_attachment',
        removeAfterUpload: true,
        queueLimit: 5
    });
    $scope.$on('open', function (event, args) {
        if(args.open == true){
            uploader.clearQueue();
        }
    });
    // FILTERS
    uploader.filters.push({
        name: 'customFilter',
        fn: function(item /*{File|FileLikeObject}*/ , options) {
            return this.queue.length < 6;
        }
    });

    $scope.upload = function(item){
        _jiyin.fileMd5(item._file).then(function (result) {
            _jiyin.dataPost('attachment/check_md5', dataToURL({md5_code: result.md5Code}))
                .then(function (result) {
                    if(result.exist == true){
                        $scope.attachment_ids = [];
                        $scope.attachment_ids.push(result.attachment_id);
                        $scope.$emit('attachment_ids', $scope.attachment_ids);
                        $scope.$emit('path', result.path);
                        item.file.size = item._file.size;
                        item.progress = 100;
                        item.isSuccess = true;
                        item.isUploaded = true;
                        item.uploader.progress += 100/uploader.queue.length;
                    }else{
                        item.upload();
                    }
                });
        });
    };
    $scope.uploadAll = function () {
        angular.forEach(uploader.queue, function (data, index) {
            _jiyin.fileMd5(data._file).then(function (result) {
                _jiyin.dataPost('attachment/check_md5', dataToURL({md5_code: result.md5Code}))
                    .then(function (result) {
                        if(result.exist == true){
                            $scope.attachment_ids = [];
                            $scope.attachment_ids.push(result.attachment_id);
                            $scope.$emit('attachment_ids', $scope.attachment_ids);
                            $scope.$emit('path', result.path);
                            data.file.size = data._file.size;
                            data.progress = 100;
                            data.isSuccess = true;
                            data.isUploaded = true;
                            uploader.progress += 100/uploader.queue.length;
                        }else{
                            data.upload();
                        }
                    });
            });
        });
    };
    uploader.onSuccessItem = function(fileItem, response, status, headers) {
        $scope.attachment_ids = [];
        $scope.attachment_ids.push(response.attachment_id);
        $scope.$emit('attachment_ids', $scope.attachment_ids);
        $scope.$emit('path', response.url);
    };
    $scope.$on('clearQueue', function() {
        uploader.clearQueue();
    });
}]);