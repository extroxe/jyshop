<header>
    <div class="titlebar">
        <a class="titlebar-button" ng-click="back()"><i class="icon size16 icon-arrowleft back_btn"></i></a>
        <h1 class="text-center"><?php echo $title; ?></h1>
    </div>
</header>
<article ng-controller="myReportCtrl">
    <div class="item" ng-repeat="report in reports" ng-click="watch_pdf(report)">
        <div class="item-title">订单编号：{{ report.order_number }}</div>
        <hr>
        <div class="item-content">
            <div>
                <p class="item-name">{{ report.name }} <span>{{ report.gender == 1 ? '男' : '女'  }} {{ report.age }}岁</span></p>
                <p><img ng-src="{{ SITE_URL + 'source/mobile/img/icon/phone.png' }}" alt=""><span>{{ report.phone }}</span></p>
                <p><img ng-src="{{ SITE_URL + 'source/mobile/img/icon/id-card.png' }}" alt=""><span>{{ report.identity_card }}</span></p>
            </div>
            <div>
                <i class="icon size20 icon-arrowright"></i>
            </div>
        </div>
        <div class="item-footer">{{ report.upload_time }}</div>
    </div>
    <div ng-if="reports.length == 0" style="padding: 77px;text-align: center;">
        未查到相关报告！！
    </div>
</article>