<?php ?>

<table width="600" border="0" cellspacing="0" cellpadding="0" align="center">
 <tr><td class="tableheading" >
	->> Přidat zprávu - povinné údaje zvýrazněny tučným písmem
	<hr width="100%" size="1" color="#7D7642" noshade>
 </td></tr>
</table> 

<table width="600" border="0" cellspacing="0" cellpadding="1" align="center" bgcolor="#7D7642"><tr><td>
<table width="100%" border="0" cellspacing="0" cellpadding="3" align="center" bgcolor="#eaead7">
<form method="post">
 <tr>
 	<td class="table"><b>Vaše jméno:</b></td>
	<td>
	
	 <input type="hidden" name="author" size="30" maxlength="50" class="input" style="WIDTH: 250px" <?php echo "value=\"".$nick."\" >"; ?>
	
	<?php
	echo "<span style=\"color: ; font-size: 12px; \" >".$nick."</span>";
	?>
	
	</td>
 </tr>
 
 <tr>
 	<td class="table">Váš e-mail:</td>
	<td><input type="text" name="email" size="30" maxlength="50" class="input" style="WIDTH: 250px" <?php echo "value=\"".$email."\" >"; ?></td>
 </tr>
 
 <tr>
 	<td class="table"><b>Zobrazit</b></td>
 	<td class=table>od: 
	<select name="from_day" size=1 class="input">
	
	<?php
	
	//od - den
	for($i=1;$i<=31;$i++){
	
		echo "<option value=" . $i;
		if($from_day==$i) echo " selected";
		echo ">" . $i . "</option>\n";
	}
	
	?>
	
	</select>
	.
	<select name="from_month" size="1" class="input">
	<?php
	//od - měsíc
	$month=Array(1=> "ledna", "února", "března", "dubna", "května", "června", "července", "srpna", "září", "října", "listopadu", "prosince");
	for($i=1;$i<=12;$i++):
		echo "<option value=" . $i;
		if($from_month==$i) echo " selected";
		echo ">" . $month[$i] . "</option>\n";
	endfor;
	?>
	</select>

	<select name="from_year" size="1" class="input">
	<?
	//od - rok
	for($i=2008;$i<= date("Y");$i++):
		echo '<option value=' . $i;
		if($from_year==$i) echo " selected";
		echo ">" . $i . "</option>\n";
	endfor;
	?>
	</select>
	<br>

	do: 
	<select name="to_day" size=1 class="input">
	<?
	//do - den
	for($i=1;$i<=31;$i++):
		echo "<option value=" . $i;
		if($to_day==$i) echo " selected";
		echo ">" . $i . "</option>\n";
	endfor;
	?>
	</select>
	.
	<select name="to_month" size="1" class="input">
	<?
	//do - měsíc
	for($i=1;$i<=12;$i++):
		echo "<option value=" . $i;
		if($to_month==$i) echo " selected";
		echo ">" . $month[$i] . "</option>\n";
	endfor;
	?>
	</select>

	<select name="to_year" size="1" class="input">
	<?
	//do - rok
	for($i=2008;$i<= date("Y");$i++):
		echo "<option value=" . $i;
		if($to_year==$i) echo " selected";
		echo ">" . $i . "</option>\n";
	endfor;
	?>
	</select>
	 </td>
 </tr>

 <tr>
 	<td class="table"><b>Předmět:</b></td>
	<td><input style="WIDTH: 250px" type="text" name="subject" size="30" maxlength="50" class="input" 
	<? echo "value=\"".$subject."\""; ?> ></td>
 </tr>
 
 <tr>
	<td valign="top" class="table"><b>Text zprávy:</b></td>
	<td><textarea cols="40" rows="7" name="body" class="input" style="WIDTH: 350px"><?echo $body; ?></textarea></td>
  </tr>
  
 <tr>
 	<td colspan="2" align="center"><input type="submit" name="send" value="Odeslat" class="input"></td>
 </tr>

<input type="hidden" name="sent" value="">
</form>
</table></td></tr></table>

<? if (isset($error) ) echo '<br><center><font class="error" >' . $error . '</font></center>'; //chybová hláška 
?>

