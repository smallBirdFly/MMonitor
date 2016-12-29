<template>
	<div id="app">
        <h5>异常统计</h5>
        <div class="date-select-bar">
            <div class="control-bar">
                <a href="javascript:;" @click="today" class="date" style="background:green">今天</a>
                <a href="javascript:;" @click="yesterday" class="date">昨天</a>
            </div>
            
        </div>
        <div id="grid3"></div>
        <div>
        	<table>
        		<tr>
        			<th>时间</th>
        			<th>错误量</th>
        			<th>异常量</th>
        		</tr>
        		<tr v-for="cont in content">
        			<td>{{cont[0]}}</td>
        			<td>{{cont[1]}}</td>
        			<td>{{cont[2]}}</td>
        		</tr>
        	</table>
    	</div>
    </div>
</template>

<script>
    import $ from '../jquery-1.12.1'
    import moment from 'moment'
	var echarts = require('echarts');
	//今天初始化函数
	var to_s = {
    	appkey : '201612274',
    	day : 0		//day=0 为今天 ， day=1为昨天
    };
    //昨天初始化参数
    var yes_s = {
    	appkey : '201612274',
    	day : 1
    };
	export default {
		methods:{
			toddy(){
				this.exceptionHoursOneDay(to_s);
			},
			yesterday(){
                this.exceptionHoursOneDay(yes_s);
			},
			exceptionHoursOneDay(param){
			    var  myChart = echarts.init(document.getElementById('grid3'));
                var  com = this;
                // this.tag = 1;
                $.ajax({
                	url:'http://192.168.1.126/mmonitor/exceptions/exception-hours-one-day',
                	method:'post',
                	dataType:'json',
                	data:{
                		appkey : param.appkey,
                		day : param.day
                	},
                	success:function(data){
                		var date = data.data.item[0];
                		var war_name = date + '异常量';
                		var err_name = date + '错误量';
                		var hours = data.data.item[1];
                		var war_data = data.data.item[2];
                		var err_data = data.data.item[3];
                		console.log('当天的时间'+ date);
                		console.log('总的小时数'+ hours);
                		console.log('当天的异常量' + war_data);
                		console.log('当天的错误量'+err_data);
                		// 填入数据
                		myChart.setOption({
                			xAxis: {
                				//x轴对应小时数
                			    data: hours
                			    // data: ['0','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23']
                			},
                			legend:{
                				data: [war_name,err_name]
                				//data:['2016-12-26','2016-12-25']
                			},
                			series: [
                                {
                                    // 根据名字对应到相应的系列
                                    //画出昨天的图
                                    name : war_name,
                                    data : war_data
                                },
                                {
                                    // 根据名字对应到相应的系列
                                    //画出今天的图
                                    name : err_name,
                                    data : err_data
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
						},
						{
							name:com.compare,
							type:'line',
							areaStyle: {normal: {}},
					       	// data:[220, 182, 191, 234, 290, 330, 310,120, 132, 101, 134, 90, 230, 210, 150, 120, 80, 50, 20,120, 132, 101, 134, 90]
							data:[]
							// data : ['0','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23']
						}
                	]
                });
			},
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
    #grid3 {
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