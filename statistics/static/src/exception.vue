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
        			<th>时间段</th>
        			<th>错误量</th>
        			<th>异常量</th>
        		</tr>
        		<tr v-for="cont in content">
        			<td>{{cont[0]}}</td>
        			<td>{{cont[1]}}</td>
                    <td>{{cont[2]}}</td>

<!--         			<td>
                        <span>{{err[2][0]}}</span><br>
                        <span>{{err[2][1]}}</span>
                    </td> -->
        		</tr>
        	</table>
<!--             <table class="msg-table">
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
            </table> -->
    	</div>
    </div>
</template>

<script>
    import $ from '../jquery-1.12.1'
    import moment from 'moment'
	var echarts = require('echarts');

    var dayData = {
        appkey:'201612274',
        day:6
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
    var weeAndMon = {
        appkey : '201612274',
        day : 6
    };
	export default {
        data() {
            return{
                appkey:'201612191',
                errors:'',
                warns:'',
                content:[]
            }
        },
		methods:{
			today(){
				this.exceptionHoursCompare(to_s);
			},
			yesterday(){
                this.exceptionHoursCompare(yes_s);
			},
            week(){
                weeAndMon.day = 6;
                this.exceptionDaysCompare(weeAndMon);
            },
            month(){
                weeAndMon.day = 29;
                this.exceptionDaysCompare(weeAndMon);
            },
			exceptionHoursCompare(param){
			    var  myChart = echarts.init(document.getElementById('grid3'));
                var  com = this;
                $.ajax({
                	url:'http://192.168.1.126/mmonitor/exceptions/exception-hours-compare',
                	method:'post',
                	dataType:'json',
                	data:{
                		appkey : param.appkey,
                		day : param.day
                	},
                	success:function(data){
                        if(data.code == 200){
                            //得到后端传递来的所有数据
                            var code = data.code;   //得到状态返回值
                    		var date = data.data.item[0];     //得到当天的日期
                    		var hours = data.data.item[1];   //小时数
                            var time_interval = data.data.item[2];  //时间区间
                            var err_data = data.data.item[3];     //每个时间区间内的错误量
                    		var war_data = data.data.item[4];     //每个时间区间内的异常量
                            var war_name = date + '警告';   //2016-12-30异常量
                            var err_name = date + '错误';  //2016-12-30错误量
                    		//打印数据，验证数据的正确性
                            // console.log(code);
                            // console.log('当天的时间：'+ date);
                            // console.log('总的小时数：'+ hours);
                            // console.log('时间区间：' + time_interval);
                            // console.log('当天的异常量：' + war_data);
                            // console.log('当天的错误量：'+err_data);

                            //计算数组的长度                             
                            var len = date.length;
                            //console.log(len);
                            //计算统计的值
                            var sum_err = 0;
                            var sum_war = 0;
                            for(var i = 0; i < len; i++){
                                sum_err = sum_err + err_data[i];
                                sum_war = sum_war + war_data[i];
                            }
                            var sum_info = new Array();
                            sum_info[0] = '总计：';
                            sum_info[1] = sum_err;
                            sum_info[2] = sum_war;
                            //console.log('错误量' + sum_err);
                            //console.log('警告量' + sum_war);
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
                            arrs.push(sum_info);
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
                    				data: [err_name,war_name]
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
            exceptionDaysCompare(param){
                var  myChart = echarts.init(document.getElementById('grid3'));
                var com = this;
                $.ajax({
                    url:'http://192.168.1.126/mmonitor/exceptions/exception-days-compare',
                    method:'post',
                    dateType:'json',
                    data:{
                        appkey:param.appkey,
                        day:param.day,
                    },
                    success:function(data){
                        if(data.code == 200){
                            //得到后端传递来的所有数据
                            var code = data.code;   //得到状态返回值
                            var date_title = data.data.item[0];     //得到数据标题
                            var date_name = data.data.item[1];    //得到数据的日期名  
                            var err_data = data.data.item[2];     //每个时间区间内的错误量
                            var war_data = data.data.item[3];     //每个时间区间内的异常量
                            var war_name = date_title + '警告';   //2016-12-30异常
                            var err_name = date_title + '错误';  //2016-12-30错误
                            //打印数据，验证数据的正确性
                            // console.log(code);
                            // console.log('当天的数据标题：'+ date_title);
                            // console.log('数据的日期名：'+ date_name);
                            // console.log('当天的错误量：'+ err_data);
                            // console.log('当天的异常量：' + war_data);

                            //计算数组的长度                             
                            var len = date_name.length;
                            //console.log(len);
                            //计算统计的值
                            var sum_err = 0;
                            var sum_war = 0;
                            for(var i = 0; i < len; i++){
                                sum_err = sum_err + err_data[i];
                                sum_war = sum_war + war_data[i];
                            }
                            var sum_info = new Array();
                            sum_info[0] = '总计：';
                            sum_info[1] = sum_err;
                            sum_info[2] = sum_war;
                            //console.log('错误量' + sum_err);
                            //console.log('警告量' + sum_war);
                            //声明一个数据接收时间区间
                            var arrs = new Array();
                            for(var i = 0; i < len; i++){
                                var arr = new Array();
                                arr[0] = date_name[i];
                                arr[1] = err_data[i];
                                arr[2] = war_data[i];
                                arrs[i] = arr;
                                //console.log(arr);
                                //console.log(arrs);
                            };
                            arrs.push(sum_info);
                            //console.log(arrs);
                            com.content = arrs;
                            //console.log(com.content);
                            // 填入数据
                            myChart.setOption({
                                xAxis: {
                                    //x轴对应小时数
                                    data: date_name
                                    // data: ['0','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23']
                                },
                                legend:{
                                    data: [err_name,war_name]
                                    //data:['2016-12-26','2016-12-25']
                                },
                                series: [
                                    {
                                        // 根据名字对应到相应的系列
                                        //画出昨天的图
                                        name : err_name,
                                        data : err_data
                                    },
                                    {
                                        // 根据名字对应到相应的系列
                                        //画出今天的图
                                        name : war_name,
                                        data : war_data
                                    }
                                ]
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
                                name:com.compared,
                                type:'line',
                                areaStyle: {normal: {}},
                                data:[]
                            },
                            {
                                name:com.compare,
                                type:'line',
                                areaStyle: {normal: {}},
                                data:[]
                            }
                        ]
                });
            },
		},
		mounted(){
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