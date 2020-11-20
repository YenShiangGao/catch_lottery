<?php
	require_once('../web_conf/languages/global.inc');
	function GetLanguageInf(){
		$Data = GetLanguageInf_gbl();
		
		$Data['lang_web_title'] ="RBG";
		$Data['lang_Keyword'] = "";
		$Data['lang_Description'] = "";
		
		$Data['lang_game_1'] = "충칭10분로또";
		$Data['lang_game_2'] = "신장10분로또";
		$Data['lang_game_3'] = "헤이룽장10분로또";
		$Data['lang_game_4'] = "강서10분로또";
		$Data['lang_game_5'] = "천진10분로또";
		$Data['lang_game_6'] = "상하이10분로또";
		$Data['lang_game_7'] = "복지복권3D";
		$Data['lang_game_8'] = "체육복권3D";
		$Data['lang_game_9'] = "북경PK10";
		$Data['lang_game_10'] = "안후이3";
		$Data['lang_game_11'] = "장쑤3";
		$Data['lang_game_12'] = "광둥로또";
		$Data['lang_game_13'] = "강서로또";
		$Data['lang_game_14'] = "상하이로또";
		$Data['lang_game_15'] = "산둥로또";
		$Data['lang_game_16'] = "518-5분로또";
		$Data['lang_game_17'] = "518-3분로또";
		$Data['lang_game_18'] = "518-1분로또";
		$Data['lang_game_19'] = "518-5분사다리게임";
		$Data['lang_game_20'] = "518-3분사다리게임";
		$Data['lang_game_21'] = "사다리게임";
		$Data['lang_game_22'] = "달팽이레이싱";
		
		$Data['lang_play_205'] = "결과";
		$Data['lang_play_206'] = "시작";
		$Data['lang_play_207'] = "사다리수";
		$Data['lang_play_208'] = "결과 x 사다리수";
		$Data['lang_play_209'] = "冠军";
		$Data['lang_play_210'] = "冠亚军";
		$Data['lang_play_211'] = "冠亚季军";
		
		$Data['lang_login_info_err'] = "아이디 또는 비밀번호를 잘못 입력";
		$Data['lang_time_expired'] = "기한 초과";
		$Data['lang_re_login'] = "다시 로그인하세요";
		$Data['lang_no_this_game'] = "해당 게임 찾을 수 없음";
		$Data['lang_not_login_info'] = "로그인 기록 없음";
		$Data['lang_system_msg'] = "시스템 메시지";
		$Data['lang_close'] = "닫기";
		$Data['lang_confirm'] = "확인";
		$Data['lang_cancel'] = "취소";
		$Data['lang_renew'] = "새로 고침";
		$Data['lang_item'] = "항목";
		$Data['lang_all'] = "전부";
		$Data['lang_status_processing'] = "작업중";
		$Data['lang_status_win'] = "적중[number]";
		$Data['lang_status_not_win'] = "미적중";
		$Data['lang_chase'] = "자동 구매";
		$Data['lang_cancel_bet'] = "취소";

		$Data['lang_error_109'] = "자료 부족";
		$Data['lang_error_9999'] = "구매 불가 상태입니다. 구매에 관한 문의가 있으시면 고객센터에 문의하세요.";
		$Data['lang_error_7778'] = "권한 없음";
		$Data['lang_error_998'] = "다시 로그인하세요.(998)";
		$Data['lang_error_999'] = "다시 로그인하세요.";
		$Data['lang_error_101'] = "자료 없음";

		$Data['lang_error_bet_101'] = "착오,고객센터에 문의하세요.";
		$Data['lang_error_bet_9999'] = "선로/시스템 이상.고객센터에 문의하세요.(코드：9999)";
		$Data['lang_error_bet_8899'] = "선로/시스템 이상.고객센터에 문의하세요.(코드：8899)";
		$Data['lang_error_bet_2011'] = "구매 내용 다시 확인하세요.";
		$Data['lang_error_bet_2012'] = "구매 내용이 구매 수량과 일치하지 않습니다.";
		$Data['lang_error_bet_2013'] = "구매 내용 다시 확인하세요.";
		$Data['lang_error_bet_2021'] = "구매 위치 착오";
		$Data['lang_error_bet_2022'] = "구매 위치 착오";
		$Data['lang_error_bet_2023'] = "구매 위치 착오";
		$Data['lang_error_bet_2031'] = "구매하신 회차 문제가 생겨서 고객센터에 문의하세요.";
		$Data['lang_error_bet_2032'] = "구매하신 회차 문제가 생겨서 고객센터에 문의하세요.";
		$Data['lang_error_bet_2033'] = "판매마감";
		$Data['lang_error_bet_2034'] = "판매마감";
		$Data['lang_error_bet_2035'] = "판매마감";
		$Data['lang_error_bet_2041'] = "구매 금액<0.01 불가 ";
		$Data['lang_error_bet_2042'] = "구매개수한도초과";
		$Data['lang_error_bet_2043'] = "구매내용한도초과";
		$Data['lang_error_bet_2044'] = "한 회차 최고 구매 금액 ";
		$Data['lang_error_bet_2051'] = "보유금액 부족";
		$Data['lang_error_bet_2099'] = "증가 실패";
		
		$Data['lang_betto_total'] = "오더 아이디：[order_id],총 구매금액：[total_money]";
		$Data['lang_betto_over_periods_money'] = "해당 회차의 최대 구매 금액을 초과함([max_give_periods])。";
		$Data['lang_betto_over_single_money'] = "최고 구매금액을 초과함([max_give_single])。";
		$Data['lang_betto_get_reback'] = "오더 아이디：[order_id],리워드[bet_rakeback]%)：[money]";
		$Data['lang_betto_get_win_money'] = "오더 아이디：[order_id],적중개수：[win_num]，상금：[win_money]";
		$Data['lang_betto_get_win_money_over'] = "오더 아이디：[order_id],적중개수：[win_num]，상금：[win_money]，최고상금을 초과하여 보낸 상금：[get_money]";
		$Data['lang_betto_not_processing'] = "(미추첨，시스템 자동 취소)오더 아이디：[order_id]，,되돌리는 금액：[money]";
		$Data['lang_betto_cancel'] = "오더 아이디：[order_id]，되돌리는 금액：[money]";
		$Data['lang_betto_cancel_m'] = "(수동 취소)오더 아이디：[order_id]，되돌리는 금액：[money]";
		$Data['lang_betto_cancel_a'] = "(시스템 취소)오더 아이디：[order_id]，되돌리는 금액：[money]";
		$Data['lang_betto_cancel_c'] = "(추가 취소)오더 아이디：[order_id]，되돌리는 금액：[money]";
		$Data['lang_betto_cancel_e'] = "(이상 취소)오더 아이디：[order_id]，되돌리는 금액：[money]";
		$Data['lang_betto_cancel_d'] = "(취소)오더 아이디：[order_id]，되돌리는 금액：[money]";
		$Data['lang_betto_cancel_bonus'] = "오더 아이디：[order_id]，상금금액：[bonus_money]， 되돌리는 금액：[money]";
		$Data['lang_betto_lot_error'] = "오더 아이디：[order_id]，추첨이상，상금 취소：[bonus_money]원";
		$Data['lang_betto_lot_error_capital'] = "오더 아이디：[order_id]，추첨이상，되돌리는 구매금액：[bet_total_money]원";

		$Data['lang_bet_cancel'] = "취소";
		$Data['lang_bet_cancel_m'] = "취소";
		$Data['lang_bet_cancel_a'] = "시스템 취소";
		$Data['lang_bet_cancel_c'] = "추가 취소";
		$Data['lang_bet_order_id'] = "오더 아이디";
		$Data['lang_bet_acc'] = "아이디";
		$Data['lang_bet_s_money'] = "구매금액";
		$Data['lang_bet_time'] = "구매시간";
		$Data['lang_bet_number'] = "베팅 개수";
		$Data['lang_bet_game'] = "게임";
		$Data['lang_bet_totle_money'] = "베팅총액";
		$Data['lang_bet_preiods'] = "회수";
		$Data['lang_bet_b_r'] = "베당율 / 리워드 비율";
		$Data['lang_bet_play'] = "패턴";
		$Data['lang_bet_reback'] = "리워드";
		$Data['lang_bet_lot'] = "적중번호";
		$Data['lang_bet_bouns_money'] = "적중금액";
		$Data['lang_bet_status'] = "상태";
		$Data['lang_bet_profit_and_loss'] = "손익";
		$Data['lang_bet_content'] = "구매 내용";
		$Data['lang_bet_win_content'] = "적중번호";

		$Data['lang_game_order'] = "구매 관리";
		$Data['lang_game_report'] = "구매 보고표";
		$Data['lang_game_openlot'] = "회차 결과";
		$Data['lang_game_trend'] = "분석 데이터";
		$Data['lang_game_act'] = "아이디";
		$Data['lang_game_money'] = "보유 금액";
		$Data['lang_game_periods'] = "회차";
		$Data['lang_game_currency'] = "원";
		$Data['lang_game_no_money'] = "보유 금액 부족";
		$Data['lang_game_bet_details'] = "구매명세";
		$Data['lang_game_chase_details'] = "추가명세";
		$Data['lang_game_periods_not_open'] = "대기중";
		$Data['lang_game_periods_expired'] = "이 회수 끝";
		$Data['lang_game_enter_money'] = "구매 금액을 입력하세요.";
		$Data['lang_game_bet_success'] = "구매:[number] , 성공:[success_number], 실패:[err_number]";
		$Data['lang_game_order_cancel'] = "취소";
		$Data['lang_game_class'] = "종 류";
		$Data['lang_game_order_select'] = "구매 정보 조회";
		$Data['lang_game_order_remind'] = "꼭 알아두세요 :  번호를 눌러 더 자세한 정보를 볼 수 있음.";
		$Data['lang_game_order_cancel_btn'] = "취소";
		
		$Data['lang_game_order_order_id'] = "구매 번호";
		$Data['lang_game_order_bet_time'] = "최중구매시간";
		$Data['lang_game_order_game_class'] = "종류";
		$Data['lang_game_order_periods'] = "회차";
		$Data['lang_game_order_play'] = "구매 정보";
		$Data['lang_game_order_content'] = "내용";
		$Data['lang_game_order_one_money'] = "구매금액";
		$Data['lang_game_order_total_money'] = "총액";
		$Data['lang_game_order_chase'] = "자동 구매";
		$Data['lang_game_order_all_select'] = "선택하기";
		$Data['lang_game_order_statistics'] = "합계";
		$Data['lang_game_order_statistics_money'] = "구매 총액";
		$Data['lang_game_order_can_cancel'] = "취소 가능";
		$Data['lang_game_order_is_cancel'] = "취소";
		$Data['lang_game_order_not_cancel'] = "취소 불가";
		$Data['lang_game_order_cancel_confirm_title'] = "구매 취소하겠습니까?";
		$Data['lang_game_order_cancel_confirm_content'] = "선택한 구매를 취소하겠습니까?";
		$Data['lang_game_order_cancel_confirm_content_this'] = "이 구매를 취소하겠습니까?";
		$Data['lang_game_order_cancel_success'] = "취소 [number] 개，성공[success_number] 개，실패 [err_number] 개";

		$Data['lang_game_report_select_date'] = "날짜 선택";
		$Data['lang_game_report_date_today'] = "오늘";
		$Data['lang_game_report_date_yesterday'] = "어제";
		$Data['lang_game_report_date_thisweek'] = "이번 주";
		$Data['lang_game_report_date_lastweek'] = "지난 주";
		$Data['lang_game_report_date_thismonth'] = "이번 달";
		$Data['lang_game_report_date_lastmonth'] = "지난 달";
		$Data['lang_game_report_date_remind'] = "*조회 버튼을 눌러  보내면 됩니다* 00:00:00-06:59:59구간의 구매 전보는 어제의 보고표를  참조하세요";
		$Data['lang_game_report_select'] = "조회";
		
		$Data['lang_game_report_date'] = "선택";
		$Data['lang_game_report_bet_number'] = "유효 구매 수량";
		$Data['lang_game_report_bet_money_effective'] = "유효 구매 총액";
		$Data['lang_game_report_profit_and_loss'] = "수익";
		$Data['lang_game_report_function'] = "기능 선택";
		$Data['lang_game_report_statistics'] = "합계";
		$Data['lang_game_report_statistics_bet_number'] = "유효 구매 개수";
		$Data['lang_game_report_statistics_bet_money'] = "유효 구매 총액";
		$Data['lang_game_report_statistics_bet_profit_and_loss'] = "수익";
		$Data['lang_game_report_function_all'] = "전부";
		$Data['lang_game_report_function_winning'] = "적중";
		$Data['lang_game_report_function_cancel'] = "취소";
		$Data['lang_game_report_function_effective'] = "유효";
		$Data['lang_game_report_function_notawarded'] = "대기중";
		
		$Data['lang_game_report_order_id'] = "구매  번호";
		$Data['lang_game_report_game'] = "종류";
		$Data['lang_game_report_periods'] = "회차";
		$Data['lang_game_report_play'] = "구매 정보";
		$Data['lang_game_report_bet_money'] = "구매금액";
		$Data['lang_game_report_reback_money'] = "보너스";
		$Data['lang_game_report_win_money'] = "획득금액";
		$Data['lang_game_report_bonus_money'] = "반환 금액";
		$Data['lang_game_report_status'] = "적중여부";
		$Data['lang_game_report_number'] = "개수";
		
		$Data['lang_game_lot_class'] = "종류";
		$Data['lang_game_lot_select'] = "조회";
		$Data['lang_game_lot_results'] = "추첨 결과";
		$Data['lang_game_lot_periods'] = "회차";
		$Data['lang_game_lot_date'] = "날짜";
		$Data['lang_game_lot_time'] = "시간";
		$Data['lang_game_lot_week'] = "요일";
		$Data['lang_game_lot_number'] = "추첨 결과";
		$Data['lang_game_lot_proportion'] = "구매 분포";
		$Data['lang_game_lot_pager'] = "[pagenumber]개 기록(페이지)/ 총 [count] 개/ 총 [endpage] 페이지";
		$Data['lang_game_lot_pager_box'] = "[box] 페이지";
		$Data['lang_game_lot_ball_number'] = "볼 수";
		$Data['lang_game_lot_ten_thousand'] = "万";
		$Data['lang_game_lot_thousand'] = "千";
		$Data['lang_game_lot_hundred'] = "百";
		$Data['lang_game_lot_ten'] = "十";
		$Data['lang_game_lot_a'] = "个";
		$Data['lang_game_lot_rank_1'] = "1등";
		$Data['lang_game_lot_rank_2'] = "2등";
		$Data['lang_game_lot_rank_3'] = "3등";
		$Data['lang_game_lot_rank_4'] = "4등";
		$Data['lang_game_lot_rank_5'] = "5등";
		$Data['lang_game_lot_rank_6'] = "6등";
		$Data['lang_game_lot_rank_7'] = "7등";
		$Data['lang_game_lot_rank_8'] = "8등";
		$Data['lang_game_lot_rank_9'] = "9등";
		$Data['lang_game_lot_rank_10'] = "10등";
		$Data['lang_game_lot_number_1'] = "1번";
		$Data['lang_game_lot_number_2'] = "2번";
		$Data['lang_game_lot_number_3'] = "3번";
		$Data['lang_game_lot_number_4'] = "4번";
		$Data['lang_game_lot_number_5'] = "5번";
		$Data['lang_game_lot_number_6'] = "6번";
		$Data['lang_game_lot_number_7'] = "7번";
		$Data['lang_game_lot_number_8'] = "8번";
		$Data['lang_game_lot_number_9'] = "9번";
		$Data['lang_game_lot_number_10'] = "10번";
		
		$Data['lang_game_trend_class'] = "종류";
		$Data['lang_game_trend_type'] = "분석 유형";
		$Data['lang_game_trend_other_select'] = "동향분석";
		$Data['lang_game_trend_type_basic'] = "기본 분석";
		$Data['lang_game_trend_type_same'] = "회차분석";
		$Data['lang_game_trend_type_location'] = "위치분석";
		$Data['lang_game_trend_type_number'] = "번호분석";
		$Data['lang_game_trend_type_champion'] = "결과분석";
		
		$Data['lang_game_trend_periods_select'] = "회차 선택";
		$Data['lang_game_trend_periods_select_btn'] = "조회";
		$Data['lang_game_trend_periods'] = "회차";
		$Data['lang_game_trend_lot_number'] = "회차 결과";
		$Data['lang_game_trend_total_number'] = "총횟수";
		$Data['lang_game_trend_max_miss'] = "미스";
		$Data['lang_game_trend_trend'] = "경향";
		$Data['lang_game_trend_where_ball'] = "[number] 공";
		
		$Data['lang_game_trend_swingback'] = "방향";
		$Data['lang_game_trend_reverse'] = "역";
		$Data['lang_game_trend_renumber'] = "중복";
		$Data['lang_game_trend_forward'] = "정";
		$Data['lang_game_trend_reverse_s'] = "역";
		$Data['lang_game_trend_renumber_s'] = "중복";
		$Data['lang_game_trend_forward_s'] = "정";
		
		$Data['lang_game_trend_odd_even'] = "홀/짝";
		$Data['lang_game_trend_odd'] = "홀";
		$Data['lang_game_trend_even'] = "짝";
		$Data['lang_game_trend_odd_s'] = "홀";
		$Data['lang_game_trend_even_s'] = "짝";
		
		$Data['lang_game_trend_big_small'] = "크기";
		$Data['lang_game_trend_big'] = "큰";
		$Data['lang_game_trend_small'] = "소";
		$Data['lang_game_trend_big_s'] = "큰";
		$Data['lang_game_trend_small_s'] = "소";
		
		$Data['lang_game_trend_rank'] = "전후방";
		$Data['lang_game_trend_rank_front'] = "전";
		$Data['lang_game_trend_rank_middle'] = "중";
		$Data['lang_game_trend_rank_rear'] = "후";
		$Data['lang_game_trend_rank_front_s'] = "전";
		$Data['lang_game_trend_rank_middle_s'] = "중";
		$Data['lang_game_trend_rank_rear_s'] = "후";
		
		$Data['lang_game_ladder_money'] = "돈";
		$Data['lang_game_ladder_note'] = "게임 설명";
		$Data['lang_game_ladder_note2'] = "구매분포는 구매인원에따라 그래프가 변동되며, 추첨과는 무관합니다.";
		$Data['lang_game_ladder_lot_statistics'] = "홀/짝 통계분석";
		$Data['lang_game_ladder_lot_statistics2'] = "분석";
		$Data['lang_game_ladder_btn_lot_record'] = "패턴분석";
		$Data['lang_game_ladder_btn_lot_statistics'] = "회차분석";
		$Data['lang_game_ladder_btn_note'] = "게임 설명";
		
		$Data['lang_game_ladder_lot_E0'] = "홀";
		$Data['lang_game_ladder_lot_E1'] = "짝";
		$Data['lang_game_ladder_lot_S0'] = "좌";
		$Data['lang_game_ladder_lot_S1'] = "우";
		$Data['lang_game_ladder_lot_L3'] = "3줄";
		$Data['lang_game_ladder_lot_L4'] = "4줄";
		$Data['lang_game_ladder_lot_T03'] = "3홀";
		$Data['lang_game_ladder_lot_T04'] = "4홀";
		$Data['lang_game_ladder_lot_T13'] = "3짝";
		$Data['lang_game_ladder_lot_T14'] = "4짝";
		
		$Data['lang_game_ladder_total_bet_money'] = "총 구매 금액";
		$Data['lang_game_ladder_win_money'] = "보유금액";
		$Data['lang_game_ladder_bet_close'] = "구매 종료";
		
		$Data['lang_game_ladder_close_time'] = "[time] 후";
		$Data['lang_game_ladder_skip_animation'] = "애니메이션 건너 뛰기";
		$Data['lang_game_ladder_can_bet'] = "추첨을 시작합니다.";
		$Data['lang_game_ladder_wait_open'] = "대기중";
		$Data['lang_game_ladder_with_open'] = "결과는 [lot] 입니다";
		$Data['lang_game_ladder_ready'] = "준비하기";
		$Data['lang_game_ladder_bet_periods'] = "구매 회차";
		$Data['lang_game_ladder_bet_confirm'] = "구매 내용 재확인";
		$Data['lang_game_ladder_wayvalue_title'] = "회차  홀/짝 분석";
		$Data['lang_game_ladder_wayvalue_select_periods'] = "최근 [select] 회차  홀/짝 분석";
		$Data['lang_game_ladder_statisics_open'] = "연속 기록";
		$Data['lang_game_ladder_statisics_notopen'] = "연속 미스 기록";
		$Data['lang_game_ladder_statisics_periods'] = "[periods] 회차까지의 통계데이터";
		$Data['lang_game_ladder_statisics_periods_accumulation'] = "회차 통계";
		
		/*營運後台使用*/
		$Data['lang_id'] = "아이디";
		$Data['lang_data_name'] = "이름";
		$Data['lang_status'] = "상태";
		$Data['lang_enable_y'] = "사용";
		$Data['lang_enable_y_ing'] = "사용중";
		$Data['lang_enable_n'] = "중지";
		$Data['lang_enable_n_ing'] = "중지 상태";
		$Data['lang_member_info'] = "회원자료";
		$Data['lang_chase_id'] = "추가번호";
		$Data['lang_save'] = "저장";
		$Data['lang_add_success'] = "추가성공。";
		$Data['lang_update_success'] = "수정성공。";
		$Data['lang_del_success'] = "삭제성공。";
		$Data['lang_save_success'] = "저장성공。";
		$Data['lang_to_select'] = "검색";
		$Data['lang_remark'] = "비고";
		$Data['lang_currency'] = "통화별";
		$Data['lang_date'] = "날짜";
		$Data['lang_time'] = "시간";
		$Data['lang_periods'] = "회차";
		$Data['lang_date_start'] = "시작 날짜";
		$Data['lang_date_end'] = "끝난 날짜";
		$Data['lang_open_lot'] = "추첨";
		$Data['lang_statistics'] = "통계";
		$Data['lang_location'] = "위치";
		$Data['lang_minute'] = "분";
		$Data['lang_second'] = "초";
		$Data['lang_setuptime'] = "건립시간";
		$Data['lang_details'] = "상세 자료";
		$Data['lang_money'] = "금액";
		$Data['lang_data_count'] = "[number] 개";

		$Data['lang_not_enter'] = "[data] 입력하세요.";
		$Data['lang_login'] = "로그인";
		$Data['lang_logout'] = "로그아웃";
		$Data['lang_bet_select'] = "오더 검색";
		$Data['lang_member_act'] = "회원 아이디";
		$Data['lang_member_select'] = "회원 검색";
		$Data['lang_highest_level'] = "최상층";
		$Data['lang_down_level'] = "아래층";
		$Data['lang_insert'] = "추가";
		$Data['lang_edit'] = "수정";
		$Data['lang_del'] = "삭제";
		$Data['lang_look'] = "보기";
		$Data['lang_operator'] = "조작자";
		$Data['lang_operator_item'] = "조작 항목";
		$Data['lang_move_up'] = "위로 이동";
		$Data['lang_move_down'] = "아래로 이동";
		$Data['lang_sequence'] = "순서";
		$Data['lang_update_sequence'] = "순서 변경";
		$Data['lang_update_sequence_close'] = "순서 변경 닫기";

		$Data['lang_ag_company_name'] = "iTwins로또사이트";
		$Data['lang_ag_title'] = "iTwins로또사이트 - 관리시스템";
		$Data['lang_ag_system'] = "관리시스템";

		$Data['lang_ag_error_101'] = "자료 없음";
		$Data['lang_ag_error_101_member_act'] = "이 회원아이디 없음";

		$Data['lang_ag_language'] = "언어";
		$Data['lang_ag_user'] = "아이디";
		$Data['lang_ag_password'] = "비밀번호";
		$Data['lang_ag_verification_code'] = "보안 검사";

		$Data['lang_ag_not_pem_look'] = "권한없음";
		$Data['lang_ag_error'] = "시스템 오류,기술자 연락하세요.";

		$Data['lang_ag_error_login'] = "자료 오류";
		$Data['lang_ag_error_login_889'] = "이 IP등록 권한 없음";
		$Data['lang_ag_error_login_107'] = "보안 검사 오류";
		$Data['lang_ag_error_login_108'] = "아이디、비번 오류";
		$Data['lang_ag_error_login_109'] = "아이디、비번 오류";
		$Data['lang_ag_error_login_110'] = "아이디 중지상태입니다.";
		$Data['lang_ag_error_login_111'] = "아이디의 층별 설치하세요.";

		$Data['lang_ag_not_bet_info'] = "구매 자료 없음";
		$Data['lang_ag_not_member_info'] = "이 회원 자료 없음";
		$Data['lang_ag_not_chase_info'] = "추가 구매내용 없음";

		$Data['lang_ag_update_password'] = "비번 수정";
		$Data['lang_ag_now_password'] = "현재 번호";
		$Data['lang_ag_new_password'] = "새로운 번호";
		$Data['lang_ag_check_password'] = "번호 확인";

		$Data['lang_ag_error_password'] = "비번 오류";
		$Data['lang_ag_different_password'] = "비번 다름";
		$Data['lang_ag_update_password_confirm'] = "비번 수정 확실합니까?";

		$Data['lang_ag_quick_select'] = "빠른 검색";

		$Data['lang_ag_enter_order_id'] = "오더 아이디 입력하세요.";
		$Data['lang_ag_enter_member_act'] = "회원 아이디 입력하세요.";

		$Data['lang_ag_pem_bet'] = "베팅 자료";
		$Data['lang_ag_pem_bet_member'] = "회원 베팅 자료";
		$Data['lang_ag_pem_bet_game'] = "게임 베팅 자료";
		$Data['lang_ag_pem_bet_details'] = "상세한 베팅 자료";

		$Data['lang_ag_pem_member'] = "회원 자료";
		$Data['lang_ag_pem_member_list'] = "회원 자료 리스트";
		$Data['lang_ag_pem_member_reback'] = "보너스 수정";
		$Data['lang_ag_pem_member_cash_flow'] = "회원 크레딧 출입금";

		$Data['lang_ag_pem_game'] = "게임 관리";
		$Data['lang_ag_pem_game_lot'] = "추첨 리스트";
		$Data['lang_ag_pem_game_lot_bet'] = "추첨리스트-베팅 자료";
		$Data['lang_ag_pem_game_type_set'] = "게임 설치";
		$Data['lang_ag_pem_game_play_set'] = "패턴 설치";

		$Data['lang_ag_pem_user'] = "사용자 관리";
		$Data['lang_ag_pem_user_level'] = "층별관리";
		$Data['lang_ag_pem_uset_set'] = "사용자 관리";

		$Data['lang_ag_pem_maintain'] = "점검 관리";
		$Data['lang_ag_pem_maintain_pem'] = "권한 자료 관리";

		$Data['lang_ag_pem_quick_select'] = "빠른 검색";

		$Data['lang_ag_pem_maintain_pem_add'] = "권한 자료 추가";
		$Data['lang_ag_pem_maintain_pem_update'] = "권한 수정";
		$Data['lang_ag_pem_maintain_pem_del_confirm'] = "권한을 삭제하면 해당 사용자의 권한도 변경됩니다.<br>권한 삭제 확인하세요.";
		$Data['lang_ag_pem_maintain_link_type'] = "연결 방식";
		$Data['lang_ag_pem_maintain_link_type_gopage'] = "프로그램머 연결";
		$Data['lang_ag_pem_maintain_link_type_url'] = "사이트 연결";
		$Data['lang_ag_pem_maintain_link_type_block'] = "따로 열기";
		$Data['lang_ag_pem_maintain_data_rel'] = "연결 코드";
		$Data['lang_ag_pem_maintain_classify'] = "형태";
		$Data['lang_ag_pem_maintain_classify_c'] = "유형";
		$Data['lang_ag_pem_maintain_classify_i'] = "항목";
		$Data['lang_ag_pem_maintain_classify_p'] = "권한";
		$Data['lang_ag_pem_maintain_can_use'] = "권한 가능";
		$Data['lang_ag_pem_maintain_choose_one'] = "권한 선택(최저 한 가지 선택)";

		$Data['lang_ag_pem_user_level_add'] = "층별 추가";
		$Data['lang_ag_pem_user_level_update'] = "층별 수정";
		$Data['lang_ag_pem_user_level_del_confirm'] = "층별 삭제하면 해당 인원의 권한도 변경됩니다.<br>이 층별 삭제 확인하세요.";
		$Data['lang_ag_pem_user_level_edit_confirm'] = "층별 권한 변동은 해당 인원의 권한도 변동됩니다.<br>변동 확인하세요.";
		$Data['lang_ag_pem_user_level_name'] = "층별";
		$Data['lang_ag_pem_user_level_number'] = "층별 인수";

		$Data['lang_ag_pem_user_level'] = "층별";
		$Data['lang_ag_pem_user_level_all'] = "전부";
		$Data['lang_ag_pem_user_level_choose'] = "층별 선택";
		$Data['lang_ag_pem_user_add'] = "사용자 추가";
		$Data['lang_ag_pem_user_update'] = "사용자 자료 수정";
		$Data['lang_ag_pem_user_enable_n_confirm'] = "중지 후 이 아이디가 시스템 등록 불가능합니다.<br>중지 확인 하세요.";
		$Data['lang_ag_pem_user_enable_y_confirm'] = "사용 가능하면 이 아이디가 시스템 등록하겠습니다.<br>사용 확인 하세요.";
		$Data['lang_ag_pem_user_enable_n_act'] = "아이디를 중지했음";
		$Data['lang_ag_pem_user_enable_y_act'] = "아이디 사용 가능";
		$Data['lang_ag_pem_user_act_re'] = "아이디 중복";
		$Data['lang_ag_pem_user_act_error'] = "아이디 격식 오류";
		$Data['lang_ag_pem_user_edit_confirm'] = "층별 이동은 해당 아이디의 권한을 바꿉니다.<br>이동 확인 하세요.";

		$Data['lang_game_lot_class_choose'] = "게임 선택";
		$Data['lang_game_lot_bet_money'] = "베팅 금액";
		$Data['lang_game_lot_bet_reback'] = "보너스 금액";
		$Data['lang_game_lot_bet_winmoney'] = "적중 금액";
		$Data['lang_game_lot_bet_bounsmoney'] = "상금 금액";
		$Data['lang_game_lot_bet_memnumber'] = "회원 수량";
		$Data['lang_game_lot_bet_profit_and_loss'] = "운영 손익";

		$Data['lang_ag_game_lot_show_bet'] = "베팅 자료 표시함";
		$Data['lang_ag_game_lot_show_bet_n'] = "표시 안 함";
		$Data['lang_ag_game_lot_show_bet_y'] = "표시";
		$Data['lang_ag_game_lot_status'] = "추첨 상태";
		$Data['lang_ag_game_lot_status_expired'] = "추첨 시간 끝남";
		$Data['lang_ag_game_lot_enter_opentime'] = "추첨 시간 입력하세요.";
		$Data['lang_ag_game_lot_enter_date'] = "날짜 입력하세요.";
		$Data['lang_ag_game_lot_enter_periods'] = "회차 입력하세요.";

		$Data['lang_ag_game_set_money_set'] = "상금 설치";
		$Data['lang_ag_game_set_highest_reback'] = "최고 보너스 비율";
		$Data['lang_ag_game_set_max_give_single'] = "한 구매개수 최고 상금";
		$Data['lang_ag_game_set_max_give_periods'] = "한 회차 최고 상금";
		$Data['lang_ag_game_set_max_bet_periods'] = "한 회차 최고 구매 금액";
		$Data['lang_ag_game_set_play_menu'] = "게임 패턴 메뉴설치";
		$Data['lang_ag_game_set'] = "게임 설치";
		$Data['lang_ag_game_set_save_confirm'] = "설치 저장하겠습니까?";
		$Data['lang_ag_game_set_play'] = "패턴";
		$Data['lang_ag_game_set_can_chase'] = "추가 가능";
		$Data['lang_ag_game_set_maintain'] = "점검";
		$Data['lang_ag_game_set_close_bet'] = "구매 종료";
		$Data['lang_ag_game_set_open_bet_before_time'] = "구매 시작 시간은 추첨전…";
		$Data['lang_ag_game_set_close_bet_before_time'] = "구매 마감 시간은 추첨전…";

		$Data['lang_ag_play_set_name'] = "패턴";
		$Data['lang_ag_play_set_odds'] = "배당율";
		$Data['lang_ag_play_set_odds_change'] = "배당율 바뀜(1%)";
		$Data['lang_ag_play_set_default_limit'] = "예설 한도";
		$Data['lang_ag_play_set_set_limit'] = "베팅 금액 설치";
		$Data['lang_ag_play_set_max_give_single'] = "한 구매개수(최고)";
		$Data['lang_ag_play_set_max_give_item'] = "한 구매내용(최고)";
		$Data['lang_ag_play_set_min_bet_money'] = "최저 베팅 금액";
		$Data['lang_ag_play_set_note'] = "설명：";
		$Data['lang_ag_play_set_remark'] = "비고：";
		$Data['lang_ag_play_set_example'] = "예：";
		$Data['lang_ag_play_set_save_single'] = "한 개 수정";

		$Data['lang_ag_enter_select_time'] = "검색 시간 입력하세요.";
		$Data['lang_ag_member_use_money_record'] = "회원 금액 사용 기록：";

		$Data['lang_ag_member_bet_record'] = "베팅 기록";
		$Data['lang_ag_member_money_record'] = "금액 기록";
		$Data['lang_ag_member_change_record'] = "머니 이동 기록";
		$Data['lang_ag_member_reback_record'] = "회원 보너스";
		$Data['lang_ag_member_can_money'] = "크레딧";
		$Data['lang_ag_member_bet_function'] = "베팅 기능";
		$Data['lang_ag_member_cash_flow'] = "출입금";
		$Data['lang_ag_member_cash_flow_add'] = "추가 - 출입금";

		$Data['lang_ag_member_bet_function_n_confirm'] = "베팅 기능 닫겠습니까?";
		$Data['lang_ag_member_bet_function_y_confirm'] = "베팅 기능 열겠습니까?";

		$Data['lang_ag_member_money_add_reduce'] = "입금/출금";
		$Data['lang_ag_member_money_add'] = "입금";
		$Data['lang_ag_member_money_reduce'] = "출금";

		$Data['lang_ag_member_reback'] = "보너스 비율";
		
		$Data['lang_ag_member_money_not_zero'] = "0이상 입력하세요";
		$Data['lang_ag_error_info'] = "자료 오류";

		$Data['lang_ag_member_money'] = "보유금액";
		$Data['lang_ag_member_profit_and_loss'] = "회원 손익";

		$Data['lang_bet_status_class'] = "오더  분류";
		$Data['lang_bet_status_class_win'] = "적중";
		$Data['lang_bet_status_class_effective'] = "유효";
		$Data['lang_bet_status_class_payout'] = "상금 계산 완료";
		$Data['lang_bet_status_class_payout_not'] = "상금 미계산";
		$Data['lang_bet_status_class_cancel'] = "취소 완료";

		$Data['lang_bet_chase_content'] = "추가내용";
		$Data['lang_bet_count'] = "구매 수량";
		$Data['lang_ag_bet_peo_number'] = "베팅 인수";
		
		$Data['lang_ag_game_lot_status_complete'] = "추첨 완성";
		$Data['lang_ag_game_lot_status_not_payout'] = "계산 기다림";
		$Data['lang_ag_game_lot_status_Processing'] = "처리중…";
		$Data['lang_ag_game_lot_status_not_open'] = "미추첨";
		$Data['lang_ag_game_lot_status_close'] = "구매 종료";
		$Data['lang_ag_game_lot_status_open'] = "구매 가능";
		
		$Data['lang_game_type_1'] = "10분로또";
		$Data['lang_game_type_3'] = "북경PK10";
		$Data['lang_game_type_4'] = "빠른3";
		$Data['lang_game_type_5'] = "11중 택5";
		$Data['lang_game_type_6'] = "복체로또";
		$Data['lang_game_type_7'] = "사다리게임";
		$Data['lang_game_type_8'] = "달팽이레이싱";
		
		$Data['lang_mobile_fun_money'] = "보유금액";
		$Data['lang_mobile_fun_member_center'] = "회원센터";
		$Data['lang_mobile_fun_game'] = "게임 로비";
		$Data['lang_mobile_fun_pc'] = "pc버전";
		
		$Data['lang_bet_now'] = "바로 시작";
		
		$Data['lang_condition_select'] = "검색";
		$Data['lang_info_list'] = "자료 리스트";
		
		$Data['lang_order_select_bet_btn'] = "오더";
		$Data['lang_order_select_cancel_btn'] = "오더 취소";

		$Data['lang_order_edit_btn'] = "편집";
		$Data['lang_order_cancel_btn'] = "오더 취소";
		$Data['lang_order_close_btn'] = "취소";
		$Data['lang_msg_order_select_class'] = "검색 분류 선택하세요.";

		$Data['lang_mobile_game_close_time'] = " [periods]회차 남은 시간";
		$Data['lang_mobile_game_header_close_time'] = " [time]후 회차[periods] 마감";

		$Data['lang_game_ladder_bet_content_one'] = "2중 택1";
		$Data['lang_game_ladder_bet_content_two'] = "결과X줄수";
		$Data['lang_game_ladder_bet_content_number'] = "번호";
		$Data['lang_game_ladder_bet_content_odds'] = "배당율";
		$Data['lang_game_ladder_bet_content_money'] = "구매금액";
		$Data['lang_game_ladder_bet_content_money_been'] = "당기구매금액";

		$Data['lang_game_ladder_bet_content_total_money'] = "구매 총액 [money]원";
		$Data['lang_game_ladder_bet_content_total_money_been'] = "당기 구매 총액 [money]원";

		$Data['lang_game_btn_bet_info'] = "구매 자료";
		$Data['lang_game_btn_clear'] = "취소";
		$Data['lang_game_btn_bet'] = "구매";

		$Data['lang_game_ladder_lot_open_periods'] = "[periods]회차 당첨 결과";
		$Data['lang_game_ladder_open_auto_close'] = "(결과 나온 후 자동 닫음)";

		$Data['lang_game_ladder_use_chips_choose'] = "칩 선택하세요.";
		$Data['lang_game_ladder_bet_content_choose'] = "오더 먼저 추가하세요.";
		$Data['lang_game_ladder_clear_list_confirm'] = "리스트 삭제하겠습니까?";

		$Data['lang_mobile_game_maintain'] = "게임 점검중";
		$Data['lang_mobile_game_close'] = "대기중";
		$Data['lang_mobile_game_maintain_note_1'] = "소중한 회원님 안녕하세요.";
		$Data['lang_mobile_game_maintain_note_2'] = "더 좋은 환경을 제공드리기 위하여 점검중입니다.<br>불편을 드려 죄송합니다.";
		
		/*蝸牛*/
		$Data['lang_game_snail_wayvalue_title'] = "依期数统计结果";
		$Data['lang_game_snail_wayvalue_select_periods'] = "最近 [select] 期 统计结果";
		
		
		return $Data;
	}
?>