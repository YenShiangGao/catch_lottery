/* 2017-08-24 by yuni
 * vision:v1.0
 * 必要檔案
 *
 * ------------------------------------------------------
 */
var jqLotteryExt = function() {
    var _this = this;
    var colClass = 'col-lg-4 col-lg-offset-4 col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 col-xs-12 col-xs-offset-0';
    return {
        /**
         *檢查有哪些欄位為空值
         *
         *table		=form 的名稱class{型態:string}
         *checkinp	=需要檢查的input  {型態:object}
         *example:
         *	var checkinp = {
         *		"cname"	: "請輸入名稱",
         *		"acc"	: "請輸入帳號",
         *		"pwd"	: "請輸入密碼",
         *	};
         *return {true||false,obj}
         */
        checknull: function(table, checkinp = {}) {
            var inputGroup = $('body ' + table + ' input'); //在此form下的所有input
            var input_value, input_name, input_type;
            var ajaxObj = new Object(); //存放input->key[value]
            var input_null = new Array(); //用來檢查有無輸入值

            for (var i = 0; i < inputGroup.length; i++) {
                input_name = $(inputGroup[i]).attr('name');
                input_type = $(inputGroup[i]).attr('type');
                switch (input_type) {
                    case 'radio':
                        input_value = $('body ' + table + ' input[name="' + input_name + '"]:checked').val();
                        break;
                    default:
                        input_value = $('body ' + table + ' input[name="' + input_name + '"]').val();
                        break;
                }
                if (input_value === "" || input_value === null || input_value === undefined) {
                    if (checkinp[input_name] != undefined)
                        input_null.push(checkinp[input_name]);
                }
                ajaxObj[input_name] = input_value;
            }

            if (input_null.length > 0) {
                for (var i = 0; i < input_null.length; i++) {
                    if (i == 0) {
                        var msg = input_null[i];
                    } else {
                        var msg = msg + "\n" + input_null[i];
                    }
                }

                redata = {
                    "status": false,
                    "data": msg
                }
                return redata;
            } else {
                redata = {
                    "status": true,
                    "data": ajaxObj
                }
                return redata;
            }
        },
        catchData: function(URLs, data, selector, jq, opt, callback) {
            var URLs = URLs;
            var send = {
                "funID": $('nav#xmenu ul.sidebar-menu li.active').data('funid'),
                "menuID": $('nav#xmenu ul.sidebar-menu li li.active').data('funid')
            };
            $.extend(send, data);
            if(send["menuID"]==undefined) send["menuID"] = send["funID"]
            ajax_get(URLs, send, function() {
                loading(true);
            }).always(function() {
                loading(false);
            }).done(function(rt) {
                try {
                    var obj = json_decode(rt);
                } catch (err) {
                    console.log("parsing error!");
                }
                
                var mydata = [];
                switch (obj.code) {
                    case 100:
                        for (var i = 0; i < obj["data"].length; i++) {
                            mydata.push(obj["data"][i]);
                        }
                        
                        jgrid.clear(selector);
                        jgrid.update(selector, jq, mydata, opt);

                        if (obj["addBtn"] != undefined) {
                            $('.' + jq + ' .btns-group').html('');
                            $('.' + jq + ' .btns-group').html(obj["addBtn"]);
                        }
                        break;
                    case 104:
                        jgrid.clear(selector);
                        popup.alert(obj.msg);
                        break;
                    default:
                        popup.alert(obj.msg);
                        break;
                }

                if (callback && typeof callback == "function") {
                    var res = callback(mydata.length, mydata, obj);
                    if (res === false) return false;
                }
            });
        },
        /**
         * 必填欄位檢查
         * @param  vdata = $("#formPost").serializeArray()
         * @param  igone = ['string:string'];
         * @param  zgID  = 頁面ID;
         * @call   check = chk.empty(vdata, igone, zgID);
         * @return boolean
         */
        empty: function(vdata, igone, zgID, tran = false) {

            if (tran) {
                var tranD = {};
                for (let i = 0; i < Object.keys(vdata).length; i++) {
                    if (tranD[i] == undefined)
                        tranD[i] = {};
                    tranD[i]['name'] = Object.keys(vdata)[i];
                    tranD[i]['value'] = vdata[Object.keys(vdata)[i]];
                }
                vdata = tranD;
            }

            var thisTab = $('#' + zgID);
            var alertStr = '';

            for (let key in vdata) {
                for (let i = 0; i < igone.length; i++) {
                    var name = igone[i].split(':')[0];
                    var str = igone[i].split(':')[1];
                    if (vdata[key]['name'] == name && $.trim(vdata[key]['value']) === '') {
                        alertStr += str+'</br>';
                    }
                }
            }

            if (alertStr.length != '') {
                thisTab.closest('#ZgWindow').css("z-index", "999");
                tzAl.sw3(101, 1, alertStr);
                return false;
            } else {
                return true;
            }
        }
    }
};
var minejs = new jqLotteryExt();

