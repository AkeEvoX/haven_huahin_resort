var reserve = {};
reserve.info = [];
reserve.rooms = [];
reserve.options =  [];
reserve.summery = null;
//genaral.get_info();


//list_reserve

function add_room(name,type,price){
	
	var list = $('#list_reserve');
	var item = "";
	var key = moment().format("YYYYMMDDHHMMSS");
	console.log("add room >  " + key + "|" + name + "|"+type+"|"+price);
	var room = { "key" : key , "room":name , "type" : type , "price" : price }; 
	reserve.rooms.push(room); 
	
	var money_pattern = /(\d)(?=(\d\d\d)+(?!\d))/g;   //formoney
	var money = parseFloat(price).toFixed(2).replace(money_pattern,"$1,")
	
	item += "<span id='"+key+"'  href='#' class='list-group-item'>";
	item += "<h4 class='list-group-item-heading'>"+ name +" <span class='pull-right'>฿ "+money+"</span></h4>";
	item += "<p class='list-group-item-text'>" + type;
	item += "<span class='pull-right' style='cursor:pointer;' onclick=del_room("+key+")>นำออก</span></p>";
	item += "</span>";
	list.append(item);
	recalculate();
}

function del_room(key){
	
	$('#'+key).remove();
	reserve.rooms = jQuery.grep(reserve.rooms,function(item,index){ return item.key != key });
	recalculate();
}

function recalculate() {
	var view_total = $('#total_price');
	var total = 0.0;
	var money = 0.0;
	//var total_pattern = /[^0-9.-]+/g; //format calculate
	var money_pattern = /(\d)(?=(\d\d\d)+(?!\d))/g;   //formoney
	$.each(reserve.rooms,function(i,val){
		
		if(val.price != "")
			total += parseFloat(val.price);
		//total += parseFloat(val.price.replace(total_pattern,''));
		console.debug("price : " + total);
		
	});
	money = total.toFixed(2).replace(money_pattern,"$1,");
	view_total.html("฿ "+money);
	reserve.summery = {"total_amount":total.toFixed(2)};
	//$('#data_reserve').val(JSON.stringify(reserve.source));
	$('#data_reserve').val(JSON.stringify(reserve));
}

reserve.get_info = function(){
	var endpoint = "services/info.php";
	var method = "get";
	var args = {"_":new Date().getMilliseconds()};

	utility.service(endpoint,method,args,function(result){
		
		console.log(result);
		var money_pattern = /(\d)(?=(\d\d\d)+(?!\d))/g;   //format money
		
		//set rooms info;
		reserve.info = result.data.info;
		if(result.data.reserve != undefined){
			
			reserve.rooms = result.data.reserve.rooms;
			reserve.summery = result.data.reserve.summery;
		}
		
		if(reserve.summery!=undefined){

			var total =parseFloat(reserve.summery.total_amount).toFixed(2).replace(money_pattern,"$1,");
			$('#total_price').html("฿ " + total);
			$('#total_room').html(reserve.rooms.length +"ห้องพัก " + reserve.info.night + "คืน");
		}
		
		if(reserve.rooms != undefined ){
			
			$.each(reserve.rooms,function(i,val){
				
				//console.warn(val);
				var money = parseFloat(val.price).toFixed(2).replace(money_pattern,"$1,");
				
				var item = "<span id='"+val.key+"' href='#' class='list-group-item'>";
				item += "<h4 class='list-group-item-heading'>ห้องพัก "+(i+1)+"<span class='pull-right'>฿ "+money+"</span></h4>";
				item += "<h4 class='list-group-item-text '>";
				item += val.room +" <small class='pull-right'><a href='#'  >นำออก</a></small> ";
				item += val.type + "</h4></span>";

				$('#reserve_info').append(item);
				
			});
		}
	});
}

reserve.get_summery = function(){
	var endpoint = "services/info.php";
	var method="get";
	var args = {"_":new Date().getMilliseconds()};

	utility.service(endpoint,method,args,function(result){
		
		console.log(result);
		var money_pattern = /(\d)(?=(\d\d\d)+(?!\d))/g;   //format money
		
		//set rooms info;
		reserve.info = result.data.info;
		if(result.data.reserve != undefined){
			
			reserve.rooms = result.data.reserve.rooms;
			reserve.summery = result.data.reserve.summery;
		}
	});
}

reserve.add_option = function(val){
	
	//var val = JSON.parse(data);

	var money_pattern = /(\d)(?=(\d\d\d)+(?!\d))/g;   //format money
	var money = parseFloat(val.price).toFixed(2).replace(money_pattern,"$1,");
	
	var item = "<span id='"+val.key+"' href='#' class='list-group-item'>";
			item += "<h4 class='list-group-item-heading'>"+val.title+"<span class='pull-right'>฿ "+money+"</span></h4>";
			item += "<h4 class='list-group-item-text '>";
			item += "&nbsp;<small class='pull-right'>";//<a href='javascript:void(0)' onclick=reserve.del_option('"+val.key+"','" + val.price+"');  >นำออก</a></small> 
			item += "</h4></span>";
			$('#reserve_info').append(item);
	
	reserve.options.push(val);
	
	var price = parseFloat(val.price);
	var total = parseFloat(reserve.summery.total_amount ) + price;
	var money_pattern = /(\d)(?=(\d\d\d)+(?!\d))/g;   //format money
	
	reserve.summery.total_amount = total;
	total = parseFloat(total).toFixed(2).replace(money_pattern,"$1,");
	$('#total_price').html("฿ " + total);
	
	
	//reserve.calucate_option();
}

reserve.del_option = function(key,price){
	
	//var val = JSON.parse(data);
	$('#'+key).remove();
	
	reserve.options = jQuery.grep(reserve.options,function(item,index){ return item.key != key });
	
	var price = parseFloat(price);
	var total = parseFloat(reserve.summery.total_amount) - price;
	var money_pattern = /(\d)(?=(\d\d\d)+(?!\d))/g;   //format money
	
	reserve.summery.total_amount = total;
	total = parseFloat(total).toFixed(2).replace(money_pattern,"$1,");
	$('#total_price').html("฿ " + total);
	//reserve.calucate_option();
	
}