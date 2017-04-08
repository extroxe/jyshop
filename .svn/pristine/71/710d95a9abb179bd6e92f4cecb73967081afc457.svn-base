<div id="my_report" class="model_box">
    <div class="control-group" style=" border: none;">
        <h5>我的报告列表</h5>
    </div>
    <table class="table table-hover" style="margin-top: 30px;" id="report_list"></table>
</div>
<script id="report_tpl" type="text/html">
    <thead style="border: 1px solid #eee;background-color: #f2f7ff;">
    <tr>
        <th>商品名称</th>
        <th>姓名</th>
        <th>手机号</th>
        <th>身份证号</th>
        <th>报告编号</th>
        <th>更新时间</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
        <% for (var i = 0; i < list.length; i++) { %>
        <tr>
            <td class="commodity-name"><%:=list[i].commodity_name%></td>
            <td class="name"><%:=list[i].name%></td>
            <td class="phone"><%:=list[i].phone%></td>
            <td class="identity-card"><%:=list[i].identity_card%></td>
            <td class="number"><%:=list[i].number%></td>
            <td class="update_time"><%:=list[i].update_time%></td>
            <td><a href="<%=SITE_URL+list[i].path%>" download="report"><i class="glyphicon icon-download-alt"></i>下载</a></td>
        </tr>
        <% } %>
    </tbody>
</script>