<?php 
function getinboundLinks($domain_name) {
	$urls = array();
	if(is_url_exist($domain_name.'/sitemap.xml')) {

		$DomDocument = new DOMDocument();
		$DomDocument->preserveWhiteSpace = false;
		$DomDocument->load($domain_name.'/sitemap.xml');
		$DomNodeList = $DomDocument->getElementsByTagName('loc');

		foreach($DomNodeList as $url) {
		    $urls[] = $url->nodeValue;
		}
		return $urls;
	}else{
	 	$url = $domain_name;
		$url_without_www = str_replace('https://','',$url);
		$url_without_www = str_replace('http://','',$url_without_www);
		$url_without_www = str_replace('www.','',$url_without_www);
	 	$url_without_www = str_replace(strstr($url_without_www,'/'),'',$url_without_www);
		$url_without_www = trim($url_without_www);
		$input = @file_get_contents($url);

	 	$regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
		$inbound = 0;
		$outbound = 0;
		$nonfollow = 0;
		$inbound_links = [];
		if(preg_match_all("/$regexp/siU", $input, $matches, PREG_SET_ORDER)) {
			foreach($matches as $match) {
				if(!empty($match[2]) && !empty($match[3])) {
					if(strstr(strtolower($match[2]),'URL:') || strstr(strtolower($match[2]),'url:') ) {
						$nonfollow +=1;
					} else if (strstr(strtolower($match[2]),$url_without_www) || !strstr(strtolower($match[2]),'http://')) {
				     	$inbound += 1;

				     	$inbound_links[] = $match[2];
				 	}
					else if (!strstr(strtolower($match[2]),$url_without_www) && strstr(strtolower($match[2]),'http://')) {
				     	$outbound += 1;
				    }
				}
			}
		}
		if(!empty($inbound_links)){
			foreach ($inbound_links as $key => $value) {
			 	echo $url = $value;
				$url_without_www = str_replace('https://','',$url);
				$url_without_www = str_replace('http://','',$url_without_www);
				$url_without_www = str_replace('www.','',$url_without_www);
			 	$url_without_www = str_replace(strstr($url_without_www,'/'),'',$url_without_www);
				$url_without_www = trim($url_without_www);
				echo $inputs = @file_get_contents($url);
				die();
				$links = [];
			 	$regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
				$inbound = 0;
				$outbound = 0;
				$nonfollow = 0;
				if($inputs != ''){
				
					if(preg_match_all("/$regexp/siU", $inputs, $matchesdata, PREG_SET_ORDER)) {
						foreach($matchesdata as $match) {
							if(!empty($match[2]) && !empty($match[3])) {
								if(strstr(strtolower($match[2]),'URL:') || strstr(strtolower($match[2]),'url:') ) {
									$nonfollow +=1;
								} else if (strstr(strtolower($match[2]),$url_without_www) || !strstr(strtolower($match[2]),'http://')) {
							     	$inbound += 1;
							     	if($value != $match[2]){
							     		$links[] = $match[2];
							     	}
							 	}
								else if (!strstr(strtolower($match[2]),$url_without_www) && strstr(strtolower($match[2]),'http://')) {
							     	$outbound += 1;
							    }
							}
						}
					}
				}

			}
		}
		return $links;
	}
}

function get_word_count($url){
	$data =  getUrlmetaData($url);
	$tags = $data['title'].' '. $data['metaTags']['description']['value'];
	$meta_count = str_word_count($tags);
	
	$str = file_get_contents($url);
	$str =  preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $str);
	$str =  preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', "", $str);
	$str = striptags($str);
	$words = explode(' ', $str.' '.$tags);
	
	$unique_count = count(array_unique($words));
	$response['all_words'] = str_word_count($str) + $meta_count	;
	$response['unique_count'] = $unique_count;
	return $response;
	
}

function getUrlmetaData($url) {
	$result = false;

	$contents = getUrlContents($url);

	if (isset($contents) && is_string($contents)) {
	    $title = null;
	    $metaTags = null;

	    preg_match('/<title>([^>]*)<\/title>/si', $contents, $match);

	    if (isset($match) && is_array($match) && count($match) > 0) {
	        $title = strip_tags($match[1]);
	    }

	    preg_match_all('/<[\s]*meta[\s]*name="?' . '([^>"]*)"?[\s]*' . 'content="?([^>"]*)"?[\s]*[\/]?[\s]*>/si', $contents, $match);

	    if (isset($match) && is_array($match) && count($match) == 3) {
	        $originals = $match[0];
	        $names = $match[1];
	        $values = $match[2];

	        if (count($originals) == count($names) && count($names) == count($values)) {
	            $metaTags = array();

	            for ($i = 0, $limiti = count($names); $i < $limiti; $i++) {
	                $metaTags[$names[$i]] = array(
	                    'html' => htmlentities($originals[$i]),
	                    'value' => $values[$i]
	                );
	            }
	        }
	    }

	    $result = array(
	        'title' => $title,
	        'metaTags' => $metaTags
	    );
	}
	return $result;
}


function getUrlContents($url, $maximumRedirections = null, $currentRedirection = 0) {
	$result = false;
	$contents = @file_get_contents($url);

	// Check if we need to go somewhere else

	if (isset($contents) && is_string($contents)) {
	    preg_match_all('/<[\s]*meta[\s]*http-equiv="?REFRESH"?' . '[\s]*content="?[0-9]*;[\s]*URL[\s]*=[\s]*([^>"]*)"?' . '[\s]*[\/]?[\s]*>/si', $contents, $match);

	    if (isset($match) && is_array($match) && count($match) == 2 && count($match[1]) == 1) {
	        if (!isset($maximumRedirections) || $currentRedirection < $maximumRedirections) {
	            return getUrlContents($match[1][0], $maximumRedirections, ++$currentRedirection);
	        }

	        $result = false;
	    } else {
	        $result = $contents;
	    }
	}

	return $contents;
}

function is_url_exist($url){
    $ch = curl_init($url);    
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if($code == 200){
       $status = true;
    }else{
      $status = false;
    }
    curl_close($ch);
   return $status;
}

function striptags ($str) {
 return trim(strip_tags(str_replace('<', ' <', $str)));
}

?>