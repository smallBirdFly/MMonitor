<template>
	<div id="app">
        <h3>趋势分析</h3>
        <div class="date-select-bar">
            <div class="control-bar">
                <a href="javascript:;" @click="tod" class="date" style="background:green">今天</a>
                <a href="javascript:;" @click="yes" class="date">昨天</a>
                <a href="javascript:;" @click="wee" class="date">最近7天</a>
                <a href="javascript:;" @click="mon" class="date">最近30天</a>
                <div class="block">
                    <span class="demonstration">选择开始日期</span>
                    <el-date-picker
                            v-model="dateStart"
                            type="date"
                            placeholder="选择日期">
                    </el-date-picker>
                </div>
                <span><input v-model='checkedr' type="checkbox" class="compare">范围</span>
                <div class="block" v-if="rangeShow == true">
                    <span class="demonstration">选择结束日期</span>
                    <el-date-picker
                            v-model="dateEnd"
                            type="date"
                            placeholder="选择日期">
                    </el-date-picker>
                </div>
                <span><input v-model='checked' type="checkbox" class="compare">对比</span>
                <div class="block" v-if="compareDate==true">
                    <span class="demonstration">选择日期</span>
                    <el-date-picker
                            v-model="choseDate"
                            type="date"
                            placeholder="选择日期">
                    </el-date-picker>
                </div>
                <div style="float:right" v-if="compareDate == true">
                	<a href="javascript:;" @click="pv" class="compare-type">PV</a>
                	<a href="javascript:;" @click="ip" class="compare-type">IP</a>
                </div>
                <div style="float:right">
	                <a href="javascript:;" @click="hours" class="analysis-time" style="background:green">按时</a>
	                <a href="javascript:;" @click="days" class="analysis-time">按天</a>
	                <a href="javascript:;" @click="weeks" class="analysis-time">按周</a>
	                <a href="javascript:;" @click="months" class="analysis-time">按月</a>
                </div>
            </div>
            
        </div>
        <div id="grid1"></div>
        <div>
        	<table class="count-table">
        		<tr v-if="compareDate == true">
        			<th>时间</th>
        			<th>{{this.time[0]}}{{this.compareType}}量</th>
        			<th>{{this.time[1]}}{{this.compareType}}量</th>
        		</tr>
        		<tr v-if="compareDate == false">
        			<th>时间</th>
        			<th>PV量</th>
        			<th>IP量</th>
        		</tr>
        		<tr v-for="cont in content">
        			<td>{{cont[0]}}</td>
        			<td>{{cont[1]}}</td>
        			<td>{{cont[2]}}</td>
        		</tr>
        		<tr>
        			<td>合计</td>
        			<td>{{sum[0]}}</td>
        			<td>{{sum[1]}}</td>
        		</tr>
        	</table>
    	</div>
    </div>
</template>

