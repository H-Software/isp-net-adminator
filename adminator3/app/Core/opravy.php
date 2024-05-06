<?php

class opravy
{
    public $conn_mysql;

    public $conn_pgsql;

    public $logger;

    public $vypis_opravy_content_html;

    public function __construct($conn_mysql, $conn_pgsql, $logger)
    {
        $this->conn_mysql = $conn_mysql;
        $this->conn_pgsql = $conn_pgsql;
        $this->logger = $logger;
    }

    public function vypis_opravy($pocet_bunek)
    {
        $this->logger->info("opravy\\vypis_opravy called");
        $this->logger->info("opravy\\vypis_opravy: script_url: ".$_SERVER['SCRIPT_URL']);
        $ret = array();
        $this->vypis_opravy_content_html = "";

        $v_reseni_filtr = $_GET["v_reseni_filtr"];
        $vyreseno_filtr = $_GET["vyreseno_filtr"];

        $limit = $_GET["limit"];

        if (!isset($v_reseni_filtr)) {
            $v_reseni_filtr = "99";
        }
        if (!isset($vyreseno_filtr)) {
            $vyreseno_filtr = "0";
        }

        if (!isset($limit)) {
            $limit = "10";
        }

        $sql = "SELECT * FROM opravy WHERE ( id_opravy > 0 ";

        $order = " ORDER BY datum_vlozeni DESC ";

        $sf = $sql." ) ".$order;

        try {
            $dotaz = $this->conn_mysql->query($sf);
        } catch (Exception $e) {
            $this->logger->error('opravy\vypis_opravy mysql_query dotaz failed! Caught exception: ' . $e->getMessage());

            $ret[0] = "<tr><td colspan=\"".$pocet_bunek."\" >Error: Database query failed! Caught exception: " . $e->getMessage() . "</td></tr>";
            return $ret;
        }

        $dotaz_radku = $dotaz->num_rows;

        if ($dotaz_radku == 0) {
            $ret[0] = "<tr><td colspan=\"".$pocet_bunek."\" >Žádné opravy v databázi neuloženy. </td></tr>";
            return $ret;
        }

        $this->logger->info("opravy\\vypis_opravy: mysql query dotaz: num_rows: " . var_export($dotaz_radku, true));

        $zobrazeno_limit = "0";

        while($data = $dotaz->fetch_array()) {

            if($zobrazeno_limit >= $limit) {
                // prozatimni reseni limitu

                $exit = "ano";

            }

            $zobrazovat = "ne";

            $zobrazeno = "ne";
            $sekundarni_show = "ne";

            $id_opravy = $data["id_opravy"];

            try {
                $dotaz_S1 = $this->conn_mysql->query("SELECT * FROM opravy WHERE id_predchozi_opravy = '" . intval($id_opravy) . "' ");
                $dotaz_radku_S1 = $dotaz_S1->num_rows;
            } catch (Exception $e) {
                $this->logger->error("opravy\vypis_opravy mysql_query dotaz_S1 failed! Caught exception: " . $e->getMessage());
                die(init_helper_base_html("adminator3") . "<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
            }

            $this->logger->info("opravy\\vypis_opravy: mysql query dotaz_S1: num_rows: " . var_export($dotaz_radku_S1, true));

            // zde zjistit jestli uz se zobrazilo
            if(!empty($zobrazene_polozky)) {
                for ($p = 0; $p < count($zobrazene_polozky); ++$p) {
                    if($zobrazene_polozky[$p] == $id_opravy) {
                        $zobrazeno = "ano";
                    } else {
                        $zobrazeno = "ne";
                    }
                }
            }

            if($v_reseni_filtr == 1) {
                if ((($data["v_reseni"] == 1) and ($dotaz_radku_S1 == 0))) {
                    $zobrazovat = "ano";
                } elseif ($dotaz_radku_S1 > 0) {
                    while($data_S1 = $dotaz_S1->fetch_array()) {
                        if ($data_S1["v_reseni"] == 1) {
                            $zobrazovat = "ano";
                            $sekundarni_show = "ano";
                        }
                    }
                }
            } // konec if v_reseni_filtr == 1
            elseif($v_reseni_filtr == 0) {
                if((($data["v_reseni"] == 0) and ($dotaz_radku_S1 == 0))) {
                    $zobrazovat = "ano";
                } elseif ($dotaz_radku_S1 > 0) {
                    while($data_S1 = $dotaz_S1->fetch_array()) {
                        if ($data_S1["v_reseni"] == 0) {
                            $zobrazovat = "ano";
                            $sekundarni_show = "ano";
                        } else {
                            $zobrazovat = "ne";
                            $sekundarni_show = "ne";
                        }
                    }
                } else {
                    $zobrazovat = "ne";
                }

            } // konec elseif v_reseni_filtr == 0


            if($vyreseno_filtr == 1) {
                // prvne zjistime jestli jde o singl prispevek bo jestli je jich vic
                if ((($data["vyreseno"] == 1) and ($dotaz_radku_S1 == 0))) {
                    $zobrazovat = "ano";
                } elseif($dotaz_radku_S1 > 0) {
                    while($data_S1 = $dotaz_S1->fetch_array()) {
                        if ($data_S1["vyreseno"] == 1) {
                            $zobrazovat = "ano";
                            $sekundarni_show = "ano";
                        }
                    }

                } else {
                    $zobrazovat = "ne";
                }

            } // konec if vyreseno_filtr == 0
            elseif ($vyreseno_filtr == 0) {
                // prvne zjistime jestli jde o singl prispevek bo jestli je jich vic
                if ((($data["vyreseno"] == 0) and ($dotaz_radku_S1 == 0))) {
                    $zobrazovat = "ano";
                } elseif($dotaz_radku_S1 > 0) {
                    while($data_S1 = $dotaz_S1->fetch_array()) {
                        if ($data_S1["vyreseno"] == 1) {
                            $zobrazovat = "ne";
                            $sekundarni_show = "ne";
                        } else {
                            $zobrazovat = "ano";
                            $sekundarni_show = "ano";
                        }

                    }// konec while

                }// konec elseif dotaz_radku_S1

            } // konec elseif vyreseno_filtr == 0
            else {
                $this->logger->warning("opravy\\vypis_opravy: vyreseno_filtr not set! ");
            }

            if (($v_reseni_filtr == 99 and $vyreseno_filtr == 99)) {
                $zobrazovat = "ano";
            }

            if(($zobrazovat == "ano" and $zobrazeno == "ne" and $exit != "ano")) {

                $zobrazene_polozky[] = $data["id_opravy"];

                $zobrazeno_limit++;

                $class = "opravy-tab-line4";

                // zde zjistit jestli uz se vyresilo
                if($dotaz_radku_S1 == 0) { // rezim singl problemu
                    if ($data["vyreseno"] == 1) {
                        $barva = "green";
                    } elseif ($data["v_reseni"] == 1) {
                        $barva = "orange";
                    } else {
                        $barva = "red";
                    }

                } // if dotaz_radku_S1 == 0
                else {
                    while($data_S1 = $dotaz_S1->fetch_array()) {
                        if($data_S1["vyreseno"] == 1) {
                            $barva = "green";
                        } elseif($data_S1["v_reseni"] == 1) {
                            $barva = "orange";
                        } else {
                            $barva = "red";
                        }
                    }
                }

                //    $barva="red";

                $this->vypis_opravy_content_html .= "<tr>
                  <td class=\"".$class."\" style=\" color: ".$barva."; \" >".$data["id_opravy"]."</td>
                  <td class=\"".$class."\" style=\" color: ".$barva."; \" >".$data["id_predchozi_opravy"]."</td>
                  <td class=\"".$class."\" style=\" color: ".$barva."; \" >";

                $id_cloveka = $data["id_vlastnika"];

                $vlastnik_dotaz = pg_query($this->conn_pgsql, "SELECT * FROM vlastnici WHERE id_cloveka = '" . intval($id_cloveka) . "'");
                $vlastnik_radku = pg_num_rows($vlastnik_dotaz);

                $this->logger->info("opravy\\vypis_opravy: pq query vlastnik_dotaz: num_rows: " . var_export($vlastnik_radku, true));

                while ($data_vlastnik = pg_fetch_array($vlastnik_dotaz)) {
                    $firma_vlastnik = $data_vlastnik["firma"];
                    $archiv_vlastnik = $data_vlastnik["archiv"];
                    $popis_vlastnika = " ".$data_vlastnik["prijmeni"]." ".$data_vlastnik["jmeno"].", ";
                    $popis_vlastnika .= $data_vlastnik["ulice"]." ".$data_vlastnik["mesto"].", ";
                    $popis_vlastnika .= "VS: ".$data_vlastnik["vs"]." ";
                }

                if ($archiv_vlastnik == 1) {
                    $this->vypis_opravy_content_html .= "<a href=\"". fix_link_to_another_adminator("/vlastnici-archiv.php?find_id=".$data["id_vlastnika"])."\" ";
                } elseif ($firma_vlastnik == 1) {
                    $this->vypis_opravy_content_html .= "<a href=\"". fix_link_to_another_adminator("/vlastnici2.php?find_id=".$data["id_vlastnika"])."\" ";
                } else {
                    $this->vypis_opravy_content_html .= "<a href=\"". fix_link_to_another_adminator("/vlastnici.php?find_id=".$data["id_vlastnika"])."\" ";
                }

                $this->vypis_opravy_content_html .= "title=\"Detail vlastníka: ".$popis_vlastnika."\" >".$data["id_vlastnika"]."</a> \n\n";

                $this->vypis_opravy_content_html .= "</td>
                    <td class=\"".$class."\" style=\" color: ".$barva."; \" >".$data["datum_vlozeni"]."</td>
                    <td class=\"".$class."\" style=\" color: ".$barva."; \" >";

                if ($data["priorita"] == 0) {
                    $this->vypis_opravy_content_html .= "Nízká";
                } elseif ($data["priorita"] == 1) {
                    $this->vypis_opravy_content_html .= "Normální";
                } elseif ($data["priorita"] == 2) {
                    $this->vypis_opravy_content_html .= "Vysoká";
                } else {
                    $this->vypis_opravy_content_html .= "Nelze zjistit";
                }

                $this->vypis_opravy_content_html .= "</td>
              <td class=\"".$class."\" style=\" color: ".$barva."; \" >";
                if ($data["v_reseni"] == 0) {
                    $this->vypis_opravy_content_html .= "Ne";
                } elseif ($data["v_reseni"] == 1) {
                    $this->vypis_opravy_content_html .= "Ano (".$data["v_reseni_kym"].") ";
                } else {
                    $this->vypis_opravy_content_html .= "Nelze zjistit";
                }

                $this->vypis_opravy_content_html .= "</td>
              <td class=\"".$class."\" style=\" color: ".$barva."; \" >";
                if ($data["vyreseno"] == 0) {
                    $this->vypis_opravy_content_html .= "Ne";
                } elseif ($data["vyreseno"] == 1) {
                    $this->vypis_opravy_content_html .= "Ano (".$data["vyreseno_kym"].") ";
                } else {
                    $this->vypis_opravy_content_html .= "Nelze zjistit";
                }

                $this->vypis_opravy_content_html .= "</td>
              <td class=\"".$class."\" style=\" color: ".$barva."; \" >";
                if ((strlen($data["vlozil"]) > 1)) {
                    $this->vypis_opravy_content_html .= $data["vlozil"];
                } else {
                    $this->vypis_opravy_content_html .= "<br>";
                }

                $this->vypis_opravy_content_html .= "</td>";

                $this->vypis_opravy_content_html .= "<td class=\"".$class."\" style=\" color: ".$barva."; \" >
                <a href=\"". fix_link_to_another_adminator("/opravy-zacit-resit.php?id_opravy=".$data["id_opravy"]) . "\" >začít řešit</a></td>";

                $this->vypis_opravy_content_html .= "<td class=\"".$class."\" style=\" color: ".$barva."; \" ><a href=\"".
                                                       fix_link_to_another_adminator("/opravy-index.php?typ=1&id_vlastnika=".$data["id_vlastnika"]);

                if($data["id_predchozi_opravy"] == 0) {
                    $this->vypis_opravy_content_html .= "&id_predchozi_opravy=".$data["id_opravy"];
                } else {
                    $this->vypis_opravy_content_html .= "&id_predchozi_opravy=".$data["id_predchozi_opravy"];
                }

                $this->vypis_opravy_content_html .= "\" >vložit odpověď</a></td>";

                $this->vypis_opravy_content_html .= "</tr>";

                $this->vypis_opravy_content_html .= "<tr><td colspan=\"".$pocet_bunek."\" class=\"opravy-tab-line3\" >".$data["text"]."</td></tr>";

            } // konec if zobrazovat == ano

            if(($sekundarni_show == "ano" and $zobrazeno == "ne")) {

                // $zobrazene_polozky[]=$id_opravy;
                try {
                    $dotaz_S2 = $this->conn_mysql->query("SELECT * FROM opravy WHERE id_predchozi_opravy = '" . intval($id_opravy) . "' ");
                } catch (Exception $e) {
                    die(init_helper_base_html("adminator3") . "<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
                }

                $this->logger->info("opravy\\vypis_opravy: mysql query dotaz_S2: num_rows: " . var_export($dotaz_S2->num_rows, true));

                while($data_S2 = $dotaz_S2->fetch_array()) {

                    // zde zjistit jestli uz se zobrazilo
                    for ($p = 0; $p < count($zobrazene_polozky); ++$p) {
                        if($zobrazene_polozky[$p] == $id_opravy) {
                            $zobrazeno = "ano";
                        } else {
                            $zobrazeno = "ne";
                        }
                    }

                    $zobrazene_polozky[] = $data_S2["id_opravy"];

                    $id_opravy_S3 = $data_S2["id_opravy"];

                    try {
                        $dotaz_S3 = $this->conn_mysql->query("SELECT * FROM opravy WHERE id_predchozi_opravy = '" . intval($id_opravy_S3)."' ");
                    } catch (Exception $e) {
                        die(init_helper_base_html("adminator3") . "<h2 style=\"color: red; \">Error: Database query_S3 failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
                    }

                    $dotaz_radku_S3 = $dotaz_S3->num_rows;
                    $this->logger->info("opravy\\vypis_opravy: mysql query dotaz_S3: num_rows: " . var_export($dotaz_radku_S3, true));

                    // neni jiste jestli barveni ma bejt zde

                    // zde zjistit jestli uz se vyresilo
                    if($dotaz_radku_S3 == 0) { // rezim singl problemu
                        if ($data_S2["vyreseno"] == 1) {
                            $barva = "green";
                        } elseif ($data_S2["v_reseni"] == 1) {
                            $barva = "orange";
                        } else {
                            $barva = "red";
                        }

                    } // if dotaz_radku_S1 == 0
                    else {
                        while($data_S3 = $dotaz_S3->fetch_array()) {
                            if($data_S3["vyreseno"] == 1) {
                                $barva = "green";
                            } elseif($data_S3["v_reseni"] == 1) {
                                $barva = "orange";
                            } else {
                                $barva = "red";
                            }
                        }
                    }

                    if ($zobrazeno == "ne" and $exit != "ano") {

                        $zobrazeno_limit++;

                        $this->vypis_opravy_content_html .= "<tr>
                        <td class=\"".$class."\" style=\" color: ".$barva."; \" >".$data_S2["id_opravy"]."</td>
                        <td class=\"".$class."\" style=\" color: ".$barva."; \" >".$data_S2["id_predchozi_opravy"]."</td>
                        <td class=\"".$class."\" style=\" color: ".$barva."; \" >";

                        $id_cloveka = $data["id_vlastnika"];

                        $vlastnik_dotaz = pg_query("SELECT * FROM vlastnici WHERE id_cloveka = '$id_cloveka'");
                        $vlastnik_radku = pg_num_rows($vlastnik_dotaz);

                        while ($data_vlastnik = pg_fetch_array($vlastnik_dotaz)) {
                            $firma_vlastnik = $data_vlastnik["firma"];
                            $archiv_vlastnik = $data_vlastnik["archiv"];
                            $popis_vlastnika = " ".$data_vlastnik["prijmeni"]." ".$data_vlastnik["jmeno"].", ";
                            $popis_vlastnika .= $data_vlastnik["ulice"]." ".$data_vlastnik["mesto"].", ";
                            $popis_vlastnika .= "VS: ".$data_vlastnik["vs"]." ";
                        }

                        if ($archiv_vlastnik == 1) {
                            $this->vypis_opravy_content_html .= "<a href=\"" . fix_link_to_another_adminator("/vlastnici-archiv.php?find_id=".$data_S2["id_vlastnika"]) . "\" ";
                        } elseif ($firma_vlastnik == 1) {
                            $this->vypis_opravy_content_html .= "<a href=\"" . fix_link_to_another_adminator("/vlastnici2.php?find_id=".$data_S2["id_vlastnika"]) . "\" ";
                        } else {
                            $this->vypis_opravy_content_html .= "<a href=\"" . fix_link_to_another_adminator("/vlastnici.php?find_id=".$data_S2["id_vlastnika"]) . "\" ";
                        }

                        $this->vypis_opravy_content_html .= "title=\"Detail vlastníka: ".$popis_vlastnika."\" >".$data_S2["id_vlastnika"]."</a> \n\n";

                        $this->vypis_opravy_content_html .= "</td>";
                        // $this->vypis_opravy_content_html .= "<td class=\"".$class."\" >".$data_S2["text"]."</td>";
                        $this->vypis_opravy_content_html .= "<td class=\"".$class."\" style=\" color: ".$barva."; \" >".$data_S2["datum_vlozeni"]."</td>
                      <td class=\"".$class."\" style=\" color: ".$barva."; \" >";

                        if ($data_S2["priorita"] == 0) {
                            $this->vypis_opravy_content_html .= "Nízká";
                        } elseif ($data_S2["priorita"] == 1) {
                            $this->vypis_opravy_content_html .= "Normální";
                        } elseif ($data_S2["priorita"] == 2) {
                            $this->vypis_opravy_content_html .= "Vysoká";
                        } else {
                            $this->vypis_opravy_content_html .= "Nelze zjistit";
                        }

                        $this->vypis_opravy_content_html .= "</td>
                      <td class=\"".$class."\" style=\" color: ".$barva."; \" >";
                        if ($data_S2["v_reseni"] == 0) {
                            $this->vypis_opravy_content_html .= "Ne";
                        } elseif ($data_S2["v_reseni"] == 1) {
                            $this->vypis_opravy_content_html .= "Ano (".$data_S2["v_reseni_kym"].") ";
                        } else {
                            $this->vypis_opravy_content_html .= "Nelze zjistit";
                        }

                        $this->vypis_opravy_content_html .= "</td>
                      <td class=\"".$class."\" style=\" color: ".$barva."; \" >";
                        if ($data_S2["vyreseno"] == 0) {
                            $this->vypis_opravy_content_html .= "Ne";
                        } elseif ($data_S2["vyreseno"] == 1) {
                            $this->vypis_opravy_content_html .= "Ano (".$data_S2["vyreseno_kym"].") ";
                        } else {
                            $this->vypis_opravy_content_html .= "Nelze zjistit";
                        }

                        $this->vypis_opravy_content_html .= "</td>
                      <td class=\"".$class."\" style=\" color: ".$barva."; \" >";
                        if ((strlen($data_S2["vlozil"]) > 1)) {
                            $this->vypis_opravy_content_html .= $data_S2["vlozil"];
                        } else {
                            $this->vypis_opravy_content_html .= "<br>";
                        }

                        $this->vypis_opravy_content_html .= "</td>";

                        $this->vypis_opravy_content_html .= "<td class=\"".$class."\" style=\" color: ".$barva."; \" >
                      <a href=\"". fix_link_to_another_adminator("/opravy-zacit-resit.php?id_opravy=".$data_S2["id_opravy"]) . "\" >začít řešit</a></td>";

                        $this->vypis_opravy_content_html .= "<td class=\"".$class."\" style=\" color: ".$barva."; \" ><a href=\"" .
                                                          fix_link_to_another_adminator("/opravy-index.php?typ=1&id_vlastnika=".$data_S2["id_vlastnika"]);

                        if($data_S2["id_predchozi_opravy"] == 0) {
                            $this->vypis_opravy_content_html .= "&id_predchozi_opravy=".$data_S2["id_opravy"];
                        } else {
                            $this->vypis_opravy_content_html .= "&id_predchozi_opravy=".$data_S2["id_predchozi_opravy"];
                        }

                        $this->vypis_opravy_content_html .= "\" >vložit odpověď</a></td>";

                        //    $this->vypis_opravy_content_html .= "<tr><td class=\"".$class."\" colspan=\"\" >".$data_S2["text"]."</td></tr>";

                        $this->vypis_opravy_content_html .= "<tr><td colspan=\"".$pocet_bunek."\" class=\"opravy-tab-line3\" >".$data_S2["text"]."</td></tr>";

                        $this->vypis_opravy_content_html .= "</tr>";

                    } // konec if zobrazeno ne

                } // konec while2

            } // konec if sekundar == 1

        } // konec while 1

        $this->logger->info("opravy\\vypis_opravy: end of main loop");
        // $this->logger->debug("opravy\\vypis_opravy: content " . var_export($this->vypis_opravy_content_html, true));

        $ret = array("", $this->vypis_opravy_content_html);

        return $ret;

    }//konec funkce vypis_opravy

} // konec tridy opravy
