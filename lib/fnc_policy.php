<?php

function get_payment_policy($package_name,$days,$lang){
	
	$result = '';
	
		if($days >= 14 and strpos(strtolower($package_name), 'internet') !== false ){
			
			if($lang=='en'){		
				
				$result .= '<p><b>Payment Policy</b><p/>';
				$result .='Make full payment online in secure systems or bank transfer.<br/>';
				$result .='Reservation will be canceled If do not receive payment 14 days before arrival.<br/>';
			}
			else{ /*  other language */
				$result .= '<p><b>Payment Policy</b><p/>';
				$result .='Make full payment online in secure systems or bank transfer.<br/>';
				$result .='Reservation will be canceled If do not receive payment 14 days before arrival.<br/>';
			}
			
		} else {
			
			if($lang=='en'){		
				$result .= '<p><b>Payment Policy</b><p/>';
				$result .='Make full payment online in secure systems or bank transfer.<br/>';
				$result .='Reservation will be completed upon receipt of full payment.<br/>';
			}
			else{  /*  other language */
				$result .= '<p><b>Payment Policy</b><p/>';
				$result .='Make full payment online in secure systems or bank transfer.<br/>';
				$result .='Reservation will be completed upon receipt of full payment.<br/>';
			}
			
		}
		
	return $result ;
}

function get_cancel_policy($package_name,$days,$lang){
	$result = '';
	
		if($days >= 14 and strpos(strtolower($package_name), 'internet') !== false ){
			
			if($lang=='en'){		
				$result = "<p><b>Cancellation Policy </b></p><br>";
				$result .= "This offer can be canceled or modified free of charge until {expire_date}, 00:00 (UTC).<br>";
				$result .= "In case of cancellation after this date, a penalty of 100% of full stay will apply.<br>";
				$result .= "In case of no-show, a penalty of 100% of full stay will apply.<br></small>";
			}
			else{ /*  other language */
				$result = "<p><b>Cancellation Policy </b></p><br>";
				$result .= "This offer can be canceled or modified free of charge until {expire_date}, 00:00 (UTC).<br>";
				$result .= "In case of cancellation after this date, a penalty of 100% of full stay will apply.<br>";
				$result .= "In case of no-show, a penalty of 100% of full stay will apply.<br></small>";
			}
			
		} else {
			
			if($lang=='en'){		
				$result .= '<p><b>Cancellation Policy</b><p/>';
				$result .= 'This offer cannot be change , amended , modified and Non refundable.<br> ';
				$result .= 'In case of no-show, a penalty of 100% of full stay will apply. <br>';
			}
			else{  /*  other language */
				$result .= '<p><b>Cancellation Policy</b><p/>';
				$result .= 'This offer cannot be change , amended , modified and Non refundable. <br>';
				$result .= 'In case of no-show, a penalty of 100% of full stay will apply. <br>';
			}
			
		}
		
	return $result ;
}

?>