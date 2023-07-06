<?php


class Disable{

    private $fp;
    private $request_data;
    private $cron_job_id;

    public function __construct(){
        $this->fp = fopen('php://input', 'r');
        $this->request_data = json_decode(stream_get_contents($this->fp));

        $this->cron_job_id = $this->request_data->cron_job_id;

        $this->disable();
    }

    public function disable()
    {
        $url = "https://api.cron-job.org/jobs/". $this->cron_job_id;

        $headers = array(
            "Content-Type: application/json",
            "Authorization: Bearer ..."
        );
        $data = '{"job":{"enabled":false}}';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($curl);
        if (curl_exec($curl) === false) {
            echo json_encode(array('message' => 'error', 'content' => curl_error($curl)));
            exit;
        }

        curl_close($curl);
        echo json_encode(array('message' => 'success', 'content' => $this->cron_job_id));
    }


}
new Disable();





