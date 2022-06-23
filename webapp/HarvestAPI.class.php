<?php
class HarvestAPI {
    private $base_url = 'https://api.harvestapp.com/v2/';
    private $account_id = '1291813';
    private $token = '1989437.pt.8mr3Gm8cUGZGQKnLEszP_mecc-3BAwtCgvpXYFCkxqWRKuW037QeXvCihqnjWJLk5qPamNombIAARaI1rb1AOA';
    private $app = 'Parallax Benchmark';
    private $client_id = 'sNTAMrvpARppRjLXvPYhOi6z';
    private $client_secret = '2ySe7-M4F-sFtJ0YusAidRwblqWdugSweX21Oo3M16SdJMCyZPguwz6A8_8HblYQ1j0hXSWjuajlKmjpjdVaWQ';

    public function setCredentials($account_id='',$token='')
    {
        $this->account_id = $account_id;
        $this->token = $token;
    }

    public function getProjects(){
        $projects = $this->getObjects('projects');
        return $projects;
    }

    public function getUserAssignments(){
        $usera = $this->getObjects('user_assignments');
        return $usera;
    }

    public function getUsers(){
        $users = $this->getObjects('users');
        return $users;
    }

    private function getObjects($objectname='',$page=1,$params=NULL){

        $url = $this->base_url."$objectname?page=$page";

        if(!is_null($params)){
            $url .= "&".http_build_query($params);
        }

        $jsonResp = $this->curlExec($url);

        if(isset($jsonResp->next_page) && $jsonResp->next_page !== NULL){
            return array_merge($jsonResp->$objectname,$this->getObjects($objectname,$jsonResp->next_page,$params));
        }else{
            return $jsonResp->$objectname;
        }
    }

    private function curlExec($url){

        $headers = array(
            "Authorization: Bearer " . $this->token,
            "Harvest-Account-ID: "   . $this->account_id
        );

        $this->handle = curl_init();
        curl_setopt($this->handle, CURLOPT_URL, $url);
        curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->handle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($this->handle, CURLOPT_USERAGENT, "Parallax Harvest Maintenance");
        $response = curl_exec($this->handle);

        if (curl_errno($this->handle)) {
           throw new Exception("Error: " . curl_error($handle));
        } else {
            $json = json_decode($response);
            if($json === FALSE){
                throw new Exception("JSON Error: ".json_last_error_msg());
            }
            return $json;
        }
    }

    public function getCompanyInfo()
    {
        $url = $this->base_url . 'company';
        $company = $this->curlExec($url);
        return $company;
    }

    public function getAccessTokens($code='')
    {

        $url = 'https://id.getharvest.com/api/v2/oauth2/token';

        $data_fields = array(
            'code'=>$code,
            'client_id'=>$this->client_id,
            'client_secret'=>$this->client_secret,
            'grant_type'=>'authorization_code',
        );

        $this->handle = curl_init();
        curl_setopt($this->handle, CURLOPT_URL, $url);
        curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->handle, CURLOPT_POSTFIELDS, $data_fields);
        curl_setopt($this->handle, CURLOPT_USERAGENT, "Parallax Harvest Maintenance");
        $response = curl_exec($this->handle);

        if (curl_errno($this->handle)) {
           throw new Exception("Error: " . curl_error($handle));
        } else {
            $json = json_decode($response);
            if($json === FALSE){
                throw new Exception("JSON Error: ".json_last_error_msg());
            }
            return $json;
        }
    }

}