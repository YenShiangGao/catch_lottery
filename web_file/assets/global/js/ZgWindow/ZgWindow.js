var ZgWindow = function(Gv_option){
	var $this = this;
	var that;
	var bg;
	var _NowTab = 0;
	var GV = $.extend({
		smallPosition 	: "right",
		SetWinsize		: true,
		CanSetsize		: true,
		CanSetMax		: true,
		CanSetMove		: true
	},Gv_option);
	/*預設開啟視窗的控制項*/
	var OptionInit = function(){
		var options = $.extend({
			id				: false,
			type			: "html",			/*html,iframe*/
			title			: "new_windows",
			html			: false,
			url				: false,
			lock 			: false,			/*是否開啟先鎖定  lock = false 以下才有作用(停用)*/ 
			width 			: 600,	
			height 			: 500,
			SetCoordinate 	: false,			/*是否設定座標*/
			top 			: 32,
			left 			: 0,
			minTop			: 0,
			repeat_iframe	: false,			/*是否可以開啟同樣網址的iframe*/
			Classify		: "_New",
			firstShow		: true,				/*先載入視窗再載iframe*/
			UseMask			: false,			/*使用遮罩*/
			OpenMax			: false,			/*預設開啟為最大視窗*/
			SetWinsize		: true,				/*是否可以自行拉視窗大小*/
			CanSetsize		: true,				/*是否可以縮小*/
			CanSetMax		: true,				/*是否可以放到最大*/
			CanSetMove		: true,				/*是否可以移動視窗*/
			TiedObj			: false,			/*綁住的物件(縮小用)*/
			TiedPosition	: "top",			/*top / bottom / right / left / center*/
			ScalingSpeed	: 300,
			containment		: $(window),		/*放到最大的相對物件*/
			containment_top	: 0,
			post_data		: {},
			/*事件*/
			CloseEvent		: function(){},
			OpenEvent		: function(){}
		},GV);
		return options;
	}
	/*取得視窗z-index 最高值*/
	var MaxZindex = function(){
		var maxZ=0;
		that.find(".Windows").each(function(){
			if($(this).css('z-index')!="auto"){
				if($(this).css('z-index') > maxZ*1) maxZ = $(this).css('z-index');
			}
		})
		return maxZ;
	}
	/*確認此網頁(iframe)是否已存在*/
	var checkiframe = function(url){
		var check = false;
		that.find(".Windows").each(function(){
			if($(this).find("iframe[src='"+url+"']").length > 0){
				check = $(this);
			}
		})
		return check;
	}
	/*確認此ID是否已存在*/
	var check_div_id = function(id){
		var check = false;
		if(that.find(".Windows#"+id).length > 0){
			check = that.find(".Windows#"+id);
		}
		return check;
	}
	/*建立視窗*/
	this.OpenWin = function(option){
		var op = OptionInit();
		$.extend( op, option );
		if(op.id){
			var NowID = op.id;
		}else{
			var NowID = "ZgWin_" + _NowTab;
		}
		var some_obj = check_div_id(op.id);
		if(some_obj){
			var obj = some_obj;
		}else{
			var obj = CreateWin(NowID,op);
		}
		switch(op.type){
			case 'iframe':{
				if(!op.repeat_iframe){
					var ck = checkiframe(op.url);
					if(ck){/*有重覆的網址*/
						if(ck.hasClass("small")){
							ToEnlarge(ck,op);
						}
						ck.trigger("click");
						if(!some_obj){
							obj.remove();
						}
						return;
					}
				}
				obj.find(".WinContent").css("overflow","hidden")
				if(!op.url){return;}
				var $iframe = $("<iframe/>").attr({
					"id"			: NowID + "_iframe",
					"name"			: NowID + "_iframe",
					"frameborder"	: 0,
					"vspace"		: 0,
					"hspace"		: 0,
					"vspace"		: 0,
					"width"			: "100%",
					"height"		: "99%"
				}).addClass("ZgIframe");
				$iframe.on("load", function () { //Make sure it is fully loaded
					$iframe.contents().click(function (event) {
						$iframe.trigger("click");
					});
				});
				var form = $("<form>").attr({
					"id"		: NowID + "_form",
					"action" 	: op.url,
					"target"	: NowID + "_iframe",
					"method"	: "POST"
				})
				if(op.post_data){
					for(var i in op.post_data){
						form.append(
							$("<input/>").attr({
								"type"	: "hidden",
								"name"	: i
							}).val(op.post_data[i])
						)
					}
				}
				var d = $("<div/>").css({
					"height"			: "100%",
					"padding-bottom"	: "10px"
				}).html(form).append($iframe);
				obj.find(".WinContent").html(d);
				if(op.firstShow){
					$this.ShowWin(obj,op);
					form.submit();
					//$iframe.attr("src",op.url);
					//$iframe.attr("src",op.url+'#'+NowID);
				}else{
					loading(true);
					$iframe.load(function(){
						loading(false);
						$this.ShowWin(obj,op);
					});
					form.submit();
					//$iframe.attr("src",op.url); 
					//$iframe.attr("src",op.url+'#'+NowID);
				}
				break;
			}
			case 'html':{
				if(!op.html){obj.remove();return;}
				obj.find(".WinContent").html(op.html);
				$this.ShowWin(obj,op);
				break;
			}
		}
		_NowTab++;
		return obj;
	}
	this.ShowWin = function(obj,op){
		if(obj.hasClass("big")){
			obj.trigger("ToTop");
		}else{
			if(obj.hasClass("small")){
				obj.trigger("ToEnlarge")
			}else{
				if(op.TiedObj){
					obj.trigger("ToEnlarge")
					if(op.SetCoordinate){
						if(op.top < op.minTop){
							op.top = op.minTop;
						}
						var objtop = op.top;
						var objleft = op.left;
						if(objtop < 0){
							objtop = 0;
						}
						if(objleft < 0){
							objleft = 0;
						}
						obj.css({
							top 	: objtop,
							left	: objleft
						}).attr({
							"data_top" 	: objtop,
							"data_left" : objleft
						})
					}
				}else{
					obj.removeClass("small").addClass("big");
					obj.css("margin-top",op.containment_top);
					obj.width(op.width);
					obj.height(op.height);
					var w = obj.width();
					var h = obj.height();
					if(op.SetCoordinate){
						if(op.top < op.minTop){
							op.top = op.minTop;
						}
						obj.css({
							top 	: op.top,
							left	: op.left
						}).attr({
							"data_top" 	: op.top,
							"data_left" : op.left
						})
					}else{
						var w_w = $(window).width();
						var w_h = $(window).height();
						var s_top = ((w_h - h) / 2)-32 - op.containment_top;
						var s_left = ((w_w - w) / 2);
						obj.css({
							top  	: s_top,
							left	: s_left
						}).attr({
							"data_top" 	: s_top + op.containment_top,
							"data_left" : s_left
						});
					}
					if(op.CanSetMove){
						obj.draggable("enable");
					}
					if(op.SetWinsize){
						obj.resizable("enable");
					}
					obj.trigger("ToTop");
					obj.show();
					var objtop = obj.position().top;
					var objleft = obj.position().left;
					if(objtop < 0){
						objtop = 0;
					}
					if(objleft < 0){
						objleft = 0;
					}
					obj.css({
						opacity	: 0,
						top		: objtop + 50,
						left	: objleft + 50,
						width	: obj.width() - 100,
						height	: obj.height() - 100,
					})
					obj.find(".WinContent").hide(); 
					obj.stop().animate({
						opacity	: 1,
						top		: "-=50px",
						left	: "-=50px",
						width	: "+=100px",
						height	: "+=100px"
					}, op.ScalingSpeed, function() {
						if(op.OpenMax){
							obj.trigger("ToMaxEnlarge");
						}else{
							obj.find(".WinContent").show();
						}
						obj.trigger("ToTop");
						
					});
				}
				obj.trigger("OpenEvent");
			}
		}
		if(op.UseMask){
			obj.find(".bg").stop(true, true).fadeIn(op.ScalingSpeed);
			obj.addClass("UseMaskWin");
		}else{
			obj.removeClass("UseMaskWin");
		}
	}
	/*刪除視窗*/
	this.CloseWin = function(DivID){
		that.find("#"+DivID).trigger("Close");
	}
	/*刪除同分類的視窗*/
	this.CloseWin_Classify = function(Classify){
		that.find(".Windows").each(function(){
			if($(this).hasClass(Classify)){
				$(this).trigger("Close");
			}
		});
	}
	/*分類的視窗狀態*/
	this.ReStatus_Classify = function(Classify){
		var status = false;
		that.find(".Windows").each(function(){
			if($(this).hasClass(Classify)){
				if($(this).hasClass("small")){
					status = "small";
				}else{
					status = "big";
				}
			}
		});
		return status;
	}
	/*分類的視窗開啟*/
	this.Open_Classify = function(Classify){
		that.find(".Windows").each(function(){
			if($(this).hasClass(Classify)){
				if($(this).hasClass("small")){
					$(this).trigger("ToEnlarge");
				}
			}
		});
	}
	/*分類的視窗縮小*/
	this.Narrow_Classify = function(Classify){
		that.find(".Windows").each(function(){
			if($(this).hasClass(Classify)){
				if($(this).hasClass("big")){
					$(this).trigger("ToNarrow");
				}
			}
		});
	}
	/*分類的視窗置頂*/
	this.Top_Classify = function(Classify){
		var check = true;
		that.find(".Windows").each(function(){
			if($(this).hasClass(Classify)){
				var maxZ = MaxZindex();
				if($(this).css('z-index') < maxZ){
					maxZ++;
					$(this).css('z-index',maxZ)
					check = false;
				}
			}
		});
		return check;
	}
	/*產生視窗(DIV)*/
	var CreateWin = function(ObjID,op){
		var w_div = $('<div/>').attr({
			"id"		: ObjID,
			"data_tied"	: op.TiedObj ? true : false,
		}).addClass("Windows "+ op.Classify).css({
			opacity: 1
		});
		w_div.append(
			$("<div/>").attr("id","ZgWindow_bg").addClass("bg")
		);
		var Cn = $("<div/>").addClass("Cn");
		var Cnn = $("<div/>").addClass("Cnn");
		var WinTitle = $("<div/>").addClass("WinTitle");
		WinTitle.append(
			$("<div/>").addClass("tool").append(function(){
				if(op.CanSetsize){
					return $("<div/>").addClass("glyphicon glyphicon-minus ToSmall");
				}else{
					return false
				}
			}).append(function(){
				if(op.CanSetMax){
					return $("<div/>").addClass("glyphicon glyphicon-unchecked ToMaxEnlarge");
				}else{
					return false
				}
			}).append(
				$("<div/>").addClass("glyphicon glyphicon-remove ZwClose")
			)
		);
		WinTitle.append($("<div/>").addClass("title_string").append(op.title));
		WinTitle.append($("<div/>").addClass("clearfix"));
		Cnn.append($('<div class="WinContent"></div>'));
		Cn.append(Cnn);
		
		w_div.append(WinTitle);
		w_div.append(Cn);
		w_div.append($('<div class="Mask"></div>'));
		w_div.hide();
		that.append(w_div);
		w_div = CreateWinEvent(w_div,op);
		return w_div;
	}
	/*建立視窗(事件)*/
	var CreateWinEvent = function(obj,op){
		if(op.CanSetMove){
			var con = "window";
			if(op.containment.attr("id")){
				con = "#" + op.containment.attr("id");
			}
			obj.draggable({ 
				handle		: ".WinTitle",
				containment	: con, 
				cursor		: "crosshair",
				scroll		: false,
				start: function( event, ui ) {
					obj.find(".Mask").show();
					obj.trigger("ToTop");
				},
				stop: function( event, ui ) {
					obj.find(".Mask").hide();
					if(obj.css("position")=="fixed"){
						console.log(obj.css("position"))
						var eTop = obj.offset().top;
						if(eTop<0){
							eTop = 0;
							obj.css('top', eTop+"px");
						}
					}
					obj.attr({
						"data_top" :ui.position.top,
						"data_left" :ui.position.left
					})
				}
			});
			obj.draggable("disable");
		}
		if(op.SetWinsize){	
			obj.resizable({
				host: true,
				start: function( event, ui ) {
					obj.find(".Mask").show();
					obj.trigger("ToTop");
					obj.find(".WinContent").css("overflow","hidden");
				},
				stop: function( event, ui ) {
					obj.find(".Mask").hide();
					obj.attr({
						"data_width" :ui.size.width,
						"data_height" :ui.size.height
					})
					if(op.type!="iframe"){
						obj.find(".WinContent").css("overflow","auto");
					}
				}
			});
			obj.resizable("disable");
		}
		/*視窗縮小*/
		obj.bind("ToNarrow",op,function(e){
			ToNarrow($(this),e.data);
		})
		/*視窗放大*/
		obj.bind("ToEnlarge",op,function(e){
			ToEnlarge($(this),e.data);
		})
		/*視窗放大*/
		obj.bind("ToMaxEnlarge",op,function(e){
			ToMaxEnlarge($(this),e.data);
		})
		/*視窗刪除*/
		obj.bind("Close",op,function(e){
			$(this).trigger("CloseEvent");
			$(this).find(".WinContent").hide();
			$(this).stop().animate({
				opacity	: 0,
				top		: "+=50px",
				left	: "+=50px",
				width	: "-=100px",
				height	: "-=100px"
			}, op.ScalingSpeed, function() {
				$(this).remove();
			});
			$(this).find(".bg").stop(true, true).fadeOut(op.ScalingSpeed)
		})
		/*刪除前要做的事件*/
		obj.bind("CloseEvent",op.CloseEvent)
		/*開啟後要做的事件*/
		obj.bind("OpenEvent",op.OpenEvent)
		
		/*視窗置頂*/
		obj.bind("ToTop",op,function(e){
			var maxZ = MaxZindex();
			if($(this).css('z-index') < maxZ || $(this).css('z-index')=="auto"){
				maxZ++;
				$(this).css('z-index',maxZ);
				ck = false;
			}else{
				maxZ++;
				$(this).css('z-index',maxZ);
			}
		})
		/*視窗 置頂 or 縮小*/
		obj.bind("ToTopNarrow",op,function(e){
			var maxZ = MaxZindex();
			if($(this).css('z-index') < maxZ || $(this).css('z-index')=="auto"){
				maxZ++;
				$(this).css('z-index',maxZ);
			}else{
				ToNarrow($(this),e.data);
			}
		})
		obj.click(obj,function(e){
			e.data.trigger("ToTop");
		});
		obj.find(".ZwClose").click(obj,function(e){
			e.data.trigger("Close");
		});
		obj.find(".ToMaxEnlarge").click(obj,function(e){
			e.data.trigger("ToMaxEnlarge");
		});
		obj.find(".ToSmall").click(obj,function(e){
			if(e.data.hasClass("small")){
				e.data.trigger("ToEnlarge");
				return;
			}
			if(e.data.hasClass("big")){
				e.data.trigger("ToNarrow");
				return;
			}
		});
		obj.on("dblclick",".WinTitle",{
			obj : obj,
			op	: op
		},function(e){
			var obj = e.data.obj;
			var op = e.data.op;
			if(obj.hasClass("small")){
				obj.trigger("ToEnlarge");
			}else{
				if(obj.hasClass("big") && op.CanSetMax){
					obj.trigger("ToMaxEnlarge");
				}
			}
		});
		$(window).resize(function(){
			if(obj.hasClass("small")){return;}
			var w_w = $(window).width();
			var w_h = $(window).height();
			if(obj.hasClass("Max_big")){
				var m_width = op.containment.width();
				var m_height = op.containment.height() - op.containment_top;
				obj.css({
					top		: op.containment.offset().top,
					left	: op.containment.offset().left,
					width	: m_width,
					height	: m_height,
				})
			}else{
				if(obj.hasClass("big")){
					var o_w = obj.outerWidth();
					var o_h = obj.outerHeight();
					if(o_w > w_w){
						o_w = w_w
					}
					if(o_h > w_h){
						o_h = w_h - op.containment_top
					}
					obj.css({
						width		: o_w,
						height		: o_h
					}).attr({
						"data_width" 	: o_w,
						"data_height" 	: o_h
					})
				}
			}
			obj.find(".WinContent").css("overflow","hidden");
			obj.find(".WinContent").css("overflow","auto");
		})
		return obj;
	}
	/*縮小/放大的開始位置*/
	var GetToStartPosition = function(op){
		if(op.TiedObj){
			var t_obj = op.TiedObj;
			switch(op.TiedPosition){
				case 'top':{
					var top = t_obj.offset().top;
					var left = t_obj.offset().left + (op.TiedObj.width() / 2);
					break;
				}
				case 'bottom':{
					var top = t_obj.offset().top + op.TiedObj.height();
					var left = t_obj.offset().left + (op.TiedObj.width() / 2);
					break;
				}
				case 'right':{
					var top = t_obj.offset().top + (op.TiedObj.height() / 2);
					var left = t_obj.offset().left + op.TiedObj.width();
					break;
				}
				case 'left':{
					var top = t_obj.offset().top + (op.TiedObj.height() / 2);
					var left = t_obj.offset().left;
					break;
				}
				case 'center':{
					var top = t_obj.offset().top + (op.TiedObj.height() / 2);
					var left = t_obj.offset().left + (op.TiedObj.width() / 2);
					break;
				}
			}
		}else{
			var s_width = 5;
			that.find(".Windows.small[data_tied='false']").each(function(){
				s_width += $(this).width() + 5;
			})
			var top = $(window).height();
			var left = s_width;
			switch(GV.smallPosition){
				case "left": {
					left = left
					break;
				}
				case "right": {
					left = $(window).width() - left - 305;
					break;
				}
			}
		}
		return {
			top 	: top,
			left	: left
		}
	}
	/*縮小*/
	var ToNarrow = function(obj,op,ck){
		if(obj.hasClass("small")){return;}
		if(ck){
			var maxZ = MaxZindex();
			if(obj.css('z-index')*1 < maxZ || $(this).css('z-index')=="auto"){
				maxZ++;
				obj.css('z-index',maxZ);
				return;
			}
		}
		var _start = GetToStartPosition(op);
		obj.css({
			opacity	: 0.5
		})
		obj.find(".WinContent").hide();
		obj.stop().animate({
			opacity	: 0,
			top		: _start.top,
			left	: _start.left,
			width	: 0,
			height	: 0
		}, op.ScalingSpeed, function() {
			$(this).removeClass("big").addClass("small").find(".WinContent").hide();
			$(this).removeAttr("style");
			$(this).css({
				bottom	: 0,
				left	: _start.left
			})
			if(op.CanSetMove){
				$(this).draggable("disable");
			}
			if(op.SetWinsize){
				$(this).resizable("disable");
			}
			if(op.TiedObj){
				$(this).hide();
			}else{
				$(this).css({
					opacity: 1
				})
			}
			$(this).find(".bg").stop(true, true).delay(200).fadeOut(op.ScalingSpeed)
			SmallSrot();
		});
	}
	/*視窗開啟*/
	var ToEnlarge = function(obj,op){
		obj.show();
		if(op.TiedObj){
			var _start = GetToStartPosition(op);
		}else{
			var _start = {};
			_start.top = $(window).height();
			_start.left = obj.offset().left;
		}
		obj.removeClass("small").addClass("big").find(".WinContent").show();
		obj.css("margin-top",op.containment_top);
		if(obj.hasClass("Max_big")){
			var w = op.containment.width();
			var h = op.containment.height() - op.containment_top;
			obj.width(w);
			obj.height(h);
			try{
				var s_top = op.containment.offset().top;
				var s_left = op.containment.offset().left;
			}catch(e){
				var s_top = 0;
				var s_left = 0;
			}
		}else{
			obj.width(obj.attr("data_width") ? obj.attr("data_width") : op.width);
			obj.height(obj.attr("data_height") ? obj.attr("data_height") : op.height);
			var w = obj.width();
			var h = obj.height();
			if(obj.attr("data_top") && obj.attr("data_left")){
				var s_top = obj.attr("data_top");
				var s_left = obj.attr("data_left");
			}else{
				var w_w = $(window).width();
				var w_h = $(window).height();
				var s_top = ((w_h - h) / 2) - 32;
				var s_left = ((w_w - w) / 2);
			}
		}
		if(s_top < 0){s_top = 0;}
		if(s_left < 0){s_left = 0;}
		obj.trigger("ToTop");
		obj.css({
			opacity	: 0,
			width	: 0,
			height	: 0,
			top		: _start.top,
			left	: _start.left
		})
		obj.find(".WinContent").hide();
		obj.stop().animate({
			opacity	: 1,
			top		: s_top,
			left	: s_left,
			width	: w,
			height	: h
		}, op.ScalingSpeed, function() {
			$(this).find(".WinContent").show();
			if(obj.hasClass("Max_big")){
				if(op.CanSetMove){
					obj.draggable("disable");
				}
				if(op.SetWinsize){
					obj.resizable("disable");
				}
			}else{
				if(op.CanSetMove){
					$(this).draggable("enable");
				}
				if(op.SetWinsize){
					$(this).resizable("enable");
				}
			}
			SmallSrot();
			$(this).trigger("resize");
			if(op.type!="iframe"){
				$(this).find(".WinContent").css("overflow","auto");
			}
			if(op.UseMask){
				$(this).find(".bg").stop(true, true).fadeIn(op.ScalingSpeed);
				obj.addClass("UseMaskWin");
			}else{
				obj.removeClass("UseMaskWin");
			}
		});
		obj.find(".WinContent").css("overflow","hidden");
	}
	/*視窗放到最大*/
	var ToMaxEnlarge = function(obj,op){
		if(!obj.hasClass("big")){return;}
		obj.find(".WinContent").hide();
		if(obj.hasClass("Max_big")){
			obj.find(".ToMaxEnlarge").removeClass("fa fa-clone").addClass("glyphicon glyphicon-unchecked");
			obj.removeClass("Max_big").trigger("ToTop");
			var w = obj.attr("data_width") ? obj.attr("data_width") : op.width;
			var h = obj.attr("data_height") ? obj.attr("data_height") : op.height;
			if(obj.attr("data_top") && obj.attr("data_left")){
				var s_top = obj.attr("data_top");
				var s_left = obj.attr("data_left");
			}else{
				var w_w = $(window).width();
				var w_h = $(window).height();
				var s_top = ((w_h - h) / 2)-32;
				var s_left = ((w_w - w) / 2);
			}
			obj.stop().animate({
				top		: s_top,
				left	: s_left,
				width	: w,
				height	: h
			}, op.ScalingSpeed, function() {
				obj.find(".WinContent").show();
				if(op.CanSetMove){
					obj.draggable("enable");
				}
				if(op.SetWinsize){
					obj.resizable("enable");
				}
				obj.trigger("resize");
				if(op.type!="iframe"){
					obj.find(".WinContent").css("overflow","auto");
				}
			});
		}else{
			obj.find(".ToMaxEnlarge").removeClass("glyphicon glyphicon-unchecked").addClass("fa fa-clone");
			obj.addClass("Max_big").trigger("ToTop");
			var m_width = op.containment.width();
			var m_height = op.containment.height() - op.containment_top;
			obj.stop().animate({
				top		: op.containment.offset().top,
				left	: op.containment.offset().left,
				width	: m_width,
				height	: m_height
			}, op.ScalingSpeed, function() {
				obj.find(".WinContent").show();
				if(op.CanSetMove){
					obj.draggable("disable");
				}
				if(op.SetWinsize){
					obj.resizable("disable");
				}
				obj.trigger("resize");
				if(op.type!="iframe"){
					obj.find(".WinContent").css("overflow","auto");
				}
			});
		}
		obj.find(".WinContent").css("overflow","hidden");
	}
	/*縮小的順序排列*/
	var SmallSrot = function(){
		var s_width = 5;
		var s_ary = [];
		var s_obj = {};
		that.find(".Windows.small[data_tied='false']").each(function(i){
			s_ary.push($(this).offset().left);
			s_obj[$(this).offset().left] = $(this);
		});
		switch(GV.smallPosition){
			case "left": {
				s_ary.sort();
				break;
			}
			case "right": {
				s_ary.sort(function(a, b){return b-a});
				break;
			}
		}
		for(var k in s_ary){
			obj = s_obj[s_ary[k]];
			var left = 0;
			switch(GV.smallPosition){
				case "left": {
					left = s_width
					break;
				}
				case "right": {
					left = $(window).width() - s_width - 305;
					break;
				}
			}
			try{
				obj.stop().animate({
					left	: left
				},300)
				s_width += obj.width() + 5;
			}catch(e){console.log(e)}
		}
	}
	this.ReSize = function(){
		SmallSrot();
	}
	var Ready = function(){
		if($("body").children("#ZgWindow").length <= 0){
			$("body").append($("<div/>").attr("id","ZgWindow"));
		}
		that = $("body").children("#ZgWindow");
		$(window).resize(function(){
			$this.ReSize();
		})
	}
	this.init = function(){
		Ready();
	};
	this.init();
};