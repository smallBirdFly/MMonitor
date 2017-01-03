<template>
	<div id="app">
		<!-- <div class="table-list">
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
		</div> -->
		<div class="title-top">
			<h3>统计分析</h3>
			<div>

			</div>
			<select class="sel" v-model="selected">
				<option v-for="appkey in this.appkeys" :value="appkey[0]">{{appkey[1]}}</option>
			</select>
		</div>
		<div class="table-list">
			<div class="card-title"><span>今日流量</span></div>
			<table class="list" >
				<tbody>
					<tr class="title">
						<th></th>
						<th>浏览量(PV)</th>
						<th>IP数</th>
					</tr>
					<tr>
						<td class="normal">今日</td>
						<td>{{todaypv}}</td>
						<td>{{todayip}}</td>
					</tr>
					<tr>
						<td class="normal">昨日</td>
						<td>{{yesterdaypv}}</td>
						<td>{{yesterdayip}}</td>
					</tr>
					<!-- <tr>
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
					</tr> -->
					<tr class="empty-tr-2 fade"><td colspan="3"></td></tr>
				</tbody>
			</table>
		</div>
		<div class="fold"></div>
		<div class="date-select-bar">
			<div class="control-bar">
				<a href="javascript:;" @click="today" class="date">今天</a>
				<a href="javascript:;" @click="yesterday" class="date">昨天</a>
				<a href="javascript:;" @click="week" class="date">最近7天</a>
				<a href="javascript:;" @click="month" class="date">最近30天</a>
			</div>
		</div>
		<div class="wrap clearfix">
			<div class="row1 left">
				<div class="table-grid-item">
					<div class="title clearfix">
						<span>趋势图</span>
						<router-link to="/tread">&gt;</router-link>
					</div>
					<div class="line-row">
						<div class="control-bar left">
							<a href="javascript:;" @click="pv" class="type-visit">浏览量(PV)</a>
							<a href="javascript:;" @click="ip" class="type-visit">IP数</a>
						</div>
						<div class="check-group right">
							<span>对比：</span>
							<label>
								<input type="radio" name="date" checked="checked" @change="daybefore">
								前一日
							</label>
							<label>
								<input type="radio" name="date" @change="weekbefore">
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
						<router-link to="/visit">&gt;</router-link>
					</div>
					<div class="table-data">
						<table>
							<thead>
								<tr>
									<th>入口页面</th>
									<th>浏览量(PV)</th>
									<th>占比</th>
								</tr>
								<tr v-for="url in this.urls">
									<td>{{url[0]}}</td>
									<td>{{url[1]}}</td>
									<td><div style='background-color:#DCEBFE;:width="url[2]"'>{{url[2]}}</div></td>
								</tr>
								<!-- <tr>
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
								</tr> -->
							</thead>
						</table>
					</div>
				</div>
			</div>
			<div class="row3 left">
				<!-- 时间条 -->
			    <!-- <div class="exception_date-select-bar">
                    <div class="control-bar">
                        <a href="javascript:;" @click="err" class="date">今天</a>
                        <a href="javascript:;" @click="yesterday" class="date">昨天</a>
                        <a href="javascript:;" @click="week" class="date">最近7天</a>
                        <a href="javascript:;" @click="month" class="date">最近30天</a>
                    </div>
                </div> -->
				<div class="table-grid-item">
					<div class="title clearfix">
						<span>异常统计</span>
						<!-- <a href="javascript:;">&gt;</a> -->
						<router-link to="/exception">&gt;</router-link>
					</div>
					<div class="line-row">
						<div class="control-bar left">
							<a href="javascript:;" @click="err()">错误量</a>
							<a href="javascript:;" @click="warning()">警告量</a>
						</div>
					</div>
					<div id="grid3"></div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import $ from '../jquery-1.12.1'
	var echarts = require('echarts');

	var s = {
		appkey : '201612191',
		startTime : 0,
		endTime: 1,
		type:'pv'
	};
	var appkey = '201612191';
	var d = {
		date:6,
		type:'pv'
	};

	//错误初始化参数
    var err_s = {
    	appkey : '201612274',
    	type : -1,	//type= -1 为错误
    	day : 0		//day=0 为今天 ， day=1为昨天
    };

	//警告初始化参数
    var war_s = {
    	appkey : '201612274',
    	type : 0,	//type=0 为异常
    	day : 0		//day=0 为今天 ， day=1为昨天
    };


	//访问的类型，浏览量或独立访问量
	export default {
		data(){
			return {
				compare:'',
				compared:'',
				todayip:'',
				todaypv:'',
				yesterdayip:'',
				yesterdaypv:'',
				type:'pv量',
				//用于标注是按照天还是按照小时来分析ip和pv
				tag:1,
				//所有的appkey
				appkeys:'',
				selected:'',
				urls:'',
			}
		},
		methods:{
			today(){
				$(".date").click(function(){
					$(this).css('background','green').siblings().css("background-color","white");
				});
				$(".type-visit").click(function(){
					$(this).css('background','green').siblings().css("background-color","white");
				});
				$(".check-group").show();
				s.startTime = 0;
				this.compareHours(s);

				//异常统计，默认显示错误
				err_s.day = 0;
				this.exceptionHoursShow(err_s);
			},
			yesterday(){
				$(".check-group").show();
				s.startTime = 1;
				this.compareHours(s);

				//异常统计，默认显示错误
				err_s.day = 1;
				this.exceptionHoursShow(err_s);
			},
			daybefore(){
				s.endTime = s.startTime + 1;
				this.compareHours(s);
			},
			weekbefore(){
				s.endTime = s.startTime + 6;
				this.compareHours(s);
			},
			pv(){
				if(this.tag == 1){
					s.type = 'pv';
					this.compareHours(s);
				}
				else
				{
					this.type = 'pv量'	;
					d.type = 'pv';
					this.compareDays(d);
				}
			},
			ip(){
				if(this.tag == 0)
				{
					this.type = 'ip量'	;
					d.type = 'ip';
					this.compareDays(d);
				}
				else
				{
					s.type = 'ip';
					this.compareHours(s);
				}
			},
			week(){
				$(".check-group").hide();
				d.date = 6;
				this.compareDays(d);

				//异常统计，默认显示错误
				err_s.day = 6;
				this.exceptionDaysShow(err_s);
			},
			month(){
				$(".check-group").hide();
				d.date = 29;
				this.compareDays(d);

				//异常统计，默认显示错误
				err_s.day = 29;
				this.exceptionDaysShow(err_s);
			},
			err(){
				if(this.tag == 0){
					//按照天计算
					this.type = 'ip量'	;
					d.type = 'ip';
					this.exceptionDaysShow(err_s);
				}else{
					s.type = 'ip';
					this.exceptionHoursShow(err_s);
				}
			},
			warning(){
				if(this.tag == 0){
					//按照天计算
					this.type = 'ip量'	;
					d.type = 'ip';
					this.exceptionDaysShow(war_s);
				}else{
					s.type = 'ip';
					this.exceptionHoursShow(war_s);
				}				
			},
			//在展示页面按小时显示
			exceptionHoursShow(param){
			    var  myChart = echarts.init(document.getElementById('grid3'));
                var  com = this;
                // this.tag = 1;
                $.ajax({
                	url:'http://192.168.1.126/mmonitor/exceptions/exception-hours-show',
                	method:'post',
                	dataType:'json',
                	data:{
                		appkey : param.appkey,
                		type : param.type,
                		day : param.day
                	},
                	success:function(data){
                		var code = data.code;
                		var date_name = data.data.item[0];
                		var hours = data.data.item[1];
                		var date_data = data.data.item[2];
                		console.log('状态码：'+ code);
                		console.log('当天的日期'+ date_name);
                		console.log('当天的小时数' + hours);
                		console.log('当天的数据' + date_data);
                		// 填入数据
                		myChart.setOption({
           	     			xAxis: {
                			    data: hours
                			    // data: ['0','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23']
                			},
                			legend:{
                				data: [date_name]
                				//data:['2016-12-26']
                			},
                			series: [
                                {
                                    //根据名字对应到相应的系列
                                    //画出昨天的图
                                    name : date_name,
                                    data : date_data
                                }
                			]
                		});
                	}
                });
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
                	calculable: true,
                	xAxis : [
						{
							type : 'category',
							boundaryGap : false,
							data : []
						  	// data : ['0','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23']
						}
					],
                	yAxis : [
                		{
                			type : 'value'
                		}
                	],
                	series : [
                		{
							name:com.compared,
							type:'line',
							areaStyle: {normal: {}},
							data:[]
						}
                	]
                });
			},
			//在展示页面按天显示
			exceptionDaysShow(param){
			    var  myChart = echarts.init(document.getElementById('grid3'));
                var  com = this;
                // this.tag = 1;
                $.ajax({
                	url:'http://192.168.1.126/mmonitor/exceptions/exception-days-show',
                	method:'post',
                	dataType:'json',
                	data:{
                		appkey : param.appkey,
                		type : param.type,
                		day : param.day
                	},
                	success:function(data){
                		var now_type = param.type;
                		if(now_type == 0){
                			var now_title = '异常量';
                		}else{
                			var now_title = '错误量';
                		}
                		var code = data.code;
                		var date_number = data.data.item[0];
                		var date_name = data.data.item[1];
                		var date_data = data.data.item[2];
                		console.log('状态码：'+ code);
                		console.log('数据的天数'+ date_number);
                		console.log('数据的日期' + date_name);
                		console.log('数据' + date_data);
                		// 填入数据
                		myChart.setOption({
           	     			xAxis: {
                			    data: date_name
                			    // data: ['0','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23']
                			},
                			legend:{
                				data: [now_title]
                				//data:['2016-12-26']
                			},
                			series: [
                                {
                                    //根据名字对应到相应的系列
                                    //画出昨天的图
                                    name : now_title,
                                    data : date_data
                                }
                			]
                		});
                	}
                });
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
                	calculable: true,
                	xAxis : [
						{
							type : 'category',
							boundaryGap : false,
							data : []
						  	// data : ['0','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23']
						}
					],
                	yAxis : [
                		{
                			type : 'value'
                		}
                	],
                	series : [
                		{
							name:com.compared,
							type:'line',
							areaStyle: {normal: {}},
							data:[]
						}
                	]
                });
			},
			//最近7天ip数
			compareHours(data){
				var  myChart = echarts.init(document.getElementById('grid1'));
				var com = this;
				this.tag = 1;
				$.ajax({
					url:'http://192.168.1.109/mmonitor/analyse/compare-hours',
					method:'post',
					dataType:'json',
					data:{
						appkey:appkey,
						startTime:data.startTime,
						endTime:data.endTime,
						type:data.type
					},
					success:function(data){
						com.compare = data.data.item[0][0];
						com.compared = data.data.item[0][1];
						//console.log(com.compare);
						//console.log(com.compared);
					// 填入数据
						myChart.setOption({
							xAxis: {
								data: data.data.item[1]
							},
							legend:{
								data:[com.compared,com.compare]
								//data:['2016-12-26','2016-12-25']
							},
							series: [{
								// 根据名字对应到相应的系列
								name:data.data.item[0][0],
								data: data.data.item[2]
							},
							{
								// 根据名字对应到相应的系列
								name:data.data.item[0][1],
								data: data.data.item[3]
							}
							]
						});
					}
				});
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
					calculable: true,
					xAxis : [
						{
							type : 'category',
							boundaryGap : false,
							data : []
						}
					],
					yAxis : [
						{
							type : 'value'
						}
					],
					series : [
						{
							name:com.compare,
							type:'line',
							areaStyle: {normal: {}},
							data:[]
						},
						{
							name:com.compared,
							type:'line',
							areaStyle: {normal: {}},
							data:[]
						}
					]
				});
			},
			//按照天数比较
			compareDays(data){
				var  myChart = echarts.init(document.getElementById('grid1'));
				var com = this;
				this.tag = 0;
				$.ajax({
					url:'http://192.168.1.109/mmonitor/analyse/compare-days',
					method:'post',
					dataType:'json',
					data:{
						appkey:appkey,
						date:data.date,
						type:data.type
					},
					success:function(data){
						if(data.code == 200){
						// 填入数据
							myChart.setOption({
								xAxis: {
									data: data.data.item[0]
								},
								legend:{
									data:[com.type]
									//data:['2016-12-26','2016-12-25']
								},
								series: [{
									// 根据名字对应到相应的系列
									name:com.type,
									data: data.data.item[1]
								}
								]
							});
						}
					}
				});
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
					calculable: true,
					xAxis : [
						{
							type : 'category',
							boundaryGap : false,
							data : []
						  //  data : ['0','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23']
						}
					],
					yAxis : [
						{
							type : 'value'
						}
					],
					series : [
						{
							name:com.type,
							type:'line',
							areaStyle: {normal: {}},
							data:[]
						}
					]
				});
			},
			//今天和昨天的访问量
			todayYesterday(){
				var vm = this;
				$.ajax({
					url:'http://192.168.1.109/mmonitor/analyse/today',
					method:'post',
					dataType:'json',
					data:{
						appkey:appkey,
					},
					success:function(data){
						// console.log(data.data['today'].pv);
						//this.today = data.data['today']['pv'];
						vm.todaypv = data.data['today'].pv;
						vm.todayip = data.data['today'].ip;
						vm.yesterdaypv = data.data['yesterday'].pv;
						vm.yesterdayip = data.data['yesterday'].ip;
					}
				});
			},
			//查询所有的appkey
			appkeyAll(){
				var vm = this;
				$.ajax({
					url:'http://192.168.1.109/mmonitor/analyse/appkey',
					method:'post',
					dataType:'json',
					data:{
						// appkey:appkey,
					},
					success:function(data){
						vm.appkeys = data.data.item;
					}
				});
			},
			// 查询该appkey下所站点信息
			urlAll(){
				var vm = this;
				$.ajax({
					url:'http://192.168.1.109/mmonitor/analyse/entry',
					method:'post',
					dataType:'json',
					data:{
						appkey:appkey,
					},
					success:function(data){
						vm.urls = data.data.item[0];
					}
				});
			}
			
		},
		watch:{
			selected: function(val) {
            	if(val != appkey){
            		appkey = val;
            		this.today();
            	}       
            }
		},
		mounted() {
			this.today();
			this.todayYesterday();
			this.appkeyAll();
			this.urlAll();
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
.date{
	background:white;
}
.title-top{
	height: 30px;
	border: 1px solid #dedede;
	border-top: 1px solid #f0f0f0;
	background-color: #3385e3;
}
h3{
	margin-top: 2px;
	color: white;
	display: inline;
}
.sel{
	float: right;
	margin:5px 5px; 
	background-color: #3385e3;
}
</style>