<?php
/** 
 * (Icy) Radio StreamProxy by Wijnand Warren.
 * 
 * Thanks to Mike Gieson from wimpyplayer.com
 * for the socket connection to the server.
 */

// Establish response headers
// http://ammonlauritzen.com/blog/2007/08/23/ioerror-on-https-under-ie7/
header("HTTP/1.0 200 OK");
header("Pragma: I-Hate-IE");
header("Cache-control: bogus");
header("Content-Type: audio/x-mpeg, audio/x-mpeg-3, audio/mpeg3");
header("Content-Encoding: binary");
// Content-Length is required for Internet Explorer:
// - Set to a rediculous number
// - I think the limit is somewhere around 300 MB
header("Content-Length: 319324133");

// Settings
$ip = "205.188.215.231";		// Shoutcast Host
$port = "8016";					// Shoutcast Port
$mount = "";					// Used for alternate path to "Streaming URL" -- leave as "" for the default setup.
//$icecastInterval = "0";			// Max number of MB to load

if (!empty($_REQUEST["ip"])) $ip = $_REQUEST["ip"]; 
if (!empty($_REQUEST["port"])) $port = $_REQUEST["port"]; 
if (!empty($_REQUEST["mount"])) $mount = $_REQUEST["mount"]; 

// Make socket connection
$errno = "errno";
$errstr = "errstr";
$fp = fsockopen($ip, $port, $errno, $errstr, 30);

if(!$fp) exit;

// Create send headers
fputs($fp, "GET /$mount HTTP/1.0\r\n"); 
fputs($fp, "Host: $ip\r\n"); 
fputs($fp, "User-Agent: Winamp 5.2\r\n"); 
fputs($fp, "Accept: */*\r\n"); 
fputs($fp, "Icy-MetaData:0\r\n"); 
fputs($fp, "Connection: close\r\n\r\n");  

// Write the returned data back to the resource
fpassthru($fp);  

// close the socket when we're done
fclose($fp); 
?>