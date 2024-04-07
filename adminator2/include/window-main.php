<?php

echo '<script type="text/javascript" >

        var windowShow = false;
        var w = null;

        function showWindow() {
                if (w == null)
                {

                        //vytvoreni okna
                        var params = {imagePath: "/adminator2/include/js/window/img/shadow-", sizes: [4,4,4,4]};
                        w = new SZN.Window(params);
                        //nastaveni obsahu
                        var txt = SZN.cTxt("'.$windowtext.' ");
			var button = SZN.cEl(\'input\',\'closeButton\');
                        button.type="button";
                        button.value="OK";
                        button.onclick=showWindow;
                        w.content.appendChild(txt);
                        w.content.appendChild(button);

                        //window.cache[window.cache.length] = w;
                }

                w.container.style.position = \'absolute\';
                w.content.style.border = "1px solid #000000";
                w.content.style.backgroundColor = "#bababa";

                w.content.style.padding = "'.$windowpadding.'px";
                w.content.style.width = "'.$windowdelka.'px";

                var pos1 = SZN.Dom.getBoxPosition(SZN.gEl(\'windowPlaceholder\'));
                
		//w.container.style.top = pos1.top+\'px\';
		w.container.style.top = \''.$windowtop.'px\';

                w.container.style.left = \''.$windowleft.'px\';

                SZN.gEl(\'windowPlaceholder\').appendChild(w.container);

                if (windowShow) {
                        windowShow = false;
                        w.hide();
                } else {
                        windowShow = true;
                        w.show();
                }

        }


</script>';
    
?>
