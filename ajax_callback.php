<?php  
// ini_set("error_reporting", E_ALL);
require_once('common_function.php');
$response = [];


if(isset($_POST) && !empty($_POST)){

	$Domain = $_POST['url'];
	$links = getinboundLinks($Domain);

	if(!empty($links)){
		if(!empty($links)){
			$unique_array = array_unique($links);


			$searchword = 'mailto';
			$searchword2 = 'tel';
			$searchword3 = 'wp-login';
			$matches = array();

			foreach($unique_array as $k=>$v) {

				if (filter_var($v, FILTER_VALIDATE_URL)) {
					if(!preg_match("/\b$searchword\b/i", $v)){
						if(!preg_match("/\b$searchword2\b/i", $v)){
							if(!preg_match("/\b$searchword3\b/i", $v)){
								if($v != 'https://wordpress.org'){
									if($v != 'https://wordpress.org/'){
						        		$matches[$k] = $v;
									}
								}
					        }
					    }
					}
			    }else{

			    	if($v != 'javascript:void(0);'){
			    		if($v != '#'){
    				    	if(!preg_match("/\b$searchword\b/i", $v)){
								if(!preg_match("/\b$searchword2\b/i", $v)){


			    			 		$matches[$k] = str_replace($v, $Domain.'/'.$v, $v);
			    			 	}
		    			 	}
		    			}
			    	}
			    }
			}

						
			$html = '<table class="result_table" cellpadding="10"><tbody>';
			$total_words = 0;
			foreach ($matches as $key => $value) {
				$words = get_word_count($value);
				$total_words += $words['unique_count'];
				if($words['all_words'] > 0){	
					$html .='
						<tr>
							<td>'.$value.'</td>
							<td>'.$words['all_words'].'</td>
						</tr>';
				}
			}
			$html .='</tbody></table>';
			
			$response['success'] = true;
			$response['total_words'] = number_format($total_words);
			$response['data'] = $html;
			
		}
	}else{
		$response['success'] = false;
		$response['message'] = '<p class="error">We could not connect to your website, please check you enter a proper URL.</p>';
	}
	echo json_encode($response);

}



?>