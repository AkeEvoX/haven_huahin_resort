var page = { };
var page_console = '#panel_page';
var modal_control = '#modaldialog';
var modal_content = '#modalcontent';
var modal_title = '#modaltitle';
page.redirect = function(url,callback){
	$(page_console).load(url,callback);
}

page.show_modal = function(url,title){
	
	if(url!=""){
		$(modal_content).load(url);
	}
	$(modal_title).html(title);
	$(modal_control).modal();
}

page.hide_modal = function(){
	$(modal_control).modal('hide');
}

page.save = function(page,datas){
	
	$.post(page,$('#'+datas).serialize(),function(resp){
		console.log("Save Success");
		console.log(resp);
	});
	
	//page.hide_modal();
}