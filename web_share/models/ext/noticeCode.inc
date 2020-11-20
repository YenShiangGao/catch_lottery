<?php
class noticeCode {
    public static function init() {
        $data = array();

        $data['delayOpen'] = [1, '延誤開獎'];
        $data['openNumError'] = [2, '開獎號碼有誤'];
        $data['quickOpen'] = [3, '提早開獎'];
        $data['openNumRepeat'] = [4, '開獎號碼重複'];
        $data['openNumNotRange'] = [5, '開獎號碼未落在號碼區間'];
        $data['periodTabNotCre'] = [6, '期數表尚未產生'];
        $data['periodNotInTab'] = [7, '期數不存在表中'];
        $data['periodError'] = [8, '期數表不期全'];
        $data['hk6Open'] = [9, '六合彩開獎通知'];
        $data['hk6OpenDateCre'] = [10, '六合彩開獎日期新增'];
        $data['hk6OpenDateUpd'] = [11, '六合彩開獎日期異動'];
        $data['hnCityNotInTab'] = [12, '越南彩開獎城市不存在表中'];
        
        return $data;
    }
}
