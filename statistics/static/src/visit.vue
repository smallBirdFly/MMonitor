<template>
	<div id="app">
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
            </div>
        <div class="visit-sum">
        	<table>
	        	<tr>
	        		<td>
	        			<span>浏览量</span><br>
	        			<span>{{sum[0]}}</span>
	        		</td>
	        		<td>
	        			<span>访问数</span><br>
	        			<span>{{sum[1]}}</span>
	        		</td>
	        	</tr>
        	</table>
        </div>
        <div class="visit-table">
        	<table>
        		<tr>
        			<th>页面URL</th>
        			<th>浏览量(PV)</th>
        			<th>访客数(IP)</th>
        			<th>时间</th>
        		</tr>
        		<tr v-for="content in contents" v-if="visitCompare == false">
        			<td>{{content[0]}}</td>
        			<td>{{content[1]}}</td>
        			<td>{{content[2]}}</td>
        			<td>{{date}}</td>
        		</tr>
        		<tr v-for="content in contents" v-if="visitCompare == true">
        			<td>{{content[0]}}</td>
        			<td>
        				<span>{{content[2]}}</span><br>
        				<span>{{content[5]}}</span>
        			</td>
        			<td>
        				<span>{{content[3]}}</span><br>
        				<span>{{content[6]}}</span>
        			</td>
        			<td>
        				<span>{{content[1]}}</span><br>
        				<span>{{content[4]}}</span>
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
    var trend = {
		appkey : '201612191',
		startTime : moment().format('YYYY-MM-DD'),
		endTime : moment().format('YYYY-MM-DD')
	};
	var compareData = {
		appkey : '201612191',
		compareStartDay : moment().format('YYYY-MM-DD'),
		compareEndDay : moment().format('YYYY-MM-DD'),
		comparedStartDay : ""
	}
    export default {
		data(){
		    return {
		    	dateStart:'',
		    	dateEnd:'',
		    	choseDate:'',
		    	checked:false,
		    	checkedr:false,
		    	// 总数
		    	sum:'',
		    	// 内容
		    	contents:'',
		    	//日期
		    	date:'',
		    	visitCompare:false,
		    	//标记正在比较的时间
		    	tag:'today'
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
			//pv和ip分析
			today(){
				this.tag = 'today';
				$(".date").click(function(){
					$(this).css('background','green').siblings().css("background-color","white");
				});
				trend.startTime = moment().format('YYYY-MM-DD');
				trend.endTime = moment().format('YYYY-MM-DD');
				this.interview(trend);
			},
			yesterday(){
				this.tag = 'yesterday';
				trend.startTime = moment().add(-1,'days').format('YYYY-MM-DD');
				trend.endTime = moment().add(-1,'days').format('YYYY-MM-DD');
				this.interview(trend);
			},
			week(){
				// 最近7天
				this.tag = 'week';
				trend.startTime = moment().add(-6,'days').format('YYYY-MM-DD');
				trend.endTime = moment().format('YYYY-MM-DD');
				this.interview(trend);
			},
			month(){
				// 最近30天
				this.tag = 'month';
				trend.startTime = moment().add(-29,'days').format('YYYY-MM-DD');
				trend.endTime = moment().format('YYYY-MM-DD');
				this.interview(trend);
			},
			//按照时间区间分析情况
			range(){
				this.tag = 'range';
				trend.startTime = this.startDate;
				trend.endTime = this.endDate;
				this.interview(trend);
			},
			interview(data){
				var com = this;
				$.ajax({
					url:'http://192.168.1.109/mmonitor/interviewed/interview',
					method:'post',
					dateType:'json',
					data:{
						appkey:data.appkey,
						startTime : data.startTime,
						endTime : data.endTime,
					},
					success:function(data){
						if(data.code == 200){
							com.sum = data.data.item[0];
							com.contents = data.data.item[1];
							com.date = data.data.item[2][0];
						}
					}
				});
			},
			compareInterview(data){
				var com = this;
				$.ajax({
					url:'http://192.168.1.109/mmonitor/interviewed/compare-interview',
					method:'post',
					dateType:'json',
					data:{
						appkey:data.appkey,
						compareStartDay : data.compareStartDay,
						compareEndDay : data.compareEndDay,
						comparedStartDay : data.comparedStartDay
					},
					success:function(data){
						if(data.code == 200){
							com.sum = data.data.item[0];
							com.contents = data.data.item[1];
						}
					}
				});
			}
		},
		watch:{
			dateStart:function(val) {
				if(val) {
					this.startDate = moment(val).format('YYYY-MM-DD').toString();
					// console.log(moment(val).unix());
					if(this.checked == false){
						this.visitCompare = false;
						if(this.endDate && this.rangeShow == true){
							if(moment(val).unix() >  moment(this.endDate).unix()){
								alert('开始时间必须小于结束时间');
								return;
							}
						this.range();
						}else if(this.rangeShow == false){
							this.endDate = this.startDate;
						} 
					}else{
						
					}	
				}
				//console.log(this.startDate);
				// console.log(this.endDate);
			},
			dateEnd:function(val){
				if(val){
					this.endDate = moment(val).format('YYYY-MM-DD').toString();
					if(this.checked == false){
						this.visitCompare = false;
					}
					if(this.dateStart){
						if(this.endDate){
						}else if(moment(val).unix() <  moment(this.startDate).unix()){
							alert('开始时间必须小于结束时间');
							return;
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
					this.visitCompare = true;
					//this.dateCompare = moment(val).format('YYYY-MM-DD').toString();
					if(this.tag == 'today'){
						compareData.compareStartDay = moment().format('YYYY-MM-DD');
						compareData.compareEndDay = moment().format('YYYY-MM-DD');
						compareData.comparedStartDay = moment(val).format('YYYY-MM-DD').toString();
					}else if(this.tag == 'yesterday'){
						compareData.compareStartDay = moment().add(-1,'days').format('YYYY-MM-DD');
						compareData.compareEndDay = compareData.compareStartDay;
						compareData.comparedStartDay = moment(val).format('YYYY-MM-DD').toString();
					}else if(this.tag == 'week'){
						compareData.compareStartDay = moment().add(-6,'days').format('YYYY-MM-DD');
						compareData.compareEndDay =  moment().format('YYYY-MM-DD');
						compareData.comparedStartDay = moment(val).format('YYYY-MM-DD').toString();
					}else if(this.tag == 'month'){
						compareData.compareStartDay = moment().add(-29,'days').format('YYYY-MM-DD');
						compareData.compareEndDay =  moment().format('YYYY-MM-DD');
						compareData.comparedStartDay = moment(val).format('YYYY-MM-DD').toString();
					}else if(this.tag == 'range'){
						compareData.compareStartDay = this.startDate;
						compareData.compareEndDay = this.endDate;
						compareData.comparedStartDay = moment(val).format('YYYY-MM-DD').toString();
					}
					this.compareInterview(compareData);
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
</style>