<template>
	<div id="app">
		<div class="table-list">
			<div class="card-title"><span>业务系统列表</span></div>
			<table class="list">
				<tbody>
					<tr class="title">
						<th class="al">appkey</th>
						<th>系统名</th>
						<th>域名</th>
						<th>操作</th>
						<th>浏览量(PV)</th>
						<th>IP数</th>
						<th>统计信息</th>
					</tr>
					<tr>
						<td class="normal">appkey12312344565</td>
						<td>系统名</td>
						<td>域名</td>
						<td>操作</td>
						<td>999</td>
						<td>741</td>
						<td>统计信息</td>
					</tr>
					<tr>
						<td class="normal">appkey12312344565</td>
						<td>系统名</td>
						<td>域名</td>
						<td>操作</td>
						<td>999</td>
						<td>741</td>
						<td>统计信息</td>
					</tr>
					<tr>
						<td class="normal">appkey12312344565</td>
						<td>系统名</td>
						<td>域名</td>
						<td>操作</td>
						<td>999</td>
						<td>741</td>
						<td>统计信息</td>
					</tr>
					<tr>
						<td class="normal">appkey12312344565</td>
						<td>系统名</td>
						<td>域名</td>
						<td>操作</td>
						<td>999</td>
						<td>741</td>
						<td>统计信息</td>
					</tr>
					<tr>
						<td class="normal">appkey12312344565</td>
						<td>系统名</td>
						<td>域名</td>
						<td>操作</td>
						<td>999</td>
						<td>741</td>
						<td>统计信息</td>
					</tr><tr>
						<td class="normal">appkey12312344565</td>
						<td>系统名</td>
						<td>域名</td>
						<td>操作</td>
						<td>999</td>
						<td>741</td>
						<td>统计信息</td>
					</tr>
					<tr class="empty-tr fade"></tr>
				</tbody>
			</table>
		</div>
		<div class="fold"></div>
		<div class="table-list">
			<div class="card-title"><span>今日流量</span></div>
			<table class="list">
				<tbody>
					<tr class="title">
						<th></th>
						<th>浏览量(PV)</th>
						<th>IP数</th>
					</tr>
					<tr>
						<td class="normal">今日</td>
						<td>399</td>
						<td>314</td>
					</tr>
					<tr>
						<td class="normal">昨日</td>
						<td>999</td>
						<td>741</td>
					</tr>
					<tr>
						<td class="normal">预计今日</td>
						<td>1604</td>
						<td>1179</td>
					</tr>
					<tr class="empty-tr"></tr>
					<tr class="empty-tr fade"><td colspan="3"></td></tr>
					<tr class="fade">
						<td class="normal">昨日此时</td>
						<td>203</td>
						<td>159</td>
					</tr>
					<tr class="fade">
						<td class="normal">每日平均</td>
						<td>1760</td>
						<td>1226</td>
					</tr>
					<tr class="fade">
						<td class="normal">历史峰值</td>
						<td>4475</td>
						<td>2211</td>
					</tr>
					<tr class="empty-tr-2 fade"><td colspan="3"></td></tr>
				</tbody>
			</table>
		</div>
		<div class="fold">
			<a href="javascript:;">v</a>
		</div>
		<div class="date-select-bar">
			<div class="control-bar">
				<a href="javascript:;">今天</a>
				<a href="javascript:;">昨天</a>
				<a href="javascript:;">最近7天</a>
				<a href="javascript:;">最近30天</a>
			</div>
		</div>
		<div class="wrap clearfix">
			<div class="row1 left">
				<div class="table-grid-item">
					<div class="title clearfix">
						<span>趋势图</span>
						<a href="javascript:;">&gt;</a>
					</div>
					<div class="line-row">
						<div class="control-bar left">
							<a href="javascript:;">浏览量(PV)</a>
							<a href="javascript:;">IP数</a>
						</div>
						<div class="check-group right">
							<span>对比：</span>
							<label>
								<input type="radio" name="date">
								前一日
							</label>
							<label>
								<input type="radio" name="date">
								上周同期
							</label>
						</div>
					</div>
					<div id="grid1"></div>
				</div>
			</div>
			<div class="row2 right">
				<div class="table-grid-item" id="grid2">
					<div class="title clearfix">
						<span class="left">Top10受访页面</span>
						<a href="javascript:;" class="right">&gt;</a>
					</div>
					<div class="table-data">
						<table>
							<thead>
								<tr>
									<th>入口页面</th>
									<th>浏览量(PV)</th>
									<th>占比</th>
								</tr>
								<tr>
									<td>http://www.mamaxinjia.com</td>
									<td>999</td>
									<td><div style="background-color:#DCEBFE; width:90%;">90%</div></td>
								</tr>
								<tr>
									<td>http://www.mamaxinjia.com</td>
									<td>999</td>
									<td><div style="background-color:#DCEBFE; width:80%;">80%</div></td>
								</tr>
								<tr>
									<td>http://www.mamaxinjia.com</td>
									<td>999</td>
									<td><div style="background-color:#DCEBFE; width:70%;">70%</div></td>
								</tr>
								<tr>
									<td>http://www.mamaxinjia.com</td>
									<td>999</td>
									<td><div style="background-color:#DCEBFE; width:60%;">60%</div></td>
								</tr>
								<tr>
									<td>http://www.mamaxinjia.com</td>
									<td>999</td>
									<td><div style="background-color:#DCEBFE; width:50%;">50%</div></td>
								</tr>
								<tr>
									<td>http://www.mamaxinjia.com</td>
									<td>999</td>
									<td><div style="background-color:#DCEBFE; width:40%;">40%</div></td>
								</tr>
								<tr>
									<td>http://www.mamaxinjia.com</td>
									<td>999</td>
									<td><div style="background-color:#DCEBFE; width:30%;">30%</div></td>
								</tr>
								<tr>
									<td>http://www.mamaxinjia.com</td>
									<td>999</td>
									<td><div style="background-color:#DCEBFE; width:20%;">20%</div></td>
								</tr>
								<tr>
									<td>http://www.mamaxinjia.com</td>
									<td>999</td>
									<td><div style="background-color:#DCEBFE; width:10%;">10%</div></td>
								</tr>
								<tr>
									<td>http://www.mamaxinjia.com</td>
									<td>999</td>
									<td><div style="background-color:#DCEBFE; width:5%;">5%</div></td>
								</tr>
							</thead>
						</table>
					</div>
				</div>
			</div>
			<div class="row3 left">
				<div class="table-grid-item">
					<div class="title clearfix">
						<span>异常统计</span>
						<a href="javascript:;">&gt;</a>
					</div>
					<div class="line-row">
						<div class="control-bar left">
							<a href="javascript:;">错误量</a>
							<a href="javascript:;">警告量</a>
						</div>
					</div>
					<div id="grid3"></div>
				</div>
			</div>
		</div>
		
	</div>
