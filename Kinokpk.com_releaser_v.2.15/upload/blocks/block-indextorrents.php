<?php 
if (!defined('BLOCK_FILE')) { 
 Header("Location: ../index.php"); 
 exit; 
}

$blocktitle = "������".(get_user_class() >= UC_USER ? "<font class=\"small\"> - [<a class=\"altlink\" href=\"upload.php\"><b>������</b></a>]  </font>" : "<font class=\"small\"> - (����� �����������)</font>");


if (!defined("CACHE_REQUIRED")){
 	require_once(ROOT_PATH . 'classes/cache/cache.class.php');
	require_once(ROOT_PATH .  'classes/cache/fileCacheDriver.class.php');
	define("CACHE_REQUIRED",1);
  }
  		$cache=new Cache();
		$cache->addDriver('file', new FileCacheDriver());

    $count = $cache->get('block-indextorrents', 'count');
    if($count===false){
      $count = sql_query("SELECT COUNT(*) FROM torrents WHERE banned = 'no' AND visible = 'yes'");
$count = @mysql_result($count,0);
        $cache->set('block-indextorrents', 'count', $count);

}

if (!$count) { $content = "<div align=\"center\">��� �������</div>"; } else {

    $perpage = 5;
    list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER["PHP_SELF"] . "?" );

if (isset($_GET['page']) && !is_numeric($_GET['page'])) die('There is a problem with PAGE [method = get] parameter');

// get ID's
      $ids = $cache->get('block-indextorrents', 'ids'.(($_GET['page']==0)?"":$_GET['page']));

     if($ids===false){

     $idsrow = sql_query("SELECT id FROM torrents ORDER BY added DESC $limit");
while (list($id) = mysql_fetch_array($idsrow))
$ids[] = $id;

$time = time();
        $cache->set('block-indextorrents', 'ids'.(($_GET['page']==0)?"":$_GET['page']), $ids);

}
// get ID's END

$content = '<table with="100%">';
    $content .= "<tr><td>";
    $content .= $pagertop;
    $content .= "</td></tr>";
foreach ($ids as $id) {
$peers[$id] = array();
$dd[$id] = array();
}

$ids = implode(",",$ids);



      $reldata = $cache->get('block-indextorrents', 'query'.(($_GET['page']==0)?"":$_GET['page']));
     if($reldata===false){

 $res = sql_query("SELECT torrents.*, categories.id AS catid, categories.name AS catname, categories.image AS catimage, users.username, users.id AS userid, users.class, descr_torrents.value, descr_details.name AS dname, descr_details.input FROM torrents LEFT JOIN users ON torrents.owner = users.id LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN descr_torrents ON torrents.id = descr_torrents.torrent LEFT JOIN descr_details ON descr_details.id = descr_torrents.typeid  WHERE banned = 'no' AND visible = 'yes' AND torrents.id IN ($ids) AND descr_details.mainpage = 'yes' ORDER BY torrents.added DESC, descr_details.sort ASC");
     
    while ($relarray = mysql_fetch_array($res))
    $reldata[] = $relarray;

        $cache->set('block-indextorrents', 'query'.(($_GET['page']==0)?"":$_GET['page']), $reldata);

                        }

// Now fucking my brain...

