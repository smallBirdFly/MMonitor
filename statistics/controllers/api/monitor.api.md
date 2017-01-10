## AnalyseController
### 1.获取今天和昨天的访问量
+ 请求方式： `HTTP POST`
+ 请求地址：`analyse/today`
+ 请求参数：
``` 
	{
		"appkey": "201612191"   //站点的appkey
	}
```
+ 数据返回格式：
``` 
    response:{
          "code": 200,
          "data": {
                "domain": {
                  "id": "14",
                  "appkey": "201612191",   //网站appkey  
                  "domain_name": "www.test1.com"   //appkey所对应的页面地址
                },
                "today": {
                  "pv": "pv量",   //今天的pv访问量
                  "ip": "ip量"   //今天的ip访问量
                },
                "yesterday": {
                  "pv": "pv量",   //昨天的pv访问量
                  "ip": "ip量"   //昨天的ip访问量
                }
            }
    }
```

### 2.获取所有的站点信息
+ 请求方式：`HTTP POST`
+ 请求地址：`analyse/appkey`
+ 请求参数：无
+ 数据返回格式：
``` 
    response:{
        "code": 200,
        "data": {
            "item": [
                [
	                "appkey1",
	                "域名1"
                ],
                [
	                "appkey2",
	                "域名2"
                ],
            ]
        }
    }
```

### 3.统计分析-按照小时分析
+ 请求方式：`HTTP POST`
+ 请求地址：`analyse/compare-hours`
+ 请求参数：
``` 
	{
        "appkey": "站点的appkey",
        "startTime": "分析开始的日期",
        "endTime": "分析结束的日期"
    }
``` 
+ 数据返回格式：

```
	{
	    "code": 200,
	    "data": {
            "item": [
                ["每个小时"],
                ["比较日期的结果"],
                ["被比较日期的结果"]
            ]
        }
    }
```

### 4.统计分析-按照天数分析
+ 请求方式：`HTTP POST`
+ 请求地址：`analyse/compare-days`
+ 请求参数：
```
	{
		"appkey": "201612194",
		"startTime": "要分析的开始日期"
	}
```
+ 数据返回格式：
```
    {
	    "code": 200,
        "data": {
            "item": [
                ["每天日期"],
                ["相应的值"]
            ]
        }
	}
```

### 5.趋势分析-区间时间内按照小时显示pv和ip量
+ 请求方式：`HTTP POST`
+ 请求地址：`analyse/trend-hours`
+ 请求参数：
```
	{
		"appkey": "201612194",
		"startTime": "开始日期",
		"endTime": "结束日期"
	}
```
+ 数据返回格式：
```
	{
		"code": 200,
		"data": {
			"sum": [
				"总pv量",
				"总ip量"
			],
			"item": [
				"开始日期",
				"结束日期"
			],
			["0-23时"],
			["pv量"],
			["ip量"]
		}

	}
```

### 6.趋势分析-区间时间内按照天显示pv和ip量
+ 请求方式：`HTTP POST`
+ 请求地址：`analyse/trend-days`
+ 请求参数：
```
	{
		"appkey": "201612194",
		"startTime": "开始日期",
		"endTime": "结束日期"
	}
```
+ 数据返回格式：
```
	{
		"code": 200,
		"data": {
			"sum": [
				[
					"总pv量",
					"总ip量"
				]
			]，
			"item": [
				["每一天的日期"],
				["pv量"],
				["ip量"]
			]
		}
	}
```

### 7.趋势分析-区间时间内按照星期显示pv和ip量
+ 请求方式：`HTTP POST`
+ 请求地址：`analyse/trend-weeks`
+ 请求参数：
```
	{
		"appkey": "201612194",
		"startTime": "开始日期",
		"endTime": "结束日期"
	}
```
+ 数据返回格式：
```
	{
        "code": 200,
        "data": {
            "sum": [
              [
                "总pv量",
                "总ip量"
              ]
            ],
            "item": [
                ["每个星期的日期"],
                ["pv量"],
                ["ip量"]
            ]
        }
    }
```

### 8.趋势分析-区间时间内按照月份显示pv和ip量
+ 请求方式：`HTTP POST`
+ 请求地址：`analyse/trend-months`
+ 请求参数：
```
	{
        "appkey": "站点的appkey",
        "startTime": "开始日期",
        "endTime": "结束日期"
    }
```
+ 数据返回格式：
```
	{
        "code": 200,
        "data": {
            "sum": [
              [
                "总pv量",
                "总ip量"
              ]
            ],
            "item": [
                ["每个月的日期"],
                ["pv量"],
                ["ip量"]
            ]
        }
    }
```

