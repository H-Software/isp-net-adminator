<table border="1" width="100%" bgcolor="#EEEEEE" >

 <tr>
 
  <td colspan="2" >
  
  <span style="color: #990033; vertical-align: 50%; font-weight: bold; font-family: arial; padding-right: 20px; font-size: 14px; ">
  Jste přihlášeni v administračním systému: </span>
  
  <? echo '<img src="'.$cesta.'img2/im-adm8.jpg" height="40px" width="400px" alt="logo-main" >'; ?>
  
    <span style="padding-left: 20px; color: #990033; vertical-align: 50%; font-weight: bold; font-family: arial; font-size: 12px; ">
  Přihlášen jako : <span style="color: black; "><? echo \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email; ?></span>
  , z ip : <span style="color: black; "><? echo $_SERVER['REMOTE_ADDR']; ?> </span></span>  
  </td>

 </tr>

