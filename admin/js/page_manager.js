var page = { };
var page_console = '#panel_page';
var modal_control = '#modaldialog';
var modal_content = '#modalcontent';
var modal_title = '#modaltitle';

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

page.save = function(source,datas){
	
	$.post(source,$('#'+datas).serialize(),function(resp){
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
	//var dialog = $(obj).attr("");
	//"services/room_service.php?type=item"
	$.getJSON(_item,{"id":id},function(resp){
		page.show_modal(_page,_title,function(){
			$.each(resp.result,function(name,data){
				$('#'+name).val(data);
			});
		});
	});
	
}

page.remove = function(obj){
	
	var id = $(obj).attr("data-id");
	var _item = $(obj).attr("data-item");
	var _page = $(obj).attr("data-page");
	var _title = $(obj).attr("data-title");
	
	//console.log("delete id ="+id + ",source="+source);
	
	$.getJSON(_item,{"id":id},function(resp){
		page.show_modal(_page,_title,function(){
			$.each(resp.result,function(name,data){
				$('#'+name).val(data);
			});
		});
	});
}

page.data_reload = function(){
	
	var data = $('#data_loader');
	var datasource =  data.attr('datasource');
	console.log("source data = " + datasource);
	
	$.getJSON(datasource,function(resp){
		data.html(resp.result);
	});
}