### 9.趋势分析-两个区间时间按照小时比较分析pv
+ 请求方式：`HTTP POST`
+ 请求地址：`analyse/compare-hour-pv`
+ 请求参数：
```
	{
        "appkey": "站点的appkey",
        "compareStartDay": "比较开始的时间",
        "compareEndDay": "比较结束的时间",
        "comparedStartDay": "被比较开始的时间"
    }
```
+ 数据返回格式：
```
	{
        "code": 200,
        "data": {
            "sum": [
              [
                "比较时间总pv量",
                "被比较时间总pv量"
              ]
            ],
            "item": [
                [
                    ["比较时间区间"],
                    ["被比较时间区间"]
                ],
                ["每个小时区间"],
                ["比较时间的各个小时pv量"],
                ["被比较时间的各个小时pv量"]
            ]
        }
    }
```

### 10.趋势分析-两个区间时间按照小时比较分析ip
+ 请求方式：`HTTP POST`
+ 请求地址：`analyse/compare-hour-ip`
+ 请求参数：
```
	{
        "appkey": "站点的appkey",
        "compareStartDay": "比较开始的时间",
        "compareEndDay": "比较结束的时间",
        "comparedStartDay": "被比较开始的时间"
    }
```
+ 数据返回格式：
```
	{
        "code": 200,
        "data": {
            "sum": [
              [
                "比较时间总ip量",
                "被比较时间总ip量"
              ]
            ],
            "item": [
                [
                    ["比较时间区间"],
                    ["被比较时间区间"]
                ],
                ["每个小时区间"],
                ["比较时间的各个小时ip量"],
                ["被比较时间的各个小时ip量"]
            ]
        }
    }
```

### 11.趋势分析-两个区间时间按照天数比较分析pv
+ 请求方式：`HTTP POST`
+ 请求地址：`analyse/compare-day-pv`
+ 请求参数：
```
	{
        "appkey": "站点的appkey",
        "compareStartDay": "比较开始的时间",
        "compareEndDay": "比较结束的时间",
        "comparedStartDay": "被比较开始的时间"
    }
```
+ 数据返回格式：
```
	{
        "code": 200,
        "data": {
            "sum": [
              [
                "比较时间总pv量",
                "被比较时间总pv量"
              ]
            ],
            "item": [
                [
                    ["比较时间区间"],
                    ["被比较时间区间"]
                ],
                ["每天时间区间"],
                ["比较时间的每天pv量"],
                ["被比较时间的每天pv量"]
            ]
        }
    }
```

### 12.趋势分析-两个区间时间按照天数比较分析ip
+ 请求方式：`HTTP POST`
+ 请求地址：`analyse/compare-day-ip`
+ 请求参数：
```
	{
        "appkey": "站点的appkey",
        "compareStartDay": "比较开始的时间",
        "compareEndDay": "比较结束的时间",
        "comparedStartDay": "被比较开始的时间"
    }
```
+ 数据返回格式：
```
	{
        "code": 200,
        "data": {
            "sum": [
              [
                "比较时间总ip量",
                "被比较时间总ip量"
              ]
            ],
            "item": [
                [
                    ["比较时间区间"],
                    ["被比较时间区间"]
                ],
                ["每天时间区间"],
                ["比较时间的 ip时量"],
                ["被比较时间的每天ip量"]
            ]
        }
    }
```

###13. 趋势分析-两个区间时间按照星期数比较分析pv
+ 请求方式：`HTTP POST`
+ 请求地址：`analyse/compare-week-pv`
+ 请求参数：
```
	{
        "appkey": "站点的appkey",
        "compareStartDay": "比较开始的时间",
        "compareEndDay": "比较结束的时间",
        "comparedStartDay": "被比较开始的时间"
    }
```
+ 数据返回格式：
```
	{
        "code": 200,
        "data": {
            "sum": [
              [
                "比较时间总pv量",
                "被比较时间总pv量"
              ]
            ],
            "item": [
                [
                    ["比较时间区间"],
                    ["被比较时间区间"]
                ],
                ["每个星期时间区间"],
                ["比较时间的各个星期pv量"],
                ["被比较时间的各个星期pv量"]
            ]
        }
    }
```

