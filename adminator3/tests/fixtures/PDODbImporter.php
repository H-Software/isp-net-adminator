<?php

// https://gist.github.com/b4oshany/0698d9f32589b77abdcb

class PDODbImporter{
    private static $keywords = array(
        'ALTER', 'CREATE', 'DELETE', 'DROP', 'INSERT',
        'REPLACE', 'SELECT', 'SET', 'TRUNCATE', 'UPDATE', 'USE',
        'DELIMITER', 'END'
    );

    public static function loadFromFile($pdo, $file)
    {
        try {
            $affectedRows = $pdo->exec("
            LOAD DATA LOCAL INFILE ".$pdo->quote($file)." INTO TABLE ".
                    $pdo->quote($dbTable)." FIELDS TERMINATED BY ';'
                    LINES TERMINATED BY '\n,\\n'");
        
            $mex= "Loaded a total of $affectedRows records from this csv file.\n";
            return $mex;
        
        } 
        catch (PDOException $e) {
                $mex= "ATTENZIONE, ERRORE CARICAMENTO DATI: ".$e->getMessage();
                return $mex;
        }
    }

    /**
     * Loads an SQL stream into the database one command at a time.
     *
     * @params $sqlfile The file containing the mysql-dump data.
     * @params $connection Instance of a PDO Connection Object.
     * @return boolean Returns true, if SQL was imported successfully.
     * @throws Exception
     */
    public static function importSQL($sqlfile, $connection)
    {    

        # read file into array
        $file = file($sqlfile);

        # import file line by line
        # and filter (remove) those lines, beginning with an sql comment token
        // $file = array_filter($file,
        //                 create_function('$line',
        //                         'return strpos(ltrim($line), "--") !== 0;'));
        $file = array_filter($file, function ($line) {
            return strpos(ltrim($line), "--") !== 0;
        });
        # and filter (remove) those lines, beginning with an sql notes token
        // $file = array_filter($file,
        //                 create_function('$line',
        //                         'return strpos(ltrim($line), "/*") !== 0;'));
        $file = array_filter($file, function ($line) {
            return strpos(ltrim($line), "/*") !== 0;
        });
        $sql = "";
        $del_num = false;
        foreach($file as $line){
            $query = trim($line);
            try
            {
                $delimiter = is_int(strpos($query, ";"));
                // if($delimiter || $del_num){
                //     if($delimiter && !$del_num ){
                //         $sql = "";
                //         $sql = $query."; ";
                //         echo "OK";
                //         echo "<br/>\n";
                //         echo "---";
                //         echo "<br/>\n";
                //         $del_num = true;
                //     }else if($delimiter && $del_num){
                //         $sql .= $query." ";
                //         $del_num = false;
                //         echo $sql;
                //         echo "<br/>\n";
                //         echo "do---do";
                //         echo "<br/>\n";
                //         $connection->exec($sql);
                //         $sql = "";
                //     }else{                            
                //         $sql .= $query."; ";
                //     }
                // }else
                {
                    $delimiter = is_int(strpos($query, ";"));
                    if($delimiter){
                        echo "$sql $query";
                        $connection->exec("$sql $query");

                        echo "<br/>\n";
                        echo "---";
                        echo "<br/>\n";
                        $sql = "";
                    }else{
                        $sql .= " $query";
                    }
                }
            }
            catch (\Exception $e)
            {
                echo "\n" . $e->getMessage() . "<br />"; // <p>The sql is: $query</p>";
            }
            
        }
    }
}
