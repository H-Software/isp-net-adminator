<?php
/*
  stripslashesArray by Jamie Curnow
	strips slashes from all strings in an array of strings. array can be as multi dimensional.
*/
function stripslashesArray($arr) {
	if (is_array($arr)) {
		$keys = array_keys($arr);
		for ($x=0;$x<count($keys);$x++) {
			if (!is_object($arr[$keys[$x]])) {
				$arr[$keys[$x]] = stripslashesArray($arr[$keys[$x]]);
			}
		}
		return $arr;
	} else {
		return stripslashes($arr);
	}
}

/*
  addslashesArray by Jamie Curnow
	adds slashes on all strings in an array of strings. array can be as multi dimensional.
*/
function addslashesArray($arr) {
	if (is_array($arr)) {
		$keys = array_keys($arr);
		for ($x=0;$x<count($keys);$x++) {
			if (!is_object($arr[$keys[$x]])) {
				$arr[$keys[$x]] = addslashesArray($arr[$keys[$x]]);
			}
		}
		return $arr;
	} else {
		return addslashes($arr);
	}
}

/*
  TieString by Jamie Curnow

	This function ties the $pre to the start of $string, if $pre is not already there.
	also does the same to the end of the string with $post.
*/
function TieString($string,$pre='',$post='') {
	if (strlen($pre) > 0) {
		//tie the pre - check if not already tied.
		if (strpos($string,$pre) !== 0) {
			$string = $pre . $string;
		}
	}
	if (strlen($post) > 0) {
		//tie the post - check if not already tied
		//get sub
		$t = substr($string,(strlen($string) - strlen($post)),strlen($post));
		if ($t != $post) {
			$string .= $post;
		}
	}
	return $string;
}

/*
  GetSize by Jamie Curnow

	returns a formatted string like 2.5kb from an integer of bytes.
	also returns mb
*/
function GetSize($bytes) {
	if ($bytes > 1024) {
		$size_kb = $bytes / 1024;
		if ($size_kb > 1024) {
			$size_mb = $size_kb / 1024;
			if ($size_mb > 1024) {
				$size_gb = $size_mb / 1024;
				$sizer = number_format($size_gb,2,".",",") . " Gig";
			} else {
				$sizer = number_format($size_mb,2,".",",") . " Mb";
			}
		} else {
			$sizer = number_format($size_kb,0,".",",") . " Kb";
		}
	} else {
		$sizer = $bytes . " b";
	}
	return $sizer;					
}

/*
  GetExt by Jamie Curnow

	gets the right part of a string before the '.' in lowercase
*/
function GetExt($file) {
	$tempext = strtolower(substr($file, strrpos($file,'.')+1,strlen($file)-strrpos($file,'.')));
	return $tempext;
}

/*
  GetFilename by Jamie Curnow

	Gets a filename from a full path with it's parent directory,
	The right part of string before the '/'
*/
function GetFilename($file) {
	$filename = substr($file, strrpos($file,'/')+1,strlen($file)-strrpos($file,'/'));
	return $filename;
}

/*
  RemoveExtension by Jamie Curnow

	Removes the extension (and dot) of a filename
*/
function RemoveExtension($filename) {
	$file = substr($filename, 0,strrpos($filename,'.'));	
	return $file;
}

/*
  readfile_chunked by Jamie Curnow

	Reads the content of a file, either local or remote (http://blah)
	in chunks that are protocol safe and avoid network disruption.
*/
function readfile_chunked ($filename) {
  $chunksize = 1*(1024*1024); // how many bytes per chunk
  $buffer = '';
  $contents = '';
  $handle = @fopen($filename, 'rb');
  if ($handle === false) {
   return false;
  }
  while (!feof($handle)) {
   $buffer = fread($handle, $chunksize);
   $contents .= $buffer;
   //print $buffer;
  }
  @fclose($handle);
  return $contents;
}

/*
  GetXML by Jamie Curnow
	
	For simple use.
	Gets the contents of an XML file into an array - does not work with branch arrays
	Does not get self terminated values, as the xml parser in php is terrible.
	
	dependancies:
	  readfile_chunked
*/
function GetXML($xmlfile) {
	$data = readfile_chunked($xmlfile);
	if ($data) {
		//interprate result
		$xml_parser = xml_parser_create();
		xml_parse_into_struct($xml_parser, $data, $vals, $index);
		xml_parser_free($xml_parser);
		
		$params = array();
		$level = array();
		foreach ($vals as $xml_elem) {
			if ($xml_elem['type'] == 'open') {
			 if (array_key_exists('attributes',$xml_elem)) {
			 		list($level[$xml_elem['level']],$extra) = array_values($xml_elem['attributes']);
			 } else {
			 		$level[$xml_elem['level']] = $xml_elem['tag'];
			 }
			}
			if ($xml_elem['type'] == 'complete') {
			 $start_level = 1;
			 $php_stmt = '$params';
			 while($start_level < $xml_elem['level']) {
			 		$php_stmt .= '[$level['.$start_level.']]';
			 		$start_level++;
			 }
			 $php_stmt .= '[$xml_elem[\'tag\']] = $xml_elem[\'value\'];';
			 eval($php_stmt);
			}
		}
		return $params;
	}
	return false;
}

function GetIntelligentName($name) {
	//this function strips the extension, and aims to produce a friendly filename
	if (strpos($name,'.') !== false) {
		return ucwords(trim(str_replace("_"," ",substr($name, 0, strrpos($name,'.')))));
	} else {
		return ucwords(trim(str_replace("_"," ",$name)));
	}
}

function ApplyWildcards($contents, $TemplateKeys) {	
	foreach ($TemplateKeys as $cont=>$val ) {
	  $contents = str_replace ( $cont, $val, $contents );
	}
	return $contents;
}
?>