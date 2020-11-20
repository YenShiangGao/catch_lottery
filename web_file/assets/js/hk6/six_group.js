var Shengxiao = new function(){
	var $this = this;
	var Item = ["鼠","牛","虎","兔","龙","蛇","马","羊","猴","鸡","狗","猪"];
	var sheng_group = {
		"0" : ["01","13","25","37","49"],
		"1" : ["12","24","36","48"],
		"2" : ["11","23","35","47"],
		"3" : ["10","22","34","46"],
		"4" : ["09","21","33","45"],
		"5" : ["08","20","32","44"],
		"6" : ["07","19","31","43"],
		"7" : ["06","18","30","42"],
		"8" : ["05","17","29","41"],
		"9" : ["04","16","28","40"],
		"10" : ["03","15","27","39"],
		"11" : ["02","14","26","38"]
	};
	var six_group = {
		"R" : ["01","02","07","08","12","13","18","19","23","24","29","30","34","35","40","45","46"],
		"B" : ["03","04","09","10","14","15","20","25","26","31","36","37","41","42","47","48"],
		"G" : ["05","06","11","16","17","21","22","27","28","32","33","38","39","43","44","49"]
	};
	var FiveItem = ["金","木","水","火","土"];
	var five_group = {
		"G" : ["03","04","17","18","25","26","33","34","47","48"],
		"T" : ["07","08","15","16","29","30","37","38","45","46"],
		"W" : ["05","06","13","14","21","22","35","36","43","44"],
		"F" : ["01","02","09","10","23","24","31","32","39","40"],
		"E" : ["11","12","19","20","27","28","41","42","49"]
	};
	var sheng_ary = {};
	for(var k in sheng_group){
		for(var kk in sheng_group[k]){
			sheng_ary[sheng_group[k][kk]] = k;
		}
	}
	var getShengxiao = function(yyyy){
		var arr=[8,9,10,11,0,1,2,3,4,5,6,7]; 
	  	return /^\d{4}$/.test(yyyy)?arr[yyyy%12]:null;
	}
	var shengAry = {};
	this.GetNowShengxiao = function(dateStr){
		var Year = new Date(dateStr).getFullYear();
		if(shengAry.hasOwnProperty(Year)){
			return shengAry[Year]
		}
		var now = getShengxiao(Year);
		var ary = [];
		for(var i = 0;i < 12;i++){
			ary.push(Item[now]);
			now++;
			if(now==12){
				now = now - 12;
			}
		}
		shengAry[Year] = ary;
		return ary;
	}
	this.GetFiveGroup = function(){
		return five_group;
	}
	this.GetSizeGroup = function(){
		return six_group;
	}
	this.GetShengxiao = function(date){
		if(date===undefined){
			var pcDate = LibLunarFun.convertSolarToLunarToDay();
			date = pcDate[0] + "/" + pcDate[8] + "/" + pcDate[5];
		}else{
			var pcDate = LibLunarFun.convertSolarToLunarToDay(new Date(date));
			date = pcDate[0] + "/" + pcDate[8] + "/" + pcDate[5];
		}
		var today = GetDateInf(new Date(date));
		return getShengxiao(today.Y);
	}
	this.GetNowShengGroup = function(date){
		if(date===undefined){
			var pcDate = LibLunarFun.convertSolarToLunarToDay();
			date = pcDate[0] + "/" + pcDate[8] + "/" + pcDate[5];
		}else{
			var pcDate = LibLunarFun.convertSolarToLunarToDay(new Date(date));
			date = pcDate[0] + "/" + pcDate[8] + "/" + pcDate[5];
		}
		var sh = $this.GetNowShengxiao(date);
		var a = [];
		for(var g in Item){
			var t = {
				str : Item[g]
			}
			var index = $.inArray(Item[g],sh);
			t.number = sheng_group[index];
			a.push(t);
		}
		return a;
	}
	this.GetShengToNumber = function(number,date){
		if(date===undefined){
			var pcDate = LibLunarFun.convertSolarToLunarToDay();
			var today = GetDateInf(new Date());
			date = today.m + "/" + today.d + "/" + today.Y;
		}else{
			var pcDate = LibLunarFun.convertSolarToLunarToDay(new Date(date));
			date = pcDate[0] + "/" + pcDate[8] + "/" + pcDate[5];
		}
		var sh = $this.GetNowShengxiao(date);
		var str = "";
		if(sheng_ary.hasOwnProperty(number)){
			str = sh[sheng_ary[number]];
		}
		return str;
	}
	var init = function(){
		var today = GetDateInf(new Date());
		var s = today.m + "/" + today.d + "/" + today.Y;
		$this.GetNowShengxiao(s);
	}
	init();
}