#### 抓獎系統網址

| 名稱 | 網址 |
| ------ | :------ |
| 測試站 |  http://cdt.3cstore.info/  |
| 香港站 |  http://cdt.fotety8735.com:8091/  |
| 大陸站 |  http://cdt.hzjiashang.com:8091/  |

#### 排程開啟位址

| 名稱 | ＩＰ |
| ------ | :------ |
| 測試站 |  http://210.68.95.166:60001/  |
| 香港站 |  http://47.91.227.224:60001  |
| 大陸站 |  http://117.28.243.185:60001/  |
| 正式站 v8 備用機 |  http://202.133.245.179:60001/  |

#### TG通知群組

| 名稱 | 說明 |
| ------ | :------ |
| 大陸 抓獎通知 |  僅大陸主機相關訊息  |
| 香港 抓獎通知 |  僅香港主機相關訊息  |
| 開獎重要通知 |  香港以及大陸主機<br>開獎號碼有誤、提早開獎。兩項訊息  |
| 六合彩專用 |  六合彩的開獎日期、開獎號碼、下一期開獎資訊  |


#### FTP

| 名稱 | ＩＰ | 位置 |
| ------ | :------ | :------ |
| Node測試機 |  210.68.95.161:4161  | /home/web_gs_pb/fun/web_nj |
| Node正式站 node機 V8 - 2 |  202.133.245.179:4433  | /home/web_gs_pb/fun/web_nj |
| Node 大陸站 |  117.28.243.185:4433  | /home/web_gs_pb/fun/web_nj |
| PHP  大陸站 |  117.28.243.185:4433  | /home/web_gs_pb/fun/game-catch |
| Node/PHP香港站 |  47.91.227.224:4433  | /home/web_gs_pb/fun/web_nj |

#### [資料庫](http://gitlab.ibc6demo.com:8080/518king/catch_lottery/blob/master/database.md)

| 名稱 | 說明 |
| ------ | :------ |
| hn_city |  紀錄越南彩的開獎地區，開獎時間，開獎日期。  |
| LT_api_rec |  紀錄api得過程  |
| LT_edit_record |  任何從前端進行的操作。  |
| LT_game |  記錄所有遊戲的資訊。  |
| LT_history |  每一筆抓獎源取回的開獎資訊。  |
| LT_openset |  六合彩、萬字票等，指定開獎日期設定。  |
| LT_periods |  每日每期期數紀錄。  |
| LT_period_error |  期數號碼錯誤紀錄。  |
| LT_permissions |  前台權限。  |
| LT_proxy |  proxy存取。  |
| LT_url |  所有抓獎源資訊。  |
| LT_user |  使用者登入資訊。  |
| LT_user_level |  使用者級別。  |
| LT_vac |  暫停開獎日期設定。  |
| tb_game_periods |  事先設定好的遊戲每日期數。  |
| tb_telegram_group |  telegram群組  |
| tb_telegram_notice |  telegram通知訊息  |
| tb_telegram_user |  telegram使用者  |

#### 開獎異常

> 所有狀況都需先至官方確認，確認後若為官方問題，則通知窗口，請窗口通知各平台。
然後通知組長，請組長評斷是否先暫時關盤。

***code為資料庫與程式相對應的代碼***

