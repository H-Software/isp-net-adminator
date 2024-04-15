<?php

namespace App\Core;

class ArchivZmen {

    var $conn_mysql;
    var $smarty;
    var $logger;

    public function __construct($conn_mysql, $smarty, $logger)
    {
        $this->conn_mysql = $conn_mysql;
        $this->smarty = $smarty;
        $this->logger = $logger;
        
        $this->logger->addInfo("topology\__construct called");
    }
    
    function archivZmenWork()
    {
        $output = "";

        $pocet=$_GET["pocet"];
    
        $output .= "<div style=\"padding-left: 5px; padding-top: 10px; \">";
        
        $output .= "<div style=\" padding-bottom: 10px; padding-right: 40px; font-size: 18px; font-weight: bold; float: left; \" >";
        $output .= " Archiv změn Work (restartování)</div>";
    
        $output .= "<div style=\" \" ><form method=\"GET\" action=\"\" >";
        
        $output .= "<span style=\"margin-right: 20px; \" ><label>Vyberte počet záznamů: </label></span>
        
                    <select name=\"pocet\" size=\"1\" >
                        <option value=\"1\" "; if ($pocet == "1" or !isset($pocet) ){ $output .= " selected "; } $output .= " >1</option>
                        <option value=\"3\" "; if( $pocet == "3" ){ $output .= " selected "; } $output .= " >3</option>
                        <option value=\"5\""; if( $pocet == "5" ){ $output .= " selected "; } $output .= " >5</option>
                    </select>";

        $output .= "<span style=\"margin-left: 10px; \"><input type=\"submit\" name=\"odeslano\" value=\"OK\" ></span>
        
                    <!-- <span style=\"margin-left: 40px; \"><a href=\"include\export-archiv-zmen.php\">export dat zde</a></span> -->
                    
                    </form></div>";
    
        $output .= "</div>"; //konec hlavni divu
        
        $pocet_check=ereg('^([[:digit:]]+)$',$pocet);
        
        $zaklad_sql = "select *,DATE_FORMAT(provedeno_kdy, '%d.%m.%Y %H:%i:%s') as provedeno_kdy2 from archiv_zmen_work ";
        
        if( ($pocet_check) )
        {   
            if ( ( strlen($pocet) > 0 ) )
            { $sql=$zaklad_sql." order by id DESC LIMIT $pocet "; }
            else
            { $sql=$zaklad_sql." order by id DESC "; }
        }
        else
        { $sql=$zaklad_sql." order by id DESC LIMIT 1 "; }

        try {
            $vysl = $this->conn_mysql->query($sql);
        } catch (Exception $e) {
            // die ("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
        }

        $radku = $vysl->num_rows;
        
        // echo '<br><a href="include\export-archiv-zmen.php">export dat zde</a><br><br>';     
        
        if ( $radku==0 )
        { $output .= "<div class=\"alert alert-warning\" role=\"alert\" style=\"margin-top: 15px; margin-bottom: 15px;\">Žádné změny v archivu</div>"; }
        else
        {
            $output .= "<table width=\"100%\" border=\"0\" cellpadding=\"5\" class=\"az-main-table\" >";
                
            $output .= "<tr >";    
                $output .= "<td class=\"az-border2\" ><b>id:</b></td>";
                $output .= "<td class=\"az-border2\" ><b>akce:</b></td>";
            // $output .= "<td><b>pozn:</b></td>";
                $output .= "<td class=\"az-border2\" ><b>Provedeno kdy:</b></td>";
                $output .= "<td class=\"az-border2\" ><b>Provedeno kým:</b></td>";
                $output .= "<td class=\"az-border2\" ><b>Provedeno úspěšně:</b></td>";
            $output .= "</tr>";
                
            while ($data=$vysl->fetch_array()):
                
                $output .= "<tr>";    
                    $output .= "<td class=\"az-border1\" style=\"vertical-align: top;\" >".$data["id"]."</td>";
                $output .= "<td class=\"az-border1\" ><span class=\"az-text\" >";
            
                $id_cloveka_res = "";  
                $akce = $data["akce"];
                
                $output .= "<pre>".$akce."</pre></span></td>";
            
                $output .= "<td class=\"az-border1\" style=\"vertical-align: top;\"><span class=\"az-provedeno-kdy\" >";
                if ( ( strlen($data["provedeno_kdy2"]) < 1 ) ){ $output .= "&nbsp;"; }
                else{ echo $data["provedeno_kdy2"]; }
                $output .= "</span></td>";
                
                $output .= "<td class=\"az-border1\" style=\"vertical-align: top;\"><span class=\"az-provedeno-kym\" >";
                if ( ( strlen($data["provedeno_kym"]) < 1 ) ){ $output .= "&nbsp;"; }
                else{ echo $data["provedeno_kym"]; }
                $output .= "</span></td>";		   
            
                $output .= "<td class=\"az-border1\" style=\"vertical-align: top;\">";
                if ( $data["vysledek"] == 1 ){ $output .= "<span class=\"az-vysl-ano\">Ano</span>"; }
                else{ $output .= "<span class=\"az-vysl-ne\">&nbsp;</span>"; }
                $output .= "</td>";
            
                $output .= "</tr>";
                
            endwhile;
                
            $output .= "</table>";
            
        } //konec else
    
        return $output;
    }
}