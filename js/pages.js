var pages = {};
pages.init = function(name){
	
	var lang = pages.lang();
	console.log("language : "+lang);
	var path = "js/pages/"+lang+"/"+name+".json";
	
	$.getJSON(path,function (resp){
		//console.log(resp);
		$.each(resp,function(title,val){
			console.log(title+"="+val);
			$('#'+title).html(val);
		});
	});
	
	
}

pages.lang  = function(lang){
	
	if (typeof(Storage) !== "undefined") {
		if(lang!==undefined){
			localStorage.lang = lang;
			console.log("set lang ="+localStorage.lang);
		}else{

			if(localStorage.lang=="undefined") {
				localStorage.lang='en' ;//defautl language
				console.log("set default language");
			}
			console.log("get lang ="+localStorage.lang);
			return localStorage.lang;
		}
	} else {
    // Sorry! No Web Storage support..
	}
}