<div class="home-vypis-useru-napis" >Přihlašení uživatelé: </div>

{section name="users" loop=$logged_users}

  <div class="home-vypis-useru-main" >
    uživatel: <span class="home-vypis-useru-font1" >{$logged_users[users].email}</span>, 
    naposledy viděn: <span class="home-vypis-useru-font2" >{$logged_users[users].date}</span>
  </div>
  
{* *}
{sectionelse}
    {* Tato cast se provede v pripade prazdneho pole *}
    <div 
      class="alert alert-danger" 
      role="alert"
      style="width: 80%; "
      >   
      Přihlášené uživatel nelze vypsat.</br></br>
      ({$logged_users_error_message|default: ''})
    </div>
{/section}
