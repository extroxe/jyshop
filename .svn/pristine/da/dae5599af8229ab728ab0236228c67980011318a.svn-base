<article ng-controller="evaluationListCtrl" ng-init="commodity_id = '<?php echo isset($commodity_id) && intval($commodity_id) > 0 ? $commodity_id : ''; ?>'">
    <div class="titlebar">
        <a class="titlebar-button" ng-click="back()">
            <i style="color: #eee" class="icon icon-arrowleft back_btn"></i>
        </a>
        <h1><?php echo $title;?></h1>
    </div>
    <div class="group evaluate_box" ng-repeat="commodity_list in commodity">

        <div class="inputbox review_img">
            <img ng-src="{{ SITE_URL + commodity_list.user_avatar_path }}">
            <span class="username">{{commodity_list.user_nickname}}</span>
            <textarea class="textareabackgroung" disabled >{{commodity_list.content}}</textarea>
        </div>
        <div class="inputbox load_img" style="padding: 15px" ng-if="commodity_list.evaluation_pic != undefined">
            <div class="thumb" ng-repeat="item in commodity_list.evaluation_pic" style="position:relative;">
                <!-- 采用angular循环的方式，对存入thumb的图片进行展示-->
                <img class="imgloaded" ng-src="{{item.path}}"/>
            </div>

        </div>
        <div class="inputbox underline topline score">
            <label class="inputbox-left">商品评分</label>
            <div class="inputbox-right inputbox star_box">
                <div class="star">
                    <img ng-repeat="stars in commodity_list.star" class="star1" ng-src="{{stars.star_src}}">
                </div>
            </div>
        </div>
    </div>
    <div ng-if="flag == true" style="text-align: center;
    margin-top: 100px;">此商品暂时没有评价！</div>
</article>

