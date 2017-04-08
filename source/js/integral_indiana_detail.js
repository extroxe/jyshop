$(function(){
    $('#indiana-now').click(function(){
        var id = $(this).data('id');
        var point = $(this).data('point');
        if(confirm('本次活动需要'+point+'积分，确认参加？')){
            $.ajax({
                type : 'post',
                dataType: "json",
                url : SITE_URL+'integral_indiana/join_integral_indiana',
                data : {
                    id: id,
                    bet_num: 1
                },
                success : function(response){
                    if (response.success){
                        alert('参与成功，请耐心等待夺宝结果！');
                        window.location.reload();
                    }else{
                        alert(response.msg);
                    }
                },
                error: function(error){

                }
            });
        }
    });
});