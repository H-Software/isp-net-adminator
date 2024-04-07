
{include file="inc.header.tpl"}

{include file="inc.head.tpl"}

<link href="style-login.css" rel="stylesheet" type="text/css">
 
<title>{$page_title}</title>

</head>

<body>

<div style="text-align: center;" >

 <img alt="logo" src="img2/logo.png" border="0" >
 <br><br>
 <img alt="logo 2" src="img2/im-adm10b.jpg" width="400" >
 <br><br><br>

 <b class=big>PŘIHLÁŠENÍ</b><br>

{if $lp_on == 1}
  <form method="POST" action="index.php?lp={$last_page}" >
{else}
  <form method="POST" action="index.php" >
{/if}

<table width=300 border=1 align="center" >

<tr>
  <td align=left width="150"><b>Login:</b></td>
  <td align=left ><input name="login" type="text" ></td>
</tr>

<tr>
  <td align=left><b>Heslo:</b></td>
  <td align=left ><input name="password" type="password" ></td>
</tr>

<tr>
  <td align=center colspan="2"><input type="Submit" name="odesli" value="OK"></td>
</tr>

</table>
</form>

{$body}

</div>

{include file="inc.footer.tpl"}