</template>

<script>
	var echarts = require('echarts');
	export default {
		name: 'app',
		mounted: () => {
			var  myChart = echarts.init(document.getElementById('grid1'));
			myChart.setOption({
			    tooltip : {
			        trigger: 'axis'
			    },
			    legend: {
			        data:['昨日','今日']
			    },
			    grid: {
			        left: '3%',
			        right: '4%',
			        bottom: '3%',
			        containLabel: true
			    },
			    xAxis : [
			        {
			            type : 'category',
			            boundaryGap : false,
			            data : ['周一','周二','周三','周四','周五','周六','周日']
			        }
			    ],
			    yAxis : [
			        {
			            type : 'value'
			        }
			    ],
			    series : [
			        {
			            name:'昨日',
			            type:'line',
			            stack: '总量',
			            areaStyle: {normal: {}},
			            data:[120, 132, 101, 134, 90, 230, 210]
			        },
			        {
			            name:'今日',
			            type:'line',
			            stack: '总量',
			            areaStyle: {normal: {}},
			            data:[220, 182, 191, 234, 290, 330, 310]
			        }
			    ]
			});
			var  myChart = echarts.init(document.getElementById('grid3'));
			myChart.setOption({
			    tooltip : {
			        trigger: 'axis'
			    },
			    grid: {
			        left: '3%',
			        right: '4%',
			        bottom: '3%',
			        containLabel: true
			    },
			    xAxis : [
			        {
			            type : 'category',
			            boundaryGap : false,
			            data : ['周一','周二','周三','周四','周五','周六','周日']
			        }
			    ],
			    yAxis : [
			        {
			            type : 'value'
			        }
			    ],
			    series : [
			        {
			            name:'邮件营销',
			            type:'line',
			            stack: '总量',
			            areaStyle: {normal: {}},
			            data:[120, 132, 101, 134, 90, 230, 210]
			        }
			    ]
			});
		}
	}
