
<div class="tableheading typ-zprav" >{$mod_hlaska}</div>

<div class="writing-main" >

<form method="post" name="form-board" >

<div class="table writing-left" >Vaše jméno:</div>

<div style="padding-left: 110px; font-size: 12px; padding-top: 10px;" >{$nick}</div>

<div class="table writing-left" style="clear: both;" >Váš e-mail:</div>

<div style="padding-top: 10px; padding-left: 110px; " >
    <input type="text" name="email" size="30" maxlength="50" class="input" style="WIDTH: 250px" value="{$email}" >
</div>

<div class="table writing-field-zobr" ><b>Zobrazit</b></div>

<div class="table writing-field-date" >
    od: <input type="text" name="from_date" class="input" value="{$from_date}">

{literal}
 <script language="JavaScript">
    new tcal ({'formname': 'form-board','controlname': 'from_date'});
</script>
{/literal}
    <div style="padding-top: 5px;" ></div>do:
<input type="text" name="to_date" class="input" value="{$to_date}">

{literal}
<script language="JavaScript">
  var d_selected = new Date();
  d_selected.setDate(d_selected.getDate() + 7);
  var s_selected = f_tcalGenerDate(d_selected);

  new tcal ({'formname':'form-board','controlname':'to_date','selected':s_selected});
</script>
{/literal}
        
</div>

<div class="table writing-left2" >Předmět:</div>

<div class="writing-right1" >
    <input style="WIDTH: 250px" type="text" name="subject" size="30" maxlength="50" class="input" value="{$subject}" >
</div>

<div class="table writing-left2" >Text zprávy:</div>

<div class="writing-right1" >
    <textarea cols="40" rows="7" name="body" class="input" style="WIDTH: 350px">{$body}</textarea>
</div>

<div style="text-align: center; padding-top: 10px;" >
    <input type="submit" name="send" value="Odeslat" class="input">
</div>

<div style="padding-top: 10px;" ><font class="error" >{$error}</font></div>
 
<input type="hidden" name="sent" value="true" >
</form>
 	
</div>
