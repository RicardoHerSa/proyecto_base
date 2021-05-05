<?php

namespace App\Services;

/**
 * Undocumented class
 *
 * @author David Guanga <david.guanga@carvajal.com>
 * 
 * Documentacion : https://secure.na3.adobesign.com/public/docs/restapi/v5#!/transientDocuments/createTransientDocument
 * 
 */
class adobe
{

    public  $config = array();
    

    public  function   __construct($api_version)
    {
        $this->config = config("adobe." . $api_version);
    }


    /**
     * Get the value of config
     */
    public  function  getConfig()
    {

        return $this->config;
    }

    /**
     * Undocumented function
     *
     * @param [] $urlFile
     * @return json
     *  {
     *      "transientDocumentId": ""
     *  }
     * @author David Guanga <david.guanga@carvajal.com>
     */
    public function transientDocuments($urlFile){
        
        if (function_exists('curl_file_create')) { // php 5.5+
             $filePath = curl_file_create($urlFile);
         } else {
             $filePath = '@' . realpath($urlFile);
         }
        $get_api = $this->curl_post_get('POST', array('File'=> $filePath ), [], 'transientDocuments');
        return   json_decode($get_api);
    }


    public function agreements($transientDocumentId , $name,$mail){

        $json = '{
            "documentCreationInfo": {
              "fileInfos": [
                {
                  "transientDocumentId": "'.$transientDocumentId.'"
                }
              ],
              "name": "'.$name.'",
              "recipientSetInfos": [
                {
                  "recipientSetMemberInfos": '.$mail.',
                  "recipientSetRole": "SIGNER"
                }
              ],
              "signatureType": "ESIGN",
              "signatureFlow": "SENDER_SIGNATURE_NOT_REQUIRED"
            }
          }';

        $get_api = $this->curl_post_get('POST', $json, ['Content-Type: application/json'], 'agreements');
        return   json_decode($get_api);
    }

    public function getUrlAgreements($agreementId){
      
        $get_api = $this->curl_post_get('GET', '{"query":"","variables":{}}', ['Content-Type: application/json'], 'agreements/'.$agreementId.'/combinedDocument/url?attachSupportingDocuments=true&auditReport=true');
        return   json_decode($get_api);

    }

    public function getLogStatus($agreementId){

        $get_api = $this->curl_post_get('GET', '',['Content-Type: application/json'], 'agreements/'.$agreementId);

        return   json_decode($get_api);


    }


    /**
     * Undocumented function
     *
     * @param [string] $Customrequest  POST/GET
     * @param []       $Postfields
     * @param [array]  $Httpheader
     * @param [string] $Operacion
     * @return string
     * @author David Guanga <david.guanga@carvajal.com>
     */
    private function curl_post_get($Customrequest, $Postfields, $Httpheader, $Operacion)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->config['url'].$Operacion,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $Customrequest,

            CURLOPT_POSTFIELDS => $Postfields,
            CURLOPT_HTTPHEADER => array_merge(array('Authorization: Bearer ' . $this->config['Access_Token']), $Httpheader),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }



    
}
