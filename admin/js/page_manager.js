var page = { };
var page_console = '#panel_page';
var modal_control = '#modaldialog';
var modal_content = '#modalcontent';
var modal_title = '#modaltitle';

$.ajaxSetup({
		global: false,
		processData: false,
		contentType:false,
		cache: false
});

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
			$.each(resp.result,function(name,data){
				assign_value(name,data);
			});
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
	});
}

function assign_value(objName,value){

	var obj = $('#'+objName);
	
	switch (obj.prop("type")) {
		case "textarea":
		case "text" :
			obj.val(value);
		break;
		case "checkbox" :
		case "radio" :
			obj.prop("checked", value==1 ? true : false )
		break;
		case "select-one" : 
			obj.val(value).change();
		break;
	}

}