### 14.趋势分析-两个区间时间按照星期数比较分析ip
+ 请求方式：`HTTP POST`
+ 请求地址：`analyse/compare-week-ip`
+ 请求参数：
```
	{
        "appkey": "站点的appkey",
        "compareStartDay": "比较开始的时间",
        "compareEndDay": "比较结束的时间",
        "comparedStartDay": "被比较开始的时间"
    }
```
+ 数据返回格式：
```
	{
        "code": 200,
        "data": {
            "sum": [
              [
                "比较时间总ip量",
                "被比较时间总ip量"
              ]
            ],
            "item": [
                [
                    ["比较时间区间"],
                    ["被比较时间区间"]
                ],
                ["每个星期时间区间"],
                ["比较时间的各个星期ip量"],
                ["被比较时间的各个小星期ip量"]
            ]
        }
    }
```

### 15.趋势分析-两个区间时间按照月份数比较分析pv
+ 请求方式：`HTTP POST`
+ 请求地址：`analyse/compare-month-pv`
+ 请求参数：
```
	{
        "appkey": "站点的appkey",
        "compareStartDay": "比较开始的时间",
        "compareEndDay": "比较结束的时间",
        "comparedStartDay": "被比较开始的时间"
    }
```
+ 数据返回格式：
```
	{
        "code": 200,
        "data": {
            "sum": [
              [
                "比较时间总pv量",
                "被比较时间总pv量"
              ]
            ],
            "item": [
                [
                    ["比较时间区间"],
                    ["被比较时间区间"]
                ],
                ["每个月时间区间"],
                ["比较时间的各个月份pv量"],
                ["被比较时间的各个月份pv量"]
            ]
        }
    }
```

### 16.趋势分析-两个区间时间按照月份数比较分析ip
+ 请求方式：`HTTP POST`
+ 请求地址：`analyse/compare-month-ip`
+ 请求参数：
```
	{
        "appkey": "站点的appkey",
        "compareStartDay": "比较开始的时间",
        "compareEndDay": "比较结束的时间",
        "comparedStartDay": "被比较开始的时间"
    }
```
+ 数据返回格式：
```
	{
        "code": 200,
        "data": {
            "sum": [
              [
                "比较时间总pv量",
                "被比较时间总pv量"
              ]
            ],
            "item": [
                [
                    ["比较时间区间"],
                    ["被比较时间区间"]
                ],
                ["每个月时间区间"],
                ["比较时间的各个月份pv量"],
                ["被比较时间的各个月份pv量"]
            ]
        }
    }
```

## InterviewedController
### 1.访问页面分析-区间时间内站点下所有网页访问情况
+ 请求方式：`HTTP POST`
+ 请求地址：`interviewed/interview`
+ 请求参数：
```
	{
        "appkey": "站点的appkey",
        "startTime": "开始日期",
        "endTime": "结束日期"
    }
```
+ 数据返回格式：
```
	{
        "code": 200,
        "data":{
            "item":[
                ["pv总量, "ip总量"]
            ],
            [   
                ["站点1", "PV量", "ip量"]
            ],
            [   
                ["站点2", "PV量", "ip量"]
            ]
        }
    }
```

### 2.访问页面分析-比较区间时间内站点下所有网页访问情况
+ 请求方式：`HTTP POST`
+ 请求地址：`interviewed/interview`
+ 请求参数：
```
	{
            "appkey": "站点的appkey",
            "compareStartDay": "比较开始的时间",
            "compareEndDay": "比较结束的时间",
            "comparedStartDay": "被比较开始的时间"
        }
```
+ 数据返回格式：
```
	{
        "code":200,
        "data":{
            "item":[
                ["pv总量", "ip总量"]
            ],
            [   
                ["站点1", "时间区间1", "pv量", "ip量", "时间区间2", "pv量", "ip量"]
            ],
            [   
                ["站点2", "时间区间1" , "pv量" , "ip量" , "时间区间2" , "pv量" , "ip量" ]
            ]
        }
      }
```

