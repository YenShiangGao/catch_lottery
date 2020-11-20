<?php
	require_once('../web_conf/languages/global.inc');
	function GetLanguageInf(){
		$Data = GetLanguageInf_gbl();

		$Data['lang_web_title'] ="開獎監控系統";
		$Data['lang_Keyword'] = "";
		$Data['lang_Description'] = "";

		$Data['lang_game_1'] = "重庆时时彩";
		$Data['lang_game_2'] = "新疆时时彩";
		$Data['lang_game_3'] = "黑龙江时时彩";
		$Data['lang_game_4'] = "江西时时彩";
		$Data['lang_game_5'] = "天津时时彩";
		$Data['lang_game_6'] = "上海时时乐";
		$Data['lang_game_7'] = "福彩3D";
		$Data['lang_game_8'] = "体彩P3";
		$Data['lang_game_9'] = "北京赛车";
		$Data['lang_game_10'] = "安徽快三";
		$Data['lang_game_11'] = "江苏快三";
		$Data['lang_game_12'] = "广东11选5";
		$Data['lang_game_13'] = "江西11选5";
		$Data['lang_game_14'] = "上海11选5";
		$Data['lang_game_15'] = "山东11选5";
		$Data['lang_game_16'] = "518五分彩";
		$Data['lang_game_17'] = "518三分彩";
		$Data['lang_game_18'] = "518分分彩";
		$Data['lang_game_19'] = "518五分梯";
		$Data['lang_game_20'] = "518三分梯";
		$Data['lang_game_21'] = "梯子游戏";
		$Data['lang_game_22'] = "蜗牛赛跑";

		$Data['lang_play_205'] = "终点";
		$Data['lang_play_206'] = "起点";
		$Data['lang_play_207'] = "梯子数";
		$Data['lang_play_208'] = "终点x梯子";
		$Data['lang_play_209'] = "冠军";
		$Data['lang_play_210'] = "冠亚军";
		$Data['lang_play_211'] = "冠亚季军";

		$Data['lang_login_info_err'] = "登入资讯错误";
		$Data['lang_time_expired'] = "时间过期";
		$Data['lang_re_login'] = "请重新登入";
		$Data['lang_no_this_game'] = "查无此游戏";
		$Data['lang_not_login_info'] = "尚未登入资讯";
		$Data['lang_system_msg'] = "系统讯息";
		$Data['lang_close'] = "关闭";
		$Data['lang_confirm'] = "确认";
		$Data['lang_cancel'] = "取消";
		$Data['lang_renew'] = "刷新";
		$Data['lang_item'] = "项目";
		$Data['lang_all'] = "全部";
		$Data['lang_status_processing'] = "处理中";
		$Data['lang_status_win'] = "中奖[number]";
		$Data['lang_status_not_win'] = "未中奖";
		$Data['lang_chase'] = "追号";
		$Data['lang_cancel_bet'] = "撤单";

		$Data['lang_error_109'] = "资料短缺";
		$Data['lang_error_9999'] = "该帐号投注功能暂时关闭，如有问题请洽线上客服。";
		$Data['lang_error_7778'] = "您无权使用。";
		$Data['lang_error_998'] = "请重新登入。(998)";
		$Data['lang_error_999'] = "请重新登入。";
		$Data['lang_error_101'] = "尚无资料。";

		$Data['lang_error_bet_101'] = "检查错误，请联系客服人员。";
		$Data['lang_error_bet_9999'] = "线路异常，请联络客服。(代码：9999)";
		$Data['lang_error_bet_8899'] = "线路异常，请联络客服。(代码：8899)";
		$Data['lang_error_bet_2011'] = "投注内容有问题。";
		$Data['lang_error_bet_2012'] = "投注内容跟投注注数不符。";
		$Data['lang_error_bet_2013'] = "投注内容有问题。";
		$Data['lang_error_bet_2021'] = "投注位置有误。";
		$Data['lang_error_bet_2022'] = "投注位置有误。";
		$Data['lang_error_bet_2023'] = "投注位置有误。";
		$Data['lang_error_bet_2031'] = "您指定的下注期数有问题。请通知客服人员";
		$Data['lang_error_bet_2032'] = "您指定的下注期数有问题。请通知客服人员";
		$Data['lang_error_bet_2033'] = "您指定的下注期数已关盘。";
		$Data['lang_error_bet_2034'] = "您指定的下注期数已关盘。";
		$Data['lang_error_bet_2035'] = "您指定的下注期数已关盘。";
		$Data['lang_error_bet_2041'] = "单注金额不可小於 0.01 ";
		$Data['lang_error_bet_2042'] = "单注不可超过";
		$Data['lang_error_bet_2043'] = "单项不可超过";
		$Data['lang_error_bet_2044'] = "单期注单金额不可超过 ";
		$Data['lang_error_bet_2051'] = "金额不足。";
		$Data['lang_error_bet_2099'] = "新增失败";

		$Data['lang_betto_total'] = "注单编号：[order_id],下注总金额为：[total_money]";
		$Data['lang_betto_over_periods_money'] = "已超过每期最高金额([max_give_periods])。";
		$Data['lang_betto_over_single_money'] = "已超过注单最高金额([max_give_single])。";
		$Data['lang_betto_get_reback'] = "注单编号：[order_id],返水額[bet_rakeback]%)：[money]";
		$Data['lang_betto_get_win_money'] = "注单编号：[order_id],中奖注数：[win_num]，中奖额：[win_money]";
		$Data['lang_betto_get_win_money_over'] = "注单编号：[order_id],中奖注数：[win_num]，中奖额：[win_money]，因超过最高派奖金额所以派奖降为：[get_money]";
		$Data['lang_betto_not_processing'] = "(未开奖，系统自动撤单)注单编号：[order_id]，,退还金额：[money]";
		$Data['lang_betto_cancel'] = "注单编号：[order_id]，退还金额：[money]";
		$Data['lang_betto_cancel_m'] = "(手动撤单)注单编号：[order_id]，退还金额：[money]";
		$Data['lang_betto_cancel_a'] = "(系统撤单)注单编号：[order_id]，退还金额：[money]";
		$Data['lang_betto_cancel_c'] = "(追号撤单)注单编号：[order_id]，退还金额：[money]";
		$Data['lang_betto_cancel_e'] = "(异常撤单)注单编号：[order_id]，退还金额：[money]";
		$Data['lang_betto_cancel_d'] = "(撤单)注单编号：[order_id]，退还金额：[money]";
		$Data['lang_betto_cancel_bonus'] = "注单编号：[order_id]，派奖金额：[bonus_money]，退还金额：[money]";
		$Data['lang_betto_lot_error'] = "注单编号：[order_id]，开奖异常，撤回派奖：[bonus_money]元";
		$Data['lang_betto_lot_error_capital'] = "注单编号：[order_id]，开奖异常，撒单退回本金：[bet_total_money]元";

		$Data['lang_bet_cancel'] = "撤单";
		$Data['lang_bet_cancel_m'] = "撤单";
		$Data['lang_bet_cancel_a'] = "系统撤单";
		$Data['lang_bet_cancel_c'] = "追号撤单";
		$Data['lang_bet_order_id'] = "订单号";
		$Data['lang_bet_acc'] = "帐号";
		$Data['lang_bet_s_money'] = "单注金额";
		$Data['lang_bet_time'] = "下注时间";
		$Data['lang_bet_number'] = "投注注数";
		$Data['lang_bet_game'] = "彩种";
		$Data['lang_bet_totle_money'] = "投注总额";
		$Data['lang_bet_preiods'] = "期号";
		$Data['lang_bet_b_r'] = "奖金 / 返点";
		$Data['lang_bet_play'] = "玩法";
		$Data['lang_bet_reback'] = "销售返点";
		$Data['lang_bet_lot'] = "开奖号码";
		$Data['lang_bet_bouns_money'] = "中奖金额";
		$Data['lang_bet_status'] = "状态";
		$Data['lang_bet_profit_and_loss'] = "盈亏";
		$Data['lang_bet_content'] = "投注内容";
		$Data['lang_bet_win_content'] = "中奖号码";

		$Data['lang_game_order'] = "订单管理";
		$Data['lang_game_report'] = "投注报表";
		$Data['lang_game_openlot'] = "开奖结果";
		$Data['lang_game_trend'] = "走势图";
		$Data['lang_game_act'] = "帐号";
		$Data['lang_game_money'] = "帐户金额";
		$Data['lang_game_periods'] = "期";
		$Data['lang_game_currency'] = "元";
		$Data['lang_game_no_money'] = "金额不足";
		$Data['lang_game_bet_details'] = "投注明细";
		$Data['lang_game_chase_details'] = "追号明细";
		$Data['lang_game_periods_not_open'] = "目前尚未开盘";
		$Data['lang_game_periods_expired'] = "此期数已过期。";
		$Data['lang_game_enter_money'] = "请先输入下注金额。";
		$Data['lang_game_bet_success'] = "投注 [number] 笔，成功 [success_number] 笔，失败 [err_number] 笔";
		$Data['lang_game_order_cancel'] = "订单撤单";
		$Data['lang_game_class'] = "彩 类";
		$Data['lang_game_order_select'] = "订单查询";
		$Data['lang_game_order_remind'] = "贴心小提醒：点击号码能查询详细投注明细";
		$Data['lang_game_order_cancel_btn'] = "撤单";

		$Data['lang_game_order_order_id'] = "注单号";
		$Data['lang_game_order_bet_time'] = "下注时间";
		$Data['lang_game_order_game_class'] = "彩种";
		$Data['lang_game_order_periods'] = "期号";
		$Data['lang_game_order_play'] = "玩法";
		$Data['lang_game_order_content'] = "内容";
		$Data['lang_game_order_one_money'] = "单注额";
		$Data['lang_game_order_total_money'] = "总额";
		$Data['lang_game_order_chase'] = "追号";
		$Data['lang_game_order_all_select'] = "全选";
		$Data['lang_game_order_statistics'] = "统计";
		$Data['lang_game_order_statistics_money'] = "下注金额";
		$Data['lang_game_order_can_cancel'] = "可撤单";
		$Data['lang_game_order_is_cancel'] = "已撤單";
		$Data['lang_game_order_not_cancel'] = "不可撤单";
		$Data['lang_game_order_cancel_confirm_title'] = "确认撤销注单!?";
		$Data['lang_game_order_cancel_confirm_content'] = "您确定要撤销选中的注单资料吗?";
		$Data['lang_game_order_cancel_confirm_content_this'] = "您确定要撤销此注单资料吗?";
		$Data['lang_game_order_cancel_success'] = "撤單 [number] 笔，成功 [success_number] 笔，失败 [err_number] 笔";

		$Data['lang_game_report_select_date'] = "浏览日期";
		$Data['lang_game_report_date_today'] = "今日";
		$Data['lang_game_report_date_yesterday'] = "昨日";
		$Data['lang_game_report_date_thisweek'] = "本周";
		$Data['lang_game_report_date_lastweek'] = "上周";
		$Data['lang_game_report_date_thismonth'] = "本月";
		$Data['lang_game_report_date_lastmonth'] = "上月";
		$Data['lang_game_report_date_remind'] = "※请按查询送出※&nbsp;&nbsp;&nbsp;&nbsp;00:00:00-06:59:59 的注单，归属于上一日报表。";
		$Data['lang_game_report_select'] = "报表查询";

		$Data['lang_game_report_date'] = "报表日期";
		$Data['lang_game_report_bet_number'] = "有效笔数";
		$Data['lang_game_report_bet_money_effective'] = "有效投注总额";
		$Data['lang_game_report_profit_and_loss'] = "实际盈亏";
		$Data['lang_game_report_function'] = "功能操作";
		$Data['lang_game_report_statistics'] = "总计";
		$Data['lang_game_report_statistics_bet_number'] = "有效笔数";
		$Data['lang_game_report_statistics_bet_money'] = "有效投注总额";
		$Data['lang_game_report_statistics_bet_profit_and_loss'] = "实际盈余";
		$Data['lang_game_report_function_all'] = "全部";
		$Data['lang_game_report_function_winning'] = "中奖";
		$Data['lang_game_report_function_cancel'] = "撤单";
		$Data['lang_game_report_function_effective'] = "有效";
		$Data['lang_game_report_function_notawarded'] = "未颁";

		$Data['lang_game_report_order_id'] = "注单号";
		$Data['lang_game_report_game'] = "彩 种";
		$Data['lang_game_report_periods'] = "期 号";
		$Data['lang_game_report_play'] = "玩 法";
		$Data['lang_game_report_bet_money'] = "下注金额";
		$Data['lang_game_report_reback_money'] = "返水金额";
		$Data['lang_game_report_win_money'] = "中奖金额";
		$Data['lang_game_report_bonus_money'] = "返还金额";
		$Data['lang_game_report_status'] = "状态";
		$Data['lang_game_report_number'] = "笔数";

		$Data['lang_game_lot_class'] = "彩 类";
		$Data['lang_game_lot_select'] = "开奖查询";
		$Data['lang_game_lot_results'] = "开奖结果";
		$Data['lang_game_lot_periods'] = "期数";
		$Data['lang_game_lot_date'] = "开奖日期";
		$Data['lang_game_lot_time'] = "开奖时间";
		$Data['lang_game_lot_week'] = "星期";
		$Data['lang_game_lot_number'] = "开状号码";
		$Data['lang_game_lot_proportion'] = "下注比例";
		$Data['lang_game_lot_pager'] = "每页 [pagenumber] 笔记录 / 共 [count] 笔 / 共 [endpage] 页";
		$Data['lang_game_lot_pager_box'] = "第 [box] 页";
		$Data['lang_game_lot_ball_number'] = "彩球号码";
		$Data['lang_game_lot_ten_thousand'] = "万";
		$Data['lang_game_lot_thousand'] = "千";
		$Data['lang_game_lot_hundred'] = "百";
		$Data['lang_game_lot_ten'] = "十";
		$Data['lang_game_lot_a'] = "个";
		$Data['lang_game_lot_rank_1'] = "冠";
		$Data['lang_game_lot_rank_2'] = "亚";
		$Data['lang_game_lot_rank_3'] = "季";
		$Data['lang_game_lot_rank_4'] = "四";
		$Data['lang_game_lot_rank_5'] = "五";
		$Data['lang_game_lot_rank_6'] = "六";
		$Data['lang_game_lot_rank_7'] = "七";
		$Data['lang_game_lot_rank_8'] = "八";
		$Data['lang_game_lot_rank_9'] = "九";
		$Data['lang_game_lot_rank_10'] = "十";
		$Data['lang_game_lot_number_1'] = "号码1";
		$Data['lang_game_lot_number_2'] = "号码2";
		$Data['lang_game_lot_number_3'] = "号码3";
		$Data['lang_game_lot_number_4'] = "号码4";
		$Data['lang_game_lot_number_5'] = "号码5";
		$Data['lang_game_lot_number_6'] = "号码6";
		$Data['lang_game_lot_number_7'] = "号码7";
		$Data['lang_game_lot_number_8'] = "号码8";
		$Data['lang_game_lot_number_9'] = "号码9";
		$Data['lang_game_lot_number_10'] = "号码10";

		$Data['lang_game_trend_class'] = "彩 类";
		$Data['lang_game_trend_type'] = "走势类别";
		$Data['lang_game_trend_other_select'] = "走势图细项";
		$Data['lang_game_trend_type_basic'] = "基本走势图";
		$Data['lang_game_trend_type_same'] = "同期走势图";
		$Data['lang_game_trend_type_location'] = "位置走势图";
		$Data['lang_game_trend_type_number'] = "号码走势图";
		$Data['lang_game_trend_type_champion'] = "冠亚和走势图";

		$Data['lang_game_trend_periods_select'] = "选择期数";
		$Data['lang_game_trend_periods_select_btn'] = "走势圖查询";
		$Data['lang_game_trend_periods'] = "期数";
		$Data['lang_game_trend_lot_number'] = "开奖號碼";
		$Data['lang_game_trend_total_number'] = "总次数";
		$Data['lang_game_trend_max_miss'] = "最大遗漏";
		$Data['lang_game_trend_trend'] = "走势";
		$Data['lang_game_trend_where_ball'] = "第 [number] 球";

		$Data['lang_game_trend_swingback'] = "回摆";
		$Data['lang_game_trend_reverse'] = "反向";
		$Data['lang_game_trend_renumber'] = "重号";
		$Data['lang_game_trend_forward'] = "正向";
		$Data['lang_game_trend_reverse_s'] = "反";
		$Data['lang_game_trend_renumber_s'] = "重";
		$Data['lang_game_trend_forward_s'] = "正";

		$Data['lang_game_trend_odd_even'] = "单双";
		$Data['lang_game_trend_odd'] = "单";
		$Data['lang_game_trend_even'] = "双";
		$Data['lang_game_trend_odd_s'] = "单";
		$Data['lang_game_trend_even_s'] = "双";

		$Data['lang_game_trend_big_small'] = "大小";
		$Data['lang_game_trend_big'] = "大";
		$Data['lang_game_trend_small'] = "小";
		$Data['lang_game_trend_big_s'] = "大";
		$Data['lang_game_trend_small_s'] = "小";

		$Data['lang_game_trend_rank'] = "前中后段";
		$Data['lang_game_trend_rank_front'] = "前";
		$Data['lang_game_trend_rank_middle'] = "中";
		$Data['lang_game_trend_rank_rear'] = "后";
		$Data['lang_game_trend_rank_front_s'] = "前";
		$Data['lang_game_trend_rank_middle_s'] = "中";
		$Data['lang_game_trend_rank_rear_s'] = "后";

		$Data['lang_game_ladder_money'] = "余额";
		$Data['lang_game_ladder_note'] = "游戏说明";
		$Data['lang_game_ladder_note2'] = "购买分布会依照购买人数的多寡而变更，不影响开奖结果。";
		$Data['lang_game_ladder_lot_statistics'] = "单双开奖统计";
		$Data['lang_game_ladder_lot_statistics2'] = "开奖统计";
		$Data['lang_game_ladder_btn_lot_record'] = "路单记录";
		$Data['lang_game_ladder_btn_lot_statistics'] = "开奖统计";
		$Data['lang_game_ladder_btn_note'] = "游戏说明";

		$Data['lang_game_ladder_lot_E0'] = "单";
		$Data['lang_game_ladder_lot_E1'] = "双";
		$Data['lang_game_ladder_lot_S0'] = "左";
		$Data['lang_game_ladder_lot_S1'] = "右";
		$Data['lang_game_ladder_lot_L3'] = "三条";
		$Data['lang_game_ladder_lot_L4'] = "四条";
		$Data['lang_game_ladder_lot_T03'] = "三条单";
		$Data['lang_game_ladder_lot_T04'] = "四条单";
		$Data['lang_game_ladder_lot_T13'] = "三条双";
		$Data['lang_game_ladder_lot_T14'] = "四条双";

		$Data['lang_game_ladder_total_bet_money'] = "总投注";
		$Data['lang_game_ladder_win_money'] = "盈余";
		$Data['lang_game_ladder_bet_close'] = "盘口关闭";

		$Data['lang_game_ladder_close_time'] = "剩余 [time] 开奖";
		$Data['lang_game_ladder_skip_animation'] = "略过开奖动画";
		$Data['lang_game_ladder_can_bet'] = "开盘中…";
		$Data['lang_game_ladder_wait_open'] = "等待开奖";
		$Data['lang_game_ladder_with_open'] = "开奖为：[lot]";
		$Data['lang_game_ladder_ready'] = "准备中";
		$Data['lang_game_ladder_bet_periods'] = "下注期数";
		$Data['lang_game_ladder_bet_confirm'] = "确认投注!?";
		$Data['lang_game_ladder_wayvalue_title'] = "依期数统计单/双结果";
		$Data['lang_game_ladder_wayvalue_select_periods'] = "最近 [select] 期单/双统计结果";
		$Data['lang_game_ladder_statisics_open'] = "连续开奖记录";
		$Data['lang_game_ladder_statisics_notopen'] = "连续未开纪录";
		$Data['lang_game_ladder_statisics_periods'] = "統計至 [periods] 期";
		$Data['lang_game_ladder_statisics_periods_accumulation'] = "累积期数";

		/*營運後台使用*/
		$Data['lang_id'] = "编号";
		$Data['lang_data_name'] = "名称";
		$Data['lang_status'] = "状态";
		$Data['lang_enable_y'] = "启用";
		$Data['lang_enable_y_ing'] = "启用中";
		$Data['lang_enable_n'] = "停用";
		$Data['lang_enable_n_ing'] = "停用中";
		$Data['lang_member_info'] = "会员资料";
		$Data['lang_chase_id'] = "追号号";
		$Data['lang_save'] = "储存";
		$Data['lang_add_success'] = "新增成功。";
		$Data['lang_update_success'] = "修改成功。";
		$Data['lang_del_success'] = "刪除成功。";
		$Data['lang_save_success'] = "储存成功。";
		$Data['lang_to_select'] = "搜寻";
		$Data['lang_remark'] = "备注";
		$Data['lang_currency'] = "币别";
		$Data['lang_date'] = "日期";
		$Data['lang_time'] = "时间";
		$Data['lang_periods'] = "期数";
		$Data['lang_date_start'] = "开始日期";
		$Data['lang_date_end'] = "结束日期";
		$Data['lang_open_lot'] = "开奖";
		$Data['lang_statistics'] = "统计";
		$Data['lang_location'] = "位置";
		$Data['lang_minute'] = "分";
		$Data['lang_second'] = "秒";
		$Data['lang_setuptime'] = "建立时间";
		$Data['lang_details'] = "详细资料";
		$Data['lang_money'] = "金额";
		$Data['lang_data_count'] = "[number] 笔";

		$Data['lang_not_enter'] = "未填：[data]";
		$Data['lang_login'] = "登入";
		$Data['lang_logout'] = "登出";
		$Data['lang_bet_select'] = "注单查询";
		$Data['lang_member_act'] = "会员帐号";
		$Data['lang_member_select'] = "会员查询";
		$Data['lang_highest_level'] = "最上层";
		$Data['lang_down_level'] = "下层";
		$Data['lang_insert'] = "新增";
		$Data['lang_edit'] = "修改";
		$Data['lang_del'] = "删除";
		$Data['lang_look'] = "查看";
		$Data['lang_operator'] = "操作者";
		$Data['lang_operator_item'] = "操作选项";
		$Data['lang_move_up'] = "上移";
		$Data['lang_move_down'] = "下移";
		$Data['lang_sequence'] = "顺序";
		$Data['lang_update_sequence'] = "修改顺序";
		$Data['lang_update_sequence_close'] = "关闭修改顺序";

		$Data['lang_ag_company_name'] = "開獎監控系統";
		$Data['lang_ag_title'] = "開獎監控 - 管端系统";
		$Data['lang_ag_system'] = "管端系统";

		$Data['lang_ag_error_101'] = "查询条件查无资料。";
		$Data['lang_ag_error_101_member_act'] = "查无此会员。";

		$Data['lang_ag_language'] = "语言";
		$Data['lang_ag_user'] = "帐号";
		$Data['lang_ag_password'] = "密码";
		$Data['lang_ag_verification_code'] = "验证码";

		$Data['lang_ag_not_pem_look'] = "您尚无权限查看此表。";
		$Data['lang_ag_error'] = "系统错误，请联络技术人员";

		$Data['lang_ag_error_login'] = "登入资讯有误。";
		$Data['lang_ag_error_login_889'] = "您的IP([ip])，无权登入系统!";
		$Data['lang_ag_error_login_107'] = "验证码错误";
		$Data['lang_ag_error_login_108'] = "帐号密码错误。";
		$Data['lang_ag_error_login_109'] = "帐号密码错误。";
		$Data['lang_ag_error_login_110'] = "您的账号已被停用。";
		$Data['lang_ag_error_login_111'] = "您的账号尚未设定层级。";

		$Data['lang_ag_not_bet_info'] = "查无注单资料。";
		$Data['lang_ag_not_member_info'] = "查无此会员资料。";
		$Data['lang_ag_not_chase_info'] = "查无追号资料。";

		$Data['lang_ag_update_password'] = "修改密码";
		$Data['lang_ag_now_password'] = "现在密码";
		$Data['lang_ag_new_password'] = "新建密码";
		$Data['lang_ag_check_password'] = "确认密码";

		$Data['lang_ag_error_password'] = "现在密碼輸入錯誤。";
		$Data['lang_ag_different_password'] = "两组密码不一样。";
		$Data['lang_ag_update_password_confirm'] = "确认是否修改密码?";

		$Data['lang_ag_quick_select'] = "快速查询";

		$Data['lang_ag_enter_order_id'] = "请输入注单号";
		$Data['lang_ag_enter_member_act'] = "请输入会员号帐";

		$Data['lang_ag_pem_bet'] = "投注资料";
		$Data['lang_ag_pem_bet_member'] = "会员投注资料";
		$Data['lang_ag_pem_bet_game'] = "游戏投注资料";
		$Data['lang_ag_pem_bet_details'] = "投注详细资料";

		$Data['lang_ag_pem_member'] = "会员资料";
		$Data['lang_ag_pem_member_list'] = "会员资料列表";
		$Data['lang_ag_pem_member_reback'] = "修改会员返水";
		$Data['lang_ag_pem_member_cash_flow'] = "会员金额存提";

		$Data['lang_ag_pem_game'] = "游戏管理";
		$Data['lang_ag_pem_game_lot'] = "开奖列表";
		$Data['lang_ag_pem_game_lot_bet'] = "开状列表-投注资料";
		$Data['lang_ag_pem_game_type_set'] = "彩种设置";
		$Data['lang_ag_pem_game_play_set'] = "玩法设置";

		$Data['lang_ag_pem_user'] = "使用者管理";
		$Data['lang_ag_pem_user_level'] = "层级管理";
		$Data['lang_ag_pem_uset_set'] = "使用者管理";

		$Data['lang_ag_pem_maintain'] = "维护相关管理";
		$Data['lang_ag_pem_maintain_pem'] = "权限资料管理";

		$Data['lang_ag_pem_quick_select'] = "快速查询";

		$Data['lang_ag_pem_maintain_pem_add'] = "新增权限资料";
		$Data['lang_ag_pem_maintain_pem_update'] = "修改权限资料";
		$Data['lang_ag_pem_maintain_pem_del_confirm'] = "删除权限会将现有的使用者权限删除。<br>是否确认要删除此权限?";
		$Data['lang_ag_pem_maintain_link_type'] = "连结方式";
		$Data['lang_ag_pem_maintain_link_type_gopage'] = "程式连结";
		$Data['lang_ag_pem_maintain_link_type_url'] = "网址连结";
		$Data['lang_ag_pem_maintain_link_type_block'] = "另开视窗";
		$Data['lang_ag_pem_maintain_data_rel'] = "连结代码";
		$Data['lang_ag_pem_maintain_classify'] = "型态";
		$Data['lang_ag_pem_maintain_classify_c'] = "类别";
		$Data['lang_ag_pem_maintain_classify_i'] = "项目";
		$Data['lang_ag_pem_maintain_classify_p'] = "权限";
		$Data['lang_ag_pem_maintain_can_use'] = "可使用权限";
		$Data['lang_ag_pem_maintain_choose_one'] = "请选择权限。(最少使用一种权限)";

		$Data['lang_ag_pem_user_level_add'] = "新增层级";
		$Data['lang_ag_pem_user_level_update'] = "修改层级";
		$Data['lang_ag_pem_user_level_del_confirm'] = "删除层级限会将层级中的人员权限异动。<br>是否确认要删除此层级?";
		$Data['lang_ag_pem_user_level_edit_confirm'] = "层级权限异动将会影响在该底下的人员权限<br>是否确认要修改?";
		$Data['lang_ag_pem_user_level_name'] = "层级名称";
		$Data['lang_ag_pem_user_level_number'] = "层级人数";

		$Data['lang_ag_pem_user_level'] = "层级";
		$Data['lang_ag_pem_user_level_all'] = "全部层级";
		$Data['lang_ag_pem_user_level_choose'] = "请选择层级";
		$Data['lang_ag_pem_user_add'] = "新增使用者";
		$Data['lang_ag_pem_user_update'] = "修改使用者资料";
		$Data['lang_ag_pem_user_enable_n_confirm'] = "停用后此人员将不可登入此系统<br>是否确认要停用此人员?";
		$Data['lang_ag_pem_user_enable_y_confirm'] = "启用后此人员将可以登入此系统<br>是否确认要启用此人员?";
		$Data['lang_ag_pem_user_enable_n_act'] = "已停用帐号";
		$Data['lang_ag_pem_user_enable_y_act'] = "已启用帐号";
		$Data['lang_ag_pem_user_act_re'] = "帐号已重覆";
		$Data['lang_ag_pem_user_act_error'] = "帐号格式错误";
		$Data['lang_ag_pem_user_edit_confirm'] = "层级异动将会影响该人员的相关权限<br>是否确认要修改?";

		$Data['lang_game_lot_class_choose'] = "请选择彩种";
		$Data['lang_game_lot_bet_money'] = "投注金额";
		$Data['lang_game_lot_bet_reback'] = "返水金额";
		$Data['lang_game_lot_bet_winmoney'] = "中奖金额";
		$Data['lang_game_lot_bet_bounsmoney'] = "派奖金额";
		$Data['lang_game_lot_bet_memnumber'] = "会员数量";
		$Data['lang_game_lot_bet_profit_and_loss'] = "营运盈亏";

		$Data['lang_ag_game_lot_show_bet'] = "显示投注资料";
		$Data['lang_ag_game_lot_show_bet_n'] = "不显示";
		$Data['lang_ag_game_lot_show_bet_y'] = "显示";
		$Data['lang_ag_game_lot_status'] = "开奖状态";
		$Data['lang_ag_game_lot_status_expired'] = "已过开奖时间";
		$Data['lang_ag_game_lot_enter_opentime'] = "请输入开奖时间";
		$Data['lang_ag_game_lot_enter_date'] = "请输入查询日期";
		$Data['lang_ag_game_lot_enter_periods'] = "请输入查询期数";

		$Data['lang_ag_game_set_money_set'] = "派奖金额设置";
		$Data['lang_ag_game_set_highest_reback'] = "最高设定返水值";
		$Data['lang_ag_game_set_max_give_single'] = "单注最高派发金额";
		$Data['lang_ag_game_set_max_give_periods'] = "单期最高派发金额";
		$Data['lang_ag_game_set_max_bet_periods'] = "单期最高下注金额";
		$Data['lang_ag_game_set_play_menu'] = "游戏玩法选单设置";
		$Data['lang_ag_game_set'] = "游戏设置";
		$Data['lang_ag_game_set_save_confirm'] = "是否储存设置?";
		$Data['lang_ag_game_set_play'] = "玩法";
		$Data['lang_ag_game_set_can_chase'] = "可追号";
		$Data['lang_ag_game_set_maintain'] = "维护";
		$Data['lang_ag_game_set_close_bet'] = "关盘";
		$Data['lang_ag_game_set_open_bet_before_time'] = "每期开盘为开奖前…";
		$Data['lang_ag_game_set_close_bet_before_time'] = "每期關盤為開獎前…";

		$Data['lang_ag_play_set_name'] = "玩法名称";
		$Data['lang_ag_play_set_odds'] = "赔率";
		$Data['lang_ag_play_set_odds_change'] = "转换赔率(1%)";
		$Data['lang_ag_play_set_default_limit'] = "默认限额";
		$Data['lang_ag_play_set_set_limit'] = "限额设置";
		$Data['lang_ag_play_set_max_give_single'] = "单注(最高)";
		$Data['lang_ag_play_set_max_give_item'] = "单项(最高)";
		$Data['lang_ag_play_set_min_bet_money'] = "最小押注";
		$Data['lang_ag_play_set_note'] = "说明：";
		$Data['lang_ag_play_set_remark'] = "注解：";
		$Data['lang_ag_play_set_example'] = "例子：";
		$Data['lang_ag_play_set_save_single'] = "单笔修改";

		$Data['lang_ag_enter_select_time'] = "请输入查询时间";
		$Data['lang_ag_member_use_money_record'] = "会员使用金额记录：";

		$Data['lang_ag_member_bet_record'] = "下注记录";
		$Data['lang_ag_member_money_record'] = "金额记录";
		$Data['lang_ag_member_change_record'] = "转额记录";
		$Data['lang_ag_member_reback_record'] = "会员返水";
		$Data['lang_ag_member_can_money'] = "可用金额";
		$Data['lang_ag_member_bet_function'] = "下注功能";
		$Data['lang_ag_member_cash_flow'] = "金额存提";
		$Data['lang_ag_member_cash_flow_add'] = "新增 - 金额存提";

		$Data['lang_ag_member_bet_function_n_confirm'] = "是否将会员的下注功能设为停用?";
		$Data['lang_ag_member_bet_function_y_confirm'] = "是否将会员的下注功能设为启用?";

		$Data['lang_ag_member_money_add_reduce'] = "存入/提出";
		$Data['lang_ag_member_money_add'] = "存入";
		$Data['lang_ag_member_money_reduce'] = "提出";

		$Data['lang_ag_member_reback'] = "返水值";

		$Data['lang_ag_member_money_not_zero'] = "金额不可小於等於零。";
		$Data['lang_ag_error_info'] = "资料有误。";

		$Data['lang_ag_member_money'] = "会员馀额";
		$Data['lang_ag_member_profit_and_loss'] = "会员盈亏";

		$Data['lang_bet_status_class'] = "注单分类";
		$Data['lang_bet_status_class_win'] = "中奖";
		$Data['lang_bet_status_class_effective'] = "有效";
		$Data['lang_bet_status_class_payout'] = "已派奖";
		$Data['lang_bet_status_class_payout_not'] = "未派奖";
		$Data['lang_bet_status_class_cancel'] = "已撤单";

		$Data['lang_bet_chase_content'] = "追号详单";
		$Data['lang_bet_count'] = "注单量";
		$Data['lang_ag_bet_peo_number'] = "下注人数";

		$Data['lang_ag_game_lot_status_complete'] = "已完成";
		$Data['lang_ag_game_lot_status_not_payout'] = "尚未派奖";
		$Data['lang_ag_game_lot_status_Processing'] = "处理中…";
		$Data['lang_ag_game_lot_status_not_open'] = "尚未开奖";
		$Data['lang_ag_game_lot_status_close'] = "已关盘";
		$Data['lang_ag_game_lot_status_open'] = "开盘中";

		$Data['lang_game_type_1'] = "时时彩";
		$Data['lang_game_type_3'] = "北京赛车";
		$Data['lang_game_type_4'] = "快三";
		$Data['lang_game_type_5'] = "11选5";
		$Data['lang_game_type_6'] = "福体彩";
		$Data['lang_game_type_7'] = "梯子游戏";
		$Data['lang_game_type_8'] = "蜗牛赛跑";

		$Data['lang_mobile_fun_money'] = "钱包余额";
		$Data['lang_mobile_fun_member_center'] = "会员中心";
		$Data['lang_mobile_fun_game'] = "游戏大厅";
		$Data['lang_mobile_fun_pc'] = "电脑版";

		$Data['lang_bet_now'] = "立即投注";

		$Data['lang_condition_select'] = "查询条件";
		$Data['lang_info_list'] = "资料列表";

		$Data['lang_order_select_bet_btn'] = "注单";
		$Data['lang_order_select_cancel_btn'] = "撤单";

		$Data['lang_order_edit_btn'] = "编辑";
		$Data['lang_order_cancel_btn'] = "撤单";
		$Data['lang_order_close_btn'] = "取消";
		$Data['lang_msg_order_select_class'] = "请选择查询分类。";

		$Data['lang_mobile_game_close_time'] = "距 [periods] 期截止";
		$Data['lang_mobile_game_header_close_time'] = "距 [periods] 期截止： [time]";

		$Data['lang_game_ladder_bet_content_one'] = "双面";
		$Data['lang_game_ladder_bet_content_two'] = "终点X梯子";
		$Data['lang_game_ladder_bet_content_number'] = "号码";
		$Data['lang_game_ladder_bet_content_odds'] = "赔率";
		$Data['lang_game_ladder_bet_content_money'] = "投注金额";
		$Data['lang_game_ladder_bet_content_money_been'] = "已投注";

		$Data['lang_game_ladder_bet_content_total_money'] = "投注总额 [money] 元";
		$Data['lang_game_ladder_bet_content_total_money_been'] = "已注总额 [money] 元";

		$Data['lang_game_btn_bet_info'] = "投注资料";
		$Data['lang_game_btn_clear'] = "取消";
		$Data['lang_game_btn_bet'] = "下注";

		$Data['lang_game_ladder_lot_open_periods'] = "第[periods]期开奖结果";
		$Data['lang_game_ladder_open_auto_close'] = "(开盘后自动关闭)";

		$Data['lang_game_ladder_use_chips_choose'] = "请选择使用筹码";
		$Data['lang_game_ladder_bet_content_choose'] = "请先添加注单。";
		$Data['lang_game_ladder_clear_list_confirm'] = "是否要清空列表?";

		$Data['lang_mobile_game_maintain'] = "游戏维护中";
		$Data['lang_mobile_game_close'] = "游戏尚未开盘";
		$Data['lang_mobile_game_maintain_note_1'] = "尊敬的会员您好";
		$Data['lang_mobile_game_maintain_note_2'] = "为了提供您更便捷快速的娱乐环境<br>目前正在进行游戏维护作业<br>若有不便之处，敬请见谅。";


		/*蝸牛*/
		$Data['lang_game_snail_wayvalue_title'] = "依期数统计结果";
		$Data['lang_game_snail_wayvalue_select_periods'] = "最近 [select] 期 统计结果";

        $Data['lang_game_vdn_lot_item_0'] ='特奖';
        $Data['lang_game_vdn_lot_item_1'] ='一奖';
        $Data['lang_game_vdn_lot_item_2'] ='二奖';
        $Data['lang_game_vdn_lot_item_3'] ='三奖';
        $Data['lang_game_vdn_lot_item_4'] ='四奖';
        $Data['lang_game_vdn_lot_item_5'] ='五奖';
        $Data['lang_game_vdn_lot_item_6'] ='六奖';
        $Data['lang_game_vdn_lot_item_7'] ='七奖';
        $Data['lang_game_vdn_lot_item_8'] ='八奖';
        $Data['lang_game_vdn_lot_item_9'] ='九奖';

		$Data['lang_game_gamename'] = "游戏名称";
		$Data['lang_game_openperiod'] = "开奖期数";

		$Data['lang_game_one'] = "正一";
		$Data['lang_game_two'] = "正二";
		$Data['lang_game_three'] = "正三";
		$Data['lang_game_four'] = "正四";
		$Data['lang_game_five'] = "正五";
		$Data['lang_game_six'] = "正六";
		$Data['lang_game_special'] = "特码";
		
		return $Data;
	}
?>