var tableAct = function() {
    var _this = this;
    return {
        del: function(page, id, callback) {
            var URLs = burl + "main/" + page + "/del";
            var send = {
                "id": id
            };

            ajax_get(URLs, send, function() {
                loading(true);
            }).always(function() {
                loading(false);
            }).done(function(rt) {
                try {
                    var obj = json_decode(rt)
                } catch (err) {
                    console.log("parsing error!");
                }

                switch (obj.code) {
                    case 100:
                        popup.alertSuccess(obj.msg, function() {
                            window.parent.jgrid.reloadCurrent();
                        });

                        if (callback && $.isFunction(callback)) {
                            callback();
                        }
                        break;
                    default:
                        popup.alert(obj.msg);
                        break;
                }
            });
        }
    }
}
var tableAct = new tableAct();

var jqGridFn = function() {
    var fn = {
        grids: {},
        currentId: null,
        _init: function() {
            var _this = this;
            $(window).resize(function() {
                _this.resizeAll();
            });
            return;
        },
        _getElem: function(selector) {
            elem = typeof selector == 'string' ? $(selector).eq(0) : selector.eq(0);
            return elem;
        },
        _pushElem: function(elem) {
            var _this = this,
                uuid = generateUUID();
            elem.attr('uuid', uuid);
            _this.currentId = uuid;
            _this.grids[uuid] = { item: elem, timer: null };
        },
        _removeElem: function(elem) {
            var uuid = typeof elem == 'string' ? elem : elem.attr('uuid');
            delete _this.grids[uuid];
        },
        _removeAll: function() {
            $.each(_this.grids, function(k, v) {
                delete _this.grids[k];
            });
            _this.grids = {};
        },
        check: function(selector) {
            var _this = this,
                elem = _this._getElem(selector),
                uuid = elem.attr('uuid') || '';

            if (uuid) {
                return (_this.grids[uuid]) ? true : false;
            }
            return false;
        },
        create: function(selector, jq, colN, colM, callback, opt) {
            var _this = this,
                elem = _this._getElem(selector),
                lastSel = null,
                defaults = {
                    datatype: 'local',
                    rowNum: 100,
                    cmTemplate: {
                        sortable: true
                    },
                    width: $('.' + jq + '_div_grid').width(),
                    height: $(window).height() - $('.' + jq + '_div_grid').offset().top - 150,
                    // height: $('.' + jq + '_div_grid').height(),
                    multiselect: false, //第一列 勾選列
                    pager: '#' + jq + '_pagered',
                    hoverrows: true,
                    autoencode: false,
                    ignoreCase: true,
                    viewrecords: true,
                    footerrow: true,
                    sortable: true,
                    sortname: colM[0].name,
                    sortorder: 'asc',
                    colNames: colN,
                    colModel: colM,
                    gridComplete: function() {
                        //當grid產出後，判斷是否會出現卷軸，若沒有卷軸則加上class="hideScroll"，隱藏原本jqgride會預留的卷軸空間
                        $('.ui-jqgrid-bdiv > div').innerHeight() <= $('.ui-jqgrid-bdiv').innerHeight() ? $('.ui-jqgrid').addClass('hideScroll') : $('.ui-jqgrid').removeClass('hideScroll');
                    },
                    // onSelectRow: function(id) {
                    //     if (id && id !== lastSel) {
                    //         $(selector + jq).jqGrid('restoreRow', lastSel);
                    //         $(selector + jq).jqGrid('editRow', id, true);
                    //         lastSel = id;
                    //     }
                    // },
                    loadComplete: function(res) {
                        if (callback && $.isFunction(callback)) {
                            callback($(this));
                        } else {
                            var to_sum = {};
                            to_sum[colM[0].name] = '總計:(共 0 筆)';
                            var cnt = 0;
                            if (res) {
                                var row = res['rows'];
                                for (var i = 0; i < row.length; i++) {
                                    cnt++;
                                }

                                to_sum[colM[0].name] = '總計:(共 ' + cnt + ' 筆)';
                            }
                            elem.jqGrid('footerData', 'set', to_sum);
                        }
                    }
                },
                options = $.extend({}, defaults, opt || {});
            _this._pushElem(elem);
            elem.jqGrid(options);
            // elem.jqGrid('filterToolbar', { stringResult: true, searchOnEnter: false });
            return elem;
        },
        update: function(selector, jq, data, opt) {
            var _this = this,
                elem = _this._getElem(selector);
            var options = { datatype: 'local', data: data};
            options = $.extend({}, options, opt || {});
            elem.jqGrid('setGridParam', options).trigger('reloadGrid');
        },
        setGroupHeaders: function(selector, opt) {
            var _this = this,
                elem = _this._getElem(selector);
            elem.jqGrid('setGroupHeaders', opt);
        },
        refresh: function(selector) {
            this.reload(selector);
        },
        clear: function(selector) {
            var _this = this,
                elem = _this._getElem(selector);
            elem.jqGrid('clearGridData');
        },
        clearCurrent: function() {
            var _this = this,
                elem = _this.grids[_this.currentId].item;

            elem.jqGrid('clearGridData');
        },
        reload: function(selector) {
            var _this = this,
                elem = _this._getElem(selector);
            elem.trigger('reloadGrid');
        },
        reloadCurrent: function() {
            var _this = this,
                elem = _this.grids[_this.currentId].item;
            elem.trigger('reloadGrid');
        },
        destroy: function(selector) {
            var _this = this,
                elem = _this._getElem(selector);
            _this._removeElem(elem);
            elem.jqgrid('destroy');
        },
        destroyAll: function() {
            var _this = this;
            $.each(_this.grids, function(index, elem) {
                var item = elem.item || null;
                if (item && item.length) {
                    item.jqgrid('destroy');
                }
            });
            _this.removeAll();
        },
        resize: function(selector) {
            var _this = this,
                elem = _this._getElem(selector);
            elem.setGridWidth($(window).width());
        },
        resizeAll: function() {
            var _this = this;
            $.each(_this.grids, function(index, obj) {
                obj.item = obj.item || null;
                obj.timer = obj.timer || null;
                if (obj.item && obj.item.length && obj.item.is(':visible')) {
                    if (obj.timer) clearTimeout(obj.timer);
                    obj.timer = setTimeout(function() {
                        obj.item.setGridWidth(obj.item.closest('.box-body').width());
                    }, 300);
                }
            });
        },
        getData: function(selector) {
            var _this = this,
                elem = _this._getElem(selector);
            data = elem.jqGrid('getGridParam', 'data');
            return data;
        }
    };
    fn._init();
    return fn;
};
var jgrid = new jqGridFn();

