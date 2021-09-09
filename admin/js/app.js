(function ($) {
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
}(jQuery))