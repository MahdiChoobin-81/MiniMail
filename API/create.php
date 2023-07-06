<?php

header('Content-Type: application/json');


class Create{

private $fp;
private $request_data;
private $cron_job_url;
private $site_name;


    public function __construct()
    {

        $this->fp = fopen('php://input', 'r');
        $this->request_data = json_decode(stream_get_contents($this->fp));

        $this->cron_job_url = $this->request_data->cron_job_url;
        $this->site_name = $this->request_data->site_name;


       if($this->checkCronJobName()){
           echo json_encode(array('message' => 'failed'));
           exit;
       }

       $this->create();

    }



    function checkCronJobName(){
        $url = 'https://api.cron-job.org/jobs';
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ...'
        );

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($curl);

        if (curl_exec($curl) === false) {
            return true;
        }

        curl_close($curl);

        $result = json_decode($response);


        for ($i=0; $i<count($result->jobs); $i++){
            if($result->jobs[$i]->title == $this->site_name)
            {
                return true;
            }
        }
        return false;
    }

    function create(){
     $url = "https://api.cron-job.org/jobs";

    $headers = array(
        "Content-Type: application/json",
        "Authorization: Bearer kKrzJnRhcl13z0L1IOHKq+0MUOWn21WO8U8oOVisSUI="
    );
    $data = '{"job": {"enabled": false,"title": "'.$this->site_name.'","saveResponses": true,"url": "'.$this->cron_job_url.'",
                       "schedule": {"timezone": "Asia/Tehran","hours": [-1],"mdays": [-1],"minutes": [-1],
                       "months": [-1],"wdays": [-1]}}}';

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($curl);
    $result = json_decode($response);

    if (curl_exec($curl) === false) {
        echo json_encode(array('message' => 'error', 'content' => curl_error($curl)));
        exit;
    }
    curl_close($curl);
        echo json_encode(array('message' => 'success', 'id' => $result->jobId));
    }

}

new Create();
//echo json_encode(array('message' => 'your job has been created'));