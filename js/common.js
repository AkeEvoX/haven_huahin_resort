



function adult_increase(){

	var value = $('#adult_amount').val();
	console.log('adult increase :'+value);
	if(value!=''){
		value = parseInt(value)+1;
		if(value>15) return false;
		$('#adult_amount').val(value);
	}
	$('input[name="option_diner"]:checked').trigger('click');
}

function adult_decrease(){
	var value = $('#adult_amount').val();
	console.log('adult decrease :'+value);
	if(value!=''){
		value = parseInt(value)-1;

		if(value==0) return false;
		$('#adult_amount').val(value);
	}
	$('input[name="option_diner"]:checked').trigger('click');
}

function child_increase(obj){

	var value = $('#'+obj).val();
	console.log('child increase :'+value);
	if(value!=''){
		value = parseInt(value)+1;
		if(value>2) return false;
		$('#'+obj).val(value);
	}

	//.trigger('click');
	$('input[name="option_diner"]:checked').trigger('click');
}

function child_decrease(obj){
	var value = $('#'+obj).val();
	console.log('child decrease :'+value);
	if(value!=''){
		value = parseInt(value)-1;

		if(value<0) return false;
		$('#'+obj).val(value);
	}
	//$('input[name="option_diner"]').attr('checked',false);
	$('input[name="option_diner"]:checked').trigger('click');
}