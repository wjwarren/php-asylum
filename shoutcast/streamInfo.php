<?php
	/**
	 * Simple PHP script to retrieve a radio stream's meta data.
	 * 
	 * Example output:
	 * <MetaData>
	 * 	<listeners>32</listeners>
	 * 	<ulisteners>31</ulisteners>
	 * 	<listenerpeak>181</listenerpeak>
	 * 	<serverstate>Up</serverstate>
	 * 	<icy-br>128</icy-br>
	 * 	<StreamTitle>
	 * 		<![CDATA[ Urfaust - Gespinnst Des Verderbens ]]>
	 * 	</StreamTitle>
	 * </MetaData>
	 */
	
	// Shoutcast Host
	$ip = "205.188.215.231";
	// Shoutcast Port
	$port = "8016";
	// Used for alternate path to "Streaming URL" -- leave as "" for the default setup.
	$mount = "";
	
	if(!empty($_REQUEST["ip"])) $ip = $_REQUEST["ip"];
	if(!empty($_REQUEST["port"])) $port = $_REQUEST["port"];
	if(!empty($_REQUEST["mount"])) $mount = $_REQUEST["mount"];
	
	$open = @fsockopen($ip,$port);
	// Apparently there are two different ways of retrieving stream meta data...
	if ($open) {
		fputs($open,"GET $mount/7.html HTTP/1.1\nUser-Agent:Mozilla\n\n");
		$read = fread($open,1000);
		$text = explode("content-type:text/html",$read);
		$text = explode(",",$text[1]);
		generateXMLHeader();
		generateXMLSeven($text);
		generateXMLFooter();
	}
	/*if ($open) {
		fputs($open,"GET /$mount\nIcy-MetaData:1\n\n");
		$read = fread($open,2048);
		print(strip_tags($read));
		fread($open,24576);
		$metalength = fread($open,1);
		print("<br/><br/>\nMeta data length: " . $metalength);
	}*/
	else { 
		$er = "Connection Refused!\nip: $ip\nport: $port\nmount: $mount";
		generateXMLHeader();
		generateXMLSeven($er);
		generateXMLFooter();
	}
	@fclose($open);
	
	/*
	 * function to create the XML header(s)
	 */
	function generateXMLHeader(){
		//construct the xml document headers...
		header("Content-type: text/xml");
		// do not cache or we won't update in fucking IE
		header("Expires: " . date("D, d M Y H:i:s", 0) . " GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
		// start outputting XML
		echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		echo "\t<MetaData>\n";
	}
	
	/*
	 * function to create the XML header(s)
	 */
	function generateXMLFooter(){
		//construct the xml document footer...
		echo "\t</MetaData>";
	}
	
	/*
	 * Puts the stream information in (XML) tags,
	 * when no connection could be made writes 
	 * an error message in the error tag.
	 */
	function generateXMLSeven($content) {
		if(!is_array($content)) {
			echo "\t\t<error>$content</error>\n";
		}
		else {
			if ($content[1]==1) { $state = "Up"; } else { $state = "Down"; }
			// get rid of the HTML made by shoutcast
			$listeners = substr(strrchr($content[0], ">"), 1);
			$cursong = substr($content[6], 0, strpos($content[6], "<"));
			// output the info
			echo "\t\t<listeners>$listeners</listeners>\n";
			echo "\t\t<ulisteners>$content[4]</ulisteners>\n";
			echo "\t\t<listenerpeak>$content[2]</listenerpeak>\n";
			echo "\t\t<serverstate>$state</serverstate>\n";
			echo "\t\t<icy-br>$content[5]</icy-br>\n";
			cho "\t\t<StreamTitle><![CDATA[$cursong]]></StreamTitle>\n";
		}
	}
?>