
<div class="wrap">
    <h1>Email Details :</h1>

    <form action="" method="post" >
        <table class="form-table">

            <tr valign="top">
                <th scope="row">Subject :</th>
                <td><input type="text" size="36" name="subject" value="" /></td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Body :</th>
                <td><textarea name="body" cols="40" rows="10"></textarea></td>
            </tr>
            
        </table>

        <?php submit_button('Send'); ?>
    </form>
</div>