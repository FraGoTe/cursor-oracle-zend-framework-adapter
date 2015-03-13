<?php

/**
 * @see Zend_Db_Adapter_Pdo_Oci
 */
require_once 'Zend/Db/Adapter/Oracle.php';

class Zend_Db_Adapter_Cds extends Zend_Db_Adapter_Oracle
{
    /**
     * This function is to fetch a cursor
     * with the name cursor
     * @param string $sql
     * @param array $bind
     * @return array
     * @throws Zend_Db_Adapter_Oracle_Exception
     */
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

        oci_execute($stmt, $this->_getExecuteMode());
        if ($e = oci_error($stmt)) {
            throw new Zend_Db_Adapter_Oracle_Exception($e, -1234);
        }
        
        oci_execute($curs, $this->_getExecuteMode());
        if (oci_fetch_all($curs, $data, 0, -1, OCI_FETCHSTATEMENT_BY_ROW)) {
            ;//var_dump($data);
        }
        
        oci_free_statement($stmt);
        oci_free_statement($curs);
        
        return $data;
    }
    
    /**
     * This function allows to fetch cursors
     * @param string $sql
     * @param array $cursorsName
     * @param array $bind
     * @param integer $executeMode
     * @return array
     * @throws Zend_Db_Adapter_Oracle_Exception
     */
    public function fetchCursors($sql, $cursorsName, $bind = array(), $executeMode = null)
    {
        $data = array();
        $conn = $this->getConnection();
        
        $stmt = oci_parse($conn, $sql);
        
        $cursors = array();
        foreach ($cursorsName as $key => $cursorName) {
            $cursor[$key] = oci_new_cursor($conn);
            oci_bind_by_name($stmt, $cursorName, $cursor[$key], -1, OCI_B_CURSOR);
        }
        
        foreach ($bind as $key => &$val) {
            oci_bind_by_name($stmt, $key, $val, -1, OCI_ASSOC);
        }
        
        if ($executeMode === null) {
            $executeMode = $this->_getExecuteMode();
        }
        
        
        oci_execute($stmt, $executeMode);
        if ($e = oci_error($stmt)) {
            throw new Zend_Db_Adapter_Oracle_Exception($e, -1234);
        }
        
        foreach ($cursorsName as $key => $cursorName) {
            oci_execute($cursor[$key], $executeMode);
            if (oci_fetch_all($cursor[$key], $data[], 0, -1, OCI_FETCHSTATEMENT_BY_ROW)) {
                ;//var_dump($data);
            }
            
            oci_free_statement($cursor[$key]);
        }
        oci_free_statement($stmt);
        
        return $data;
    }
}
