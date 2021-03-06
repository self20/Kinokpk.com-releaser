<?php
/**
 * Administrative tool to ban emails
 * @license GNU GPLv3 http://opensource.org/licenses/gpl-3.0.html
 * @package Kinokpk.com releaser
 * @author ZonD80 <admin@kinokpk.com>
 * @copyright (C) 2008-now, ZonD80, Germany, TorrentsBook.com
 * @link http://dev.kinokpk.com
 */

require "include/bittorrent.php";
dbconn();
loggedinorreturn();
httpauth();

if (get_user_class() < UC_ADMINISTRATOR)
stderr($REL_LANG->say_by_key('error'), $REL_LANG->say_by_key('access_denied'));



if (isset($_GET['remove']) && is_valid_id($_GET['remove']))
{
	$remove = (int) $_GET['remove'];

	sql_query("DELETE FROM bannedemails WHERE id = '$remove'") or sqlerr(__FILE__, __LINE__);
	write_log("��� $remove ��� ���� ������������� $CURUSER[username]",'emailbans');
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$email = trim($_POST["email"]);
	$comment = trim($_POST["comment"]);
	if (!$email || !$comment)
	stderr("Error", "Missing form data.");
	sql_query("INSERT INTO bannedemails (added, addedby, comment, email) VALUES(".sqlesc(time()).", $CURUSER[id], ".sqlesc($comment).", ".sqlesc($email).")") or (mysql_errno() == 1062 ? stderr($REL_LANG->say_by_key('error'), "���� e-mail ��� �������") : sqlerr(__FILE__, __LINE__));
	safe_redirect(" $_SERVER[REQUEST_URI]");
	die;
}

$res = sql_query("SELECT * FROM bannedemails ORDER BY added DESC") or sqlerr(__FILE__, __LINE__);

$REL_TPL->stdhead("��� �������");

print("<h1>������ �����</h1>\n");

if (mysql_num_rows($res) == 0)
print("<p align=center><b>�����</b></p>\n");
else
{
	print("<table border=1 cellspacing=0 cellpadding=5>\n");
	print("<tr><td class=colhead>���������</td><td class=colhead align=left>Email</td>".
        "<td class=colhead align=left>���</td><td class=colhead align=left>����������</td><td class=colhead>�����</td></tr>\n");

	while ($arr = mysql_fetch_assoc($res))
	{
		$r2 = sql_query("SELECT username FROM users WHERE id = $arr[addedby]") or sqlerr(__FILE__, __LINE__);
		$a2 = mysql_fetch_assoc($r2);
		print("<tr><td>".mkprettytime($arr[added])."</td><td align=left>$arr[email]</td><td align=left><a href=\"".$REL_SEO->make_link('userdetails','id',$arr['addedby'],'username',$a2['username'])."\">$a2[username]".
                "</a></td><td align=left>$arr[comment]</td><td><a href=\"".$REL_SEO->make_link('banemailadmin','remove',$arr['id'])."\">����� ���</a></td></tr>\n");
	}
	print("</table>\n");
}

print("<h2>��������</h2>\n");
print("<table border=1 cellspacing=0 cellpadding=5>\n");
print("<form method=\"post\" action=\"".$REL_SEO->make_link('banemailadmin')."\">\n");
print("<tr><td class=rowhead>Email</td><td><input type=\"text\" name=\"email\" size=\"40\"></td>\n");
print("<tr><td class=rowhead>����������</td><td><input type=\"text\" name=\"comment\" size=\"40\"></td>\n");
print("<tr><td colspan=2>����������� *@email.com ����� �������� ���� ������</td></tr>\n");
print("<tr><td colspan=2><input type=\"submit\" value=\"��������\" class=\"btn\"></td></tr>\n");
print("</form>\n</table>\n");

$REL_TPL->stdfoot();

?>