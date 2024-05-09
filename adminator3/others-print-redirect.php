<?php

$soubor = htmlspecialchars($_POST["soubory"]);

header("Location: print/temp/".$soubor);