/*ZgWindow*/
var ZgWindowFunOpt = function() {
    var $this = this;
    this.zw = new ZgWindow();

    this.GoPage = function(Page, Type, option, title) {
        var op = $.extend({
            effect: Type,
            post_data: false,
            win_pro: {},
            title: title,
            TiedPosition: "center",
            width: 800,
            height: 600,
            SetWinsize: false,
            complete: function(obj) {}
        }, option)
        switch (op.effect) {
            case 'iframeSimple':
                op.complete();
                return $this.zw.OpenWin($.extend({
                    type: "iframe",
                    url: Page,
                    title: op.title,
                    lock: true,
                    width: op.width,
                    height: op.height,
                    UseMask: true,
                    CanSetsize: false,
                    CanSetMove: false,
                    SetWinsize: false,
                    CanSetMax: false,
                    OpenEvent: function() {

                    }
                }, op));
                break;
            case 'inline':
                op.complete();
                return $this.zw.OpenWin($.extend({
                    type: "html",
                    html: Page,
                    title: op.title,
                    lock: true,
                    UseMask: true,
                    width: op.width,
                    height: op.height,
                    CanSetsize: false,
                    SetWinsize: false,
                    CanSetMax: false,
                    OpenEvent: function() {

                    }
                }, op));
                break;
            default:
                op.complete();
                return $this.zw.OpenWin($.extend({
                    type: "iframe",
                    url: Page,
                    title: op.title,
                    lock: false,
                    width: op.width,
                    height: op.height,
                    OpenEvent: function() {

                    }
                }, op));
                break;
        }
    }
    this.parentHide = function() {
        $('body').addClass('ovflowhide');
        top.$('.currentWindows').addClass('parentHide');
        $('body>*:not(#ZgWindow)').addClass('frostedGlass');
    }
    this.parentShow = function($elm) {
        $('body').removeClass('ovflowhide');
        top.$('.currentWindows').removeClass('parentHide');
        $('body>*:not(#ZgWindow)').removeClass('frostedGlass');
    }
};
var ZgWindowFun = new ZgWindowFunOpt();

