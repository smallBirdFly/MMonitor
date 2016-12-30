<template>
	<div id="app">
        <h5>异常统计</h5>
        <div class="date-select-bar">
            <div class="control-bar">
                <a href="javascript:;" @click="today" class="date" style="background:white">今天</a>
                <a href="javascript:;" @click="yesterday" class="date">昨天</a>
                <a href="javascript:;" @click="week" class="date">最近7天</a>
                <a href="javascript:;" @click="month" class="date">最近30天</a>
            </div>
            
        </div>
        <div id="grid3"></div>
        <div style="text-align:center;">
        	<table class="msg-table">
        		<tr>
        			<th>网站</th>
        			<th>时间</th>
        			<th>错误信息</th>
        		</tr>
        		<tr v-for="err in errors">
        			<td>{{err[0]}}</td>
        			<td>{{err[1]}}</td>
        			<td>
                        <span>{{err[2][0]}}</span><br>
                        <span>{{err[2][1]}}</span>
                    </td>
        		</tr>
        	</table>
            <table class="msg-table">
                <tr>
                    <th>网站</th>
                    <th>时间</th>
                    <th>警告信息</th>
                </tr>
                <tr v-for="war in warns">
                    <td>{{war[0]}}</td>
                    <td>{{war[1]}}</td>
                    <td>
                        <span>{{war[2][0]}}</span><br>
                        <span>{{war[2][1]}}</span>
                    </td>
                </tr>
            </table>
    	</div>
    </div>
</template>

<script>
    import $ from '../jquery-1.12.1'
    import moment from 'moment'
	var echarts = require('echarts');
    var dayData = {
        appkey:'201612191',
        type:'week'
    };
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
        data() {
            return{
                appkey:'201612191',
                errors:'',
                warns:'',
            }
        },
		methods:{
			today(){
				this.exceptionHoursOneDay(to_s);
			},
			yesterday(){
                this.exceptionHoursOneDay(yes_s);
			},
            week(){
                dayData.type = 'week';
                this.exceptionDay(dayData);
            },
            month(){
                dayData.type = 'month';
                this.exceptionDay(dayData);
            },
			exceptionHoursOneDay(param){
			    var  myChart = echarts.init(document.getElementById('grid3'));
                var  com = this;
                $.ajax({
                	url:'http://192.168.1.126/mmonitor/exceptions/exception-hours-one-day',
                	method:'post',
                	dataType:'json',
                	data:{
                		appkey : param.appkey,
                		day : param.day
                	},
                	success:function(data){
                        if(data.code == 200){
                            //得到后端触底来的所有数据
                            var code = data.code;   //得到状态返回值
                    		var date = data.data.item[0];     //得到当天的日期
                    		var war_name = date + '异常量';   //2016-12-30异常量
                    		var err_name = date + '错误量';  //2016-12-30错误量
                    		var hours = data.data.item[1];   //小时数
                            var time_interval = data.data.item[2];  //时间区间
                    		var war_data = data.data.item[3];     //每个时间区间内的异常量
                    		var err_data = data.data.item[4];     //每个时间区间内的错误量
                    		//打印数据，验证数据的正确性
                            /*console.log(code);
                            console.log('当天的时间：'+ date);
                    		console.log('总的小时数：'+ hours);
                            console.log('时间区间：' + time_interval);
                    		console.log('当天的异常量：' + war_data);
                    		console.log('当天的错误量：'+err_data);*/

                            //声明一个数据接收时间区间
                            var arrs = new Array();
                            for(var i = 0; i < 24; i++){
                                var arr = new Array();
                                arr[0] = time_interval[i];
                                arr[1] = err_data[i];
                                arr[2] = war_data[i];
                                arrs[i] = arr;
                                //console.log(arr);
                                //console.log(arrs);
                            };
                            //console.log(arrs);
                            com.content = arrs;
                            //console.log(com.content);
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
            exceptionHour(param){
                var  myChart = echarts.init(document.getElementById('grid3'));
                var com = this;
                $.ajax({
                    url:'http://192.168.1.109/mmonitor/exceptions/exception-days',
                    method:'post',
                    dateType:'json',
                    data:{
                        appkey:param.appkey,
                        type:param.type,
                    },
                    success:function(data){
                        if(data.code == 200){
                            com.compareIp = data.data.item[0][0] + '-' + data.data.item[0][data.data.item[0].length -1] + ' 错误';
                            com.comparePv = data.data.item[0][0] + '-' + data.data.item[0][data.data.item[0].length -1] + ' 异常';
                            for (var i = 0; i < data.data.item[3].length; i++) {
                                data.data.item[3][i][2] = JSON.parse(data.data.item[3][i][2]);
                            };
                            for (var i = 0; i < data.data.item[4].length; i++) {
                                data.data.item[4][i][2] = JSON.parse(data.data.item[4][i][2]);
                            };
                            com.errors = data.data.item[3];
                            com.warns = data.data.item[4];
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
            exceptionDay(param){
                var  myChart = echarts.init(document.getElementById('grid3'));
                var com = this;
                $.ajax({
                    url:'http://192.168.1.109/mmonitor/exceptions/exception-days',
                    method:'post',
                    dateType:'json',
                    data:{
                        appkey:param.appkey,
                        type:param.type,
                    },
                    success:function(data){
                        if(data.code == 200){
                            com.compareIp = data.data.item[0][0] + '-' + data.data.item[0][data.data.item[0].length -1] + ' 错误';
                            com.comparePv = data.data.item[0][0] + '-' + data.data.item[0][data.data.item[0].length -1] + ' 异常';
                            for (var i = 0; i < data.data.item[3].length; i++) {
                                data.data.item[3][i][2] = JSON.parse(data.data.item[3][i][2]);
                            };
                            for (var i = 0; i < data.data.item[4].length; i++) {
                                data.data.item[4][i][2] = JSON.parse(data.data.item[4][i][2]);
                            };
                            com.errors = data.data.item[3];
                            com.warns = data.data.item[4];
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
    .msg-table{
        height: 100%;
        margin: 0px auto;
    }
    .msg-table td{
        text-align: center;
        padding:5px;
        margin: 3px;
        background: #dfe0e0;
    }
</style>