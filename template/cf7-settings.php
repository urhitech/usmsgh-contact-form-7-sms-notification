<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css"
      rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
                <label><?php echo __('USMS-GH API Token', Contact_FormSI_TXT); ?></label>
                <input type="text" class="form-control" name="api_token" value="<?php if (!empty($api_token_data)) echo $api_token_data?>">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label><?php echo __('Choose Country', Contact_FormSI_TXT); ?></label>
                <select class="form-control select2" name="country" id="countries">
                    <option value="" <?php if (!empty($country_data)) echo $country_data?>><?= __('Select Option', Contact_FormSI_TXT); ?></option>
                </select>
            </div>
        </div>
        <div class="col-md-4 sender_id">
            <div class="form-group">
                <label><?php echo __('USMS-GH Approved Sender ID', Contact_FormSI_TXT); ?></label>
                <input type="text" class="form-control" name="sender_id" value="<?php if (!empty($sender_id_data)) echo $sender_id_data?>">
            </div>
        </div>
        <div class="col-md-4 reg_phone d-none">
            <div class="form-group">
                <label><?php echo __('USMS-GH Approved Phone Number', Contact_FormSI_TXT); ?></label>
                <input type="text" class="form-control" name="reg_phone" value="<?php if (!empty($reg_phone_data)) echo $reg_phone_data?>">
            </div>
        </div>
        <input type="hidden" name="country_code" id="country_code" value="<?php if (!empty($country_code_data)) echo $country_code_data?>">
        <p class="text-danger text-center mt-2">Please if you're not in Ghana, Select your country and Provide the phone number approved from USMS-GH.
            <br> Make sure you provide either Sender ID or Phone Number only and not both.</p>
    </div>
    <div class="mt-2">
        <input type="submit" name="save_api_settings" value="Save Changes" class="btn btn-primary" />
    </div>
</form>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('.select2').select2();
        // Get all countries
        $.ajax({
            url: 'https://restcountries.eu/rest/v2/all',
            type: 'get',
            dataType: 'json',
            success: function (res) {
                if (res) {
                    $.each(res, function (k,v) {
                        $('#countries').append(`<option value="${v.name}">${v.name}</option>`)
                    })
                }
            }
        })

        $('#countries').change(function () {
            let country = $(this).val()
            if (country === 'Ghana' || country === 'ghana') {
                $('.sender_id').removeClass('d-none')
                $('.reg_phone').addClass('d-none')
            } else {
                $('.sender_id').addClass('d-none')
                $('.reg_phone').removeClass('d-none')
            }

            // get country details by country name
            $.ajax({
                url: 'https://restcountries.eu/rest/v2/name/'+country,
                type: 'get',
                dataType: 'json',
                success: function (res) {
                    if (res) {
                        $.each(res, function (k, v) {
                            $('#country_code').val(v.callingCodes[0])
                        })
                    }
                }
            })
        })
    })
</script>