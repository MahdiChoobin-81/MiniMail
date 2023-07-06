<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require MM_LIBS.'PHPMailer/src/Exception.php';
require MM_LIBS.'PHPMailer/src/PHPMailer.php';
require MM_LIBS.'PHPMailer/src/SMTP.php';

class StoreConfiguration{

    public function __construct()
    {
        // store configuration data to 'cron-jobs/data/config.json' file

        $user = $_POST['username'];
        $pass = $_POST['password'];
        $from = $_POST['from'];
        $name = $_POST['name'];

        if(isset($user) && !empty($user) &&
           isset($pass) && !empty($pass) &&
           isset($from) && !empty($from) &&
           isset($name) && !empty($name)){

                $config_data = array("user"=>$user,
                                     "pass"=>$pass,
                                     "from"=>$from,
                                     "name"=>$name);

                $file = fopen(MM_CRON_JOBS_DATA . 'config.json', 'w');
                fwrite($file, json_encode($config_data));
                fclose($file);



                if($this->TestMail()){
                    if(!file_exists(MM_CRON_JOBS_DATA . 'id.json')){

                        // create a cronjob for this site.
                        $cronJob = $this->create_cronjob();
                        if($cronJob->message == 'success')
                            $cronJobId = $cronJob->id;
                        else{
                            echo
                            '<div class="notice notice-error  is-dismissible">
                                <p>There\'s a problem in the proccess. please contact support👨‍💻 : <br>Gmail : mahdichoobin7@gmail.com</p>
                             </div>';
                            exit;
                        }

                        // create a file to store cronjob ID..
                        $cronJobInfo = fopen(MM_CRON_JOBS_DATA . 'id.json', 'w');
                        fwrite($cronJobInfo, $cronJobId);
                        fclose($cronJobInfo);

                    }
                }




        }else{
            echo
            '<div class="notice notice-warning is-dismissible">
                        <p>please fill the fields.</p>
                     </div>';
        }

    }

    public function TestMail()
    {

        $config_path = MM_CRON_JOBS_DATA . 'config.json';
        $config_string = file_get_contents($config_path);
        $config_data = json_decode($config_string, true);

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

        $mail->Subject = get_bloginfo( 'name' );
        $mail->Body = 'Your information has been confirmed.';

        $mail->addAddress($config_data['from']);

        try {
            $mail->send();
            echo
            '<div class="notice notice-success is-dismissible">
                        <p>Your information has been confirmed.</p>
                     </div>';
            $_SESSION['confirm_info'] = true;
            return true;
        } catch (\Exception $e) {


            echo
            '<div class="notice notice-error is-dismissible">
                        <p>There\'s a problem in sending mails.</p>
                        <p>Please make sure your information is correct.</p>
                     </div>';

                $_SESSION['confirm_info'] = false;
                return false;
        }
    }


    public function create_cronjob()
    {
        $headers = array(
            "Content-Type: application/json"
        );
        $cron_job_url = plugins_url('mini_mail/cron-jobs/cj-send-email.php');
        $site_name = get_bloginfo( 'name' );

        $array = array(
            "cron_job_url" => $cron_job_url,
            "site_name"    => $site_name
        );


        $data = json_encode($array);


        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://backbox.ir/wp-plugin/wp-content/plugins/mini_mail/API/create.php');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($curl);

        return json_decode($response);

    }
}

