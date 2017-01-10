<template>
	<div id="app">
        <h5>自定义上报类型</h5>
        <div class="date-select-bar">
            <div class="control-bar">
                <a href="javascript:;" @click="today" class="date" style="background:white">今天</a>
                <a href="javascript:;" @click="yesterday" class="date">昨天</a>
                <a href="javascript:;" @click="week" class="date">最近7天</a>
                <a href="javascript:;" @click="month" class="date">最近30天</a>
            </div> 
        </div>
        <div>
            type:<input type="text" id="type"/>
            描述:<input type="text" id="description">
            <input type="button" value="添加" @click="add">
        </div>
        <div>
            类型关系描述：
            <select id="desc">
                <option>参阅描述</option>
                <option v-for="cus in custom">{{cus[1]}}----{{cus[2]}}</option>
            </select>
            选择查询类型：
            <select id="type">
                <option v-for="cus in custom">{{cus[1]}}</option>
            </select>
            
            <input type="button" value="确定" @click="customQuery">
        </div>
        <div id="grid3"></div>
        <div style="text-align:center;">
        	<table class="msg-table">
        		<tr>
        			<th>时间段</th>
        			<th>统计量</th>
        		</tr>
        		<tr v-for="cont in content">
        			<td>{{cont[0]}}</td>
        			<td>{{cont[1]}}</td>
        		</tr>
        	</table>
    	</div>
    </div>
</template>