| 名稱 | code | 狀況 | 解決方法 |
| ------ | :------: | ------ | ------ |
| 延誤開獎 |  1  | 與預定時間有差距 | ❖ 若官方正常開獎，檢查各開獎源是否給予當期開獎號碼，若已給予可先檢查信任組數是否與開獎源不符合，可先手動開獎或是更改信任組數。 <br> 【信任組數 => 遊戲選單->遊戲清單】 |
| 開獎號碼有誤 |  2  | 已抓取的開獎號碼做比對 | ❖ 若官方開獎號碼正確，開獎源開獎錯誤，則手動開獎已官方號碼為主。<br>【手動開獎 => 遊戲選單->抓獎紀錄(高權重)】 |
| 開獎時間有誤 |  3  | 比預定開獎時間*(1)還要早抓取到號碼 | ❖ 若開獎源比預定時間抓取到開獎號碼，或是晚于設定時間抓取到開獎號碼，則需確認後，通知窗口。 |
| 開獎號碼重複 |  4  | 若資料庫設定*(2)此彩種開獎號碼不允許重複，但卻有一樣的開獎號則有誤 | ❖ 若開獎源取回號碼有誤，檢查信任平台，若與其號碼一樣，告知組長，請組長判斷。 |
| 開獎號碼未落在號碼區間 |  5  | 與資料庫設定*(3)的號碼區間不一樣 | ❖ 若開獎源取回號碼有誤，檢查信任平台，若與其號碼一樣，告知組長，請組長判斷。 |
| 期數表尚未產生 |  6  | 每日產生的期數表，是否產生 | ❖ 檢查是哪個彩種沒有產生隔日的期數，手動執行產生期數表api<br>【單一遊戲，網域/lotteryapi/autocreatdata/遊戲ID/產生日期】<br>【全部遊戲，網域/lotteryapi/autocreatdata/】|
| 期數不存在表中 |  7  | 抓獎源給的期數不存在表中 | ❖ 檢查此期數是否為當天當期所需要 |
| 期數表不期全 |  8  | 每日固定期數，多或少 期數 | ❖ 檢查產生的期數是否與資料庫的tb_game_periods數量相同，若不相同，則刪除LT_periods相對應的日期以及遊戲ID，要將LT_game表裡的param_1日期改為前一日。<br>❖ 若期數為序號產生模式，則將期數改為正確期數當天的最後一期。 |
| 六合彩開獎通知 |  9  | 開獎當期通知
| 六合彩開獎日期變動 |  10  | 六合彩的開獎日期新增 | ❖ 若六合彩開獎日期有所變動，則需要手動更改期數，將取消的期數日期刪除，並更改剩餘未開獎期數，最後更改遊戲主表參數1。 |
| 六合彩開獎日期異動 |  11  | 六合彩的開獎日期有變動*(4)
| 越南彩開獎城市不存在表中 |  12  | 越南彩開獎城市不存在表中

#### 補充

| 名稱 | 說明 | 備註 |
| ------ | :------ | :------ |
| Git |  http://gitlab.ibc6demo.com:8080/518king/catch_lottery  | |
| 資料庫(測試) |  http://php.ibc6demo.com:4000   | 位置：lt/game/lt_game_catech |
| 資料庫(正式) |  http://php.memlottery.org:4000/  | 伺服器選擇<br>117.28.244.156-抓結果-DB-主<br>47.91.227.224-抓結果-DB-香港
| 同步系統 |  http://ctl.rdrsync.org/  |　|
| 六合彩官方網址 |  https://bet.hkjc.com/marksix/default.aspx  |　|
| 大無限 |  http://999.da9991.com/LobbyOp#bet/lobby  |　|
| 優博 |  https://yb2246.com:5569/  |　|

### 註解

* (1) tb_game_periods 表 PeriodsTime 欄位
* (2) LT_game 表 repeat 欄位
* (3) LT_game 表 min_number 欄位 與 max_number 欄位 之間
* (4) LT_game 表parm欄位

### [controller講解](http://gitlab.ibc6demo.com:8080/518king/catch_lottery/blob/master/controller.md)
### [系統功能講解](http://gitlab.ibc6demo.com:8080/518king/catch_lottery/blob/master/system.md)
### [api](http://gitlab.ibc6demo.com:8080/518king/catch_lottery/blob/master/api.md)

# ※由於抓獎系統沒有系統同步功能，<br>因此，更改完程式必須自行將程式同步到FTP，<br>同步完成以後，需到[同步系統](http://ctl.rdrsync.org/)裡，點擊【清抓開獎opcache】更新同步。

