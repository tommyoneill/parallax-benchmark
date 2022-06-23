<?php
class Authorizations extends DataPDO {
    private $id = 0;
    
    //we need this to create a new one
    private $access_token = '';
    private $refresh_token = '';
    private $expiration_date = 0;
    private $scope = '';
    private $code = '';

    private $created_on = '';
    private $updated_on = '';

    public function addAuthorization($access_token='',$refresh_token='',$expires_in=0, $scope='',$code='')
    {
        if(isset($access_token) && $access_token!=''){
            if(isset($refresh_token) && $refresh_token!=''){
                $sql  = "INSERT INTO `authorization` (`access_token`, `refresh_token`, `expiration_date`, `scope`, `code`) " 
                        . "VALUES (:access_token, :refresh_token, :expires, :scope, :code);";
                $sth = $this->dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $sth->execute(
                    array('access_token' => $access_token,
                            'refresh_token' => $refresh_token,
                            'expires' => $this->getTime($expires_in),
                            'scope' => $scope,
                            'code' => $code));

                $this->id = $this->dbh->lastInsertId();
                $this->scope = $scope;
                $this->code = $code;
            }else{

            }
        }else{

        }
        
    }

    public function isAuthorized()
    {
        if($this->id != 0){
            return true;
        }else{
            return false;
        }
    }

    public function getID()
    {
        return $this->id;
    }

    private function getTime($seconds)
    {
        $date = new DateTime();
        $date->add(DateInterval::createFromDateString("$seconds seconds"));
        return $date->format('Y-m-d H:i:s');
    }
}
