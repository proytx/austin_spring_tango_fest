<?php
/**
 * Object Relational Mapping
 * Root of all classes that map database tables, queries field names from information schema to be in lock step
 * Ground rule - The name of the derived class has to match the table name except the 'tbl' replaced by 'cls'
 * The constructor uses a $strEnvironment to switch to production or test database
 * As a minimum, getAll and getOneByPrimaryKey should be expected of all classses
 * TODO - This class should be able to query the database and figure out its foreign key dependencies
 *        i.e., which classes it needs to invoke to get those. e.g Registration needs Email, Choices, Address
 *
 * @author     Partha Roy
 * @version    v1.0.0 8/23/2013 11:34:00 AM
 */

include 'ASTFdatabase.php';

abstract class clsObjectRelationalMapper
{
    // all child classes should be able to access the name of its fields, hence protected
    protected $m_arrFields; 
    protected $m_objDatabase;
    protected $m_strTableName;

    public function __construct($strEnvironment) {
        $this->m_objDatabase = new ASTFdatabase($strEnvironment);
        $this->strTableName = str_replace('cls','tbl', get_class($this));
        $this->m_arrFields = $this->m_objDatabase->getDescription($this->strTableName);
    } //end constructor

    public function getNumberOfConnections() {
        return $this->m_objDatabase->getNumberOfConnections(); 
    }
   
    /** * Simple function to print fields, if done in constructor, still empty * */ 
    public function showDescription() { return ($this->m_arrFields); }

    /** * Function to get all entries in a table
    * 
    */
    public function getAll()
    {
        return $this->m_objDatabase->getAll($this->getFieldNames(), $this->strTableName);
    }

    /**
    * Private function to extract fieldnames
    * 
    */
    private function getFieldNames()
    {
        $arrFieldNames = array();

        foreach ($this->m_arrFields as $key => $val) 
        {
            $arrFieldNames[] = $val['Field'];
        }
        return $arrFieldNames;
    }
} //class clsObjectRelationalMapping ends
