<form method="post" class="mt-3">
    <?php
    $api_token_data =	get_option(Contact_FormSI_DB_SLUG.'api_token','');
    $sender_id_data =	get_option(Contact_FormSI_DB_SLUG.'sender_id','');
    $country_data =	get_option(Contact_FormSI_DB_SLUG.'country','');
    $country_code_data =	get_option(Contact_FormSI_DB_SLUG.'country_code','');
    $reg_phone_data =	get_option(Contact_FormSI_DB_SLUG.'reg_phone','');
    ?>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label><?php _e('USMS-GH API Token', Contact_FormSI_TXT); ?></label>
                <input type="text" class="form-control" name="api_token" value="<?php if (!empty($api_token_data)) _e($api_token_data)?>">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label><?php _e('Choose Country', Contact_FormSI_TXT); ?></label>
                <select class="form-control" name="country" id="countries">
                    <option value="" <?php if (!empty($country_data)) _e($country_data)?>><?php _e('Select Option', Contact_FormSI_TXT); ?></option>
                </select>
            </div>
        </div>
        <div class="col-md-4 sender_id">
            <div class="form-group">
                <label><?php _e('USMS-GH Approved Sender ID', Contact_FormSI_TXT); ?></label>
                <input type="text" class="form-control" name="sender_id" value="<?php if (!empty($sender_id_data)) _e($sender_id_data)?>">
            </div>
        </div>
        <div class="col-md-4 reg_phone d-none">
            <div class="form-group">
                <label><?php _e('USMS-GH Approved Phone Number', Contact_FormSI_TXT); ?></label>
                <input type="text" class="form-control" name="reg_phone" value="<?php if (!empty($reg_phone_data)) _e($reg_phone_data) ?>">
            </div>
        </div>
        <input type="hidden" name="country_code" id="country_code" value="<?php if (!empty($country_code_data)) _e($country_code_data) ?>">
        <p class="text-danger text-center mt-2">Please if you're not in Ghana, Select your country and Provide the phone number approved from USMS-GH.
            <br> Make sure you provide either Sender ID or Phone Number only and not both.</p>
    </div>
    <div class="mt-2">
        <input type="submit" name="save_api_settings" value="Save Changes" class="btn btn-primary" />
    </div>
</form>