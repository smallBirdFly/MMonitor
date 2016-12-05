/**
 * Created by Administrator on 2016/11/21 0021.
 */
var schedule = require('node-schedule');
var http = require("http");
var rule = new schedule.RecurrenceRule();
//每天的0时0分执行
rule.dayOfWeek = [0, new schedule.Range(1, 6)];
rule.hour = 0;
rule.minute = 0;
var j = schedule.scheduleJob(rule, function(){
    var options={
        hostname:'192.168.1.109',
        port:80,
        path:'/mmonitor/site/export',
        method:'GET'
    }
//创建请求
    var req=http.request(options,function(res){
        console.log('STATUS:'+res.statusCode);
        console.log('HEADERS:'+JSON.stringify(res.headers));
        res.setEncoding('utf-8');
        res.on('data',function(chunk){
            console.log('数据片段分隔-----------------------\r\n');
            console.log(chunk);
        });
        res.on('end',function(){
            console.log('响应结束********');
        });
    });
    req.on('error',function(err){
        console.error(err);
    });
    req.end();
});

var rule = new schedule.RecurrenceRule();
//整点执行
rule.minute = 0;

var j = schedule.scheduleJob(rule, function(){

    var options={
        hostname:'192.168.1.109',
        port:80,
        path:'/mmonitor/site/savedb',
        method:'GET'
    }
//创建请求
    var req=http.request(options,function(res){
        console.log('STATUS:'+res.statusCode);
        console.log('HEADERS:'+JSON.stringify(res.headers));
        res.setEncoding('utf-8');
        res.on('data',function(chunk){
            console.log('开始-----------------------\r\n');
            console.log(chunk);
        });
        res.on('end',function(){
            console.log('响应结束********');
        });
    });
    req.on('error',function(err){
        console.error(err);
    });
    req.end();
});


