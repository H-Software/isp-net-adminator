<?php

echo '

    <span style="margin-left: 40px; "><a href="'.$cesta.'platby-subcat.php" class="odkaz-uroven-vys" >| O úrověn výš |</a></span>
    
     <span style="margin-left: 40px; "><a href="'.$cesta.'faktury/fn-index.php?filtr_stav_emailu=99" >Výpis neuhr. faktur </a></span>
     
     <span style="margin-left: 40px; "><a href="'.$cesta.'faktury/fn-aut-email.php" >Aut. odesílání emailů o N.FA.</a></span>
     
     <span style="margin-left: 40px; "><a href="'.$cesta.'faktury/fn-aut-sms.php" >Aut. odesílání SMS o N.FA.</a></span>

     <span style="margin-left: 40px; "><a href="'.$cesta.'faktury/fn-aut-sikana.php" >Aut. nastavení šikany u N.FA.</a></span>     
     
     <div style="padding-left: 40px; float: left;">A3:
      <span style="margin-left: 100px; ">
        <a href="/adminator3/fn-kontrola-omezeni.php" >Kontrola omezení obj. vůči neuhr. fakturám</a>
      </span>
     </div>
     
     <div>

    <span style="padding-left: 20px; ">
	<img src="/adminator2/img2/pohoda-sql-2.jpg" alt="pohoda sql" border="0" style="padding-top: 2px; padding-bottom: 0px; ">
    </span>
    
      <span style="margin-left: 40px; "><a href="'.$cesta.'pohoda_sql/phd_change_vs.php" > Změna VarSym</a></span>
     ,
      <span style="margin-left: 10px; "><a href="'.$cesta.'pohoda_sql/phd_synchro_nf.php" >Synchronizace neuhr. faktur</a></span>
      
      <span style="margin-left: 10px; "><a href="'.$cesta.'pohoda_sql/phd_repaid_vs.php" >Oprava Var. symbolů</a></span>

     </div>


    ';	       

?>