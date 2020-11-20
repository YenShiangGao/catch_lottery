jQuery.extend({
    stringify: function stringify(obj) {
        var t = typeof(obj);
        if (t != "object" || obj === null) {
            if (t == "string") obj = '"' + obj + '"';
            return String(obj);
        } else {
            var n, v, json = [],
                arr = (obj && obj.constructor == Array);
            for (n in obj) {
                v = obj[n];
                t = typeof(v);
                if (obj.hasOwnProperty(n)) {
                    if (t == "string") v = '"' + v + '"';
                    else if (t == "object" && v !== null) v = jQuery.stringify(v);
                    json.push((arr ? "" : '"' + n + '":') + String(v));
                }
            }
            return (arr ? "[" : "{") + String(json) + (arr ? "]" : "}");
        }
    }
});


var AjaxSendAry = {};

function ajax_get(url, data, showLoadingImgFn, option) {
    if (!data) {
        data = {};
    }
    if (!showLoadingImgFn) {
        showLoadingImgFn = function() {};
    }
    var op = $.extend({
        url: url,
        type: "POST",
        data: data,
        async: true,
        dataType: 'html',
        beforeSend: showLoadingImgFn,
        checkRepeat: true
    }, option);
    var mark = url + json_encode(data);
    if (op.checkRepeat) {
        if (AjaxSendAry.hasOwnProperty(mark)) {
            // console.log("cancel_request:" + mark);
            return $.ajax().abort();
        }
        AjaxSendAry[mark] = 1;
    }
    return $.ajax(op).fail(function(e) {
        // console.log(e)
    }).always(function(e) {
        if (AjaxSendAry.hasOwnProperty(mark)) {
            delete AjaxSendAry[mark];
        }
    })
}

function urldecode(str) {
    return decodeURIComponent((str + '').replace(/\+/g, '%20'));
}

function json_encode(obj) {
    return $.stringify(obj)
}

function json_decode(str) {
    return $.parseJSON(str)
}

function parse_to(ohtml, obj) {
    var html = ohtml;
    for (var i in obj) {
        while (html.indexOf("[" + i + "]") != -1) {
            html = html.replace("[" + i + "]", obj[i])
        }
    }
    return html
}

function getSize() {
    var rt = Array()
    var myWidth = 0,
        myHeight = 0;
    if (typeof(window.innerWidth) == 'number') {
        myWidth = window.innerWidth;
        myHeight = window.innerHeight;
    } else if (document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight)) {
        myWidth = document.documentElement.clientWidth;
        myHeight = document.documentElement.clientHeight;
    } else if (document.body && (document.body.clientWidth || document.body.clientHeight)) {
        myWidth = document.body.clientWidth;
        myHeight = document.body.clientHeight;
    }
    rt[0] = myWidth
    rt[1] = myHeight
    return rt
}

function my_decode(raw, funs) {
    try {
        var obj = json_decode(raw)
    } catch (err) {
        console.log("parsing error!");
    }
    try {
        LG.gstr("test");
    } catch (e) {
        var LG = new function() {
            var Lauguage = {
                "lang_system_msg": "系统讯息",
                "lang_error_109": "资料短缺",
                "lang_error_9999": "该帐号投注功能暂时关闭，如有问题请洽线上客服。",
                "lang_error_7778": "您无权使用。",
                "lang_error_998": "请重新登入。(998)",
                "lang_error_999": "请重新登入。"
            }
            this.gstr = function(key, ary) {
                if (Lauguage.hasOwnProperty(key)) {
                    var str = Lauguage[key];
                    if (ary) {
                        for (var k in ary) {
                            var v = ary[k];
                            while (str.indexOf("[" + k + "]") != -1) {
                                str = str.replace("[" + k + "]", v)
                            }
                        }
                    }
                    return str;
                } else {
                    return key;
                }
            }
        }
    }
    if (!obj) {
        return false
    }
    if (funs[obj.code]) {
        funs[obj.code](obj)
        return
    }
    if (obj.code == "109") {
        if (ZgCreate) {
            ZgCreate.alert(LG.gstr("lang_system_msg"), LG.gstr("lang_error_109"))
        } else {
            alert(LG.gstr("lang_error_109"))
        }
    }
    if (obj.code == "9999") {
        if (ZgCreate) {
            ZgCreate.alert(LG.gstr("lang_system_msg"), LG.gstr("lang_error_9999"))
        } else {
            /*线路异常，请联络客服。(代码：9999)*/
            alert(LG.gstr("lang_error_9999"));
        }
    }
    if (obj.code == "7778") {
        if (ZgCreate) {
            ZgCreate.alert(LG.gstr("lang_system_msg"), LG.gstr("lang_error_7778"))
        } else {
            alert(LG.gstr("lang_error_7778"));
        }
    }
    if (obj.code == "998") {
        if (ZgCreate) {
            /*您的帐号被重覆登入。<br>请查看是否使用其他装置登入。<br>如果不是您做的動作请尽快联络客服。*/
            ZgCreate.alert(LG.gstr("lang_system_msg"), LG.gstr("lang_error_998"), {
                complete: function() {
                    global_logout()
                }
            })
        } else {
            alert(LG.gstr("lang_error_998"))
            global_logout()
        }
    }
    if (obj.code == "999") {
        if (ZgCreate) {
            ZgCreate.alert(LG.gstr("lang_system_msg"), LG.gstr("lang_error_999"), {
                complete: function() {
                    global_logout()
                }
            })
        } else {
            alert(LG.gstr("lang_error_999"))
            global_logout()
        }
    }
    return obj
}
/*數字千位分*/
function formatFloat_str(num) {
    num = num + "";
    var re = /(-?\d+)(\d{3})/
    while (re.test(num)) {
        num = num.replace(re, "$1,$2")
    }
    return num;
}

function number_format(number, decimals, dec_point, thousands_sep) {
    // Strip all characters but numerical ones.
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function(n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}
/*確認數字鍵*/
function fild_valid(number) {
    var re = /^\d+$/;　
    if (number != "" && !re.test(number)) {　　
        return false;　
    } else {　　
        return true;　
    }
}

function nl2br(str, is_xhtml) {
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}
/*四捨五入*/
function formatFloat(num, pos) {
    var size = Math.pow(10, pos);
    return Math.round(num * size) / size;
}

function inIframe() {
    try {
        return window.self !== window.top;
    } catch (e) {
        return true;
    }
}

function padLeft(str, lenght) {
    str = String(str);
    if (str.length >= lenght)
        return str;
    else
        return padLeft("0" + str, lenght);
}
/*取得該日期的資訊*/
function GetDateInf(Day) {
    var DateInf = new Array();
    var y = Day.getYear() + 1900;
    var n = Day.getMonth() + 1;
    var m = ((n < 10) ? "0" : "") + n;
    var d = Day.getDate();
    d = ((d < 10) ? "0" : "") + d;
    var w = Day.getDay();
    var H = Day.getHours();
    H = ((H < 10) ? "0" : "") + H;
    var i = Day.getMinutes();
    i = ((i < 10) ? "0" : "") + i;
    var s = Day.getSeconds();
    s = ((s < 10) ? "0" : "") + s;
    var day_list = ['日', '一', '二', '三', '四', '五', '六'];
    //console.log(Day,y +" 年 "+m +" 月 " + d + " 日  星期" + day_list[w]);
    DateInf['Y'] = y;
    DateInf['n'] = n;
    DateInf['m'] = m;
    DateInf['d'] = d;
    DateInf['w'] = w;
    DateInf['H'] = H;
    DateInf['i'] = i;
    DateInf['s'] = s;
    return DateInf;
}
