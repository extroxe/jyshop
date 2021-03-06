$(document).ready(function(){
    var show_img_id = 0;
    
    $('#nav-list').css('margin-top', '-1px');

    var commodity_id = $('#page-content').attr('data-id');
    var is_favorite_flag = false;
    var evaluation_level = 0;
    evaluation_paginate(1, 10, commodity_id);
    init_evaluation_nav(commodity_id);
    /*$('div.zoomMask').css({
        "background": "url("+SITE_URL+'source/img/mask.png'+")",
        "background-repeat": "repeat",
        "background-scroll": 'scroll',
        "background-origin": "0 0",
        "background-color": "transparent"
    });*/

    $.ajax({
        type : 'post',
        dataType: "json",
        url : SITE_URL+'favorite/check_favorite_by_commodity_id',
        data : {
            commodity_id : commodity_id
        },
        success : function(response){
            if (response.success){
                $('#collect').find('span:first-child').toggleClass('collect_color');
                is_favorite_flag = true;
            }else{
                is_favorite_flag = false;
            }
        },
        error: function(error){
        }
    });

	$('#delt').click(function(){
		var qut = parseInt($('#appendedInputButtons').val()) || 0;
		qut--;
		qut = qut < 1 ? 1 : qut;
		$('#appendedInputButtons').val(qut);
        isDisabled(qut)
	});

	$('#add').click(function(){
		var qut = parseInt($('#appendedInputButtons').val()) || 0;
		qut++;
		$('#appendedInputButtons').val(qut);
        isDisabled(qut);
	});
	//减按钮可用/不可用
	function isDisabled(qut) {
        if(qut == 1){
            $('#delt').css({'opacity':'0.4', 'cursor':'default', 'border-color':'#ddd'});
        }else{
            $('#delt').css({'opacity':'1', 'cursor':'pointer','border-color':'#eee'});
        }
    }
    $('#appendedInputButtons').keyup(function () {
        this.value = this.value.replace(/[^\d]/g, '');
        if(this.value == '' || this.value == 0) {
            this.value = 1;
        }
    });

    $(document).ready(function(){

        $(".jqzoom").imagezoom();

        $("#thumblist li a").click(function(){
            $(this).parents("li").addClass("tb-selected").siblings().removeClass("tb-selected");
            $(".jqzoom").attr('src',$(this).find("img").attr("mid"));
            $(".jqzoom").attr('rel',$(this).find("img").attr("big"));
        });
    });

    /**
     * 加入购物车
     */

    $("#addCart").click(function () {
        var commodity_id = $('#page-content').attr('data-id');
        var amount = parseInt($('#appendedInputButtons').val()) || 1;
        $.ajax({
            type : 'post',
            dataType: "json",
            url : SITE_URL+'shopping_cart/add',
            data : {
                commodity_id : commodity_id,
                amount : amount
            },
            success : function(response){
                // alert(response.msg);
                if (response.success){
                    get_all_shopping_cart();
                    ani();
                }else{
                    alert(response.msg);
                }

            },
            error : function(error){
            }
        });
    });
    //添加加入购物车动画
    function ani() {
        $('#icon_cart').addClass('icon_animate1');
        $('#cart_to_cart').addClass('animation1');
        setTimeout(function () {
            $('#icon_cart').removeClass('icon_animate1');
            $('#cart_to_cart').removeClass('animation1')
        },1000);
    }


    /**
     * 立即购买
     */
    $('#buy-now').click(function(){
        var commodity_id = $('#page-content').attr('data-id');
        var amount = parseInt($('#appendedInputButtons').val()) || 1;

        $.ajax({
            type : 'post',
            dataType: "json",
            url : SITE_URL+'shopping_cart/add',
            data : {
                commodity_id : commodity_id,
                amount : amount
            },
            success : function(response){
                if (response.success){
                    window.location.href = SITE_URL+'shopping_cart/buy_now/'+response.insert_id;
                }else{
                    alert(response.msg);
                }
            },
            error : function(error){

            }
        });
    });

    /**
     * 立即兑换
     */
    $('#exchange-now').click(function(){
        var commodity_id = $('#page-content').attr('data-id');
        var amount = parseInt($('#appendedInputButtons').val()) || 1;

        $.ajax({
            type : 'post',
            dataType: "json",
            url : SITE_URL+'commodity/check_point_enough',
            data : {
                commodity_id : commodity_id,
                amount : amount
            },
            success : function(response){
                if (response.success){
                    window.location.href = SITE_URL+'order/settlement/'+commodity_id+'/1';
                }else{
                    alert(response.msg);
                }
            },
            error : function(error){

            }
        });
    });

    /**
     * 查看评价图片
     */
    $(document).on('click', '.review-img-min', function(){
        var id = $(this).data('id');
        var path = $(this).attr('src');
        var img = $(this).parent().next().find('img');
        if (show_img_id != id){
            show_img_id = id;
            img.attr('src', path);
            img.show();
        }else{
            show_img_id = 0;
            img.attr('src', '#');
            img.hide();
        }

    });

    /**
     * 上一页
     */
    $(document).on('click', '#prev-page', function(){
        var now_page = $(this).closest('ul').find('#now-page').attr('data-page');
        evaluation_paginate(parseInt(now_page) - 1, 10, commodity_id, evaluation_level);
    });

    /**
     * 下一页
     */
    $(document).on('click', '#next-page', function(){
        var now_page = $(this).closest('ul').find('#now-page').attr('data-page');
        evaluation_paginate(parseInt(now_page) + 1, 10, commodity_id, evaluation_level);
    });

    /**
     * 跳页
     */
    $(document).on('click', '#jump-page', function(){
        var target_page = $(this).closest('ul').find('input').val();
        evaluation_paginate(target_page, 10, commodity_id, evaluation_level);
    });

    /**
     * 全部评价
     */
    $('#all-evaluation').click(function(){
        evaluation_paginate(1, 10, commodity_id);
        evaluation_level = 0;
    });

    /**
     * 好评
     */
    $('#good-evaluation').click(function(){
        evaluation_paginate(1, 10, commodity_id, 1);
        evaluation_level = 1;
    });

    /**
     * 中评
     */
    $('#mid-evaluation').click(function(){
        evaluation_paginate(1, 10, commodity_id, 2);
        evaluation_level = 2;
    });

    /**
     * 差评
     */
    $('#bad-evaluation').click(function(){
        evaluation_paginate(1, 10, commodity_id, 3);
        evaluation_level = 3;
    });

    $('.breadcrumb li').click(function(){
        var this_li = $(this);
        this_li.addClass('active');
        this_li.siblings('li').removeClass('active');
    });

    /**
     * 收藏
     */
    $(document).on('click', '#collect', function () {
        var favorite_span = $(this).find('span:first-child');
        if (is_favorite_flag){
            $.ajax({
                type : 'post',
                dataType: "json",
                url : SITE_URL+'favorite/delete_by_commodity_id',
                data : {
                    commodity_id : commodity_id
                },
                success : function(response){
                    if (response.success){
                        favorite_span.toggleClass('collect_color');
                    }
                },
                error: function(error){

                }
            });
        }else{
            $.ajax({
                type : 'post',
                dataType: "json",
                url : SITE_URL+'favorite/add',
                data : {
                    commodity_id : commodity_id
                },
                success : function(response){
                    if (response.success){
                        favorite_span.toggleClass('collect_color');
                    }else if (response.timeout) {
                        alert(response.msg);
                    }
                },
                error: function(error){

                }
            });
        }

    });
   
});

