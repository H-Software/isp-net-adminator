<?php

namespace App\Core;

class Topology {
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

    public function getNodeListForForm($search_string, $typ_nodu = 2, $show_zero_value = true)
    {
        $this->logger->addInfo("topology\getNodesFiltered called");

        if($show_zero_value === true)
        {
            $nodes[0] = "Není vybráno";
        }

        $search_string = $this->conn_mysql->real_escape_string($search_string);

        $sql = "SELECT id, jmeno, ip_rozsah from nod_list WHERE ( jmeno LIKE '%$search_string%' ";
        $sql .= " OR ip_rozsah LIKE '%$search_string%' OR adresa LIKE '%$search_string%' ";
        $sql .= " OR pozn LIKE '%$search_string%' ) AND ( typ_nodu = '" . intval($typ_nodu) . "' ) ORDER BY jmeno ASC ";
        
        $rs = $this->conn_mysql->query($sql);
        $num_rows = $rs->num_rows;
    
        if($num_rows < 1)
        {
            $nodes[0] = "nelze zjistit / žádný nod nenalezen";
            return $nodes;
        }
        else
        {
            while ($data = $rs->fetch_array() )
            {
                $nodes[$data['id']] = $data["jmeno"] . " (".$data["ip_rozsah"].")";
            }

            return $nodes;
        }
    }
}