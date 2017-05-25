var rooms = {};

rooms.filter_room = function(startdate,enddate){

	var start_date = "";
	var end_date = "";

	var endpoint = "services/rooms.php";
	var method="get";
	var args = {
		"_":new Date().getMilliseconds()
		,"service":"filter"
		,"startdate":startdate
		,"enddate":enddate
	};
	
	utility.service(endpoint,method,args,view_list_room);


};

function view_list_room(resp){
	var rooms = "";
	
	if(resp.data!=null){

		console.debug(resp.data);

		var templete_master = "";
		// var templete = "";
		templete_master = utility.get_templete("templete_room.html");
		//console.log(templete_master);
		
		// templete = templete_master.replace("{room_name}","deluxe");
		$.each(resp.data,function(index){
			//console.log(resp.data[room]);
			var room = resp.data[index];
			var templete = templete_master;
			templete = templete.replace("{room_name}",room.title);
			templete = templete.replace("{bed_list}",set_bed_list(room.beds,room.title));
			templete = templete.replace("{package_list}",set_package_list(room.packages,room.title));
			$('#list_room').append(templete);
		});

	}
}

function set_gallery_list(items,room_name){
	
	var obj_gallery = "gallery_"+room_name;
	var result = "<div id='"+obj_gallery+"' > ";
	
	$.each(items,function(index){
		var gallery = items[index];
		result +=  "<img src='"+val.image+"' data-image='"+val.image+"' onerror=this.src='images/common/unavaliable.jpg' data-description=''  />";
		result += "<option value='"+bed.id+"'>"+bed.title+"</option>";
	});

	result += "</div>";
	
	
	return result;
}

function set_bed_list(items,room_name){

	var result = "<select class='form-control' id='type_bed_"+room_name+"' name='type_bed_"+room_name+"' >";
	$.each(items,function(index){
		var bed = items[index];
		result += "<option value='"+bed.id+"'>"+bed.title+"</option>";
	});

	result += "</select>";
	return result;
}

function set_package_list(items,room_name){
	var result = "";

	if(items!=null){
		//console.log(items);
		$.each(items,function(index){
			var package = items[index];

			result+= "<span href='#' class='list-group-item'>";			
			result+= "<h4 class='list-group-item-heading'>"+package.title+"<span class='pull-right'>฿ "+package.price+"</span></h4>";		
			result+= "<small class='list-group-item-text'>";
			
			if(package.foodservice==1){
				result+= "<div class='col-sm-3'><span class='glyphicon glyphicon-remove'></span> รวมอาหารเช้า</div>";
			}
			else{
				result+= "<div class='col-sm-3'><span class='glyphicon glyphicon-remove'></span> ไม่รวมอาหารเช้า</div>";
			}
			
			if(package.cancel_room==1){
				result+= "<div class='col-sm-3' onclick='reserve.modal_change()'; style='cursor:pointer;'><span class='glyphicon glyphicon-ok'></span> สามารถเปลี่ยนแปลงหรือยกเลิกได้</div>";
			}
			else{
				result+= "<div class='col-sm-3' onclick='reserve.modal_change()'; style='cursor:pointer;'><span class='glyphicon glyphicon-ok'></span> ไม่สามารถเปลี่ยนแปลงหรือยกเลิกได้</div>";
			}
			
			if(package.payment_online==1){
				result+= "<div class='col-sm-3' onclick='reserve.modal_internet()'; style='cursor:pointer;'><span class='glyphicon glyphicon-credit-card'></span> ชำระเงินทางอินเตอร์เน็ต</div>";
			}
			else{
				result+= "<div class='col-sm-3' onclick='reserve.modal_internet()'; style='cursor:pointer;'><span class='glyphicon glyphicon-credit-card'></span> ไม่สามารถชำระเงินทางอินเตอร์เน็ต</div>";
			}
			
			result+= "<button onclick=add_room('"+package.id+"','helllo','"+package.title.replace(" ","%20")+"','"+package.price+"'); type='button' class='btn btn-default brown btn-sm col-sm-3'>เลือก</button>&nbsp;";
			result+= "</small></span>";
			
			console.log(package.title.replace(" ","_"));
			//add_room('"+package.id+"','"+room_name+"','"+package.title+"','"+package.price+"');
		});
	}
	//console.log(result);
	return result;
}

function test(){
	
	alert('hello');
}