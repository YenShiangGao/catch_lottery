//-----　ZgWindow_v1.0　-----//

1、css：UI修改

2、JS：
	01）修改放大縮小….等icon樣式
	02）/*視窗置頂*/ obj.bind("ToTop",op,function(e)：設定最高zindex加當前視窗class「currentWindows」
	03）展開時加class=frostedGlass，使其背景模糊
	04）彈窗動畫執行完後再開啟iframe
	05）開啟的iframe彈窗，其預設的id名稱加當前時間getTime();
	06）checkiframe();原本是判斷iframe[src='"+url+"']，改為form[action='"+url+"']
	07）彈窗縮到最小時仍顯示關閉視窗按鈕
	08）obj.draggable()的 containment 參數 由con待入物件名稱 改 指定'window'


