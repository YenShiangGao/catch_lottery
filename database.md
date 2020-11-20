#### hn_city
| tbName | 說明 |
| --- | --- |
| id | 編號 |
| area | 越南彩地區 |
| city | 城市名 |
| city_en | 英文名稱 |
| city_ch | 中文名稱 |
| cityAry | 有些網站的名稱不一樣，因此做成字串去讓程式判斷 |
| url | 要抓取的獎號網址 |
| w1 | 星期一是否開獎 N/Y |
| w2 | 星期二是否開獎 N/Y |
| w3 | 星期三是否開獎 N/Y |
| w4 | 星期四是否開獎 N/Y |
| w5 | 星期五是否開獎 N/Y |
| w6 | 星期六是否開獎 N/Y |
| w0 | 星期日是否開獎 N/Y |
| lottery_num | 總共開出獎號 |
| PeriodsTime | 開獎時間 |
| setuptime | 新增時間 |
| nowtime | 最後更新時間 |

#### hn_unofficial_url
| tbName | 說明 |
| --- | --- |
| id | 編號 |
| area | 程式區域 |
| url | 網址 |
| url_id | 網址ID |
| url_name | 網址名稱 |
| w1 | 星期一是否開獎 N/Y |
| w2 | 星期二是否開獎 N/Y |
| w3 | 星期三是否開獎 N/Y |
| w4 | 星期四是否開獎 N/Y |
| w5 | 星期五是否開獎 N/Y |
| w6 | 星期六是否開獎 N/Y |
| w0 | 星期日是否開獎 N/Y |
| setuptime | 新增時間 |
| nowtime | 最後更新時間 |

#### LT_api_rec
| tbName | 說明 |
| --- | --- |
| id | 編號 |
| url | 網址ID |
| gameType | 遊戲ID |
| post | post 資料 |
| descr | 取回資料 |
| ua | 日期 |
| itime | 寫入時間 | 

#### LT_edit_record
| tbName | 說明 |
| --- | --- |
| id | 編號 |
| tb | 表格名稱 |
| tb_id | 表格ID |
| type | 寫入型態 |
| value | 執行SQL |
| acc_tb | 執行者帳號 |
| acc_tb_id | 執行者id |
| setuptime | 新增時間 |
| nowtime | 最後更新時間 |

#### LT_game
| tbName | 說明 |
| --- | --- |
| id | 遊戲ID，很重要，要跟遊戲端相同 |
| cname | 官方遊戲名稱 |
| ename | 遊戲英文名稱 |
| enable | 是否開始抓取獎號狀態 |
| cycle | 每天還是每周抓取 |
| param | 最新產生期數 |
| param_1 | 最新期數產生日期 |
| repeat | 開獎號碼是否會重複 |
| urlCheck | 要幾組開獎源開回獎號，才做正式開獎的動作 |
| notice | 若有任何錯誤狀況，是否要開啟Telegram通知 |
| noticeTime | TG通知時間 |
| period_format | 期數組合的規則 |
| period_num | 期數年+月+編碼，此編碼的位數
| lottery_num | 總共開出幾個獎號 |
| min_number | 最小的開獎號碼 |
| max_number | 最大的開獎號碼 |
| nowtime | 最後更新時間 |
| setuptime | 新增時間 |

#### LT_history
| tbName | 說明 |
| --- | --- |
| id | 編號 |
| game_id | 遊戲編號 |
| lottery_id | 期數表ID(LT_periods) |
| period_str | 期數 |
| lottery | 開獎號碼 |
| lottery_time | 開獎源取回的時間 |
| url_id | 來自於哪一個開獎源 |
| proxy | 是否使用proxy |
| nowtime | 最後更新時間 |
| setuptime | 新增時間 |
| code_order | 權重 |

#### LT_openset
| tbName | 說明 |
| --- | --- |
| id | 編號 |
| game_id | 遊戲編號 |
| lottery_year | 年 |
| lottery_month | 月 |
| lottery_day | 日 以逗號為分隔 |
| enable | 啟用狀態才會進行期數新增 |
| nowtime | 最後更新時間 |
| setuptime | 新增時間 |

#### LT_periods
| tbName | 說明 |
| --- | --- |
| id | 編號 |
| game_id | 遊戲編號 |
| lt_error_id | 錯誤數表ID(LT_period_error) | 
| lottery | 開獎源取回的號碼 |
| period_date | 開獎日期 |
| preiod_num | 期數編碼 |
| period_str | 開獎期數 |
| lottery_time | 實際開獎時間 |
| be_lottery_time | 預計開獎時間 |
| lottery_status | 開獎狀態 0//1 |
| checks | 檢查狀態 0/1 |
| url_id | 來源網址ID |
| nowtime | 最後更新時間 |
| setuptime | 新增時間 |

#### LT_period_error
| tbName | 說明 |
| --- | --- |
| id | 編號 |
| game_id | 遊戲編號 |
| lottery_id | 期數表ID(LT_periods) |
| lottery | 開獎源取回的號碼 |
| nowtime | 最後更新時間 |
| setuptime | 新增時間 |

