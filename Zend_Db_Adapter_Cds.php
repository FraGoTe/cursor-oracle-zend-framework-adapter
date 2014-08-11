<?php

/**
 * @see Zend_Db_Adapter_Pdo_Oci
 */
require_once 'Zend/Db/Adapter/Oracle.php';

class Zend_Db_Adapter_Cds extends Zend_Db_Adapter_Oracle
{
    public function fetchCursor($sql, $bind = array())
    {
        $data = array();
        $conn = $this->getConnection();
        
        $curs = oci_new_cursor($conn);
        $stmt = oci_parse($conn, $sql);
        
        oci_bind_by_name($stmt, "cursor", $curs, -1, OCI_B_CURSOR);
        foreach ($bind as $key => &$val) {
            oci_bind_by_name($stmt, $key, $val, -1, OCI_ASSOC);
        }

        oci_execute($stmt);
        if ($e = oci_error($stmt)) {
            throw new Zend_Db_Adapter_Oracle_Exception($e, -1234);
        }
        
        oci_execute($curs);
        if (oci_fetch_all($curs, $data, 0, -1, OCI_FETCHSTATEMENT_BY_ROW)) {
            ;//var_dump($data);
        }
        
        oci_free_statement($stmt);
        oci_free_statement($curs);
        
        return $data;
    }
}
