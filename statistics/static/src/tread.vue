<template>
	<div id="app">
        <div id="grid1"></div>
    </div>
</template>

<script>
    import $ from '../jquery-1.12.1'
	var echarts = require('echarts');
	export default {
		name:'app',
		methods:{
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
</style>