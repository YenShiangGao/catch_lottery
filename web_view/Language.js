var LG = new function(){
	var $this = this;
	var Lauguage = {
		{Lang}"{key}":"{val}",
		{/Lang}
	}
	this.gstr = function(key,ary){
		if(Lauguage.hasOwnProperty(key)){
			var str = Lauguage[key];
			if(ary){
				for(var k in ary){
					var v = ary[k];
					while(str.indexOf("["+k+"]")!=-1){
						str=str.replace("["+k+"]",v)
					}
				}
			}
			return str;
		}else{
			return key;
		}
	}
	this.text_replace = function(content){
		
	}
}