foreach ($reldata as $release) {
  $namear[$release['id']] = $release['name'];
  $filenamear[$release['id']] = $release['filename'];
  $image1ar[$release['id']] = $release['image1'];
  $image2ar[$release['id']] = $release['image2'];
  $cat[$release['id']] = array('id'=>$release['catid'],'name' => $release['catname'],'img'=>$release['catimage']);
  $usernamear[$release['id']] = $release['username'];
  $useridar[$release['id']] = $release['userid'];
  $userclassar[$release['id']] = $release['class'];
  $ownerar[$release['id']] = $release['owner'];
  $sizear[$release['id']] = $release['size'];
  $addedar[$release['id']] = $release['added'];
  $commentsar[$release['id']] = $release['comments'];
  
  $tagsar[$release['id']] = $release['tags'];
  array_push($dd[$release['id']],array('name'=>$release['dname'],'value'=>$release['value'])); //'input'=>$release['input']));
  
}
//print_r($dd);

   foreach ($namear as $id => $tname) {
      $filename = $filenamear[$id];
       $torid = $id;
        $catid = $cat[$id]['id'];
        $catname = $cat[$id]['name'];
        $catimage = $cat[$id]['img'];
                $torname = $tname;
       $descr = '<table width="100%" border="1">';
    
    $tags = '';
        foreach(explode(",", $tagsar[$id]) as $tag)
                $tags .= "<a href=\"browse.php?tag=".$tag."\">".$tag."</a>, ";

                if ($tags)
                $tags = substr($tags, 0, -2);

    $descr .= "<tr><td valign=\"top\"><b>����:</b></td><td>".$tags."</td></tr>";

  foreach ($dd[$id] as $dddescr)
   if ($dddescr['value'] != '') $descr .= "<tr><td valign=\"top\"><b>".$dddescr['name'].":</b></td><td>".format_comment($dddescr['value'])."</td></tr>";
   
   $descr .="</table>";

                $uprow = (isset($usernamear[$id]) ? ("<a href=userdetails.php?id=" . $ownerar[$id] . ">" . htmlspecialchars($usernamear[$id]) . "</a>") : "<i>������</i>");

                $img1 = "<a href=\"details.php?id=$id&hit=1\"><img src=\"pic/noimage.gif\" width=\"160\" border=\"0\" /></a>";
                $img2 = ''; 

        $content .= "<tr><td>";
        $content .= "<table width=\"100%\" class=\"main\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\">";
        $content .= "<tr>"; 
        $content .= "<td class=\"colhead\" colspan=\"2\" align=center>"; 
        $content .= $namear[$id];
        $content .= "<a class=\"altlink_white\" href=\"bookmark.php?torrent=$id\">   ";
        $content .= "</font></td>"; 
        $content .= "</tr>"; 
                  if ($image1ar[$id] != "")
                    $img1 = "<a href=\"details.php?id=$id&hit=1\"><img width=\"160\" border='0' src=\"thumbnail.php?image=$image1ar[$id]&for=index\" /></a>";
        $content .= "<tr valign=\"top\"><td align=\"center\" width=\"160\">"; 
            $content .= $img1;
        if ($image2ar[$id] != ""){
           $img2 = "<a href=\"details.php?id=$id&hit=1\"><img width=\"160\" border='0' src=\"thumbnail.php?image=$image2ar[$id]&for=index\" /></a>";
            $content .= "<br /><br />$img2"; }
        $content .= "</td>"; 
        $content .= "<td><div align=\"left\">".$descr."</div>
            <table width=\"100%\"><tr><td>
            <hr />
            <b>�������: </b><a href=\"userdetails.php?id=$useridar[$id]\">".get_user_class_color($userclassar[$id],$usernamear[$id])."</a><br>
            <b>������: </b>".mksize($sizear[$id])."<br>";

            $content .= "<b>��������: </b>$addedar[$id]
            <hr />
                        <b>����������: </b>$commentsar[$id]</b><br></td><td>
            <div align=\"right\">".(!empty($catname) ? "<a href=\"browse.php?cat=$catid\">
            <img src=\"pic/cats/$catimage\" alt=\"$catname\" title=\"$catname\" border=\"0\" /></a>" : "")."<br/>
            [<a href=\"details.php?id=$id&hit=1\" alt=\"$namear[$id]\" title=\"$namear[$id]\"><b>�����������</b></a>] [<a href=\"browse.php\">������ ������ �������</a>]</div></td></table></td>";
        $content .= "</tr>"; 
        $content .= "</table>";
        $content .= "</td></tr>"; 
//        print_r ($cat);
    }
    $content .= "<tr><td>";
    $content .= $pagerbottom;
    $content .= "</td></tr>";
$content .= "</table>";

}
?>