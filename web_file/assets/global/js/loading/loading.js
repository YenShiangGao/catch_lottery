$(function(){
	$("body").append('<div class="div_loading"><div class="div_loading_text"><br><br><br><font color="white" class="font_loading_text"></font></div></div>');
})
function loading(b,txt){
	if(b){
		$("body").addClass("loading");
		if(txt){
			$(".font_loading_text").html(txt)
		}
	}else{
		$("body").removeClass("loading"); 
	}
}