function generateUUID() {
    var d = new Date().getTime(),
        r1 = Math.floor(Math.random() * 9 + 1).toString(),
        r2 = (Math.random()).toString(16),
        uuid = (r2 + '' + ((r1 + '' + d) * 8).toString(16)).substr(-15);
    return uuid;
}

/*權限表格建立*/
var ctlNewFn = function() {
    this.getPerm = function(option) { //取得權限表
        var doWhat = $.extend({
            "lv1Fn": function() {},
            "lv2Fn": function() {},
            "lv3Fn": function() {},
            "doneFn": function() {}
        }, option);
        ajax_get(burl + 'main/funclist/list')
            .done(function(rt) {
                var rtObj = JSON.parse(rt);
                for (var i in rtObj["data"]) { //第一層
                    var lv1PermItem = rtObj["data"][i];
                    doWhat.lv1Fn(lv1PermItem);
                    if (typeof lv1PermItem.childPerm !== 'undefined') { //第二層
                        for (var j in lv1PermItem.childPerm) {
                            var lv2PermItem = lv1PermItem.childPerm[j];
                            doWhat.lv2Fn(lv2PermItem);
                            if (typeof lv2PermItem.childPerm !== 'undefined') { //第三層
                                for (var k in lv2PermItem.childPerm) {
                                    var lv3PermItem = lv2PermItem.childPerm[k];
                                    doWhat.lv3Fn(lv3PermItem);
                                }
                            }
                        }
                    }
                }
                doWhat.doneFn();
            });
    }
    this.stateBtn = function(color, text) { //狀態按鈕
        var colorText = ["blue", "orange", "green", "pink", "purple", "purpleBlue", "garyBlue", "skyBlue", "red"];
        return '<button type="button" class="btns btns-sm btns-bd-' + colorText[color] + '">' + text + '</button>';
    }
    this.ctrlBtn = function(color, text, clickFn, icon = false) { //操作按鈕
        var colorText = ["blue", "lightBlue", "orange", "green", "pink", "purple", "purpleBlue", "garyBlue", "gary", "skyBlue", "overcast", "red", "broun", "defaultBlue", "taro"];
        var btnHtml = '<button type="button" class="btns btns-sm btns-' + colorText[color] + '" onclick="' + clickFn + '">';
        if (icon) btnHtml += '<i class="fa fa-' + icon + '" aria-hidden="true"></i> ';
        btnHtml += text + '</button>';
        return btnHtml;
    }
    this.paginateBtn = function(wrapId, allPage, nowPage, allAmount, doneFn) { //頁碼
        var wrapPaginate = $('#' + wrapId);
        wrapPaginate.addClass('pageWrap');
        wrapPaginate.html($('<ul/>').append($('<li/>').attr('data-page-num', nowPage - 1).text('上一頁')));
        wrapPaginate.children('ul').append($('<li/>').attr('data-page-num', 1).text(1));
        if (nowPage - 1 > 5) wrapPaginate.children('ul').append("… ");
        for (var i = 2; i <= allPage - 1; i++) {
            if (i - nowPage < 5 && i - nowPage > -5) wrapPaginate.children('ul').append($('<li/>').attr('data-page-num', i).text(i));
        }
        if (allPage - nowPage > 5) wrapPaginate.children('ul').append("… ");
        if (allPage > 1) wrapPaginate.children('ul').append($('<li/>').attr('data-page-num', allPage).text(allPage));
        wrapPaginate.children('ul').append($('<li/>').attr('data-page-num', nowPage + 1).text('下一頁'));
        wrapPaginate.find('li[data-page-num="' + nowPage + '"]').addClass('current');
        if (nowPage == 1) wrapPaginate.find('ul>li:first-child').addClass('disabled');
        if (nowPage == allPage) wrapPaginate.find('ul>li:last-child').addClass('disabled');
        wrapPaginate.append($('<div/>').addClass('pageCount').text('第 ' + nowPage + ' 頁，共 ' + allPage + ' 頁，共 ' + allAmount + ' 筆資料'));
        doneFn();
    }
};
var ctlNew = new ctlNewFn();

