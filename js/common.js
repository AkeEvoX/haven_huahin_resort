



function adult_increase(){

	var value = $('#adult_amount').val();
	console.log('adult increase :'+value);
	if(value!=''){
		value = parseInt(value)+1;
		if(value>15) return false;
		$('#adult_amount').val(value);
	}
}

function adult_decrease(){
	var value = $('#adult_amount').val();
	console.log('adult decrease :'+value);
	if(value!=''){
		value = parseInt(value)-1;

		if(value==0) return false;
		$('#adult_amount').val(value);
	}
}

function child_increase(){

	var value = $('#child_amount').val();
	console.log('child increase :'+value);
	if(value!=''){
		value = parseInt(value)+1;
		if(value>15) return false;
		$('#child_amount').val(value);
	}
}

function child_decrease(){
	var value = $('#child_amount').val();
	console.log('child decrease :'+value);
	if(value!=''){
		value = parseInt(value)-1;

		if(value<0) return false;
		$('#child_amount').val(value);
	}
}