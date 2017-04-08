<div id="page-content" class="home-page">
    <div class="container">
        <!-- nav-left -->
        <div style="position: relative">
            <div style="width: 263px; height: 407px; float: left">
                <div class="all-sort-list" style="margin: 0 auto">
                    <div class="item item1" style="background-color: #222;">
                        <h2 style="    font-size: 23px;
    font-family: '微软雅黑';
    font-weight: normal;">所有商品分类</h2>
                    </div>
                    <?php foreach($collection as $row) : ?>
                    <div class="item">
                        <div class="list_nav" style="">
                            <h5 style=" font-family: '微软雅黑'"><?php echo $row['type_name']; ?><span> > </span></h5>
                            <ul>
                                <?php foreach($row['category'] as $key => $category) : ?>
                                <?php if ($key <= 3): ?>
                                <li class="category" data-id="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></li>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="item-list clearfix" style="border: 1px solid #117d94">
                            <div class="subitem">
                                <?php foreach ($row['category'] as $category): ?>
                                <dl class="fore">
                                    <dt><a href="<?php echo site_url('index/search?category='.$category['id'].'&result='.$category['name']); ?>"><?php echo $category['name']; ?></a></dt>
                                    <dd>
                                        <?php foreach($category['children_category'] as $child_category) : ?>
                                        <em><a href="#" class="category" data-id="<?php echo $child_category['id']; ?>"><?php echo $child_category['name']; ?></a></em>
                                        <?php endforeach; ?>
                                    </dd>
                                </dl>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div style="width: 870px; height: 416px;margin-left: 285px;">
                <div class="row-fluid">
                    <div id="myCarousel" class="carousel slide">
                        <ol class="carousel-indicators">
                            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                            <li data-target="#myCarousel" data-slide-to="1"></li>
                            <li data-target="#myCarousel" data-slide-to="3"></li>
                        </ol>
                        <!-- Carousel items -->
                        <div class="carousel-inner">
                            <?php foreach ($banner as $key => $row) : ?>
                            <div class="<?php if ($key == 0){
                                echo 'active';
                            } ?> item">
                                <a href="<?php echo $row['url']; ?>"><img src="<?php echo site_url($row['path']); ?>"></a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- 今日上新 -->
        <?php if (!empty($new_recent)) : ?>
        <div class="row-fluid">
            <div class="span12">
                <div class="totaynew">
                    <img data-original="<?php echo site_url('source/img/new-flag.png'); ?>">
                    <strong style="position: absolute;bottom: 7px;">今日上新</strong>
                </div>
            </div>
        </div>
        <div class="totaynew_commodity" id="today_new" >
            <?php foreach ($new_recent as $row) : ?>
            <div class="box">
                <a href="<?php echo site_url('commodity/index/'.$row['id']); ?>"><img class="img_detail" data-original="<?php echo site_url($row['path']); ?>"></a>
                <div class="disc">
                    <a href="<?php echo site_url('commodity/index/'.$row['id']); ?>"><?php echo '【'.$row['type'].'】'.$row['name']; ?></a><br>
                    <p class="price">
                        <span style="color: #f6bf00; font-size: 10px">￥</span>
                        <span style="color: #f6bf00"><?php echo is_null($row['flash_sale_price']) ? $row['price'] : $row['flash_sale_price'] ?></span>
                        <span style="font-size: 12px;color: #999;text-decoration: line-through;"> <?php echo is_null($row['flash_sale_price']) ? '' : '¥'.$row['price'] ?></span>
                    </p>
                    <a class="btn btn-2 btn-link pull-right" href="<?php echo site_url('commodity/index/'.$row['id']); ?>">购买</a>
                </div>
            </div>
            <?php endforeach; ?>
            <br style="clear:both;" />
        </div>
        <?php endif; ?>
        <!-- 限时折扣 -->
        <?php if (!empty($flash_sale)): ?>
        <div class="row-fluid">
            <div class="span12">
                <div class="limited_discount_head">
                    <img data-original="<?php echo site_url('source/img/explosion_flag.png'); ?>">
                    <strong style="color: #117d94; margin-left: 10px; font-size: 16px; position: absolute;bottom: 7px;">限时折扣</strong>
                    <?php if (count($flash_sale) >= 3): ?>
                    <a class="more" href="<?php echo site_url('index/search?flash_sale=true&page_size=9'); ?>">更多></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="limited_discount">
            <div class="box_first" id="flash_sale">
                <a href="<?php echo site_url('commodity/index/'.$row['id']); ?>">
                    <img class="img_detail discount_img cover-image" data-original="<?php echo site_url($flash_sale[0]['path']); ?>">
                </a>
                <div class="mask">
                    <div>
                        <h3>限时折扣</h3>
                        <p>立即进入>></p>
                    </div>
                </div>
            </div>
            <?php foreach ($flash_sale as $row): ?>
            <div class="box">
                <p class="time_head" style="font-size: 12px;" >活动结束时间 <span><?php echo $row['end_time']; ?></span></p>
                <a href="<?php echo site_url('commodity/index/'.$row['commodity_id']); ?>">
                    <img class="img_detail discount_img" data-original="<?php echo site_url($row['path']); ?>">
                </a>
                <div class="disc">
                    <a href="<?php echo site_url('commodity/index/'.$row['commodity_id']); ?>"><?php echo '【'.$row['type'].'】'.$row['name']; ?></a><br>
                    <p class="price">
                        <span style="color: #f6bf00; font-size: 10px">￥</span>
                        <span style="color: #f6bf00"><?php echo is_null($row['flash_sale_price']) ? $row['price'] : $row['flash_sale_price'] ?></span>
                        <span style="font-size: 12px;color: #999;text-decoration: line-through;"> <?php echo is_null($row['flash_sale_price']) ? '' : '¥'.$row['price'] ?></span>
                    </p>
                    <a class="btn btn-2 btn-link pull-right" href="<?php echo site_url('commodity/index/'.$row['commodity_id']); ?>">购买</a>
                </div>
            </div>
            <?php endforeach; ?>
            <br style="clear:both;" />
        </div>
        <?php endif; ?>
