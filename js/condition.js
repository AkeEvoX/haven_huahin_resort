var condition = {};

condition.food_service = function(enable,callback){
	
	
	
	var result = {"title":"","desc":""};
	var title = "";
	var desc = "";
	console.warn("food service is " + enable);
	var src = "js/rooms/"+ pages.lang()  + "/condition.json?_="+new Date().getMilliseconds();
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
	
	//check date is over 14 day
	var current_date = moment(new Date());
	var expire_date = moment(moment(expiredate,"DD/MM/YYYY"));
	//var expire_date = moment(moment(expiredate,'DD/MM/YYYY'));
	console.log("current date is " + current_date);
	console.log("expire date is " + expiredate);
	var daydiff = expire_date.diff(current_date,'days');
	
	console.log("reserve range days is " + daydiff);
	if(daydiff < 0 ){
		enable = "0"; //disable cancel room  when rent  a room is minimum 14 day .
	}
	//ex. expiredate = 21/08/2017
	
	console.warn("cancel room is " + enable);
	
	var src = "js/rooms/"+ pages.lang()  + "/condition.json?_="+new Date().getMilliseconds();
	$.getJSON(src,function(resp){
		
		switch(enable){
		case "0" : 
			result.title = resp.disable_cancelroom.title;
			result.desc = resp.disable_cancelroom.desc;
			
		break;
		
		case "1" : 
		
			result.title = resp.enable_cancelroom.title;
			result.desc = resp.enable_cancelroom.desc;
			var date = utility.date_format(expiredate,pages.lang());
			result.desc = result.desc.replace("{0}",date);
			
		break;
		}
		
		return callback(result);
		
	});
	
}

condition.pay_now = function(enable,callback){
	
	var result = {"title":"","desc":""};
	var title = "";
	var desc = "";
	console.warn("pay now is " + enable);
	var src = "js/rooms/"+ pages.lang()  + "/condition.json?_="+new Date().getMilliseconds();
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