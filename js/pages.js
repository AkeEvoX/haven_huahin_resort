var pages = {};
pages.init = function(name){
	
	var lang = pages.lang();
	console.log("language init: "+lang);
	var path = "js/pages/"+lang+"/"+name+".json";
	$.getJSON(path,function (resp){
		//console.warn(resp);
		$.each(resp,function(title,val){
			
			//utility.print_log(title+ "|"+val);
			$('.'+title).html(val);
			//$('#'+title).html(val);
		});
	}).fail(function(xhr,status,err){
		utility.print_error("status:"+status + " <br/> Error : "+err);
	});
	
	//get common language
	path = "js/pages/"+lang+"/common.json";
	$.getJSON(path,function(resp){
			console.log("get common message.");
			pages.message = resp;
	});
	
}

pages.lang  = function(lang){
	
	if (typeof(Storage) !== "undefined") {
		if(lang!==undefined){
			localStorage.lang = lang;
			console.log("set lang ="+localStorage.lang);
		}else{
		
			if(localStorage.lang==undefined) {
				localStorage.lang='en' ;//defautl language
				console.log("set default language");
			}
			console.log("get lang ="+localStorage.lang);
			return localStorage.lang;
		}
	} else {
			console.log("Sorry! No Web Storage support..");
    // Sorry! No Web Storage support..
	}
}