<?php

$config_path = MM_CRON_JOBS_DATA . 'config.json';
if(file_exists($config_path)){
    $config_string = file_get_contents($config_path);
    $config_data = json_decode($config_string, true);
}

?>


<div class="wrap">
    <h1>Configuration Settings :</h1>

    <form action="" method="post">
        <table class="form-table">

            <tr valign="top">
                <th scope="row">Username : </th>
                <td><input type="text" aria-describedby="emailHelp" size="25" name="username" value="<?= $config_data['user'] ?>" /><br>
                    <small id="emailHelp" class="form-text text-muted">username to login to your Gmail.</small>
                </td>

            </tr>

            <tr valign="top">
                <th scope="row">Password : </th>
                <td><input type="text" name="password" value="<?= $config_data['pass'] ?>" size="25"/><br>
                    <small id="emailHelp" class="form-text text-muted">App Password of your Gmail.<a href="https://support.google.com/mail/answer/185833?hl=en#">
                            more
                        </a></small>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">From : </th>
                <td><input type="text" name="from" value="<?= $config_data['from'] ?>" size="25"/><br>
                    <small id="emailHelp" class="form-text text-muted"></small>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">Name : </th>
                <td><input type="text" name="name" value="<?= $config_data['name'] ?>" size="25"/><br>
                    <small id="emailHelp" class="form-text text-muted"></small>
                </td>
            </tr>

        </table>
        <?php submit_button(); ?>
    </form>
</div>
