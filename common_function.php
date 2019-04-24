<?php 

 // Set the output file name.
    define ("OUTPUT_FILE", "sitemap.xml");
    

    // Set the start URL. Example: define ("SITE", "https://www.example.com");
    define ("SITE", "");


    // Set true or false to define how the script is used.
    // true:  As CLI script.
    // false: As Website script.
    define ("CLI", true);


    // Define here the URLs to skip. All URLs that start with the defined URL 
    // will be skipped too.
    // Example: "https://www.example.com/print" will also skip
    //   https://www.example.com/print/bootmanager.html
    $skip_url = array (
                       SITE . "/print",
                       SITE . "/slide",
                      );
    

    // General information for search engines how often they should crawl the page.
    define ("FREQUENCY", "weekly");
    

    // General information for search engines. You have to modify the code to set
    // various priority values for different pages. Currently, the default behavior
    // is that all pages have the same priority.
    define ("PRIORITY", "0.5");


    // When your web server does not send the Content-Type header, then set
    // this to 'true'. But I don't suggest this.
    define ("IGNORE_EMPTY_CONTENT_TYPE", false);



/*************************************************************
    End of user defined settings.
*************************************************************/


function GetPage ($url)
{
    $ch = curl_init ($url);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_USERAGENT, AGENT);

    $data = curl_exec($ch);

    curl_close($ch);

    return $data;
}

function GetQuotedUrl ($str)
{
    $quote = substr ($str, 0, 1);
    if (($quote != "\"") && ($quote != "'")) // Only process a string 
    {                                        // starting with singe or
        return $str;                         // double quotes
    }                                                 

    $ret = "";
    $len = strlen ($str);    
    for ($i = 1; $i < $len; $i++) // Start with 1 to skip first quote
    {
        $ch = substr ($str, $i, 1);
        
        if ($ch == $quote) break; // End quote reached

        $ret .= $ch;
    }
    
    return $ret;
}

function GetHREFValue ($anchor)
{
    $split1  = explode ("href=", $anchor);
    $split2 = explode (">", $split1[1]);
    $href_string = $split2[0];

    $first_ch = substr ($href_string, 0, 1);
    if ($first_ch == "\"" || $first_ch == "'")
    {
        $url = GetQuotedUrl ($href_string);
    }
    else
    {
        $spaces_split = explode (" ", $href_string);
        $url          = $spaces_split[0];
    }
    return $url;
}

function GetEffectiveURL ($url)
{
    // Create a curl handle
    $ch = curl_init ($url);

    // Send HTTP request and follow redirections
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_USERAGENT, AGENT);
    curl_exec($ch);

    // Get the last effective URL
    $effective_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    // ie. "http://example.com/show_location.php?loc=M%C3%BCnchen"

    // Decode the URL, uncoment it an use the variable if needed
    // $effective_url_decoded = curl_unescape($ch, $effective_url);
    // "http://example.com/show_location.php?loc=München"

    // Close the handle
    curl_close($ch);

    return $effective_url;
}

function ValidateURL ($url_base, $url)
{
    global $scanned;
        
    $parsed_url = parse_url ($url);
        
    $scheme = $parsed_url["scheme"];
        
    // Skip URL if different scheme or not relative URL (skips also mailto)
    if (($scheme != SITE_SCHEME) && ($scheme != "")) return false;
        
    $host = $parsed_url["host"];
                
    // Skip URL if different host
    if (($host != SITE_HOST) && ($host != "")) return false;
    
    // Check for page anchor in url
    if ($page_anchor_pos = strpos ($url, "#"))
    {
        // Cut off page anchor
        $url = substr ($url, 0, $page_anchor_pos);
    }
        
    if ($host == "")    // Handle URLs without host value
    {
        if (substr ($url, 0, 1) == '/') // Handle absolute URL
        {
            $url = SITE_SCHEME . "://" . SITE_HOST . $url;
        }
        else // Handle relative URL
        {
            $path = parse_url ($url_base, PHP_URL_PATH);
            
            if (substr ($path, -1) == '/') // URL is a directory
            {
                // Construct full URL
                $url = SITE_SCHEME . "://" . SITE_HOST . $path . $url;
            }
            else // URL is a file
            {
                $dirname = dirname ($path);

                // Add slashes if needed
                if ($dirname[0] != '/')
                {
                    $dirname = "/$dirname";
                }
    
                if (substr ($dirname, -1) != '/')
                {
                    $dirname = "$dirname/";
                }

                // Construct full URL
                $url = SITE_SCHEME . "://" . SITE_HOST . $dirname . $url;
            }
        }
    }

    // Get effective URL, follow redirected URL
    $url = GetEffectiveURL ($url); 

    // Don't scan when already scanned    
    if (in_array ($url, $scanned)) return false;
    
    return $url;
}

