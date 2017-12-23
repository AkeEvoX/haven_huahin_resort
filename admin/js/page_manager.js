var page = { };
var page_console = '#panel_page';
var modal_control = '#modaldialog';
var modal_content = '#modalcontent';
var modal_title = '#modaltitle';
var cache_callback = null;

$.ajaxSetup({
		global: false,
		processData: false,
		contentType:false,
		cache: false
});

page.complete = function(callback){
	try{
		if(callback==undefined && cache_callback != undefined){
			if(cache_callback!=null){			
				cache_callback();
				console.warn("event page complete.");
				cache_callback = null;
			}
		}
		else{
			cache_callback = callback;
		}
	}
	catch(e){
		cache_callback = null;
		console.error("page complete is error.");
	}

}

page.redirect = function(url,callback){
	$(page_console).load(url,function(){
		
		//load data
		page.data_reload();
	});
}

page.show_modal = function(url,title,callback){
	
	if(url!=""){
		$(modal_content).load(url,callback);
	}
	$(modal_title).html(title);
	$(modal_control).modal();
}

page.hide_modal = function(){
	$(modal_control).modal('hide');
}

page.save = function(source,form){
	
	var data = new FormData($('#'+form)[0]);
	$.post(source,data,function(resp){
		
		console.log("Save Success");
		console.log(resp);
		alert("save complete");
		page.data_reload();
		page.hide_modal();
	});	
}

page.modify = function(obj){
	var id = $(obj).attr("data-id");
	var _item = $(obj).attr("data-item");
	var _page = $(obj).attr("data-page");
	var _title = $(obj).attr("data-title");
	var data = new FormData($(this)[0]);

	data.append("id",id);
	page.show_modal(_page,_title,function(){
		$.post(_item,data,function(resp){
			console.warn(resp);
			$.each(resp.result,function(name,data){
				assign_value(name,data);
			});
			page.complete();
		},"JSON");
	});
}

page.remove = function(obj){
	
	var id = $(obj).attr("data-id");
	var _item = $(obj).attr("data-item");
	var _page = $(obj).attr("data-page");
	var _title = $(obj).attr("data-title");
	
	var data = new FormData($(this)[0]);
	data.append("id",id);
	//getJSON
	$.post(_item,data,function(resp){
		console.warn(resp);
		page.show_modal(_page,_title,function(){
			$.each(resp.result,function(name,data){
				$('#'+name).val(data);
			});
		});
	},"JSON");
}

page.data_reload = function(){
	
	var data = $('#data_loader');
	var datasource =  data.attr('datasource');
	console.log("source data = " + datasource);
	
	$.getJSON(datasource,function(resp){
		data.html(resp.result);
		page.complete();
		
		//default initial datable
		try{
			if ( $.fn.dataTable.isDataTable('#data_loader') ){
				console.log("exist DataTable")
				table.destroy();
			}
			
			table = $('#data_loader').DataTable({"pagingType": "full_numbers"});
		}
		catch(e){}
	});
}

page.load_menu = function(){

	var endpoint = "services/userinfo.php";
	$.post(endpoint,function(resp){
		
		console.warn(resp.result.role);
		
		switch(resp.result.role){
			case "1":
				$('#menu_list').load('menu_admin.html');
			break;
			case "2":
				$('#menu_list').load('menu_user.html');
			break;
			default :
				window.location='login.html';
				console.log('force logout');
			break;
		} 
		page.redirect('item_booking.html');
		
	},"JSON");
}

function assign_value(objName,value){

	var obj = $('#'+objName);
	
	switch (obj.prop("type")) {
		case "hidden":
		case "text" :
			obj.val(value);
		break;
		case "textarea":
			obj.summernote('code',value);
		case "checkbox" :
		case "radio" :
			obj.prop("checked", value==1 ? true : false )
		break;
		case "select-one" : 
			obj.val(value).change();
			obj.attr('data-selected',value);
		break;
		default:
			//other input
			var tag = obj.prop('tagName');
			switch(tag){
				case "TABLE":
					//do strub
					obj.html(value);
				break;
				case "A":
					obj.prop('href',value);
				break;
				case "IMG":
					obj.attr('src',value);
				break;
				case "DIV":
					obj.html(value);
				break;
			}
		break;
	}

}
