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
				item += val.room +" <small class='pull-right' style='cursor:pointer;' onclick=del_room("+val.key+") >นำออก</small> ";
				item += val.type + "</h4></span>";

				$('#list_reserve').append(item);
				
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
		var summery_price = 0;
		//set rooms info;
		reserve.info = result.data.info;
		if(result.data.reserve != undefined){
			
			reserve.rooms = result.data.reserve.rooms;
			reserve.options = result.data.reserve.options;
			reserve.summery = result.data.reserve.summery;
		}
		
		//list room
		if(reserve.rooms != undefined ){
				$.each(reserve.rooms,function(i,val){
					summery_price += parseFloat(val.price);
					var money = parseFloat(val.price).toFixed(2).replace(money_pattern,"$1,");
					
					var item = "<div class='row'>";
					 item += "<div class='col-md-2'><label>ห้องพัก "+(i+1)+"</label><br><small>ผู้ใหญ่ 1</small></div>";
					 item += "<div class='col-md-7'>";
					 item += val.room+"</br>";
					 item += val.type+"</br>";
					 item += "</div >";
					 item += "<div class='col-md-3 text-right'><h4>฿ "+money+"</h4></div>";
					 item += "</div ><hr/>";
					 
					$('#list_reserve').append(item);
					
				});
		}
		//list option
		if(reserve.options != undefined){

			var item = "";
			var price_option = 0;
			$.each(reserve.options,function(i,val){
				summery_price += parseFloat(val.price);
				price_option += val.price;

				var money = parseFloat(val.price).toFixed(2).replace(money_pattern,"$1,");	

				item += "<div class='row'>";
				item += "<div class='col-md-2'>&nbsp;</div>";
				item += "<div class='col-md-7'>"+val.title+"</div>";
				item += "<div class='col-md-3 text-right'>฿ "+money+"</div>";
				item += "</div>";
			})
			//summery options
			var money = parseFloat(price_option).toFixed(2).replace(money_pattern,"$1,");	
			var summery_tag = "<div class='row'>";
			summery_tag += "<div class='col-md-2'>ทางเลือก</div>";
		  	summery_tag += "<div class='col-md-7'>&nbsp;</div>";
		   	summery_tag += "<div class='col-md-3 text-right'><h4>฿ "+money+"</h4></div>";
			summery_tag += "</div>";
			item = summery_tag + item + "<hr/>";

			$('#list_reserve').append(item);
		}

		//summery price

		var total = parseFloat(summery_price).toFixed(2).replace(money_pattern,"$1,");
		var service_price = parseFloat(summery_price) * .03;
		var tax_price = parseFloat(summery_price) * .07 ;
		var net_price = parseFloat(summery_price) + tax_price + service_price;
		console.log("net : "+net_price);
		var service = parseFloat(service_price).toFixed(2).replace(money_pattern,"$1,");	
		var tax = parseFloat(tax_price).toFixed(2).replace(money_pattern,"$1,");	
		var net = parseFloat(net_price).toFixed(2).replace(money_pattern,"$1,");	
		var	item = "<div class='row'>";
		item += "<div class='col-md-2 '><h4>รวม</h4></div>";
		item += "<div class='col-md-offset-7 col-md-3 text-right'><span><h4>$ "+total+"</h4></span></div>";
		item += "</div>";
		item += "<div class='row rowspan'>";
		item += "<div class='col-md-offset-2 col-md-7'>ไม่รวมไว้ : ค่าบริการ 3%</div>";
		item += "<div class='col-md-3 text-right'>฿ "+service+"</div>";
		item += "</div>";
		item += "<div class='row rowspan'>";
		item += "<div class='col-md-offset-2 col-md-7'>ไม่รวมไว้ : ภาษีของรัฐ 7%</div>";
		item += "<div class='col-md-3 text-right'>฿ "+tax+"</div>";
		item += "</div>";
		item += "<div class='row rowspan'>";
		item += "<div class='col-md-offset-2 col-md-7'><pre >ภาษีที่ยังไม่ได้รวมอยู่ด้วยนี้จะต้องทำการชำระให้กับ</br>โรงแรง เป็นจำนวนเงิน</pre></div>";
		item += "<div class='col-md-3 text-right'><pre><h4>฿ "+net+"</h4></pre></div>";
		item += "</div>";


		$('#list_reserve').append(item);

/*
			<div class='row'>
				<div class='col-md-2 '><h4>รวม</h4></div>
				<div class='col-md-offset-7 col-md-3 text-right'><span><h4>$ 7,113.70</h4></span></div>
			</div>
			<div class='row rowspan'>
				<div class='col-md-offset-2 col-md-7'>ไม่รวมไว้ : ค่าบริการ</div>
				<div class='col-md-3 text-right'>฿ 288.87</div>
			</div>
			<div class='row rowspan'>
				<div class='col-md-offset-2 col-md-7'>ไม่รวมไว้ : ภาษีของรัฐ</div>
				<div class='col-md-3 text-right'>฿ 222.45</div>
			</div>
			<div class='row rowspan'>
				<!-- <div class='col-md-2'>&nbsp;</div> -->
				<div class='col-md-offset-2 col-md-7'><pre >ภาษีที่ยังไม่ได้รวมอยู่ด้วยนี้จะต้องทำการชำระให้กับ</br>โรงแรง เป็นจำนวนเงิน</pre></div>
				<div class='col-md-3 text-right'><pre><h4>฿ 7,625</h4></pre></div>
			</div>
*/
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
			$('#list_reserve').append(item);
	
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