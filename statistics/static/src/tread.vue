<template>
	<div id="app">
        <h5>趋势分析</h5>
        <div class="date-select-bar">
            <div class="control-bar">
                <a href="javascript:;" @click="today" class="date" style="background:green">今天</a>
                <a href="javascript:;" @click="yesterday" class="date">昨天</a>
                <a href="javascript:;" @click="week" class="date">最近7天</a>
                <a href="javascript:;" @click="month" class="date">最近30天</a>
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
                <div class="block" v-if="dataShow==true">
                    <span class="demonstration">选择日期</span>
                    <el-date-picker
                            v-model="choseDate"
                            type="date"
                            placeholder="选择日期">
                    </el-date-picker>
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
    </div>
</template>

<script>
    import $ from '../jquery-1.12.1'
    import moment from 'moment'
	var echarts = require('echarts');
	var trend = {
		appkey:'201612191',
		startTime:0,
		endTime:-1
	};
	var compareDate = {
		appkey : '201612191',
		compareStartDay : moment().format('YYYY-MM-DD'),
		compareEndDay : moment().add(1,'days').format('YYYY-MM-DD'),
	};
	export default {
		data(){
		    return {
		    	// 时间控件
		        dateStart:'',
		        dateEnd:'',
		        choseDate:'',
		        //范围和比较
		        dataShow:false,
		        rangeShow:false,
		        checked:false,
		        checkedr:false,
		        // 比较的内容
		        compareIp:'',
		        comparePv:'',
		        // 比较的初始化类型
		        compareType:'hour',
		        // 距离现在的天数
		        dateStarts:'',
		        dateEnds:'',
		        //比较的开始日期
		        dateCompare:'',
		        // 标记是比较的类型
		        tag:'today',
		        // 被比较的日期

		    }
		},
		computed:{
			dataShow :function(){
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
			today(){
				console.log(compareDate);
				$(".date").click(function(){
					$(this).css('background','green').siblings().css("background-color","white");;
				});
				$(".analysis-time").click(function(){
					$(this).css('background','green').siblings().css("background-color","white");;
				});
				trend.startTime = 0;
				trend.endTime = -1;
				this.tag = 'today';
				this.trendHours(trend);
			},
			yesterday(){
				trend.startTime = 1;
				trend.endTime = 0;
				this.tag = 'yesterday';
				this.trendHours(trend);
			},
			week(){
				this.tag = 'week';
				if(this.compareType == 'hour'){
					trend.startTime = 6;
					trend.endTime = -1;
					this.trendHours(trend);
				}else if(this.compareType == 'day'){
					trend.startTime = 7;
					trend.endTime = 0;
					this.trendDays(trend);
				}
			},
			month(){
				this.tag = 'month';
				if(this.compareType == 'hour'){
					trend.startTime = 29;
					trend.endTime = -1;
					this.trendHours(trend);
				}else if(this.compareType == 'day'){
					trend.startTime = 30;
					trend.endTime = 0;
					this.trendDays(trend);
				}else if(this.compareType == 'week'){
					trend.startTime = 29;
					trend.endTime = -1;
					this.trendWeeks(trend);
				}
			},
			//按照时间区间分析情况
			range(){
				this.tag = 'range';
				if(this.compareType == 'hour'){
					trend.startTime = this.dateStarts;
					trend.endTime = this.dateEnds - 1;
					this.trendHours(trend);
				}else if(this.compareType == 'day'){
					if(this.rangeShow == true){
						trend.startTime = this.dateStarts + 1;
						trend.endTime = this.dateEnds;
						this.trendDays(trend);
					}else{
						alert('必须是一个时间区间才行');
						return;
					}
					
				}
			},
			compareToday(){
				this.comparePv();
			},
			compareYesterday(){

			},
			compareWeek(){

			},
			compareMonth(){

			},
			hours(){
				this.compareType = 'hour';
			},
			days(){
				this.compareType = 'day';
			},
			weeks(){
				this.compareType = 'week';
			},
			months(){
				this.compareType = 'month';
			},
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
							if(com.rangeShow == false){
								com.compareIp = data.data.item[0][0] + ' ip';
								com.comparePv = data.data.item[0][0] + ' pv';
							}else{
								com.compareIp = data.data.item[0][0] + ' - ' + data.data.item[0][1] + ' ip';
								com.comparePv = data.data.item[0][0] + ' - ' + data.data.item[0][1] + ' pv';
							}
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
							com.compareIp = data.data.item[0][0] + ' - ' + data.data.item[0][data.data.item[0].length -1] + ' ip';
							com.comparePv = data.data.item[0][0] + ' - ' + data.data.item[0][data.data.item[0].length -1] + ' pv';
							/*if(com.rangeShow == false){
								return;	
							}else{
								
							}*/
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
						// console.log(data.data.item[0][data.data.item[0].length -1]);
						if(data.code == 200){
							com.compareIp = '独立IP';
							com.comparePv = '访问量';
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
			compareHourPv(data){

			},
			compareHourIp(data){

			},
		},
		watch:{
			dateStart:function(val) {
				if(val) {
					this.dateStarts = moment(val).format('YYYY-MM-DD').toString();
					//获取距离现在的天数
					var now = moment().format('YYYY-MM-DD HH:mm:ss');
					//console.log(moment(now,'YYYY-MM-DD').unix());
					this.dateStarts = (moment(now,'YYYY-MM-DD').unix() - moment(this.dateStarts,"YYYY-MM-DD").unix())/86400;
					// console.log(this.dateStarts);
					if(this.dateEnd){
						this.range();
					}else if(this.rangeShow == false){
						this.dateEnds = this.dateStarts - 1;
						this.range();
					}
				}
			},
			dateEnd:function(val){
				if(val){
					this.dateEnds = moment(val).format('YYYY-MM-DD').toString();
					//获取距离现在的天数
					var now = moment().format('YYYY-MM-DD HH:mm:ss');
					//console.log(moment(now,'YYYY-MM-DD').unix());
					this.dateEnds = (moment(now,'YYYY-MM-DD').unix() - moment(this.dateEnds,"YYYY-MM-DD").unix())/86400;
					// console.log(this.dateEnds);
					if(this.dateStart){
						if(this.dateEnds - this.dateStarts < 0){
							this.range();
						}else{
							alert('开始时间必须小于结束时间');
							return;
						}
						
					}else{
						
					}
				}
			},
			choseDate : function(val){
				if(val){
					this.dateCompare = moment(val).format('YYYY-MM-DD').toString();
					if(this.tag == 'today'){
						this.compareToday();
					}else if(this.tag == 'yesterday'){
						this.compareYesterday();
					}else if(this.tag == 'week'){
						this.compareWeek();
					}else if(this.tag == 'month'){
						this.compareMonth();
					}
				}
			}
		},
		mounted() {
			this.today();
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
</style>