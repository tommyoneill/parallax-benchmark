<?php
class DataPDO {
    private $dsn = 'mysql:unix_socket=/opt/cloudsql/parallax-benchmark:us-central1:parallax-benchmark';
    private $dbname = 'parallax-benchmark';
    private $user = 'parallax-benchmark';
    private $pass = '474Arnette***';
    private $charset = '';
    protected $dbh;

    public function __construct(){
        try{
            $this->dbh = new PDO($this->dsn.';dbname=parallax-benchmark', $this->user, $this->pass);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch (PDOException $exception){
           var_export($exception);
        }    
    }

    public function getVersion(){
        try{
            $stm = $this->dbh->query("SELECT VERSION()");
            $version = $stm->fetch();
            echo $version[0] . PHP_EOL;
        }catch (PDOException $exception){
           var_export($exception);
        }
    }

    public function truncate($table){
        try{
            $stm = $this->dbh->query("TRUNCATE $table;");
        }catch (PDOException $exception){
           var_export($exception);
        }
    }

    public function query($sql='')
    {
        if(isset($sql) && $sql != null && $sql != ''){
            try{
                $stmt = $this->dbh->query($sql);
                if(is_a($stmt,'PDOStatement')){
                    return $stmt->fetchAll();
                }else{
                    throw new Exception("MySQL Error (".$this->dbh->errorInfo()[2].")");
                }
            }catch (Exception $e){
                throw new Exception("Local Data Base Error: - ".$e->getMessage());
            }
        }
    }

}