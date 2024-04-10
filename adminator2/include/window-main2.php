<?

echo '<script type="text/javascript" >

        var windowShow2 = false;
        var w2 = null;

        function showWindow2() {
                if (w2 == null)
                {

                        //vytvoreni okna
                        var params = {imagePath: "/include/js/window/img/shadow-", sizes: [4,4,4,4]};
                        w2 = new SZN.Window(params);
                        //nastaveni obsahu
                        var txt = SZN.cTxt("'.$windowtext2.' ");
			var button = SZN.cEl(\'input\',\'closeButton\');
                        button.type="button";
                        button.value="OK";
                        button.onclick=showWindow2;
                        w2.content.appendChild(txt);
                        w2.content.appendChild(button);

                        //window.cache[window.cache.length] = w;
                }

                w2.container.style.position = \'absolute\';
                w2.content.style.border = "1px solid #000000";
                w2.content.style.backgroundColor = "#bababa";

                w2.content.style.padding = "'.$windowpadding2.'px";
                w2.content.style.width = "'.$windowdelka2.'px";

                var pos1 = SZN.Dom.getBoxPosition(SZN.gEl(\'windowPlaceholder2\'));
                
		//w.container.style.top = pos1.top+\'px\';
		w2.container.style.top = \''.$windowtop2.'px\';

                w2.container.style.left = \''.$windowleft2.'px\';

                SZN.gEl(\'windowPlaceholder2\').appendChild(w2.container);

                if (windowShow2) {
                        windowShow2 = false;
                        w2.hide();
                } else {
                        windowShow2 = true;
                        w2.show();
                }

        }


</script>';
    
?>
