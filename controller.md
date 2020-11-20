### 程式controllers講解
開獎系統邏輯如下：
由node排程呼叫catchApi傳入以下資訊
```json
{
	$ename = 開獎源代號(遊戲清單/代號), 
	$url_id = 開獎源編號(遊戲清單/編號), 
	$api_name = 開獎源網址名稱(遊戲線路狀態/api名稱), 
	$proxy = 0/1(不啟用/啟用)), 
	$from = null/node(php做完完整流程/node抓取獎號後，傳回php), 
}
```
進行網址解析->到頁面取得資訊->存入DB


- [callapi.php](http://gitlab.ibc6demo.com:8080/518king/catch_lottery/blob/master/web_mem/application/controllers/callapi.php)
- [gloco.php](http://gitlab.ibc6demo.com:8080/518king/catch_lottery/blob/master/web_mem/application/controllers/gloco.php)
- [hk6.php](http://gitlab.ibc6demo.com:8080/518king/catch_lottery/blob/master/web_mem/application/controllers/hk6.php)
- [lotteryapi.php](http://gitlab.ibc6demo.com:8080/518king/catch_lottery/blob/master/web_mem/application/controllers/lotteryapi.php)
- [main.php](http://gitlab.ibc6demo.com:8080/518king/catch_lottery/blob/master/web_mem/application/controllers/main.php)

#### main.php
> 後臺系統主要controllers
> 後臺所有頁面的入口

##### class function 解析
|  className | 說明  |
| ------------ | ------------ |
| fun | 需呼叫到lib的function |
| catchApi | 主要抓獎入口，進行網址判斷、內容擷取、號碼儲存等動作 |
| hnUnofficialCatchApi | 抓取越南彩，同catchApi |
| specifyGame | 總後端 → 抓獎系統( game ) |
| specifyPeriod | 總後端 → 抓獎系統(錯誤開獎號碼查詢) |
| openDate | 總後端 → 抓獎系統(六合彩&萬字票抓取開獎日期) |
| vacList | 總後端 → 抓取遊戲假期 |
| gameGroup | 抓出所有遊戲 以及 遊戲的網址 |
| openNumCheck | 檢查 開獎時間 延誤 |
| checkPeriods | 檢查期數表是否已經產生 |
| sendNotice | 跑telegram通知 |
| autocreatdata | 自動產生期數表 |
| specifyDay | 指定日期產生期數表 |

#### lotteryapi.php
> 所有與抓獎相關的api
> 由node排程與遊戲端呼叫的入口

#### gloco.php
> 輔助頁面入口的controllers

#### hk6.php
> 六合彩的開獎結果以及開獎日期api

##### class function 解析
|  className | 說明  |
| ------------ | ------------ |
| hk6_openResult | 六合彩開獎號碼通知 |
| hk6_openDate | 六合彩開獎日期檢查 |

#### callapi.php
> 測試用的controllers，沒有意義性



## ※由於抓獎系統為最一開始撰寫之專案，因此裡面有許多冗贅的檔案



