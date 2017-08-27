var condition = {};

condition.food_service = function(enable,callback){
	
	
	
	var result = {"title":"","desc":""};
	var title = "";
	var desc = "";
	
	var src = "js/rooms/"+ pages.lang()  + "/condition.json";
	$.getJSON(src,function(resp){
		
		switch(enable){
			case "0" : 
				title = resp.disable_foodeservice.title;
				desc = resp.disable_foodeservice.desc;
			break;
			case "1" : 
				title = resp.enable_foodservice.title;
				desc = resp.enable_foodservice.desc;
			break;
		}
		
		
		result.title = title;
		result.desc = desc;
		
		return callback(result);
		
	});
	
	
	
}

condition.cancel_room = function(enable,expiredate,callback){
	
	
	var result = {"title":"","desc":""};
	var title = "";
	var desc = "";
	
	var src = "js/rooms/"+ pages.lang()  + "/condition.json";
	$.getJSON(src,function(resp){
		
		switch(enable){
		case "0" : 
			result.title = resp.disable_cancelroom.title;
			result.desc = resp.disable_cancelroom.desc;
			
			var date = utility.date_format(expiredate,pages.lang());
			
			result.desc = result.desc.replace("{0}",date);
			
		break;
		case "1" : disable_cancelroom
			result.title = resp.enable_cancelroom.title;
			result.desc = resp.enable_cancelroom.desc;
		break;
		}
		
		return callback(result);
		
	});
	
}

condition.pay_now = function(enable,callback){
	
	var result = {"title":"","desc":""};
	var title = "";
	var desc = "";
	
	var src = "js/rooms/"+ pages.lang()  + "/condition.json";
	$.getJSON(src,function(resp){
		
		switch(enable){
		case "0" : 
			title = resp.disable_paynow.title;
			desc = resp.disable_paynow.desc;
		break;
		case "1" : 
			title = resp.enable_paynow.title;
			desc = resp.enable_paynow.desc;
		break;
		}
		
		result.title = title;
		result.desc = desc;
		return callback(result);
	});
	
}