</script>

<style>
a {
	text-decoration: none;
}
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: #2c3e50;
}
.table-list {
	padding-top: 6px;
	padding-right: 1px;
	border: 1px solid #dedede;
	border-bottom: 0;
	margin-top: 20px;
}
.card-title {
	padding: 14px 15px 0;
	font-weight: bold;
}
.list {
	width: 100%;
	border-collapse: collapse;
}
.list td, .list th {
	padding: 6px 20px 6px 20px;
}
.list td {
	border-left: 1px solid #f0f0f0;
}
.list td:first-child {
	border-left: 0;
}
.list tr {
	text-align: right;
}
.list .normal {
	width: 185px;
	text-align: left;
}
.list .fade {
	background: #f9f9f9;
}
.empty-tr {
	height: 20px;
	border-top: 1px solid #f0f0f0;
}
.empty-tr2 {
	border-top: 0;
}
.fold {
	height: 30px;
	border: 1px solid #dedede;
	border-top: 1px solid #f0f0f0;
	text-align: center;
}
.fold a {
	display: inline-block;
	width: 50px;
	height: 26px;
	margin-top: -1px;
	background: #fbfcfc;
}
.date-select-bar {
	margin-top: 18px;
	border: 1px solid #dfe0e0;
	overflow: hidden;
}
.control-bar a {
	height: 30px;
	line-height: 30px;
	padding: 0 20px;
	float: left;
	border-right: 1px solid #f2f4f4;
}
.control-bar a:hover {
	background: #f3f4f4;
}

.line-row .control-bar a {
	height: 26px;
	line-height: 26px;
}
.table-grid-item {
	margin: 18px 0;
	overflow: hidden;
	width: 530px;
	height: 412px;
	border: 1px solid #e1e2e2;
}
.left {
	float: left;
}
.right {
	float: right;
}
.wrap {
	width: 1080px;
	margin: 0 auto;
}
.clearfix:after {
	content: '';
	height: 0;
	display: block;
	clear: both;
}
.title {
	padding: 20px 20px 10px;
}
.title span {
	float: left;
	font-size: 18px;
	font-weight: bold;
}
.title a {
	float: right;
	width: 18px;
	height: 18px;
	border-radius: 50%;
	color: #fff;
	background: #D5D6D8;
	text-align: center;
}
.table-data {
	padding: 0 20px;
}
.table-data table {
	width: 100%;
}
.table-data *:nth-child(1) {
	text-align: left;
}
.table-data *:nth-child(2) {
	width: 20%;
	text-align: right;
}
.table-data *:nth-child(3) {
	width: 20%;
	padding-left: 20px;
	text-align: left;
}
.table-data td, .table-data th {
	padding: 0 0 10px;
}

.al {
	text-align: left;
}
.ar {
	text-align: right;
}
#grid1, #grid3 {
	height: 315px;
}
.line-row {
	height: 26px;
	font-size: 14px;
	padding: 0 20px;
}
.line-row .control-bar {
	border: 1px solid #e1e3e4;
	border-radius: 5px;
}
.check-group {
	margin-top: 9px;
}
.check-group input {
	vertical-align: middle;
}
</style>