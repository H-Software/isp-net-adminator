<div class="home-vypis-useru-napis" >Poslední přihlašení uživatelé: </div>

{section name="users" loop=$logged_users}

  <div class="home-vypis-useru-main" >
    uživatel: <span class="home-vypis-useru-font1" >{$logged_users[users].nick}</span>, 
    přihlášen dne: <span class="home-vypis-useru-font2" >{$logged_users[users].datum}</span>
    , z ip adresy: <span class="home-vypis-useru-font2" >{$logged_users[users].ip}</span>
  </div>
  
{* *}
{sectionelse}
    {* Tato cast se provede v pripade prazdneho pole *}
                        
 <div class="" >Chyba! Žádní přihlášení uživatelé ..</div>
                        
{/section}