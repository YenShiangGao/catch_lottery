var mod_path = "/usr/lib/node_modules/";
var request = require(mod_path + 'request');
var schedule = require(mod_path + 'node-schedule');
var Log = require('../_libs/Log');


var telegram = function() {
    var _this = this;
    // var rule = new schedule.RecurrenceRule();
    var config = {
        "catchlottery": {},
        "autocreatdata": {
            "url": "http://www.catch-lottery.yuni20170704.rdgs.team/gloco/autocreatdata",
            "time": {
                "hours": 6,
                "minute": 15
            }
        }
    };

    var Run = function(url) {
        console.log('開始執行');
        Log.SetLog(__dirname, '開始執行');
        Log.SetLog(__dirname, url);

        var options = { encoding: 'utf8', url: url, timeout: 1000 * 60 };

        request.post(options, function(err, resp, body) {
            if (!err && resp.statusCode == 200) {
                console.log('回傳 : ' + body);
                Log.SetLog(__dirname, '回傳 : ' + body);
                var obj = parseJSON(body);
                if (obj) {
                    switch (obj.code) {
                        case 100:
                            console.log('執行成功');
                            Log.SetLog(__dirname, '執行成功');
                            break;
                        default:
                            console.log('執行失敗');
                            Log.SetLog(__dirname, '執行失敗');
                            break;
                    }
                } else {
                    console.log('執行失敗，回傳值非JSON格式');
                    Log.SetLog(__dirname, '執行失敗，回傳值非JSON格式');
                }
            }
        });
    }

    var parseJSON = function(str) {
        try {
            var obj = JSON.parse(str);
            return obj;
        } catch (e) {
            return false;
        }
    }

    var setRule = function(data) {
        console.log('時間初始');
        Log.SetLog(__dirname, '時間初始');

        var rule = new schedule.RecurrenceRule();

        if (data["time"]["hours"] != undefined)
            rule.hours = data["time"]["hours"];

        if (data["time"]["minute"] != undefined)
            rule.minute = data["time"]["minute"];

        initRun(rule, data);
    }

    var initRun = function(rule, data) {
        Log.SetLog(__dirname, '等待執行...');

        var j = schedule.scheduleJob(rule, function() {
            Run(data["url"]);
        });
    }

    var init = function() {
        console.log('排程初始化...');
        Log.SetLog(__dirname, '排程初始化...');

        for (var key in config) {
            switch (key) {
                case 'catchlottery':
                    for (var key2 in config[key]) {
                        setRule(config[key][key2]);
                        // initRun(config[key][key2]);
                    }
                    break;
                case 'autocreatdata':
                    setRule(config[key]);
                    // initRun(config[key]);
                    break;
            }
        }
    }

    var initData = function(callback) {
        console.log('開始執行 基本設定');
        Log.SetLog(__dirname, '開始執行 基本設定');

        var options = {
            encoding: 'utf8',
            url: "http://www.catch-lottery.yuni20170704.rdgs.team/gloco/gameGroup",
            timeout: 1000 * 60
        };

        request.post(options, function(err, resp, body) {
            Log.SetLog(__dirname, resp);
            if (!err && resp.statusCode == 200) {
                console.log('回傳 基本設定 : ' + body);
                Log.SetLog(__dirname, '回傳 基本設定 : ' + body);
                var obj = parseJSON(body);
                if (obj) {
                    switch (obj.code) {
                        case 100:
                            console.log('執行成功 基本設定');
                            Log.SetLog(__dirname, '執行成功 基本設定');

                            if (config["catchlottery"] == undefined)
                                config["catchlottery"] = new Object();

                            for (var i = 0; i < obj.list.length; i++) {
                                var ename = obj.list[i]["ename"];
                                var url = "http://www.catch-lottery.yuni20170704.rdgs.team/gloco/catchApi/" + ename;
                                var param_2 = obj.list[i]["param_2"];
                                if (config["catchlottery"][ename] == undefined)
                                    config["catchlottery"][ename] = new Object();

                                config["catchlottery"][ename]["url"] = url;
                                config["catchlottery"][ename]["time"] = param_2;
                            }

                            callback();

                            break;
                        default:
                            console.log('執行失敗 基本設定');
                            Log.SetLog(__dirname, '執行失敗 基本設定');
                            break;
                    }


                } else {
                    console.log('執行失敗 基本設定，回傳值非JSON格式');
                    Log.SetLog(__dirname, '執行失敗 基本設定，回傳值非JSON格式');
                }
            }
        });
    }
    initData(init);
    // init();
}

var telegramRun = new telegram();
