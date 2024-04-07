<?

global $update_id;

  //zobrazime formular
    print '
    <br><H4>Přidání/úprava uživatele: ';
    
    if ($update_status == 1)
    { echo "<span style=\"color: grey; \">uprava uzivatele id: $update_id </div>"; }
    
    print '</H4><br>
    <form method="POST" action="'.$_SERVER["PHP_SELF"].'" name="form1">

    <input type="hidden" name="send" value="true">
    <input type="hidden" name="update_id" value="'.$update_id.'" >
    
     <table border="1" width="100%" id="table2">


 <tr>
  <td width="25%"><label>Přihlašovací jméno: </label></td>
  <td width="25%"><input type="text" name="login_jmeno" size="30" value="'.$login_jmeno.'"></td>
  <td colspan="2"><br></td>
 </tr>

  <tr>
       <td><label>Jméno: </label></td>
        <td><input type="text" name="jmeno" size="40" value="'.$jmeno.'" ></td>
	<td colspan="2"><br></td>
 </tr>

 <tr>
    <td><label>Heslo: </label></td>
    <td>';
    
    if ( ($update_status != 1) or ($zmenit_heslo == 1) )
    { print '<input type="password" name="login_password" size="40" >'; }
    else
    { echo "<span style=\"color: grey; font-weight: bold; \">nelze měnit </span>"; }
       
    print '</td>
    <td width="25%" colspan="">Změnit heslo:</td>
    <td>';
    
    if ( $update_status != 1)
    { echo "<span style=\"color: grey; font-weight: bold; \">Není dostupné </span>"; }
    else
    {
    echo '
	<select name="zmenit_heslo" size="1" onChange="self.document.forms.form1.submit()" >
	    <option value="0" '; if ($zmenit_heslo == 0 )echo " selected "; echo '>Ne</option>
	    <option value="1" '; if ($zmenit_heslo == 1 )echo " selected "; echo '>Ano</option>
	    
	</select>';
    }
    
    echo '</td>
 </tr>

 <tr>
     <td><label>Email:  </label></td>
     <td><input type="text" name="email" size="40" value="'.$email.'" ></td>
     <td colspan="2"><br></td>
   </tr>

 <tr>
    <td><label>Level: </label></td>
    <td><input type="text" name="login_level" size="20" value="'.$login_level.'" ></td>
    <td colspan="2"><br></td>
 </tr>

   <tr>
    <td>login jméno do síťového disku: </td>
   <td>';
   
   if ( $update_status != 1 )
   { echo '<input type="text" name="smb_user" size="20" value="'.$smb_user.'" >'; }
   else
   { 
    echo $smb_user."<span style=\"color: grey; font-weight: bold; \">  ( nelze měnit ) </span>"; 
    
    echo '<input type="hidden" name="smb_user" size="20" value="'.$smb_user.'" >';
   }

   echo '</td>
   <td colspan="2"><br></td>
   </tr>

   <tr>
    <td>heslo do síťového disku: </td>
    <td>';
 
 if ( ($update_status != 1) or ($zmenit_samba_heslo == 1) )
 { print "<input type=\"password\" name=\"smb_pass\" size=\"20\" >"; }
 else
 { echo "<span style=\"color: grey; font-weight: bold; \">nelze měnit </span>"; }
   
 print '</td>
 <td colspan="">Změnit heslo:</td>
 <td>';  
 
     if ( $update_status != 1)
    { echo "<span style=\"color: grey; font-weight: bold; \">Není dostupné </span>"; }
    else
    {
    echo '
	<select name="zmenit_samba_heslo" size="1" onChange="self.document.forms.form1.submit()" >
	    <option value="0" '; if ($zmenit_samba_heslo == 0 )echo " selected "; echo '>Ne</option>
	    <option value="1" '; if ($zmenit_samba_heslo == 1 )echo " selected "; echo '>Ano</option>
	    
	</select>';
    }

 echo ' 
 </tr>

   <tr>
    <td><br></td>
   <td></td>
   </tr>

   <tr>
    <td><br></td>
    <td></td>
   </tr>

    <tr>
     <td></td>
      <td>
      
       <input name="odeslano" type="submit" value="OK">

   </td>
    </tr>

 </table>

 </form>';

 ?>

 </td>

  </tr>

  </table>


