/**
 * Created by sailwish009 on 2016/12/2.
 */
$(document).ready(function () {
    $('.info_input').each(function () {

    });

    /**
     * 登录验证
     */
    $('#username').bind('keypress',function(event){
        if(event.keyCode == "13") {
            $(this).parents('.form-horizontal').find('#password').focus();
        }
    });
    $('#password').bind('keypress',function(event){
        if(event.keyCode == "13") {
            sign_in();
        }
    });

    $('#login').click(function(){
        sign_in();
        Save();
    });

    if ($.cookie("username") != undefined && $.cookie("password") != undefined) {
        $("#remenberUser").attr("checked", true);
        $("#username").val($.cookie("username"));
        $("#password").val('ertyughj');
    }

});

/**
 * 登录验证
 */
function sign_in() {
    var username = $('#username').val();
    var new_password = $('#password').val();
    var password = '';
    var auto_login = 0;
    if ($('#remenberUser').is(':checked')){
        auto_login = 1;
    }

    if (auto_login == 1 && $.cookie("password") != undefined){
        if (new_password == 'ertyughj'){
            password = $.cookie("password");
        }else{
            password = new_password;
        }
    }else{
        password = new_password;
    }

    $.ajax({
        type : 'post',
        dataType: "json",
        url : SITE_URL+'index/do_login',
        data : {
            username : username,
            password : password,
            auto_login : auto_login
        },
        success : function(response){
            if (response.success){
                var prev_url = document.referrer;
                var url_arr = prev_url.substr(7, prev_url.length).split('/');

                if (prev_url == '' || (url_arr[1] == 'index' && (url_arr[2] == 'sign_up' || url_arr[2] == 'sign_in'))){
                    window.location.href = SITE_URL + 'index';
                }else{
                    history.go(-1);
                }
            }else{
                alert(response.msg);
            }
        },
        error : function(error){

        }
    });
}

//记住用户名密码
/*function Save() {
    if ($("#remenberUser").prop("checked")) {
        var str_username = $("#username").val();
        var str_password = $("#password").val();
        $.cookie("rmbUser", "true", { expires: 7 }); //存储一个带7天期限的cookie
        $.cookie("username", str_username, { expires: 7 });
        $.cookie("password", str_password, { expires: 7 });
    }
    else {
        $.cookie("rmbUser", "false", { expire: -1 });
        $.cookie("username", "", { expires: -1 });
        $.cookie("password", "", { expires: -1 });
    }
}*/
