<?php
if (!defined('BLOCK_FILE')) {
header("Location: ../index.php");
exit;
}

$content .= "<table  width=100%><tr><td  valign=top align=center>";

$content .= "<small>[<a href=viewrequests.php>��</a>] [<a href=requests.php?action=new>��������<a>]</small><hr><table border=1 class=bottom>";

 if (!defined("CACHE_REQUIRED")){
 	require_once(ROOT_PATH . 'classes/cache/cache.class.php');
	require_once(ROOT_PATH .  'classes/cache/fileCacheDriver.class.php');
	define("CACHE_REQUIRED",1);
  }
  		$cache=new Cache();
		$cache->addDriver('file', new FileCacheDriver());

			$reqarray = $cache->get('block-req', 'query');
			
if ($reqarray===false) {

$reqarray = array();
$req=sql_query("SELECT requests.* FROM requests INNER JOIN categories ON requests.cat = categories.id WHERE requests.filled = '' ORDER BY added DESC LIMIT 3");

while ($reqres = @mysql_fetch_array($req))
$reqarray[]=$reqres;

$cache->set('block-req', 'query', $reqarray);
}

    if (!$reqarray) {$content .= '<b>��� ��������</b>'; } else
foreach ($reqarray as $requests) {
if ($requests[filledby]!=0) {
$done = "<a href=".addslashes($requests[filled])."><img border=\"0\" src=\"pic/chk.gif\" alt=\"��������\"/></a>";
}
else {
$done = "";
}

$content .= "<tr><td class='req'><b><a href=requests.php?id=$requests[id]>$requests[request]</b></a>&nbsp;&nbsp;&nbsp;$done<br> [����:  $requests[comments], �����������: $requests[hits]]<br><small><a href=requests.php?action=vote&voteid=$requests[id]>�������������� � �������</a></small></td></tr>";

}

$content .= "</td></tr></table></table>";

$blocktitle = "<font color=\"red\">���� �������</font>";
?>