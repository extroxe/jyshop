<div ng-controller="searchResultCtrl">
    <header>
        <?php if(isset($key_words) && !empty($key_words)) : ?>
            <div class="titlebar" ng-init="searchfor.key_words = '<?php echo $key_words; ?>';searchfor.flag = 'key_words'">
        <?php elseif (isset($category) && !empty($category)) : ?>
            <div class="titlebar" ng-init="searchfor.category = '<?php echo $category; ?>';searchfor.flag = 'category'">
        <?php endif; ?>
            <a class="titlebar-button" ng-click="back()"><i class="icon size18 icon-arrowleft" style="color: #666666;"></i></a>
            <form class="inputbox lrmargin8 radius40 bordered" data-input="clear" ng-submit="search()">
                <i class="icon icon-search" ></i>
                <input type="search" id="search_box" placeholder="请输入搜索内容" class="search input-text" ng-model="search_words"/>
                <i class="icon icon-clear-fill color-placeholder" id="empty_content"></i>
            </form>
        </div>
    </header>
    <article>
        <div class="info-screen" style="display: none;">
            <div class="active">
                销量
                <div class="triangle">
                    <span class="triangle-up"></span>
                    <span class="triangle-down active"></span>
                </div>
            </div>
            <div>
                价格
                <div class="triangle">
                    <span class="triangle-up"></span>
                    <span class="triangle-down"></span>
                </div>
            </div>
            <div>
                时间
                <div class="triangle">
                    <span class="triangle-up"></span>
                    <span class="triangle-down"></span>
                </div>
            </div>
            <div>
                评论
                <div class="triangle">
                    <span class="triangle-up"></span>
                    <span class="triangle-down"></span>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="inputbox underline" ng-click="open_commodity(commodity.id)" style="display: flex;" ng-repeat="commodity in commodities">
                <a href="{{ SITE_URL + 'weixin/index/commodity_detail/' + commodity.id }}">
                    <img ng-if=" commodity.path !== null " ng-src="{{ SITE_URL + commodity.path }}" alt="">
                    <img ng-if=" commodity.path === null " ng-src="{{ SITE_URL + 'source/mobile/img/photo.jpg' }}" alt="">
                </a>
                <div class="info-box">
                    <div class="info-title">{{commodity.name}}</div>
                    <div class="info-group">
                        <div ng-if="commodity.is_point == 0" class="info-price">
                            <span>¥ </span>
                            <span>{{ commodity.flash_sale_price ? commodity.flash_sale_price : commodity.price }}</span>
                            <span class="old-price" ng-if="commodity.flash_sale_price">{{ '¥'+commodity.price}}</span>
                        </div>
                        <div ng-if="commodity.is_point == 1" class="info-price">
                            <span>{{commodity.price | trans_int}}积分</span>
                        </div>
                        <div class="info-bottom">
                            <span>{{commodity.evaluation_num}}条评价</span>
                            <span>{{commodity.good_evaluation * 100}}%好评</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </article>
</div>