<script>
    import $ from '../jquery-1.12.1'
    import moment from 'moment'
	var echarts = require('echarts');
	var trend = {
		appkey : '201612191',
		startTime : moment().format('YYYY-MM-DD'),
		endTime : moment().format('YYYY-MM-DD')
	};
	//比较的初始化数据
	var compareData = {
		appkey : '201612191',
		compareStartDay : moment().format('YYYY-MM-DD'),
		compareEndDay : moment().format('YYYY-MM-DD'),
		comparedStartDay : ""
	};
	export default {
		data(){
		    return {
		    	// 时间控件
		        dateStart:'',
		        dateEnd:'',
		        choseDate:'',
		        //范围和比较
		        compareDate:false,
		        rangeShow:false,
		        checked:false,
		        checkedr:false,
		        // 比较的内容
		        compareIp:'',
		        comparePv:'',
		        // 比较的初始化类型
		        compareTypeTime:'hour',
		        // 开始日期
		        startDate:'',
		        // 结束日期
		        endDate:'',
		        //比较的开始日期
		        dateCompare:'',
		        // 标记是比较的类型
		        tag:'today',
		        // 比较的类型
		        compareType:'pv',
		        // 输出时间
		        content:'',
		        //总数
		        sum:'',
		        //表格比较的时间
		        time:''
		    }
		},
		computed:{
			compareDate :function(){
				if(this.checked==true){
					return true;
				}else if(this.checked==false){
					return false;
				}
			},
			rangeShow : function(){
				if(this.checkedr == true){
					return true;
				}else if(this.checkedr == false){
					return false;
				}
			}
		},
		methods:{
			hours(){
				this.compareTypeTime = 'hour';
				this.common();
			},
			days(){
				this.compareTypeTime = 'day';
				this.common();
			},
			weeks(){
				this.compareTypeTime = 'week';
				this.common();
			},
			months(){
				this.compareTypeTime = 'month';
				this.common();
			},
			tod(){
				this.tag = 'today';
				this.common();
			},
			yes(){
				this.tag = 'yesterday';
				this.common();
			},
			wee(){
				this.tag = 'week';
				this.common();
			},
			mon(){
				this.tag = 'month';
				this.common();
			},
			ran(){
				this.tag = 'range';
				this.common();
			},
			//选择比较的方式
			pv(){
				this.compareType = 'pv';
				this.common();
			},
			ip(){
				this.compareType = 'ip';
				this.common();
			},
			common(){
				//比较的情况
				if(this.compareDate == true){
					if(this.tag == 'today'){
						this.compareToday();
					}else if(this.tag == 'yesterday'){
						this.compareYesterday();
					}else if(this.tag == 'week'){
						this.compareWeek();
					}else if(this.tag == 'month'){
						this.compareMonth();
					}else if(this.tag == 'range'){
						this.compareRange();
					}
				}else{	//不比较的情况
					// 选择的是今天
					if(this.tag == 'today'){
						this.today();
					}else if(this.tag == 'yesterday'){
						this.yesterday();
					}else if(this.tag == 'week'){
						this.week();
					}else if(this.tag == 'month'){
						this.month();
					}else if(this.tag == 'range'){
						this.range();
					}
				}
			},
			//pv和ip分析
			today(){
				//console.log(compareDate);
				$(".date").click(function(){
					$(this).css('background','green').siblings().css("background-color","white");
				});
				$(".analysis-time").click(function(){
					$(this).css('background','green').siblings().css("background-color","white");
				});
				trend.startTime = moment().format('YYYY-MM-DD');
				trend.endTime = moment().format('YYYY-MM-DD');
				this.tag = 'today';
				this.trendHours(trend);
			},
			yesterday(){
				trend.startTime = moment().add(-1,'days').format('YYYY-MM-DD');
				trend.endTime = moment().add(-1,'days').format('YYYY-MM-DD');
				this.trendHours(trend);
			},
			week(){
				// 最近7天
				trend.startTime = moment().add(-6,'days').format('YYYY-MM-DD');
				trend.endTime = moment().format('YYYY-MM-DD');
				if(this.compareTypeTime == 'hour'){
					this.trendHours(trend);
				}else if(this.compareTypeTime == 'day'){
					this.trendDays(trend);
				}
			},
			month(){
				// 最近30天
				trend.startTime = moment().add(-29,'days').format('YYYY-MM-DD');
				trend.endTime = moment().format('YYYY-MM-DD');
				if(this.compareTypeTime == 'hour'){
					this.trendHours(trend);
				}else if(this.compareTypeTime == 'day'){
					this.trendDays(trend);
				}else if(this.compareTypeTime== 'week'){
					this.trendWeeks(trend);
				}
			},
			//按照时间区间分析情况
			range(){
				trend.startTime = this.startDate;
				trend.endTime = this.endDate;
				if(this.compareTypeTime == 'hour'){
					this.trendHours(trend);
				}else if(this.compareTypeTime == 'day'){
					this.trendDays(trend);	
				}else if(this.compareTypeTime == 'week'){
					this.trendWeeks(trend);
				}else if(this.compareTypeTime == 'month'){
					this.trendMonth(trend);
				}
			},

			compareToday(){
				compareData.compareStartDay = moment().format('YYYY-MM-DD'),
				compareData.compareEndDay = compareData.compareStartDay,
				compareData.comparedStartDay = this.dateCompare;
				this.compareHour(compareData);
			},
			compareYesterday(){
				compareData.compareStartDay = moment().add(-1,'days').format('YYYY-MM-DD');
				compareData.compareEndDay = compareData.compareStartDay;
				compareData.comparedStartDay = this.dateCompare;
				this.compareHour(compareData);
			},
			compareWeek(){
				//console.log(this.compareTypeTime);
				compareData.compareStartDay = moment().add(-6,'days').format('YYYY-MM-DD');
				compareData.compareEndDay =  moment().format('YYYY-MM-DD');
				compareData.comparedStartDay = this.dateCompare;
				if(this.compareTypeTime == 'hour'){
					this.compareHour(compareData);
				}else if(this.compareTypeTime == 'day'){
					this.compareDay(compareData);
				}
			},
			compareMonth(){
				//console.log(this.compareTypeTime);
				compareData.compareStartDay = moment().add(-29,'days').format('YYYY-MM-DD');
				compareData.compareEndDay =  moment().format('YYYY-MM-DD');
				compareData.comparedStartDay = this.dateCompare;
				if(this.compareTypeTime == 'hour'){
					this.compareHour(compareData);
				}else if(this.compareTypeTime == 'day'){
					this.compareDay(compareData);
				}else if(this.compareTypeTime == 'week'){
					this.compareWeeks(compareData);
				}
			},
			compareRange(){
				//console.log(this.compareTypeTime);
				compareData.compareStartDay = this.startDate;
				compareData.compareEndDay = this.endDate;
				compareData.comparedStartDay = this.dateCompare;
				if(this.compareTypeTime == 'hour'){
					console.log(compareData.compareStartDay);
					console.log(compareData.compareEndDay);
					console.log(compareData.comparedStartDay);
					this.compareHour(compareData);
				}else if(this.compareTypeTime == 'day'){
					this.compareDay(compareData);
				}else if(this.compareTypeTime == 'week'){
					this.compareWeeks(compareData);
				}else if(this.compareTypeTime == 'month'){
					this.compareMonths(compareData);
				}
			},

			// 按照小时分析pv/ip
			trendHours(data){
			    var  myChart = echarts.init(document.getElementById('grid1'));
                var com = this;
				$.ajax({
					url:'http://192.168.1.109/mmonitor/analyse/trend-hours',
					method:'post',
					dateType:'json',
					data:{
						appkey:data.appkey,
						startTime:data.startTime,
						endTime:data.endTime
					},
					success:function(data){
						if(data.code == 200){
							com.compareIp = data.data.item[0][0] + ' ip';
							com.comparePv = data.data.item[0][0] + ' pv';
							var arrs = new Array();
							for(var i = 0; i < data.data.item[1].length; i++){
								var arr = new Array();
								arr[0]= data.data.item[1][i];
								arr[1]= data.data.item[2][i];
								arr[2]= data.data.item[3][i];
								arrs[i] = arr;
								// console.log(arr);
							}
							com.content = arrs;
							com.sum = data.data.sum[0];
							console.log(com.content);
							console.log(com.sum);
							myChart.setOption({
								xAxis:{
									data:data.data.item[1]
								},
								legend:{
	                				data:[com.compareIp,com.comparePv]
	                			},
								series : [{
									name:com.comparePv,
									data:data.data.item[2],
								},
								{
									name:com.compareIp,
									data:data.data.item[3],
								}
								],
							});
							//console.log(com.compareIp);
							//console.log(com.comparePv);
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
								name:com.comparePv,
								type:'line',
								areaStyle: {normal: {}},
								data:[]
							},
							{
								name:com.compareIp,
								type:'line',
								areaStyle: {normal: {}},
						  //      <!--data:[220, 182, 191, 234, 290, 330, 310,120, 132, 101, 134, 90, 230, 210, 150, 120, 80, 50, 20,120, 132, 101, 134, 90]-->
								data:[]
							}
						]
				});
			},
			// 按照天数分析pv/ip
			trendDays(data){
				var  myChart = echarts.init(document.getElementById('grid1'));
                var com = this;
				$.ajax({
					url:'http://192.168.1.109/mmonitor/analyse/trend-days',
					method:'post',
					dateType:'json',
					data:{
						appkey:data.appkey,
						startTime:data.startTime,
						endTime:data.endTime
					},
					success:function(data){
						if(data.code == 200){
							com.compareIp = data.data.item[0][0] + '-' + data.data.item[0][data.data.item[0].length -1] + ' ip';
							com.comparePv = data.data.item[0][0] + '-' + data.data.item[0][data.data.item[0].length -1] + ' pv';
							var arrs = new Array();
							for(var i = 0; i < data.data.item[1].length; i++){
								var arr = new Array();
								arr[0]= data.data.item[0][i];
								arr[1]= data.data.item[1][i];
								arr[2]= data.data.item[2][i];
								arrs[i] = arr;
								// console.log(arr);
							}
							com.content = arrs;
							com.sum = data.data.sum[0];
							myChart.setOption({
								xAxis:{
									data:data.data.item[0]
								},
								legend:{
	                				data:[com.compareIp,com.comparePv]
	                			},
								series : [{
									name:com.comparePv,
									data:data.data.item[1],
								},
								{
									name:com.compareIp,
									data:data.data.item[2],
								}
								],
							});
						}
						
					}
				});
                console.log(com.compareIp);
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
								name:com.comparePv,
								type:'line',
								areaStyle: {normal: {}},
								data:[]
							},
							{
								name:com.compareIp,
								type:'line',
								areaStyle: {normal: {}},
								data:[]
							}
						]
				});
			},
			// 按照星期分析pv/ip
			trendWeeks(data){
				var  myChart = echarts.init(document.getElementById('grid1'));
                var com = this;
				$.ajax({
					url:'http://192.168.1.109/mmonitor/analyse/trend-weeks',
					method:'post',
					dateType:'json',
					data:{
						appkey:data.appkey,
						startTime:data.startTime,
						endTime:data.endTime
					},
					success:function(data){
						if(data.code == 200){
							com.compareIp = '独立IP';
							com.comparePv = '访问量';
							var arrs = new Array();
							for(var i = 0; i < data.data.item[1].length; i++){
								var arr = new Array();
								arr[0]= data.data.item[0][i];
								arr[1]= data.data.item[1][i];
								arr[2]= data.data.item[2][i];
								arrs[i] = arr;
								// console.log(arr);
							}
							com.content = arrs;
							com.sum = data.data.sum[0];
							myChart.setOption({
								xAxis:{
									data:data.data.item[0]
								},
								legend:{
	                				data:[com.compareIp,com.comparePv]
	                			},
								series : [{
									name:com.comparePv,
									data:data.data.item[1],
								},
								{
									name:com.compareIp,
									data:data.data.item[2],
								}
								],
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
							}
						],
						yAxis : [
							{
								type : 'value'
							}
						],
						series : [
							{
								name:com.comparePv,
								type:'line',
								areaStyle: {normal: {}},
								data:[]
							},
							{
								name:com.compareIp,
								type:'line',
								areaStyle: {normal: {}},
								data:[]
							}
						]
				});
			},
			//按照月份分析pv和ip
			trendMonth(data){
				var  myChart = echarts.init(document.getElementById('grid1'));
                var com = this;
				$.ajax({
					url:'http://192.168.1.109/mmonitor/analyse/trend-months',
					method:'post',
					dateType:'json',
					data:{
						appkey:data.appkey,
						startTime:data.startTime,
						endTime:data.endTime
					},
					success:function(data){
						if(data.code == 200){
							com.compareIp = '独立IP';
							com.comparePv = '访问量';
							var arrs = new Array();
							for(var i = 0; i < data.data.item[1].length; i++){
								var arr = new Array();
								arr[0]= data.data.item[0][i];
								arr[1]= data.data.item[1][i];
								arr[2]= data.data.item[2][i];
								arrs[i] = arr;
								// console.log(arr);
							}
							com.content = arrs;
							com.sum = data.data.sum[0];
							myChart.setOption({
								xAxis:{
									data:data.data.item[0]
								},
								legend:{
	                				data:[com.compareIp,com.comparePv]
	                			},
								series : [{
									name:com.comparePv,
									data:data.data.item[1],
								},
								{
									name:com.compareIp,
									data:data.data.item[2],
								}
								],
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
							}
						],
						yAxis : [
							{
								type : 'value'
							}
						],
						series : [
							{
								name:com.comparePv,
								type:'line',
								areaStyle: {normal: {}},
								data:[]
							},
							{
								name:com.compareIp,
								type:'line',
								areaStyle: {normal: {}},
								data:[]
							}
						]
				});
			},

			// 按照小时比较两个时间内的pv/ip量
			compareHour(data){
				var  myChart = echarts.init(document.getElementById('grid1'));
                var com = this;
                var url = '';
                if(this.compareType == 'pv'){
                	url = 'http://192.168.1.109/mmonitor/analyse/compare-hour-pv'
                }else if(this.compareType == 'ip'){
                	url = 'http://192.168.1.109/mmonitor/analyse/compare-hour-ip';
                }
                //console.log(url);
				$.ajax({
					url:url,
					method:'post',
					dateType:'json',
					data:{
						appkey:data.appkey,
						compareStartDay : data.compareStartDay,
						compareEndDay : data.compareEndDay,
						comparedStartDay : data.comparedStartDay
					},
					success:function(data){
						// console.log(data.data.item[0][data.data.item[0].length -1]);
						if(data.code == 200){
							com.comparePv = data.data.item[0][0] + com.compareType;
							com.compareIp = data.data.item[0][1] + com.compareType;
							var arrs = new Array();
							for(var i = 0; i < data.data.item[1].length; i++){
								var arr = new Array();
								arr[0]= data.data.item[1][i];
								arr[1]= data.data.item[2][i];
								arr[2]= data.data.item[3][i];
								arrs[i] = arr;
								// console.log(arr);
							}
							com.content = arrs;
							com.time = data.data.item[0];
							com.sum = data.data.sum[0];
							myChart.setOption({
								xAxis:{
									data:data.data.item[1]
								},
								legend:{
	                				data:[com.comparePv,com.compareIp]
	                			},
								series : [{
									name:com.comparePv,
									data:data.data.item[2],
								},
								{
									name:com.compareIp,
									data:data.data.item[3],
								}
								],
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
							}
						],
						yAxis : [
							{
								type : 'value'
							}
						],
						series : [
							{
								name:com.comparePv,
								type:'line',
								areaStyle: {normal: {}},
								data:[]
							},
							{
								name:com.compareIp,
								type:'line',
								areaStyle: {normal: {}},
								data:[]
							}
						]
				});
			},
			// 按照天数比较两个时间内的pv/ip量
			compareDay(data){
				var  myChart = echarts.init(document.getElementById('grid1'));
                var com = this;
                var url = '';
                if(this.compareType == 'pv'){
                	url = 'http://192.168.1.109/mmonitor/analyse/compare-day-pv'
                }else if(this.compareType == 'ip'){
                	url = 'http://192.168.1.109/mmonitor/analyse/compare-day-ip';
                }
                //console.log(url);
				$.ajax({
					url:url,
					method:'post',
					dateType:'json',
					data:{
						appkey:data.appkey,
						compareStartDay : data.compareStartDay,
						compareEndDay : data.compareEndDay,
						comparedStartDay : data.comparedStartDay
					},
					success:function(data){
						// console.log(data.data.item[0][data.data.item[0].length -1]);
						if(data.code == 200){
							com.comparePv = data.data.item[0][0] + com.compareType;
							com.compareIp = data.data.item[0][1] + com.compareType;
							var arrs = new Array();
							for(var i = 0; i < data.data.item[1].length; i++){
								var arr = new Array();
								arr[0]= data.data.item[1][i];
								arr[1]= data.data.item[2][i];
								arr[2]= data.data.item[3][i];
								arrs[i] = arr;
								// console.log(arr);
							}
							com.content = arrs;
							com.time = data.data.item[0];
							com.sum = data.data.sum[0];
							myChart.setOption({
								xAxis:{
									data:data.data.item[1]
								},
								legend:{
	                				data:[com.compareIp,com.comparePv]
	                			},
								series : [{
									name:com.comparePv,
									data:data.data.item[2],
								},
								{
									name:com.compareIp,
									data:data.data.item[3],
								}
								],
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
							}
						],
						yAxis : [
							{
								type : 'value'
							}
						],
						series : [
							{
								name:com.comparePv,
								type:'line',
								areaStyle: {normal: {}},
								data:[]
							},
							{
								name:com.compareIp,
								type:'line',
								areaStyle: {normal: {}},
								data:[]
							}
						]
				});
			},
			compareWeeks(data){
				var  myChart = echarts.init(document.getElementById('grid1'));
                var com = this;
                var url = '';
                if(this.compareType == 'pv'){
                	url = 'http://192.168.1.109/mmonitor/analyse/compare-week-pv'
                }else if(this.compareType == 'ip'){
                	url = 'http://192.168.1.109/mmonitor/analyse/compare-week-ip';
                }
                //console.log(url);
				$.ajax({
					url:url,
					method:'post',
					dateType:'json',
					data:{
						appkey:data.appkey,
						compareStartDay : data.compareStartDay,
						compareEndDay : data.compareEndDay,
						comparedStartDay : data.comparedStartDay
					},
					success:function(data){
						// console.log(data.data.item[0][data.data.item[0].length -1]);
						if(data.code == 200){
							com.comparePv = data.data.item[0][0] + com.compareType;
							com.compareIp = data.data.item[0][1] + com.compareType;
							var arrs = new Array();
							for(var i = 0; i < data.data.item[1].length; i++){
								var arr = new Array();
								arr[0]= data.data.item[1][i];
								arr[1]= data.data.item[2][i];
								arr[2]= data.data.item[3][i];
								arrs[i] = arr;
								// console.log(arr);
							}
							com.content = arrs;
							com.time = data.data.item[0];
							com.sum = data.data.sum[0];
							myChart.setOption({
								xAxis:{
									data:data.data.item[1]
								},
								legend:{
	                				data:[com.compareIp,com.comparePv]
	                			},
								series : [{
									name:com.comparePv,
									data:data.data.item[2],
								},
								{
									name:com.compareIp,
									data:data.data.item[3],
								}
								],
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
							}
						],
						yAxis : [
							{
								type : 'value'
							}
						],
						series : [
							{
								name:com.comparePv,
								type:'line',
								areaStyle: {normal: {}},
								data:[]
							},
							{
								name:com.compareIp,
								type:'line',
								areaStyle: {normal: {}},
								data:[]
							}
						]
				});
			},
			compareMonths(data){
				var  myChart = echarts.init(document.getElementById('grid1'));
                var com = this;
                var url = '';
                if(this.compareType == 'pv'){
                	url = 'http://192.168.1.109/mmonitor/analyse/compare-month-pv'
                }else if(this.compareType == 'ip'){
                	url = 'http://192.168.1.109/mmonitor/analyse/compare-month-ip';
                }
                //console.log(url);
				$.ajax({
					url:url,
					method:'post',
					dateType:'json',
					data:{
						appkey:data.appkey,
						compareStartDay : data.compareStartDay,
						compareEndDay : data.compareEndDay,
						comparedStartDay : data.comparedStartDay
					},
					success:function(data){
						// console.log(data.data.item[0][data.data.item[0].length -1]);
						if(data.code == 200){
							com.comparePv = data.data.item[0][0] + com.compareType;
							com.compareIp = data.data.item[0][1] + com.compareType;
							var arrs = new Array();
							for(var i = 0; i < data.data.item[1].length; i++){
								var arr = new Array();
								arr[0]= data.data.item[1][i];
								arr[1]= data.data.item[2][i];
								arr[2]= data.data.item[3][i];
								arrs[i] = arr;
								// console.log(arr);
							}
							com.content = arrs;
							com.time = data.data.item[0];
							com.sum = data.data.sum[0];
							myChart.setOption({
								xAxis:{
									data:data.data.item[1]
								},
								legend:{
	                				data:[com.compareIp,com.comparePv]
	                			},
								series : [{
									name:com.comparePv,
									data:data.data.item[2],
								},
								{
									name:com.compareIp,
									data:data.data.item[3],
								}
								],
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
							}
						],
						yAxis : [
							{
								type : 'value'
							}
						],
						series : [
							{
								name:com.comparePv,
								type:'line',
								areaStyle: {normal: {}},
								data:[]
							},
							{
								name:com.compareIp,
								type:'line',
								areaStyle: {normal: {}},
								data:[]
							}
						]
				});
			},
		},
		watch:{
			dateStart:function(val) {
				if(val) {
					this.startDate = moment(val).format('YYYY-MM-DD').toString();
					if(this.compareDate == false){
						if(this.endDate && this.endDate != ''){
							if(moment(val).unix() >  moment(this.endDate).unix()){
								alert('开始时间必须小于结束时间');
								return;
							}
						this.ran();
						}else{
							this.endDate = this.startDate;
							this.endDate = this.startDate;
							//console.log(this.endDate);
							this.ran();
							this.endDate = '';
						} 
					}else{
						if(this.endDate && this.rangeShow == true && this.dateCompare){
							if(moment(val).unix() >  moment(this.endDate).unix()){
								alert('开始时间必须小于结束时间');
								return;
							}
						this.compareRange();
						}else if(this.rangeShow == false){
							this.endDate = this.startDate;
							this.endDate = this.startDate;
							//console.log(this.endDate);
							this.compareRange();
						} 
					}	
				}
				//console.log(this.startDate);
				// console.log(this.endDate);
			},
			dateEnd:function(val){
				if(val){
					this.endDate = moment(val).format('YYYY-MM-DD').toString();
					if(this.dateStart){
						if(moment(val).unix() <  moment(this.startDate).unix()){
							alert('开始时间必须小于结束时间');
							return;
						}else{
							if(this.dateCompare){
								this.compareRange();
							}else{
								this.ran();
							}
						}	
					}else{
						alert('选择开始时间');
						return;
					}
				}
			},
			choseDate : function(val){
				// console.log(this.tag);
				if(val){
					this.dateCompare = moment(val).format('YYYY-MM-DD').toString();
					this.common();
				}
			}
		},
		mounted() {
			this.common();
		}
	}
</script>

<style>
    #app {
      font-family: 'Avenir', Helvetica, Arial, sans-serif;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      color: #2c3e50;
    }
    #grid1 {
	height: 315px;
    }
    .date-select-bar {
        margin-top: 18px;
        border: 1px solid #dfe0e0;
        overflow: hidden;
    }
    .compare{
		display: inline;
    }
    .block{
    	display: inline;
    }
    .analysis-time{
    	float: right;
    }
    .count-table{
    	height: 100%;
    	margin: 0px auto;
    }
    .count-table td{
    	text-align: center;
    	padding:5px;
    	margin: 3px;
    	background: #dfe0e0;
    }
</style>