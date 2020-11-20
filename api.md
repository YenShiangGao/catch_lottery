### API 使用方式 
資料傳遞 : POST<br>
說明：開獎源抓取開獎號碼<br>
網址：{host}/lotteryapi/catchApi/遊戲代號/開獎源編碼/開獎源API/是否使用proxy<br>
回傳格式如下<br>
```object
{
    "code":100,
    "msg":[{
        "expect":20201012059,
        "opencode":"9,3,5,0,1",
        "opentime":"2020-10-1311:36:51"
    }]
}
```

| 名稱 | 說明 |
| --- | --- |
| code | 成功代碼100 |
| msg | expect:期數<br>opencode:開獎號碼<br>opentime:開獎時間 |

資料傳遞 : POST<br>
說明：取得指定遊戲期間開獎狀態<br>
網址：{host}/lotteryapi/specifyGame/token/遊戲編號/開始時間/結束時間<br>
回傳格式如下<br>
```object
{
    "code":100,
    "GameTypeInfo":[{
        "date":"2020-10-13",
        "game_id":"1",
        "code":"6,5,1,7,8",
        "period":"20201013027",
        "time":"2020-10-13 13:10:00",
        "rectime":"2020-10-13 13:13:10"
    }]
}
```

| 名稱 | 說明 |
| --- | --- |
| code | 成功代碼100 |
| GameTypeInfo | date:日期<br>game _ id:遊戲編號<br>code:開獎號碼<br>period:期數<br>time:開獎時間<br>rectime:取回獎號時間 |

資料傳遞 : POST<br>
說明：取得指定遊戲期數給全部抓到的開獎 所有網址<br>
網址：{host}/lotteryapi/specifyPeriod/token/遊戲編號/期數<br>
回傳格式如下<br>
```object
{
  "code": 100,
  "GameTypeInfo": {
    "game_id": "1",
    "period_date": "2020-10-13",
    "periods": "20201013001",
    "time": "2020-10-13\r\n04:31:31",
    "lottery_info": [{
        "code": "8,9,0,7,7",
        "source": "星彩網",
        "url": "http://a.apilottery.com/api/e731f825b511c1473355d4ef888b8104/cqssc/json",
        "rectime": "2020-10-13\r\n00:32:10"
      }]
  }
}
```

| 名稱 | 說明 |
| --- | --- |
| code | 成功代碼100 |
| GameTypeInfo | game _ id:遊戲編號<br> period _ date:開獎日期<br>periods:期數<br>time:開獎時間<br>lottery _ info:開獎資訊 |
| _ lottery_info | code:獎號<br>source:來源<br>url:來源網址<br>rection:取回時間 | 

資料傳遞 : POST<br>
說明：錯誤開獎號碼查詢<br>
網址：{host}/lotteryapi/specifyPeriodError/token/開始日期/結束日期<br>
回傳格式如下<br>
```object
{
    "code": 100,
    "GameTypeInfo": [{
        "id": "1",
        "game_id": "110",
        "lottery_id": "12894",
        "lottery": "{\"8\":[\"19\"],\"7\":[\"000\"],\"6\":[\"0000\",\"0000\",\"0000\"]",
        "nowtime": "2020-08-08\r\n17:18:07",
        "setuptime": "2020-08-08\r\n17:18:07",
        "period_str": "20200808_17"
    }]
}
```

| 名稱 | 說明 |
| --- | --- |
| code | 成功代碼100 |
| GameTypeInfo | id:編號<br>game _ id:遊戲編號<br>lottery _ id:期數db對應id<br>lottery:開獎號碼<br>nowtime:更新時間<br>setUptime:寫入時間<br>period_str:期數 | 

資料傳遞 : POST<br>
說明：六合彩&萬字票抓取開獎日期<br>
網址：{host}/lotteryapi/specifyPeriod/token/遊戲編號/年/月<br>
回傳格式如下<br>
```object
{
  "code": 100,
  "Lottery_day": [
    {
      "Lottery_year": "2018",
      "Lottery_month": "3",
      "Lottery_day": "1,4,6,8,10,13,15,17,20,22,24,29,31"
    }
  ]
}
```

| 名稱 | 說明 |
| --- | --- |
| code | 成功代碼100 |
| Lottery_day | Lottery _ year:年<br>Lottery _ month:月<br>Lottery _ day:日 | 

資料傳遞 : POST<br>
說明：抓取遊戲假期<br>
網址：{host}/lotteryapi/vacList/token/年/遊戲編號<br>
回傳格式如下<br>
```object
{
  "code": 100,
  "list": [{
      "id": "46",
      "game_id": "1",
      "vacStart": "2020-01-22 00:00:00",
      "vacEnd": "2020-03-10\r\n23:59:59"
    }]
}
```

| 名稱 | 說明 |
| --- | --- |
| code | 成功代碼100 |
| list | id: 編號<br>game _ id: 遊戲編號<br>vacStart: 假期開始<br>vacEnd: 假期結束 | 
