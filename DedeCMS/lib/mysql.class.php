<?php
define ("MYSQL_HOST", "localhost");
define ("MYSQL_USER", "root");
define ("MYSQL_PWD","212ddc8fe8");
define ("MYSQL_DB","m_120gw_com");
define ("MYSQL_CHARSET","UTF8");

class MySQL{
    private $mysqli;
    public function __construct(){
        $this->mysqli = new MySQLi(MYSQL_HOST,MYSQL_USER,MYSQL_PWD,MYSQL_DB);
        $this->mysqli->query("set names ".MYSQL_CHARSET);
    }
    public function query($sql){
        $res = $this->mysqli->query($sql);
        $data = array();
        while($row = $res->fetch_assoc()){
            $data[] = $row;
        }
        return $data;
    }


    public function close(){
        $this->mysqli->close();
    }



}
