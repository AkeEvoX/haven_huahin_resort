var reserve = {};

reserve.get_confirmation = function(){

	var reserve_id = utility.querystr("reserve_id");
	$('#reserve_id').html(reserve_id);

	var endpoint = "services/reserve.php";
	var method = "get";
	var args = {"key":reserve_id,"_":new Date().getMilliseconds()};

	utility.service(endpoint,method,args,function(result){

		console.log(result);
		var money_pattern = /(\d)(?=(\d\d\d)+(?!\d))/g;   //format money
		var summary_price = 0;
		if(result.data.customer==null){
			alert('Sorry!! Not Found Information Reserve.');
			window.location="cancel_confirm.html";
			return false;
		}

		$('#customer_email').html(result.data.customer.email);

		var date = moment(result.data.info.date_start).add('days',14).format('DD/MM/YYYY');
		console.log(date + " || " + result.data.info.date_start);
		var cancel_date = utility.date_format_th(date);
		$('#cancel_date').html(cancel_date);
		
		
		//define information
		if(result.data != undefined){
			
			reserve.rooms = result.data.rooms;
			reserve.options = result.data.options;
			reserve.summary = result.data.summary;
		}
		
		//list room
		if(reserve.rooms != undefined ){
			
			console.warn("found cancel rooms.");
				$.each(reserve.rooms,function(i,val){
					summary_price += parseFloat(val.price);
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
				summary_price += parseFloat(val.price);
				price_option += parseFloat(val.price);

				var money = parseFloat(val.price).toFixed(2).replace(money_pattern,"$1,");	

				item += "<div class='row'>";
				item += "<div class='col-md-2'>&nbsp;</div>";
				item += "<div class='col-md-7'>"+val.title+"</div>";
				item += "<div class='col-md-3 text-right'>฿ "+money+"</div>";
				item += "</div>";
			});
			//summary options
			var money = parseFloat(price_option).toFixed(2).replace(money_pattern,"$1,");	
			var summary_tag = "<div class='row'>";
			summary_tag += "<div class='col-md-2'>ทางเลือก</div>";
		  	summary_tag += "<div class='col-md-7'>&nbsp;</div>";
		   	summary_tag += "<div class='col-md-3 text-right'><h4>฿ "+money+"</h4></div>";
			summary_tag += "</div>";
			item = summary_tag + item + "<hr/>";

			$('#list_reserve').append(item);
		}

		//summary price

		var total = parseFloat(summary_price).toFixed(2).replace(money_pattern,"$1,");
		var service_price = parseFloat(summary_price) * .10;
		var tax_price = parseFloat(summary_price) * .07 ;
		var net_price = parseFloat(summary_price) + tax_price + service_price;
		console.log(net_price);
		var service = parseFloat(service_price).toFixed(2).replace(money_pattern,"$1,");
		var tax = parseFloat(tax_price).toFixed(2).replace(money_pattern,"$1,");
		var net = parseFloat(net_price).toFixed(2).replace(money_pattern,"$1,");
		
		var	item = "<div class='row'>";
		item += "<div class='col-md-3 '><h4>รวม</h4></div>";
		item += "<div class='col-md-offset-6 col-md-3 text-right'><span><h4>$ "+total+"</h4></span></div>";
		item += "</div>";
		
			
		item += "<div class='row'>";
		item += "<div class='col-md-offset-2 col-md-3 '><h4>ค่าบริการ</h4></div>";
		item += "<div class='col-md-offset-4 col-md-3 text-right'><span><h4>$ "+service+"</h4></span></div>";
		item += "</div>";
		
		item += "<div class='row'>";
		item += "<div class='col-md-offset-2 col-md-3 '><h4>ภาษีของรัฐ</h4></div>";
		item += "<div class='col-md-offset-3 col-md-4 text-right'><span><h4>$ "+tax+"</h4></span></div>";
		item += "</div>";
		
		item += "<div class='row rowspan'>";
		item += "<div class='col-md-offset-2 col-md-7'><pre><h4>รวมเป็นเงินที่ต้องชำระให้กับโรงแรมทั้งสิ้น</h4></pre></div>";
		item += "<div class='col-md-3 text-right'><pre><h4>฿ "+net+"</h4></pre></div>";
		item += "</div>";


		$('#list_reserve').append(item);
		

	});

};


reserve.get_summary = function(){
	var endpoint = "services/info.php";
	var method="get";
	var args = {"_":new Date().getMilliseconds()};

	utility.service(endpoint,method,args,function(result){
		
		console.log(result);
		var money_pattern = /(\d)(?=(\d\d\d)+(?!\d))/g;   //format money
		var summary_price = 0;
		//set rooms info;
		reserve.info = result.data.info;

		if(result.data.info==null) { 
			alert('Sorry!! Not Found Information Reserve.');
			window.location="quick_reservation.html";
		}

		if(result.data.info){
			
			var info = result.data.info;
			$('#checkpoint_date').html(info.date_start);
			$('#travle_date').html(info.date_start);

			$('#date_start').html(utility.date_format_th(info.date_start));
			$('#date_end').html(utility.date_format_th(info.date_end));

			var rent ="";
			rent = "ผู้ใหญ่ "+ info.adults + " ท่าน ";
			rent += "เด็ก "+ info.children +" ท่าน"
			$('#rent_amount').html(rent);

			$('#comment').text(reserve.info.comment);
		}

		if(result.data.reserve != undefined){
			
			reserve.rooms = result.data.reserve.rooms;
			reserve.options = result.data.reserve.options;
			reserve.summary = result.data.reserve.summary;
		}
		
		//list room
		if(reserve.rooms != undefined ){
			console.warn("found cancel rooms.");
				$.each(reserve.rooms,function(i,val){
					summary_price += parseFloat(val.price);
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
				summary_price += parseFloat(val.price);
				price_option += parseFloat(val.price);

				var money = parseFloat(val.price).toFixed(2).replace(money_pattern,"$1,");	

				item += "<div class='row'>";
				item += "<div class='col-md-2'>&nbsp;</div>";
				item += "<div class='col-md-7'>"+val.title+"</div>";
				item += "<div class='col-md-3 text-right'>฿ "+money+"</div>";
				item += "</div>";
			})
			//summary options
			var money = parseFloat(price_option).toFixed(2).replace(money_pattern,"$1,");	
			var summary_tag = "<div class='row'>";
			summary_tag += "<div class='col-md-2'>ทางเลือก</div>";
		  	summary_tag += "<div class='col-md-7'>&nbsp;</div>";
		   	summary_tag += "<div class='col-md-3 text-right'><h4>฿ "+money+"</h4></div>";
			summary_tag += "</div>";
			item = summary_tag + item + "<hr/>";

			$('#list_reserve').append(item);
		}

		//summary price

		var total = parseFloat(summary_price).toFixed(2).replace(money_pattern,"$1,");
		
		
		var	item = "<div class='row'>";
		item += "<div class='col-md-2 '><h4>รวม</h4></div>";
		item += "<div class='col-md-offset-7 col-md-3 text-right'><span><h4>$ "+total+"</h4></span></div>";
		item += "</div>";
	
		
		item += "<div class='row rowspan'>";
		item += "<div class='col-md-offset-2 col-md-7'><pre><h4>รวมเป็นเงินที่ต้องชำระให้กับโรงแรมทั้งสิ้น</h4></pre></div>";
		item += "<div class='col-md-3 text-right'><pre><h4>฿ "+net+"</h4></pre></div>";
		item += "</div>";


		$('#list_reserve').append(item);

	});
}