<!--热卖商品-->
        <?php if (!empty($recommend_hot_sale)): ?>
        <div class="row-fluid">
            <div class="span12">
                <div class="limited_discount_head hot_sale">
                    <img data-original="<?php echo site_url('source/img/hot.png'); ?>">
                    <strong>热卖商品</strong>
                </div>
            </div>
        </div>
        <div class="sale">
            <div class="hot-cover-image">
                <img data-original="<?php echo site_url(isset($recommend_hot_sale_cover['value']) ? $recommend_hot_sale_cover['value'] : 'source/img/06.png'); ?>">
            </div>
            <?php foreach ($recommend_hot_sale as $key => $row): ?>
            <?php if ($key <= 2): ?>
            <div class="box hot_change">
                <a href="<?php echo site_url('commodity/index/'.$row['commodity_id']); ?>">
                    <img class="img_detail border_right" data-original="<?php echo site_url($row['path']); ?>">
                </a>
                <div class="disc">
                    <a href="<?php echo site_url('commodity/index/'.$row['commodity_id']); ?>"><?php echo '【'.$row['type'].'】'.$row['name']; ?></a><br>
                    <p class="price">
                        <span style="color: #f6bf00; font-size: 10px">￥</span>
                        <span style="color: #f6bf00"><?php echo is_null($row['flash_sale_price']) ? $row['price'] : $row['flash_sale_price'] ?><span style="text-decoration: line-through; font-size: 10px; color: #aaa; margin-left: 10px"></span>
                        </span>
                        <span style="font-size: 12px;color: #e4e4e4;text-decoration: line-through;"> <?php echo is_null($row['flash_sale_price']) ? '' : '¥'.$row['price'] ?></span>
                    </p>
                    <a class="btn btn-2 btn-link pull-right" href="<?php echo site_url('commodity/index/'.$row['commodity_id']); ?>">购买</a>
                </div>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
            <div class="box hot_change" id="hot_sale">
                <a href="<?php echo site_url('index/search?hot_sale=true&page_size=10'); ?>" style="display: block;">
                    <img class="img_detail discount_img border_right" data-original="<?php echo site_url($recommend_hot_sale[count($recommend_hot_sale) - 1]['path']); ?>">
                </a>
                <div>
                    <div class="mask">
                        <h3>HOT</h3>
                        <p>立即进入></p>
                    </div>
                </div>
            </div>
<!--            <p style="clear: both"></p>-->
        </div>
        <?php endif; ?>
        <!--热换商品-->
        <?php if(!empty($recommend_hot_exchange)): ?>
        <div class="row-fluid">
            <div class="span12">
                <div class="limited_discount_head hot_sale">
                    <img data-original="<?php echo site_url('source/img/hot_sale_flag.png'); ?>">
                    <strong>热换商品</strong>
                </div>
            </div>
        </div>
        <div class="sale">
            <div class="hot-cover-image">
                <img data-original="<?php echo site_url(isset($recommend_hot_exchange_cover['value']) ? $recommend_hot_exchange_cover['value'] : 'source/img/06.png'); ?>">
            </div>
            <?php foreach ($recommend_hot_exchange as $key => $row): ?>
            <?php if ($key <= 2): ?>
            <div class="box hot_change">
                <a href="<?php echo site_url('commodity/index/'.$row['commodity_id']); ?>">
                    <img class="img_detail border_right" data-original="<?php echo site_url($row['path']); ?>">
                </a>
                <div class="disc">
                    <a href="<?php echo site_url('commodity/index/'.$row['commodity_id']); ?>"><?php echo '【'.$row['type'].'】'.$row['name']; ?></a><br>
                    <p class="price">
                        <span style="color: #f6bf00"><?php echo $row['price']; ?>
                        </span>
                        <span style="color: #f6bf00;">积分</span>
                    </p>
                    <a class="btn btn-2 btn-link pull-right" href="<?php echo site_url('commodity/index/'.$row['commodity_id']); ?>">兑换</a>
                </div>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
            <div class="box hot_change" id="hot_change">
                <a href="<?php echo site_url('index/search?hot_exchange=true&page_size=10'); ?>" style="display: block;">
                    <img class="img_detail discount_img border_right" data-original="<?php echo site_url($recommend_hot_exchange[count($recommend_hot_exchange) - 1]['path']); ?>">
                </a>
                <div>
                    <div class="mask">
                        <h3>HOT</h3>
                        <p>立即进入></p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>