// Skip URLs from the $skip_url array
function SkipURL ($url)
{
    global $skip_url;

    if (isset ($skip_url))
    {
        foreach ($skip_url as $v)
        {           
            if (substr ($url, 0, strlen ($v)) == $v) return true; // Skip this URL
        }
    }

    return false;            
}

function Scan ($url)
{
    global $scanned, $pf;

    $scanned[] = $url;  // Add URL to scanned array

    if (SkipURL ($url))
    {
        // echo "Skip URL $url" . NL;
        return false;
    }
    
    // Remove unneeded slashes
    if (substr ($url, -2) == "//") 
    {
        $url = substr ($url, 0, -2);
    }
    if (substr ($url, -1) == "/") 
    {
        $url = substr ($url, 0, -1);
    }


    // echo "Scan $url" . NL;

    $headers = get_headers ($url, 1);

    // Handle pages not found
    if (strpos ($headers[0], "404") !== false)
    {
        // echo "Not found: $url" . NL;
        return false;
    }

    // Handle redirected pages
    if (strpos ($headers[0], "301") !== false)
    {   
        $url = $headers["Location"];     // Continue with new URL
        // echo "Redirected to: $url" . NL;
    }
    // Handle other codes than 200
    else if (strpos ($headers[0], "200") == false)
    {
        $url = $headers["Location"];
        // echo "Skip HTTP code $headers[0]: $url" . NL;
        return false;
    }

    // Get content type
    if (is_array ($headers["Content-Type"]))
    {
        $content = explode (";", $headers["Content-Type"][0]);
    }
    else
    {
        $content = explode (";", $headers["Content-Type"]);
    }
    
    $content_type = trim (strtolower ($content[0]));
    
    // Check content type for website
    if ($content_type != "text/html") 
    {
        if ($content_type == "" && IGNORE_EMPTY_CONTENT_TYPE)
        {
            // echo "Info: Ignoring empty Content-Type." . NL;
        }
        else
        {
            if ($content_type == "")
            {
             /*   echo "Info: Content-Type is not sent by the web server. Change " .
                     "'IGNORE_EMPTY_CONTENT_TYPE' to 'true' in the sitemap script " .
                     "to scan those pages too." . NL;*/
            }
            else
            {
                // echo "Info: $url is not a website: $content[0]" . NL;
            }
            return false;
        }
    }

    $html = GetPage ($url);
    $html = trim ($html);
    if ($html == "") return true;  // Return on empty page
    
    $html = preg_replace("/(\<\!\-\-.*\-\-\>)/sU", "", $html); // Remove commented text
    $html = str_replace ("\r", " ", $html);        // Remove newlines
    $html = str_replace ("\n", " ", $html);        // Remove newlines
    $html = str_replace ("\t", " ", $html);        // Remove tabs
    $html = str_replace ("<A ", "<a ", $html);     // <A to lowercase

    $first_anchor = strpos ($html, "<a ");    // Find first anchor

    if ($first_anchor === false) return true; // Return when no anchor found

    $html = substr ($html, $first_anchor);    // Start processing from first anchor

    $a1   = explode ("<a ", $html);
    echo "<pre>";
    print_r($a1);
    echo "</pre>";
    $url_data = [];
    foreach ($a1 as $next_url)
    {
        $next_url = trim ($next_url);
        
        // Skip empty array entry
        if ($next_url == "") continue; 
        
        // Get the attribute value from href
        $next_url = GetHREFValue ($next_url);
        
        // Do all skip checks and construct full URL
        $next_url = ValidateURL ($url, $next_url);
        
        // Skip if url is not valid
        if ($next_url == false) continue;

        if (Scan ($next_url))
        {
        	$url_data[] = $next_url;
            // Add URL to sitemap
       /*     fwrite ($pf, "  <url>\n" .
                         "    <loc>" . htmlentities ($next_url) ."</loc>\n" .
                         "    <changefreq>" . FREQUENCY . "</changefreq>\n" .
                         "    <priority>" . PRIORITY . "</priority>\n" .
                         "  </url>\n"); */
        }
    }

    return $url_data;
}


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
		/*$url_without_www = str_replace('https://','',$url);
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
				     	$match[2] =  preg_replace('{/$}', '', $match[2]);
				     	$inbound_links[] = $match[2];
				 	}
					else if (!strstr(strtolower($match[2]),$url_without_www) && strstr(strtolower($match[2]),'http://')) {
				     	$outbound += 1;
				    }
				}
			}
		}*/
		return Scan($url);
		// return $inbound_links;
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