<?php


class SendEmail
{
    public function __construct()
    {
        global $wpdb;
        if (isset($_POST['subject']) &&
            !empty($_POST['subject']) &&
            isset($_POST['body']) &&
            !empty($_POST['body'])) {


//                 Get Data and saved them to Json file ['subject and body' => mm_email_data.json],['subscribers' => mm_emails.json]
                $emails = array();
                $email_data = array('subject' => $_POST['subject'], 'body' => $_POST['body']);
                $subscribers = get_users(array('role' => 'subscriber'));
                foreach ($subscribers as $sub) :
                    $emails[] = [
                        'user_id' => $sub->id,
                        'username' => $sub->user_login,
                        'email' => $sub->user_email
                    ];
                endforeach;



//                 Store Emails Data to 'data' Folder.
                try {


                        // create a file to store subscriber info.
                    $mm_emails = fopen(MM_CRON_JOBS_DATA . 'mm_emails.json', 'w');
                    fwrite($mm_emails, json_encode($emails));
                    fclose($mm_emails);

                        // create a file to store emails subject and body.
                    $mm_email_data = fopen(MM_CRON_JOBS_DATA . 'mm_email_data.json', 'w');
                    fwrite($mm_email_data, json_encode($email_data));
                    fclose($mm_email_data);
//                    echo 'success';
                } catch (Exception $e) {
                    echo $e->getMessage();
                }



                // run Send function to enable cron job
                $result = $this->enable_cronjob();
                if($result->message != 'success'){

                    echo
                    '<div class="notice notice-error  is-dismissible">
                        <p>There\'s a problem in the proccess. please contact support👨‍💻 : <br>Gmail : mahdichoobin7@gmail.com</p>
                     </div>';

                    exit;
                }


            echo
            '<div class="notice notice-success is-dismissible">
                        <p>your\'r email is being sent to user\'s.</p>
                     </div>';


            } else {

                echo
                '<div class="notice notice-warning  is-dismissible">
                    <p>subject or body field is empty.</p>
                </div>';

            }

    }


    public function enable_cronjob()
    {

        $cronJobIdPath = MM_CRON_JOBS_DATA . 'id.json';
        $cronJobIdString = file_get_contents($cronJobIdPath);
        $cronJobId = json_decode($cronJobIdString, true);


        $email_data_path = MM_CRON_JOBS_DATA . 'mm_email_data.json';
        $email_data_string = file_get_contents($email_data_path);
        $email_data = json_decode($email_data_string, true);

        // start cron
        $headers = array(
            "Content-Type: application/json"
        );
        $array = array(
            "cron_job_id" => $cronJobId
        );
        $data = json_encode($array);


        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://backbox.ir/wp-plugin/wp-content/plugins/mini_mail/API/enable.php');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($curl);

        return json_decode($response);

    }

}