## ExceptionsController
### 1.按照小时分析，得到一天的错误或警告信息，显示在前台index.vue页面
+ 请求方式：`HTTP POST`
+ 请求地址：`exceptions/exception-hours-show`
+ 请求参数：
```
	{
        "appkey": "201612274",
        "day": "today",   //今天或昨天。today为今天，yesterday为昨天
        "type": "warning"   //错误或警告。warning为警告，error为错误
    }
```
+ 数据返回格式：
```
	{
        "code": 200,
        "data": {
            "item":[
                ["当天的日期"],   //Y-m-d格式
                ["0-23"],   
                ["统计量"]   //错误或警告的统计量
            ]
        }
    }
```
### 2.按照天数分析，得到1周或1月的错误或警告信息，展示在前台index.vue页面
+ 请求方式：`HTTP POST`
+ 请求地址：`exceptions/exception-days-show`
+ 请求参数：
```
	{
        "appkey": "201612274",   //网站的appkey
        "day": "week",   //1周或1月。week为1周，month为1月
        "type": "warning"   //错误或警告。warning为警告，errpr为错误
    }
```
+ 数据返回格式：
```
	{
        "code": 200,
        "data": {
            "item": [
                ["天数数组"],   //0,1,2,3,4,5,6一周，0-29一月
                ["对应天数每一天的日期"],   //Y-m-d格式
		        ["统计量"]   //对应日期每天的错误或异常量
            ]
        }
    }
```
### 3.按照小时分析，得到1天的错误或警告信息，显示在后台excetion.vue页面
+ 请求方式：`HTTP POST`
+ 请求地址：`exceptions/exception-hours-compare`
+ 请求参数：
```
	{
        "appkey": "201612274",   //网站的appkey
        "day": "today"   //今天或明天。today表今天，yesterday表昨天
    }
```
+ 数据返回格式：
```
	{
        "code": 200,
        "data": {
            "item": [
                ["当天的日期"],   //Y-m-d格式
                ["0-23"],   //当天的小时数
                ["00:00-23:59"],   //共24个小时段
                ["错误统计数"],   //对应每个时间区间的错误统计数
                ["警告统计数"],   //对应每个时间区间的警告统计数
                [
                    ["错误页面的地址"]，
                    ["错误页面发生错误的时间"],   //Y-m-d H:i:s格式
                    ["错误信息"]
                ],
                [
                    ["警告页面的地址"]，
                    ["警告页面发生警告的时间"],
                    ["警告信息"] 
                ]
            ]
        }
    }
```
### 4.按照天数分析，得到1周或30天的错误或警告信息，显示在后台excetion.vue页面
+ 请求方式：`HTTP POST`
+ 请求地址：`exceptions/exception-days-compare`
+ 请求参数：
```
	{
        "appkey": "201612274"   //网站的appkey
        "day": "week"   ///表1周或1月。week为1周，month为1月
    }
```
+ 数据返回格式：
```
	{
        "code": 200,
        "data": {
            "item":[
                ["开始日期-结束日期"],   //1周或1月的,Y-m-d格式
                ["对应的每天日期"],
                ["对应日期的错误量"],
                ["对应日期的警告量"],
                [
                    ["错误页面的地址"]，
                    ["错误页面发生错误的时间"],   //Y-m-d H:i:s格式'
                    ["错误信息"]
                ],
                [
                    ["警告页面的地址"]，
                    ["警告页面发生警告的时间"],
                    ["警告信息"] 
                ]
            ]
        }
    }
```
### 5.按小时分析 得到今天或明天，错误或警告排名前十页面信息，显示在index.vue页面
+ 请求方式：`HTTP POST`
+ 请求地址：`exceptions/exception-hours-statistics`
+ 请求参数：
```
	{
        "appkey": "201612274"   //网站的appkey
        "day": "today"   //表今天或昨天。today表今天，yesterday表昨天
        "type": "error"   //表错误或警告，error表错误，warning表警告
    }
```
+ 数据返回格式：
```
	{
        "code": 200,
        "data": {
            "item": [
                ["当天的日期"],
                ["当天错误或"],
                ["当天各个小时段的统计"],
                ["发生错误或警告的详细信息"]
            ]
        }
    }
```
### 6.按天数分析，得到1周或一月，错误或警告排名前十的页面信息，显示在index.vue页面
+ 请求方式：`HTTP POST`
+ 请求地址：`exceptions/exception-days-statistics`
+ 请求参数：
```
	{
        "appkey": "201612274"   //网站的appkey
        "day": "week"   //表1周或1月。week表1周，month表1月
        "type": "error"   //表错误或警告，error表错误，warning表警告
    }
```
+ 数据返回格式：
```
	{
        "code": 200,
        "data": {
            "item": [
                ["日期段"],   //1周或1月的
                ["每天日期"],
                ["每天日期错误或警告统计"],
                ["发生错误或警告的详细信息"]
            ]
        }
    }
```