#### LT_permissions
| tbName | 說明 |
| --- | --- |
| id | 編號 |
| up_id | 階級ID |
| name | 選單名稱 |
| classify | 型態 C第一層I下一層 |
| link_type | 開啟方式 |
| addcol | 新增權限 0/1 |
| editcol | 修改權限 0/1 |
| delcol | 刪除權限 0/1 |
| look | 觀看權限 0/1 |
| data_rel | 指向網頁名稱 |
| enablel | 狀態 0正常1停止使用2刪除 |
| seq | 排序 |
| icon | 圖示 |
| nowtime | 最後更新時間 |
| setuptime | 新增時間 |

#### LT_permissions_lvl
| tbName | 說明 |
| --- | --- |
| id | 編號 |
| game_id | 遊戲編號 |
| lvl_id_ | 對應ID (LT_user_lvl) |
| perm_id_ | 對應ID (LT_permissions) |
| addcol | 新增權限 0/1 |
| editcol | 修改權限 0/1 |
| delcol | 刪除權限 0/1 |
| look | 觀看權限 0/1 |
| data_rel | 指向網頁名稱 |
| enablel | 狀態 0正常1停止使用2刪除 |
| nowtime | 最後更新時間 |
| setuptime | 新增時間 |

#### LT_proxy
| tbName | 說明 |
| --- | --- |
| id | 編號 |
| enable | 開啟狀態 |
| IP | IP位址 |
| port | port |
| proxy_acc | proxy帳號 |
| proxy_pwd | proxy密碼 |
| remark | 備註 |
| nowtime | 最後更新時間 |
| setuptime | 新增時間 |

#### LT_url
| tbName | 說明 |
| --- | --- |
| id | 編號 |
| game_id  | 開獎源屬於哪個遊戲 |
| url | 網址 |
| url_name | 開獎源名稱 |
| api_name | 開獎源的英文代號 |
| post |  |
| enable | 開獎源是否開啟，若未開啟則不會進行抓獎 |
| code_order | 0 最高權重，1 低權重 |
| last_period | 最新取回的期數 |
| last_status | 是否更新 |
| last_time | 最後更新時間 |
| last_cost | 一次取回所花費的時間 |
| last_proxy | 是否使用 |
| nowTime | 最後更新時間 |
| setuptime | 新增時間 |
| proxy_enable | 是否啟動proxy |

#### LT_user
| tbName | 說明 |
| --- | --- |
| id | 編號 |
| lvl_id_ | 權限<br>0超級管理員<br>1高級管理員<br>2管理員<br>3一般使用者 | 
| acc | 帳號 |
| pwd | 密碼 |
| cname | 名稱 |
| remark | 備註 |
| status | 狀態 0正常1停止使用2刪除 |
| nowTime | 最後更新時間 |
| setuptime | 新增時間 |

#### LT_user_level
| tbName | 說明 |
| --- | --- |
| id | 編號 |
| up_id | 級別ID |
| lvl_name | 階級名稱 |
| status | 狀態<br>0正常<br>1停止使用 |
| nowTime | 最後更新時間 |
| setuptime | 新增時間 |

#### LT_vac
| tbName | 說明 |
| --- | --- |
| id | 編號 |
| game_id | 遊戲編號 |
| vacStart | 假期 起 |
| vacEnd | 假期 訖 |
| enable | 啟用狀態才會在產生期數時進行檢查 |
| nowTime | 最後更新時間 |
| setuptime | 新增時間 |

#### tb_acc_login
| tbName | 說明 |
| --- | --- |
| id | 編號 |
| acc | 帳號 |
| ip | ip位置 |
| agent | 登入裝置 |
| setuptime | 新增時間 |

#### tb_game_periods
| tbName | 說明 |
| --- | --- |
| id | 編號 |
| game_id | 遊戲編號 |
| cycle | day 每天 weeks 每周 |
| Periods | 第幾期 |
| PeriodsTime | 期數應開獎時間 |
| nowTime | 最後更新時間 |
| setuptime | 新增時間 |

#### tb_telegram_group
| tbName | 說明 |
| --- | --- |
| id | 編號 |
| Name | 名稱 |
| nowTime | 最後更新時間 |
| setuptime | 新增時間 |

#### tb_telegram_notice
| tbName | 說明 |
| --- | --- |
| id | 編號 |
| tb_id | 表格ID |
| game_id | 遊戲編號 |
| period_str | 期數 |
| type | 狀態名稱 |
| type_id | 狀態ID |
| user_id | tg群組編號 |
| content | 訊息內容 |
| notice | 通知狀態 N 尚未通知 Y 已通知 |
| notice_time | 通知時間 |
| nowTime | 最後更新時間 |
| setuptime | 新增時間 |

#### tb_telegram_user
| tbName | 說明 |
| --- | --- |
| id | 編號 |
| group_id | 群組ID |
| user_id | tg使用者編號 |
| first_name | 群組姓 |
| last_name | 群組名 |
| username | 使用者名稱 |
| web | 所屬類別 |
| enable | 狀態 Y 開啟 N 關閉 |
| url | 類別網址 |
| nowTime | 最後更新時間 |
| setuptime | 新增時間 |
