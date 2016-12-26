<template>
	<div id="app">
        <h5>趋势分析</h5>
        <div class="date-select-bar">
            <div class="control-bar">
                <a href="javascript:;" @click="today" class="date">今天</a>
                <a href="javascript:;" @click="yesterday" class="date">昨天</a>
                <a href="javascript:;" @click="week" class="date">最近7天</a>
                <a href="javascript:;" @click="month" class="date">最近30天</a>
                <div class="block">
                    <span class="demonstration">选择日期</span>
                    <el-date-picker
                            v-model="value1"
                            type="date"
                            placeholder="选择日期">
                    </el-date-picker>
                </div>
                <span><input v-model='checked' type="checkbox" class="compare">对比</span>
                <div class="block" v-if="dataShow==true">
                    <span class="demonstration">选择日期</span>
                    <el-date-picker
                            v-model="value2"
                            type="date"
                            placeholder="选择日期">
                    </el-date-picker>
                </div>
            </div>
            
        </div>
        <div id="grid1"></div>
    </div>
</template>

<script>
    import $ from '../jquery-1.12.1'
	var echarts = require('echarts');
	export default {
		data(){
		    return {
		        value1:'',
		        dataShow:false,
		        checked:false
		    }
		},
		computed:{
			dataShow :function(){
				if(this.checked==true){
					return true;
				}else if(this.checked==false){
					return false;
				}
			}
		},
		methods:{
			/*test(){
				var self = this;
				$(".compare").click(function(){
					if($(this).is(':checked')){
						self.datashow = true;
						console.log(1111);
						console.log(self.datashow);
					}
				});	
			},*/
			today(){

			},
			yesterday(){

			},
			week(){

			},
			month(){

			},
		    draw1(){
		        $.ajax({
					url:'http://192.168.1.109/mmonitor/analyse/compare-hours',
					method:'post',
					dataType:'json',
					data:{
						appkey:'201612192',
						startTime:'1',
						endTime:'6',
						type:'ip'
					},
					success:function(data){
						console.log(data.data)
					// 填入数据
						myChart.setOption({
							xAxis: {
								data: data.data.item[0]
							},
							series: [{
								// 根据名字对应到相应的系列
								name:'昨日',
								data: data.data.item[1]
							},
							{
								// 根据名字对应到相应的系列
								name:'今日',
								data: data.data.item[2]
							}
							]
						});
					}
				});
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
							name:'昨日',
							type:'line',
							areaStyle: {normal: {}},
							data:[]
						},
						{
							name:'今日',
							type:'line',
							areaStyle: {normal: {}},
					  //      <!--data:[220, 182, 191, 234, 290, 330, 310,120, 132, 101, 134, 90, 230, 210, 150, 120, 80, 50, 20,120, 132, 101, 134, 90]-->
							data:[]
						}
					]
				});
		    }
		},
		mounted() {
			this.draw1();
			//this.test();
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
</style>