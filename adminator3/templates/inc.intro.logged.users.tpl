
 <div class="intro-logged-users" >přihlášení uživatelé:  {$pocet_prihl_uziv} </div>

    <div style="padding-bottom: 5px; padding-top: 2px; " >
      <a href="index.php?lo=true" target="_top" class="cat" >Odhlásit se</a>
    </div>
    
    <input type="button" class="buttonalllogin" name="OK2" value="Zobrazit všechny přihlášené" onclick="showWindow2()" >

     <script type="text/javascript" src="/adminator3/include/js/main.js?2"></script>
     <script type="text/javascript" src="/adminator3/include/js/classmaker.js?2"></script>

     <script type="text/javascript" src="/adminator3/include/js/dom.js?2"></script>
     <script type="text/javascript" src="/adminator3/include/js/window.js?2"></script>

     {literal}
     <script type="text/javascript" >

        var windowShow2 = false;
        var w2 = null;

        function showWindow2()
        {
            if (w2 == null)
            {

                //vytvoreni okna
                var params = {imagePath: "/adminator2/include/js/window/img/shadow-", sizes: [4,4,4,4]};
                w2 = new SZN.Window(params);
                //nastaveni obsahu
     {/literal}
                var txt = SZN.cTxt("{$windowtext2}");
     {literal}
                var button = SZN.cEl('input','closeButton');
                button.type="button";
                button.value="OK";
                button.onclick=showWindow2;
                w2.content.appendChild(txt);
                w2.content.appendChild(button);

                //window.cache[window.cache.length] = w;
            }

            w2.container.style.position = 'absolute';
            w2.content.style.border = "1px solid #000000";
            w2.content.style.backgroundColor = "#bababa";
     {/literal}
            w2.content.style.padding = "{$windowpadding2}px";
            w2.content.style.width = "{$windowdelka2}px";

            var pos1 = SZN.Dom.getBoxPosition(SZN.gEl('windowPlaceholder2'));
            w2.container.style.top = '{$windowtop2}px';
            w2.container.style.left = '{$windowleft2}px';

     {literal}
            SZN.gEl('windowPlaceholder2').appendChild(w2.container);

            if (windowShow2) {
                windowShow2 = false;
                w2.hide();
            } else {
                windowShow2 = true;
                w2.show();
            }
        }

     </script>
    {/literal}

    <div id="windowPlaceholder2" ></div>
