<div class="home-vypis-useru-napis" >Přihlašení uživatelé: </div>

{section name="users" loop=$logged_users}

  <div class="home-vypis-useru-main" >
    uživatel: <span class="home-vypis-useru-font1" >{$logged_users[users].email}</span>, 
    naposledy viděn: <span class="home-vypis-useru-font2" >{$logged_users[users].date}</span>
    <span class="home-vypis-useru-font2" ></span>
  </div>
  
{* *}
{sectionelse}
    {* Tato cast se provede v pripade prazdneho pole *}
                        
 <div class="" >Chyba! Žádní přihlášení uživatelé ..</div>
                        
{/section}