## CustomController
### 1.添加自定义类型到type数据表
+ 请求方式：`HTTP POST`
+ 请求地址：`custom/add`
+ 请求参数：
```
	{
		"type": "5",   //自定义的type类型，不能与已存在的type类型冲突
		"description": "good"   //自定义type类型的描述，不能为空
	}
```
+ 数据返回格式：
```
	{
		"code": 200,
		"data": {
			"item": [
				["操作后的返回信息"]   //添加成功或添加失败
			]
		}
	}
```
### 2.查询type表的type 和 description 字段
+ 请求方式： `HTTP GET`
+ 请求地址：`custom/query`
+ 请求参数：无
+ 数据返回格式：
```
	{
		"code": 200,
		"data": {
			"item":[
				[
					"1",   //type表id字段的id值
					"-1",   //type表type字段的值
					"error"   //type表description字段的描述信息,错误类型
				],
				[
					"2",
					"0",
					"warning"
				],
				[
					"3",
					"1",
					"enter"
				],
				[...]
			]
		}
	}
```
### 3.按小时自定义查询
+ 请求方式：`HTTP POST`
+ 请求地址：`custom/custom-hours-query`
+ 请求参数：
```
	{
		"appkey": "201612274",   //appkey
		"day": "today",   //day=today，表今天；day=yesterday,表昨天
		"type": "2"   //为type表中type字段的任意值
	}
```
+ 数据返回格式：
```
	{
		"code": 200,
		"data": {
			"item": [
				["2017-01-10"],   //当天的日期
				["0-23的整数"],   //小时数
				["00:00-00:59",
				 "...",
				"23:00-23:59"
				],   //小时段数
				["每小时段的类型统计数"],
				["出现所查类型数据的详细信息"]
			]
		}
	}
```
###4.按天自定义查询
+ 请求方式：`HTTP POST`
+ 请求地址：`custom/custom-hours-query`
+ 请求参数：
```
	{
		"appkey": "201612274",   //appkey
		"day": "today",   //day=week，表上一周；day=month,表上一月
		"type": "2"   //为type表中type字段的任意值
	}
```
+ 数据返回格式：
```
	{
		"code": 200,
		"data": {
			"item": [
				["2016-12-12-2017-01-10"],   //所查的开始日期和结束日期
				["从开始日期到结束日期每天的日期"],
				["所查类型在每天的统计量"],
				["出现所查类型数据的详细信息"]
			]
		}
	}
```

## SiteController
### 6.获取spmcode和content
+ 请求方式：`HTTP GET`
+ 请求地址：`site/saveredis`
+ 请求参数：
```
	{
        "content": [
            {
                "code": "appkey.page.type",   //出现错误的时候传错误信息
                "msg": [err1:'错误信息1',err2:'错误信息2']
            }
        ]
    }
```
+ 数据返回格式：
```
	{
        "code": 200,
        "data": {
            "content": "成功"
        }
    }
```

### 7.将redis数据存入数据库中
+ 请求方式：`HTTP GET`
+ 请求地址：`site/savedisk`
+ 请求参数：无
+ 数据返回格式：
```
	{
        "code": 200,
        "data": {
            "content": "成功"
        }
    }
```

### 8.数据库的导出
+ 请求方式：`HTTP GET`
+ 请求地址：`site/export`
+ 请求参数：无
+ 数据返回格式：
```
	{
        "code": 200,
        "data": {
            "content": "成功"
        }
    }
```

### redis数据格式
+ 请求方式：`HTTP POST`
+ 请求地址：``
+ 请求参数：无
+ 数据返回格式：
```
    {
        "ip": "ip地址",
        "time": "时间",
        "referrer": "信息来源",
        "code": "appkey.page(页面或者逻辑块).类型",  //page是指站点下的某个页面，或页面上的某个部分。类型是指对应的格式，例如 -1：错误；0：警告；1：登入；2：登出；自定义类型：待确定
        "msg": "产生的相关信息"    //msg是指一些备注说明，例如，若是错误或者异常信息，就会带着错误异常信息，其他类型则是其他类型的信息
    }
```