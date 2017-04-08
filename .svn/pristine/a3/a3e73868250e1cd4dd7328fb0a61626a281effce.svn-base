<div ng-controller="commodityDetailCtrl">
    <header>
        <div class="titlebar">
            <a class="titlebar-button" ng-click="back()"><i class="icon size16 icon-arrowleft back_btn" style="margin-top: 0px;"></i></a>
            <h1 class="text-center"><?php echo $title; ?></h1>
            <a class="titlebar-button" href="javascript:void(0)" ng-click="goShoppingCart()">
                <img class="titlebar-shopping-cart" ng-src="{{ SITE_URL + 'source/mobile/img/icon/shopping-cart-o.png' }}" alt="">
                <span class="tip shopping-cart" ng-if="cart_num > 0">{{ cart_num }}</span>
            </a>
        </div>
    </header>
    <article ng-init="commodity.id = '<?php echo isset($commodity_id) && intval($commodity_id) > 0 ? $commodity_id : ''; ?>'">
        <div class="slider-container" id="carousel1">
            <div class="slider-wrapper">
                <?php if (!empty($thumbnails)) : ?>
                <?php foreach ($thumbnails as $row) : ?>
                <div class="slider-slide">
                    <img class="slide-banner" src="<?php echo site_url($row['pic_path']) ?>"/>
                </div>
                <?php endforeach; ?>
                <?php elseif (empty($thumbnails)) : ?>
                    <div class="slider-slide">
                        <img class="slide-banner" src="<?php echo site_url('source/mobile/img/photo.jpg'); ?>"/>
                    </div>
                <?php endif; ?>
            </div>
            <div class="slider-pagination"></div>
        </div>
        <div class="item-info">
            <div style="width: 80%;display: inline-block;">
                <p class="title-commodity">{{ commodity.name }}</p>
                <div class="intro-commodity" ng-bind-html="commodity.introduce | to_trusted"></div>
            </div>
            <div style="width: 18%;display: inline-block;text-align: center;">
                <i class="icon size32 icon-heart favorite" ng-click="favorite($event)" ng-if="!favorite_flag"></i>
                <i class="icon size32 icon-heart-fill favorite" ng-click="favorite($event)" ng-if="favorite_flag"></i>
            </div>
            <span class="price-span" ng-if="commodity.is_point === '0'"><font style="font-size: 18px;">¥</font> {{ commodity.flash_sale_price ? commodity.flash_sale_price : commodity.price }}</span>
            <span class="old-price" ng-if="commodity.flash_sale_price">{{ '¥'+commodity.price }}</span>
            <span class="price-span" ng-if="commodity.is_point === '1'">{{commodity.price | trans_int}}<span class="inline-span"> 积分</span></span>
            <span class="salves-item">销量：<span class="inline-span">{{ commodity.sales_volume }}笔</span></span>
        </div>
        <hr>
        <div class="item-info item-num">
            <span>购买数量：</span>
            <div class="numbox bordered margin8">
                <input type="button" class="button grayscale outline" value="-" ng-click="sub_num()" />
                <input type="number" class="input-text" ng-model="num"/>
                <input type="button" class="button grayscale outline" value="+" ng-click="add_num()" />
            </div>
        </div>
        <hr>
            <ul class="tabbar tabbar-line animated">
                <li class="tab active">
                    <label class="tab-label">商品详情</label>
                </li>
                <li class="tab" ng-click="commodity_evaluation()">
                    <label class="tab-label">商品评价</label>
                </li>
            </ul>
        <div class="detail" ng-bind-html="commodity.detail | to_trusted"></div>
    </article>
    <div class="commodity-detail-footer" ng-if="commodity.is_point == 0">
        <div class="add-cart" ng-click="add_cart()">
            <label>加入购物车</label>
        </div>
        <div class="buy-directly" ng-click="buy_directly()">
            <label>立即购买</label>
        </div>
    </div>
    <?php if ($point_enough_flag) : ?>
    <div class="commodity-detail-footer" ng-if="commodity.is_point == 1">
        <div class="exchange-directly" ng-click="exchange()">
            <label>立即兑换</label>
        </div>
    </div>
    <?php else : ?>
    <div class="commodity-detail-footer" ng-if="commodity.is_point == 1">
        <div class="exchange-directly" style="background-color: #b9b9b9;">
            <label>当前积分不够</label>
        </div>
    </div>
    <?php endif; ?>
</div>