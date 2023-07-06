<?php
const DS = DIRECTORY_SEPARATOR;
const PHP_MAILER_SRC = __DIR__.'' . DS .'..' . DS .'libs' . DS .'PHPMailer' . DS .'src' . DS;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require PHP_MAILER_SRC.'Exception.php';
require PHP_MAILER_SRC.'PHPMailer.php';
require PHP_MAILER_SRC.'SMTP.php';



class CjSendEmail{

    public function __construct(){
        $this->constants();


        $email_data_path = MM_CRON_JOBS_DATA . 'mm_email_data.json';
        $email_data_string = file_get_contents($email_data_path);
        $email_data = json_decode($email_data_string, true);

        $emails_path = MM_CRON_JOBS_DATA.'mm_emails.json';
        $emails_string = file_get_contents($emails_path);
        $emails = json_decode($emails_string, true);



        $config_path = MM_CRON_JOBS_DATA . 'config.json';
        $config_string = file_get_contents($config_path);
        $config_data = json_decode($config_string, true);

        $cronJobIdPath = MM_CRON_JOBS_DATA . 'id.json';
        $cronJobIdString = file_get_contents($cronJobIdPath);
        $cronJobId = json_decode($cronJobIdString, true);

        if($emails == null){

            $result = $this->disable_cronjob();
            if($result->message != 'success'){
                echo 'There\'s a problem in the proccess. please contact support👨‍💻 : <br>Gmail : mahdichoobin7@gmail.com';
                exit;
            }

        }




        $subject = $email_data['subject'];
        $body    = $email_data['body'];


        $mail = new PHPMailer(true);

//Server settings
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "tls";
        $mail->Port = 25;
        $mail->Host = "smtp.gmail.com";
        $mail->Username = $config_data['user'];
        $mail->Password = $config_data['pass'];

//Recipients
        $mail->From = $config_data['from'];
        $mail->FromName = $config_data['name'];
//Content
        $mail->isHTML(true);



        $mail->Subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
        $mail->Body = nl2br($body);

       


        for ($i=0; $i<10; $i++){

            if(!array_key_exists($i, $emails)){
                break;
            }
            unset($emails[$i]);
        }
        $emails = array_values($emails);

        $mm_emails = fopen(MM_CRON_JOBS_DATA.'mm_emails.json', 'w');
        fwrite($mm_emails, json_encode($emails));
        fclose($mm_emails);

        try {
            $mail->send();
//    echo "Emails has been sent successfully";
        } catch (Exception $e) {
//    echo "Mailer Error: " . $mail->ErrorInfo;
        }
    }

    public function disable_cronjob()
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
        curl_setopt($curl, CURLOPT_URL, 'https://backbox.ir/wp-plugin/wp-content/plugins/mini_mail/API/disable.php');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($curl);

        return json_decode($response);

    }

    public function constants()
    {
        define(DS, DIRECTORY_SEPARATOR);
        define('MM_CRON_JOBS_DATA', __DIR__.DS.'data'.DS);
//        define(PHP_MAILER_SRC, __DIR__.DS.'..'.DS.'libs'.DS.'PHPMailer'.DS.'src'.DS);
    }
}

new CjSendEmail();
