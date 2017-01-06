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
        <!-- <div id="grid3"></div>
 -->        <div style="text-align:center;">
        	<table class="msg-table">
        		<tr>
        			<th>页面URL</th>
        			<th>错误量</th>
        			<th>异常量</th>
                    <th>时间</th>
        		</tr>
        		<tr v-for="cont in content">
        			<td>{{cont[0]}}</td>
        			<td>{{cont[1]}}</td>
                    <td>{{cont[2]}}</td>
                    <td>{{cont[3]}}</td>

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
        day:'week'   //day为week为1周，month为1月
    };
	//今天初始化函数
	var to_s = {
    	appkey : '201612274',
    	day : 'today'		//day=today 为今天 ， day=yesterday为昨天
    };
    //昨天初始化参数
    var yes_s = {
    	appkey : '201612274',
    	day : 'yesterday'
    };
    var weeAndMon = {
        appkey : '201612274',
        day : 'week'
    };
	export default {
        data() {
            return{
                appkey:'201612274',
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
                weeAndMon.day = 'week';
                this.exceptionDaysCompare(weeAndMon);
            },
            month(){
                weeAndMon.day = 'month';
                this.exceptionDaysCompare(weeAndMon);
            },
			exceptionHoursCompare(param){
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
                        if(data.code == 200) {
                            //得到后端传递来的所有数据
                            var code = data.code;   //得到状态返回值
                    		var date = data.data.item[0];     //得到当天的日期
                    		var hours = data.data.item[1];   //小时数
                            var time_interval = data.data.item[2];  //时间区间
                            var err_data = data.data.item[3];     //每个时间区间内的错误量
                    		var war_data = data.data.item[4];     //每个时间区间内的异常量
                            var err_detail = data.data.item[5];
                            var war_detail = data.data.item[6];
                    		//打印数据，验证数据的正确性
                            // console.log(code);
                            // console.log('当天的时间：'+ date);
                            // console.log('总的小时数：'+ hours);
                            // console.log('时间区间：' + time_interval);
                            // console.log('当天的异常量：' + war_data);
                            // console.log('当天的错误量：'+err_data);
                            // console.log('错误详细信息'+ err_detail);
                            // console.log('警告详细信息'+ war_detail);
                            //从二维数组中取出：页面的URLwww.test2.com/example2，得到一个唯一URL的数组
                            function unique(arr){
                                var temp = new Array();
                                var len = arr.length;
                                for(var i =0; i < len; i++){
                                    if(temp.indexOf(arr[i][0]) == -1){
                                        temp.push(arr[i][0]);
                                    }
                                }
                                return temp;
                            }

                            //从一维数组中取出：页面的URLwww.test2.com/example2，得到一个 唯一的URL的数组
                            function unique2(arr){
                                var temp = new Array();
                                var len = arr.length;
                                for(var i =0; i < len; i++){
                                    if(temp.indexOf(arr[i]) == -1){
                                        temp.push(arr[i]);
                                    }
                                }
                                return temp;
                            }
                            var new_err = unique(err_detail);
                            //console.log( new_err);
                            var new_war = unique(war_detail);
                            //console.log(new_war);
                            var totals_arr =new_err.concat(new_war);
                            //console.log(totals_arr);
                            var total_arr =unique2(totals_arr);
                            //console.log(total_arr);

                            //输入上面处理后的三个数组，得到统计信息
                            function count(arr1,arr2,arr3){
                                var err_len = arr1.length;
                                var war_len = arr2.length;
                                var total_arr_len = arr3.length;
                                var res = new Array();
                                var re = new Array();
                                if(err_len == 0 && war_len == 0 ) {
                                    re[0] = '未发生错误或警告';
                                    re[1] = 0;
                                    re[2] = 0;
                                    re[3] = date;
                                    res[0] = re;
                                }else {
                                    for(var i = 0; i < total_arr_len; i++){
                                        var re = new Array();
                                        var err_count = 0;
                                        var war_count = 0;
                                        for(var j = 0; j < err_len; j++){
                                            if(arr3[i] == arr1[j][0] ){
                                                err_count = err_count + 1;
                                            }
                                        }
                                        for(var k = 0; k < war_len; k++){
                                            if(arr3[i] == arr2[k][0] ){
                                                war_count = war_count + 1;
                                            }
                                        }
                                        re[0] = arr3[i];
                                        re[1] = err_count;
                                        re[2] = war_count;
                                        re[3] = date;
                                        res[i] = re;
                                    }
                                }
                                return res;

                            }
                            com.content = count(err_detail,war_detail,total_arr);
                        }
                	}
                });
			},
            exceptionDaysCompare(param){
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
                            var date = data.data.item[0];     //得到当天的日期
                            var date_name = data.data.item[1];   //小时数
                            var err_data = data.data.item[2];     //每个时间区间内的错误量
                            var war_data = data.data.item[3];     //每个时间区间内的异常量
                            var err_detail = data.data.item[4];
                            var war_detail = data.data.item[5];
                            //打印数据，验证数据的正确性
                            // console.log(code);
                            // console.log('当天的日期：'+ date);
                            // console.log('当天的小时数：'+ date_name);
                            // console.log('当天的错误量：' + err_data);
                            // console.log('当天的异常量：'+ war_data);
                            // console.log('错误详细信息'+ err_detail);
                            // console.log('警告详细信息'+ war_detail);
                            function unique(arr){
                                var temp = new Array();
                                var len = arr.length;
                                for(var i =0; i < len; i++){
                                    if(temp.indexOf(arr[i][0]) == -1){
                                        temp.push(arr[i][0]);
                                    }
                                }
                                return temp;
                            }

                            function unique2(arr){
                                var temp = new Array();
                                var len = arr.length;
                                for(var i =0; i < len; i++){
                                    if(temp.indexOf(arr[i]) == -1){
                                        temp.push(arr[i]);
                                    }
                                }
                                return temp;
                            }
                            var new_err = unique(err_detail);
                            //console.log( new_err);
                            var new_war = unique(war_detail);
                            //console.log(new_war);
                            var totals_arr =new_err.concat(new_war);
                            //console.log(totals_arr);
                            var total_arr =unique2(totals_arr);
                            //console.log(total_arr);

                            function count(arr1,arr2,arr3){
                                var err_len = arr1.length;
                                var war_len = arr2.length;
                                var total_arr_len = arr3.length;
                                var res = new Array();
                                var re = new Array();
                                if(err_len == 0 && war_len == 0 ) {
                                    re[0] = '未发生错误或警告';
                                    re[1] = 0;
                                    re[2] = 0;
                                    re[3] = date;
                                    res[0] = re;
                                }else{
                                    for(var i = 0; i < total_arr_len; i++){
                                        var re = new Array();
                                        var err_count = 0;
                                        var war_count = 0;
                                        for(var j = 0; j < err_len; j++){
                                            if(arr3[i] == arr1[j][0] ){
                                                err_count = err_count + 1;
                                            }
                                        }
                                        for(var k = 0; k < war_len; k++){
                                            if(arr3[i] == arr2[k][0] ){
                                                war_count = war_count + 1;
                                            }
                                        }
                                        re[0] = arr3[i];
                                        re[1] = err_count;
                                        re[2] = war_count;
                                        re[3] = date;
                                        res[i] = re;
                                    }
                                }
                                return res;
                            }
                            com.content = count(err_detail,war_detail,total_arr);
                        }
                        
                    }
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