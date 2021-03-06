<article ng-controller="orderList" ng-controller="orderList">
    <div class="titlebar">
        <a class="titlebar-button" ng-click="back()">
            <i style="color: #eee" class="icon size16 icon-arrowleft back_btn"></i>
        </a>
        <h1><?php echo $title; ?></h1>
    </div>

    <div class="group">
        <ul class="tabbar tabbar-line animated" style="background-color: #fff">
            <li class="tab active" ng-click="get_order_list('all')">
                <label class="tab-label">所有订单</label>
                <span ng-if="orderStatus.all">{{orderStatus.all}}</span>
            </li>
            <li class="tab"  ng-click="get_order_list('not_paid')">
                <label class="tab-label">待付款</label>
                <span ng-if="orderStatus.not_paid">{{orderStatus.not_paid}}</span>
            </li>
            <li class="tab"  ng-click="get_order_list('paid')">
                <label class="tab-label">待发货</label>
                <span ng-if="orderStatus.paid">{{orderStatus.paid}}</span>
            </li>
            <li class="tab"  ng-click="get_order_list('delivered')">
                <label class="tab-label">待收货</label>
                <span ng-if="orderStatus.delivered">{{orderStatus.delivered}}</span>
            </li>
            <li class="tab"  ng-click="get_order_list('sentback')">
                <label class="tab-label">已寄回</label>
                <span ng-if="orderStatus.sentback">{{orderStatus.sentback}}</span>
            </li>
            <li class="tab"  ng-click="get_order_list('assaying')">
                <label class="tab-label">检测中</label>
                <span ng-if="orderStatus.assaying">{{orderStatus.assaying}}</span>
            </li>
            <li class="tab"  ng-click="get_order_list('finished')">
                <label class="tab-label">已完成</label>
                <span ng-if="orderStatus.finished">{{orderStatus.finished}}</span>
            </li>
            <li class="tab"  ng-click="get_order_list('refunding')">
                <label class="tab-label">退款中</label>
                <span ng-if="orderStatus.refunding">{{orderStatus.refunding}}</span>
            </li>
            <li class="tab"  ng-click="get_order_list('refunded')">
                <label class="tab-label">已退款</label>
                <span ng-if="orderStatus.refunded">{{orderStatus.refunded}}</span>
            </li>
            <li class="tab"  ng-click="get_order_list('can_evaluate')">
                <label class="tab-label">待评价</label>
                <span ng-if="orderStatus.can_evaluate">{{orderStatus.can_evaluate}}</span>
            </li>
        </ul>
    </div>
    <div class="content">
        <div class="lists">
            <div class="group card" ng-repeat="orderlist in orderAll">
                <div class="order_number" ng-click="order_detail(orderlist.id)">
                    <label>订单编号：</label>
                    <span>{{orderlist.number}}</span>
                    <span ng-class="{'not_paid': not_paid, 'paid': paid}" ng-model="not_paid">{{orderlist.order_status_name}}</span>
                </div>
                <div class="inputbox sub_orders_list" ng-click="commodity_detail(sub_order.commodity_id)" ng-repeat="sub_order in orderlist.sub_orders">
                    <img ng-if="sub_order.thumbnail_path !== null" ng-src="{{ SITE_URL + sub_order.thumbnail_path }}">
                    <div class="info-box">
                        <div class="info-title" style="padding: 5px 20px;">
                            {{sub_order.commodity_name}}
                        </div>
                        <div class="info-price">
                            <span ng-if="orderlist.sub_orders[0].is_point == 0" style="color: #d9534f">¥ </span>
                            <span class="price" style="color: #d9534f">{{sub_order.price}}</span>
                            <span ng-if="orderlist.sub_orders[0].is_point == 1" style="color: #d9534f">积分</span>
                        </div>
                    </div>
                    <div class="item-num">
                        <span>x</span>
                        <span class="amount" style="font-size: 20px;">{{sub_order.amount}}</span>
                    </div>
                </div>

                <div class="commodity_info">
                    <label>共<span class="total_amount">{{orderlist.total_amount}}</span>件商品</label>
                    <label ng-if="orderlist.sub_orders[0].is_point == 0">合计：¥</label>
                    <label ng-if="orderlist.sub_orders[0].is_point == 1">合计：</label>
                    <label class="total_price">{{orderlist.total_price}}</label>
                    <label ng-if="orderlist.sub_orders[0].is_point == 1">积分</label>
                </div>
                <div class="group button_footer">
                    <input ng-if="orderlist.status_id == 60 || orderlist.status_id == 80 || orderlist.status_id == 90" type="button" class="review button lrpadding8" ng-click="evaluate_order(orderlist.id)" value="评价晒单"/>
                    <input ng-if="orderlist.status_id == 30" type="button" class="buy_again button lrpadding8" ng-click="check_logistics(orderlist.id)" value="查看物流"/>
                    <input ng-if="orderlist.status_id != 10 && orderlist.sub_orders[0].is_point == 0" type="button" class="buy_again button lrpadding8"  ng-click="add_shopping_cart(orderlist)" value="再次购买"/>
                    <input ng-if="orderlist.status_id != 10 && orderlist.sub_orders[0].is_point == 1" type="button" class="buy_again button lrpadding8"  ng-click="exchange_again(orderlist.sub_orders[0].commodity_id)" value="再次兑换"/>
                    <input ng-if="orderlist.status_id == 10" type="button" class="buy_again button lrpadding8" ng-click="pay(orderlist.id)" style="color: #d9534f; border-color: #d9534f" value="去付款"/>
                </div>
            </div>
            <div class="load_done">加载完成</div>
        </div>
    </div>
</article>