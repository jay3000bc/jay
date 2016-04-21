<?
/*
 * PHP FTP Client 0.60
 * Copyright (C) Rad Inks (Pvt) Ltd. 2004
 * http://www.radinks.com
 *
 * Licence:
 * The contents of this file are subject to the Mozilla Public
 * License Version 1.1 (the "License"); you may not use this file
 * except in compliance with the License. You may obtain a copy of
 * the License at http://www.mozilla.org/MPL/
 * 
 * Software distributed under the License is distributed on an "AS
 * IS" basis, WITHOUT WARRANTY OF ANY KIND, either express or
 * implied. See the License for the specific language governing
 * rights and limitations under the License.
 * 
 * The Initial Developer of the Original Code is Raditha Dissanayake.
 * Portions created by Raditha are Copyright (C) 2000-2003
 * Rad Inks (Pvt) Ltd. All Rights Reserved.
*/


session_start();
require_once("ftp.php");

/* make sure we are not troubled by wrong ordering */
	$user = $_SESSION['user'];
	$password = $_SESSION['password'];
	$hostname = $_SESSION['hostname'];
	$pwd = $_SESSION["pwd"];
	
$filename=$_GET['filename'];

$ftp = new FTP($hostname);
if($ftp->connect())
{
	$ftp->is_ok();
	
	$ftp->login($user,$password);
	$ftp->cwd($pwd);
	
	$ftp->is_ok();
	
	$sock = $ftp->retr(trim($filename));
	if($sock)
	{
		if($sock)
		{

			$mime_type = (strstr($HTTP_USER_AGENT,'IE') || strstr($HTTP_USER_AGENT,'OPERA'))
			? 'application/octetstream'
			: 'application/octet-stream';
			// finally send the headers and the file
			header('Content-Type: ' . $mime_type);
			header('Expires: ' . $now);

			if (strstr($HTTP_USER_AGENT,'IE')) {
				header('Content-Disposition: inline; filename="' . $filename. '"');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
			} else {
				header('Content-Disposition: attachment; filename="' . $filename .'"');
				header('Pragma: no-cache');
			}

			fpassthru($sock);
			exit();
		}

	}
	else
	{
		echo 'file could not be retrieved';
	}
}
else
{
	echo 'Could not connect';
}