//=================== 工具 ===================//
var toolsFn = function() {
    var $this = this;
    //暫存 HTML
    //class 設為 tempHtml、輸出 tempHtml[id]
    tempHtml = new Object();
    this.tempHtml = function() {
        $('.tempHtml').each(function() {
            tempHtml[this.id] = $(this).html();
            $(this).remove();
        });
    }

    //取代字串(要替換字串^變數^, obj.變數)
    this.repl = function(str, kArr) {
        for (var i in kArr)
            while (str.indexOf("^" + i + "^") != -1) str = str.replace("^" + i + "^", kArr[i]);
        return str;
    }

    //不足補0(左)(被補零的字串, 最終長度)
    this.padLeft = function(str, length) {
        while (str.toString().length < length) str = "0" + str;
        return str;
    }

    //ajax取值產生inline視窗(controller網址, 視窗選項, post資料)
    this.ajaxWinPage = function(url, opt, data = "") {
        ajax_get(url, data)
            .done(function(rt) {
                ZgWindowFun.GoPage(rt, 'inline', opt);
            });
    }

    //產生select選項(select的name, 資料obj, 選項值的key, 選項文字的key, 全部選項內的值<無則免填>)
    this.genSel = function(selName, obj, valKey, textKey, allVal = false, defaultVal = false) {
        $('select[name="' + selName + '"]').html(allVal ? $('<option/>').val(allVal).text('全部') : '');
        for (var i in obj) {
            $('select[name="' + selName + '"]').append($('<option/>').val(obj[i][valKey]).text(obj[i][textKey]));
        }
        if (defaultVal) $('select[name="' + selName + '"]').val(defaultVal);
    }

    //ZgWin裡的jqGrid重設尺寸
    this.resizeWinjqGridSize = function(jqGridTable) {
        chartW = $('.currentWindows').find('.div_grid').width();
        chartH = $('.currentWindows .WinContent').height() - $('.currentWindows .filterWrap').height() - 186;
        jqGridTable.jqGrid('setGridWidth', chartW);
        jqGridTable.jqGrid('setGridHeight', chartH);


        //當grid產出後，判斷是否會出現卷軸，若沒有卷軸則加上class="hideScroll"，隱藏原本jqgride會預留的卷軸空間
        $('.currentWindows .ui-jqgrid-bdiv > div').innerHeight() <= $('.currentWindows .ui-jqgrid-bdiv').innerHeight() ? $('.currentWindows .ui-jqgrid').addClass('hideScroll') : $('.currentWindows .ui-jqgrid').removeClass('hideScroll');
        // setTimeout(function(){
        // 	if(chartW != $('.currentWindows').find('.div_grid').width() || chartH != $('.currentWindows .WinContent').height()-$('.currentWindows .filterWrap').height()-184){
        // 		$this.setChartSize(jqGridTable);
        //           }
        //       },100);
    };

    //icon打勾、打叉HTML
    this.iconYN = function(YorN) {
            var iconCode = {
                "Y": ["check", "text-green"],
                "N": ["times", "text-red"]
            };
            return '<i class="fa fa-' + iconCode[YorN][0] + ' ' + iconCode[YorN][1] + '" aria-hidden="true"></i>';
        }
        //日期轉換
    this.dateChange = function(date) {
        var now = date != undefined ? date : new Date();
        var month = (now.getMonth() + 1);
        var day = now.getDate();
        if (month < 10)
            month = "0" + month;
        if (day < 10)
            day = "0" + day;
        var today = now.getFullYear() + '-' + month + '-' + day;

        return today;
    }
}
var tools = new toolsFn();