<script>
    import $ from '../jquery-1.12.1'
    import moment from 'moment'
    var echarts = require('echarts');
    var initial = {
        appkey : '201612274',
        day : 'today',   //day为yesterday是昨天，为today是今天,为week 为1周 month为1月
        type : -1
    };
	export default {
        data() {
            return{
                appkey:'201612191',
                errors:'',
                warns:'',
                content:[],
                custom:[]
            }
        },
		methods:{
            custom_query(){
                this.customQuery();
            },
            add(){
                //自定义添加类型
                this.addCustom();
            },
			today(){
                this.queryCustom();
                initial.day = 'today';
				this.customHoursQuery(initial);
			},
			yesterday(){
                initial.day = 'yesterday';
                this.customHoursQuery(initial);
            },
            week(){
                initial.day = 'week';
                this.customDaysQuery(initial);
            },
            month(){
                initial.day = 'month';
                this.customDaysQuery(initial);
            },
            customQuery(){
                var com = this;
                var a = $("#type option:checked").text();
                initial.type = a ;
                var t = initial.type;
                var d = initial.day;
                //console.log(initial.type);
                //console.log(initial.day);
                if( d == 'today' || d == 'yesterday'){
                    // console.log(t);
                    // console.log(d);
                    this.customHoursQuery(initial);
                }else if(d == 'week' || d == 'month'){
                    // console.log(t);
                    // console.log(d);
                    this.customDaysQuery(initial);
                }
            },
            queryCustom(){
                //查询type表中的 type 和 description
                var com = this;
                $.ajax({
                    url:'http://192.168.1.126/mmonitor/custom/query',
                    method:'post',
                    success:function(data){
                        var code = data.code;
                        var custom_data = data.data.item[0];
                        com.custom = custom_data;
                    }
                });
            },
            addCustom(){
                //向数据库中添加自定义类型
                var custom_type = $('#type').val();
                var custom_description = $('#description').val();
                //console.log(custom_type);
                //console.log(custom_description);
                $.ajax({
                    url:'http://192.168.1.126/mmonitor/custom/add',
                    method:'post',
                    dataType:'json',
                    data:{
                        type : custom_type,
                        description : custom_description
                    },
                    success:function(data){
                        var code  = data.code;
                        var message = data.data.item[0];
                        //把
                        $('#type').val("");
                        $('#description').val("");
                        //console.log('返回状态码：'+code);
                        //console.log('返回信息：'+message);
                    }
                });
            },
            customHoursQuery(param){
                var  myChart = echarts.init(document.getElementById('grid3'));
                var com = this;
                $.ajax({
                    url:'http://192.168.1.126/mmonitor/custom/custom-hours-query',
                    method: 'post',
                    dataType: 'json',
                    data: {
                        appkey : param.appkey,
                        day : param.day,
                        type : param.type
                    },
                    success: function(data){
                        if(data.code == 200){
                            //得到后端传递来的所有数据
                            var code = data.code;   //得到返回状态码
                            var date = data.data.item[0];   //得到当天的日期
                            var hours = data.data.item[1];  //得到小时数
                            var time_interval = data.data.item[2];  //得到各小时区间
                            var custom_data = data.data.item[3]; //得到各小时的统计数
                            var custom_detail = data.data.item[4];
                            //计算数组的长度                             
                            var len = hours.length;
                            // console.log(len);
                            //计算统计的值
                            var sum_custom = 0;
                            for(var i = 0; i < len; i++){
                                sum_custom = sum_custom + custom_data[i];
                            }
                            var sum_info = new Array();
                            sum_info[0] = '总计：';
                            sum_info[1] = sum_custom;
                            //console.log('统计量' + sum_custom);

                            //声明一个数据接收时间区间
                            var arrs = new Array();
                            for(var i = 0; i < 24; i++){
                                var arr = new Array();
                                arr[0] = time_interval[i];
                                arr[1] = custom_data[i];
                                arrs[i] = arr;
                            };
                            arrs.push(sum_info);
                            //console.log(arrs);
                            com.content = arrs;
                            //console.log(com.content);

                            myChart.setOption({
                                xAxis: {
                                    //x轴对应小时数
                                    data: time_interval
                                    // data: ['0','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23']
                                },
                                legend:{
                                    data: [date]
                                    //data:['2016-12-26','2016-12-25']
                                },
                                series: [
                                    {
                                        // 根据名字对应到相应的系列
                                        //画出昨天的图
                                        name : date,
                                        data : custom_data
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
                        }
                    ]
                });
            },
            customDaysQuery(param){
                var  myChart = echarts.init(document.getElementById('grid3'));
                var com = this;
                $.ajax({
                    url:'http://192.168.1.126/mmonitor/custom/custom-days-query',
                    method: 'post',
                    dataType: 'json',
                    data: {
                        appkey : param.appkey,
                        day : param.day,
                        type : param.type
                    },
                    success: function(data){
                        if(data.code == 200){
                            //得到后端传递来的所有数据
                            var code = data.code;   //得到返回状态码
                            var date = data.data.item[0];   //得到所查时间段的日期
                            var date_name = data.data.item[1];  //得到各小时区间
                            var custom_data = data.data.item[2]; //得到每一天的统计数
                            var custom_detail = data.data.item[3];  //得到那一天的详细信息

                            //计算数组的长度                             
                            var len = date_name.length;
                            //console.log(len);
                            //计算统计的值
                            var sum_custom = 0;
                            for(var i = 0; i < len; i++){
                                sum_custom = sum_custom + custom_data[i];
                            }
                            var sum_info = new Array();
                            sum_info[0] = '总计：';
                            sum_info[1] = sum_custom;
                            //console.log('错误量' + sum_err);
                            //console.log('警告量' + sum_war);
                            //声明一个数据接收时间区间
                            var arrs = new Array();
                            for(var i = 0; i < len; i++){
                                var arr = new Array();
                                arr[0] = date_name[i];
                                arr[1] = custom_data[i];
                                arrs[i] = arr;
                                //console.log(arr);
                                //console.log(arrs);
                            };
                            arrs.push(sum_info);
                            //console.log(arrs);
                            com.content = arrs;
                            myChart.setOption({
                                xAxis: {
                                    //x轴对应小时数
                                    data: date_name
                                    // data: ['0','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23']
                                },
                                legend:{
                                    data: [date]
                                    //data:['2016-12-26','2016-12-25']
                                },
                                series: [
                                    {
                                        // 根据名字对应到相应的系列
                                        //画出昨天的图
                                        name : date,
                                        data : custom_data
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
                        }
                    ]
                });
            }
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