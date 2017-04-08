/**
 * Created by sailwish009 on 2016/12/1.
 */
$(function(){
    var page = 1;
    var order_status = '0';
    var flag = true;
    var total_pages = 0;
    var init_datas = [];
    // function init_data(order_status,page) {
        ajax('get_order_by_page/', order_status, page);
    // };
    // init_data(order_status, page);

    //所有订单
    $('#order_list_all').click(function () {
        page = 1;
        order_status = '0';
        ajax('get_order_by_page/', order_status, page);
        // init_data(order_status, page);
    });
    //待付款
    $('#order_list_not_paid').click(function () {
        not_paid();
    });
    function not_paid() {
        page = 1;
        order_status = '10';
        ajax('get_order_by_page/', order_status, page);
        // init_data(order_status, page);
    }
    //待发货
    $('#order_list_paid').click(function () {
        delivered();
    });
    function delivered() {
        page = 1;
        order_status = '20';
        ajax('get_order_by_page/', order_status, page);
        // init_data(order_status, page);
    }
    //待收货
    $('#order_list_delivered').click(function () {
        page = 1;
        order_status = '30';
        ajax('get_order_by_page/', order_status, page);
        // init_data(order_status, page);
    });
    //已寄回
    $('#order_list_sentback').click(function () {
        page = 1;
        order_status = '40';
        ajax('get_order_by_page/', order_status, page);
        // init_data(order_status, page);
    });
    //检测中
    $('#order_list_assaying').click(function () {
        page = 1;
        order_status = '50';
        ajax('get_order_by_page/', order_status, page);
        // init_data(order_status, page);
    });
    //已完成
    $('#order_list_finished').click(function () {
        page = 1;
        order_status = '60';
        ajax('get_order_by_page/', order_status, page);
        // init_data(order_status, page);
    });
    //退款中
    $('#order_list_not_refunding').click(function () {
        page = 1;
        order_status = '70';
        ajax('get_order_by_page/', order_status, page);
        // init_data(order_status, page);
    });
    //已退款
    $('#order_list_not_refunded').click(function () {
        page = 1;
        order_status = '80-90';
        ajax('get_order_by_page/', order_status, page);
        // init_data(order_status, page);
    });
    //待评价
    $('#order_list_can_evaluate').click(function () {
        page = 1;
        $("#order_list_container").html('');
        order_status = "";
        ajax('get_can_evaluate_order_by_page/', '', page);
    });

    /**
     * 获取订单数据
     */
    function ajax(url, status_id, page) {
        $.ajax({
            type : 'post',
            dataType: "json",
            data:{
                page : page,
                page_size : 5
            },
            url : SITE_URL+'order/' + url + status_id,
            success : function(response){
                if (response.success){

                    if(page == response.total_page){
                        $('#Next_page').parent().addClass('disabled');
                        flag = false;
                    }else{
                        $('#Next_page').parent().removeClass('disabled');
                        flag = true;
                    };

                    init_datas = [];
                    var m = 0;
                    for(var i = 0; i<response.data.length; i++){
                        if(response.data[i].sub_orders){
                            for(var j =0; j<response.data[i].sub_orders.length; j++){
                                if(response.data[i].payment_id == '1'){
                                    response.data[i].sub_orders[j].price = '¥' + response.data[i].sub_orders[j].price;
                                    response.data[i].sub_orders[j].total_prices = '¥' + response.data[i].sub_orders[j].total_price;
                                }else{
                                    response.data[i].sub_orders[j].price = response.data[i].sub_orders[j].price + '积分';
                                    response.data[i].sub_orders[j].total_prices = response.data[i].sub_orders[j].total_price + '积分';
                                }
                            }
                            if(response.data[i].status_id == '10'){
                                response.data[i].order_operate='取消订单';
                            }else if(response.data[i].payment_id == '4' || response.data[i].payment_id == '5' || response.data[i].payment_id == '6'){
                                response.data[i].order_operate = '';
                                response.data[i].total_price = response.data[i].payment_amount;
                            }else if(response.data[i].status_id == '30'){
                                response.data[i].order_operate = '查看详情'
                            }else if(response.data[i].status_id == '70'){
                                response.data[i].order_operate = '退款中'
                            }else if(response.data[i].status_id == '80'){
                                response.data[i].order_operate = '退款完成'
                            }else if( response.data[i].status_id == '100'){
                                response.data[i].order_operate = '订单已取消'
                            }else if( response.data[i].status_id == '20'){
                                response.data[i].order_operate='退款';
                            }else if( response.data[i].status_id == '60'){
                                response.data[i].order_operate='评价';
                            }


                        }
                        init_datas[m] = response.data[i];
                        m++;
                    }
                    var tpl = document.getElementById('order_lists_tpl').innerHTML;
                    $("#order_list_container").html(template(tpl, {data: init_datas}));
                    total_pages = response.total_page;
                    $('#total_pages').html(response.total_page);
                    $('#empty_order').css('display', 'none');

                }else{
                    $('#empty_order').css('display', 'block');
                    $('#Next_page').parent().addClass('disabled');
                    flag = false;
                    $('table.cart_table tbody').html('');
                    total_pages = 1;
                    $('#total_pages').html(0);
                }

                $('#page_num').val(page);
                if(parseInt($('#page_num').val()) == 1){
                    $('#Prev_page').parent().addClass('disabled');
                }else{
                    $('#Prev_page').parent().removeClass('disabled');
                }
            }
        });
    }
    
    //上一页
    $('#Prev_page').click(function () {
        page--;
        if(page < 1){
            page = 1;
        }else{
            if(order_status == ""){
                ajax('get_can_evaluate_order_by_page/', '', page);
            }else{
                ajax('get_order_by_page/', order_status, page);
            }
        }
    });
    //下一页
    $('#Next_page').click(function () {
        if(flag == true){
            page++;
            if(order_status == ""){
                ajax('get_can_evaluate_order_by_page/', '', page);
            }else{
                ajax('get_order_by_page/', order_status, page);
            }
        }
    });
    //跳转页面
    $('#jump_to_page').click(function () {
        page = parseInt($(this).siblings('#page_num').val());
        if(order_status == ""){
            ajax('get_can_evaluate_order_by_page/', '', page);
        }else{
            ajax('get_order_by_page/', order_status, page);
        }
    });

    //取消订单，申请退款，寄回
    $(document).on('click', '#cancel_order', function () {
        var sub_order_id = $(this).data('sub-order-id');
        var status_id = $(this).data('order-status-id');
        var commodity_id = $(this).data('commodity-id');
        var order_id = $(this).data('order-id');
        if(status_id == '10' || status_id == '20'){
            if (confirm('确定申请退款？')){
                $.ajax({
                    type:'POST',
                    dataType:'json',
                    data:{
                        order_id:order_id
                    },
                    url : SITE_URL+'refund/application_for_refund/',
                    success:function (data) {
                        if(data.success){
                            alert(data.msg);
                            get_list_nav();
                            for(var i = 0; i< init_datas.length; i++){
                                if(init_datas[i].id == order_id){
                                    var index = i;
                                }
                                init_datas.splice(index,1);

                                if(init_datas[i].status_id == '10'){
                                    not_paid();
                                }else if(init_datas[i].status_id == '20'){
                                    delivered();
                                }
                            };

                        }else{
                            alert(data.msg);
                        }
                    },
                    error: function () {
                    }
                });
            }
        }else if (status_id == '30'){
            window.location.href = SITE_URL + 'order/detail/' + order_id;
        }else if(status_id == '60'){
            window.location.href = SITE_URL+'order/evaluation/' + sub_order_id;
        }
    });

    get_list_nav();

    /**
     * 选择订单状态nav
     */
    $('#operation li').click(function(){
        var status = $(this).data('status');
        $(this).addClass('active').siblings('li').removeClass('active');
    });

//    查看物流信息
    $(document).on('mouseenter', '.check_logistics_box', function () {
        $(this).find('.check_logistics').show();
        var temp = $(this);
        var order_id = $(this).data('order-id');
        var order_number = $(this).data('order-number');
        var tpl = document.getElementById('check_logistics_tpl').innerHTML;
        if($(this).find(".check_logistics_detail").html()){
            $.ajax({
                type: 'get',
                dataType:'json',
                url: SITE_URL +'order/show_express_info_by_order_id/' + order_id,
                success: function (response){
                    if(response.success){
                        response.data.order_number = order_number;
                        response.data.id = order_id;
                        temp.find(".check_logistics_detail").html(template(tpl, {data: response.data}));
                    }else{
                        temp.find(".check_logistics_detail").html('<i class="fa fa-truck" aria-hidden="true"></i>暂无物流信息');
                    }
                }
            })
        };
    });

    $(document).on('mouseleave', '.check_logistics_box', function () {
        $(this).find('.check_logistics').hide();
    });
});

/**
 * 获取订单列表nav
 */
function get_list_nav(){
    $.ajax({
        type : 'post',
        dataType: "json",
        url : SITE_URL+'order/get_order_list_nav',
        success : function(response){
            $('#all-order').text('('+(response.all || 0)+')');
            $('#not-paid-order').text('('+(response.not_paid || 0)+')');
            $('#paid-order').text('('+(response.paid || 0)+')');
            $('#delivered-order').text('('+(response.delivered || 0)+')');
            $('#sentback-order').text('('+(response.sentback || 0)+')');
            $('#assaying-order').text('('+(response.assaying || 0)+')');
            $('#finished-order').text('('+(response.finished || 0)+')');
            $('#refunding-order').text('('+(response.refunding || 0)+')');
            $('#refunded-order').text('('+(response.refunded || 0)+')');
            $('#can-evaluate-order').text('('+(response.can_evaluate || 0)+')');
        },
        error: function(error){

        }
    });
}

