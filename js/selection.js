var room_list = {};
room_list.source = [];

//list_reserve

function add_room(room,type,price){
	
	var list = $('#list_reserve');
	var item = "";
	var key = moment().format("YYYYMMDDHHMMSS");
	console.log("add room >  " + key + "|" + room + "|"+type+"|"+price);
	var data = { "key" : key , "room":room , "type" : type , "price" : price }; 
	room_list.source.push(data); 
	
	item += "<span id='"+key+"'  href='#' class='list-group-item'>";
	item += "<h4 class='list-group-item-heading'>"+ room +" <span class='pull-right'>"+price+"</span></h4>";
	item += "<p class='list-group-item-text'>" + type;
	item += "<span class='pull-right' style='cursor:pointer;' onclick=del_room("+key+")>นำออก</span></p>";
	item += "</span>";
	list.append(item);
	recalculate();
}


function del_room(key){
	
	$('#'+key).remove();
	room_list.source = jQuery.grep(room_list.source,function(item,index){ return item.key != key });
	recalculate();
}

function recalculate(){
	var view_total = $('#total_price');
	var total = 0.0;
	var money = 0.0;
	var total_pattern = /[^0-9.-]+/g; //format calculate
	var money_pattern = /(\d)(?=(\d\d\d)+(?!\d))/g;   //formoney
	$.each(room_list.source,function(i,val){
		
		if(val.price != "")
			total += parseFloat(val.price.replace(total_pattern,''));
		console.debug("price : " + total);
		
	});
	money = total.toFixed(2).replace(money_pattern,"$1,");
	view_total.html("฿ "+money);
	
	$('#data_reserve').val(JSON.stringify(room_list.source));
}

room_list.get_info = function(){
	var endpoint = "services/info.php";
	var method = "get";
	var args = {"_":new Date().getMilliseconds()};

	utility.service(endpoint,method,args,function(data){
		console.log(data);
	});
}
