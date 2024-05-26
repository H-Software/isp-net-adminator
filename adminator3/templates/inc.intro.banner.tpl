 
 <div class="intro-banner" >

     <span class="intro-banner-welcome" >
        Jste přihlášeni v administračním systému: 
     </span>
     <img src="/img2/im-adm10.jpg" height="40px" width="400px" alt="logo adminator" border="0" >

     <span class="intro-banner-logged">

        {if strlen($nick_a_level) qt 3}
        jako : <span style="color: black; ">{$nick_a_level}, </span>
        {/if}

        {if strlen($login_ip) qt 0}
        z ip : <span style="color: black; ">{$login_ip}</span>
        {/if}

    </span>
 </div>