/**
 * 初始评价nav
 */
function init_evaluation_nav(commodity_id){
    $.ajax({
        type : 'post',
        dataType: "json",
        url : SITE_URL+'commodity/evaluation_nav',
        data : {
            commodity_id : commodity_id
        },
        success : function(response){
            $('#all-evaluation').text('全部评价('+(response.all_eva || 0)+')');
            $('#good-evaluation').text('好评('+(response.good_eva || 0)+')');
            $('#mid-evaluation').text('中评('+(response.mid_eva || 0)+')');
            $('#bad-evaluation').text('差评('+(response.bad_eva || 0)+')');
            $('#rating_num').text(response.all_eva || 0);
        },
        error : function(error){

        }
    });
}

/**
 * 评价分页
 */
function evaluation_paginate(page, page_size, commodity_id, evaluation_level = 0){
    $.ajax({
        type : 'post',
        dataType: "json",
        url : SITE_URL+'commodity/evaluation_paginate/'+page+'/'+page_size+'/'+commodity_id+'/'+evaluation_level,
        success : function(response){
            if (response.success){
                $.each(response.data, function(index, row){
                    row.create_time = row.create_time.substr(0, 16);
                });
                push_evaluation(response.data, response.total_page, page);
            }else{
                if (page > response.total_page){
                    page = response.total_page;
                }else{
                    push_evaluation([], response.total_page, page);
                }
            }
            $('#praise').text(response.praise_rate * 100 + '%');
        },
        error : function(error){

        }
    });
}

/**
 * 填充评价分页数据
 */
function push_evaluation(evaluations, total_page, now_page){
    var tpl = document.getElementById('evaluation_tpl').innerHTML;
    $("#evaluation").html(template(tpl, {list: evaluations}));

    var paginate = '<ul>';

    if (now_page != 1){
        paginate += '<li class="pointer" id="prev-page">上一页 </li>';
    }

    paginate += '<li> 共 '+total_page+' 页 </li>\
                 <li data-page="'+now_page+'" id="now-page">第 <input type="text" value="'+now_page+'" style="width:15px;"> 页</li>\
                 <li class="pointer" id="jump-page">跳转</li>';

    if (now_page != total_page){
        paginate += '<li class="pointer" id="next-page"> 下一页</li>';
    }

    paginate += '</ul>';

    $('#evaluation-paginate').html(paginate);
}