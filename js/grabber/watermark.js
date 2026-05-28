var grabber_watermark =
{
    files: null,

    init: function () {
        $('#i_group_settings_watermark_link').click(function(){
            grabber_watermark.setWatermarkImage();
        });

        $('#i-isWatermark2').change(function(){
            var checked = $(this).prop('checked');
            if (checked)
            {
                $('#i-watermarkImage').css('visibility', 'visible');
                $('#i-isWatermark-2').prop('checked', true);
                $('#i_div_watermark').show();
            }
            else
            {
                $('#i-isWatermark-2').prop('checked', false);
                $('#i-watermarkImage').css('visibility', 'hidden');
                $('#i_div_watermark').hide();
            }
        });

        $('#i-isWatermark4').change(function(){
            var checked = $(this).prop('checked');
            if (checked)
            {
                $('#i-isWatermark-4').prop('checked', true);
                $('#i-watermarkText').css('visibility', 'visible');
                $('#i_div_watermark_text').show();
            }
            else
            {
                $('#i-isWatermark-4').prop('checked', false);
                $('#i-watermarkText').css('visibility', 'hidden');
                $('#i_div_watermark_text').hide();
            }
        });

        $('#i_settings_watermark_file').on('change', function(event){
            grabber_watermark.files = event.target.files;
            var data = new FormData();
            $.each(grabber_watermark.files, function(key, value)
            {
                data.append(key, value);
            });

            data.append('action', 'watermarkUpload');

            $.ajax({
                type: 'POST',
                data: data,
                cache: false,
                dataType: 'json',
                processData: false,
                contentType: false,
                beforeSend: function()
                {
                    $('#i_settings_watermark_submit').html('Подождите, идет загрузка');
                },
                success: function(data)
                {
                    if (data.success)
                    {
                        $('#i_div_watermark').html('<img src="'+data.src+'" style="width: 100%;" />');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown)
                {
                    $('#i_settings_watermark_result').addClass('alert alert-danger').html('Ошибка при загрузке файла.');
                },
                complete: function()
                {
                    $('#i_settings_watermark_submit').html('Загрузить');
                }
            });
        });

        $('#i_settings_watermark_form').on('submit', function(event){
            event.stopPropagation(); // Stop stuff happening
            event.preventDefault(); // Totally stop stuff happening
        });

        $('.c-watermark-param').click(function(){
            grabber_watermark.setWatermarkImage();
        }).keyup(function(){
            grabber_watermark.setWatermarkImage();
        });
    },
    setWatermarkImage: function()
    {
        var watermarkPos = $('#i-watermark-param-watermarkPos option:selected').val();
        var watermarkOpacity = $('#i-watermark-param-watermarkOpacity option:selected').val();
        var watermarkMaxSize = $('#i-watermark-param-watermarkMaxSize option:selected').val();

        var watermarkText = $('#i-watermark-param-watermarkText').val();
        var watermarkColor = $('#i-watermark-param-watermarkColor').val();
        var watermarkFont = $('#i-watermark-param-watermarkFont option:selected').val();
        var watermarkSize = $('#i-watermark-param-watermarkSize option:selected').val();
        var watermarkTextPos = $('#i-watermark-param-watermarkTextPos option:selected').val();
        var watermarkTextOpacity = $('#i-watermark-param-watermarkTextOpacity option:selected').val();

        $('#i-watermarkPos').val(watermarkPos);
        $('#i-watermarkOpacity').val(watermarkOpacity);
        $('#i-watermarkMaxSize').val(watermarkMaxSize);

        $('#i-watermarkText').val(watermarkText);
        $('#i-watermarkColor').val(watermarkColor);
        $('#i-watermarkFont').val(watermarkFont);
        $('#i-watermarkSize').val(watermarkSize);
        $('#i-watermarkTextPos').val(watermarkTextPos);
        $('#i-watermarkTextOpacity').val(watermarkTextOpacity);

        $('#i_div_watermark').css({opacity: 1 - watermarkOpacity, 'max-width': watermarkMaxSize + '%'});

        $('#i_div_watermark_text').html(watermarkText);
        $('#i_div_watermark_text').css({opacity: 1 - watermarkTextOpacity, color: watermarkColor, 'font-family': watermarkFont, 'font-size': watermarkSize + 'px', 'line-height': (parseInt(watermarkSize) + parseInt(watermarkSize * 0.22)) + 'px', position: 'absolute'});

        if (watermarkTextPos == 0)
        {
            var w = $('#i_div_watermark_text').width();
            var h = $('#i_div_watermark_text').height();
            $('#i_div_watermark_text').css({position: 'absolute', top: '50%', left: '50%', right: 'auto', bottom: 'auto', 'margin-top': '-'+(h/2)+'px', 'margin-left': '-'+(w/2)+'px'});
        }
        else if(watermarkTextPos == 1)
        {
            $('#i_div_watermark_text').css({position: 'absolute', top: 'auto', left: 'auto', right: 0, bottom: 0, 'margin-top': 0, 'margin-left': 0});
        }
        else if (watermarkTextPos == 2)
        {
            $('#i_div_watermark_text').css({position: 'absolute', top: 'auto', left: 0, right: 'auto', bottom: 0, 'margin-top': 0, 'margin-left': 0});
        }
        else if (watermarkTextPos == 3)
        {
            $('#i_div_watermark_text').css({position: 'absolute', top: 0, left: 0, right: 'auto', bottom: 'auto', 'margin-top': 0, 'margin-left': 0});
        }
        else if (watermarkTextPos == 4)
        {
            $('#i_div_watermark_text').css({position: 'absolute', top: 0, left: 'auto', right: 0,  bottom: 'auto', 'margin-top': 0, 'margin-left': 0});
        }
        else if (watermarkTextPos == 5) // верх центр
        {
            var w = $('#i_div_watermark_text').width();
            $('#i_div_watermark_text').css({position: 'absolute', top: 0, left: '50%', right: 'auto',  bottom: 'auto', 'margin-top': 0, 'margin-left': '-'+(w/2)+'px'});
        }
        else if (watermarkTextPos == 6) // Лево центр
        {
            var h = $('#i_div_watermark_text').height();
            $('#i_div_watermark_text').css({position: 'absolute', top: '50%', left: 0, right: 'auto',  bottom: 'auto', 'margin-top': '-'+(h/2)+'px', 'margin-left': 0});
        }
        else if (watermarkTextPos == 7) // Право центр
        {
            var h = $('#i_div_watermark_text').height();
            $('#i_div_watermark_text').css({position: 'absolute', top: '50%', left: 'auto', right: 0,  bottom: 'auto', 'margin-top': '-'+(h/2)+'px', 'margin-left': 0});
        }
        else if (watermarkTextPos == 8) // Низ центр
        {
            var w = $('#i_div_watermark_text').width();
            $('#i_div_watermark_text').css({position: 'absolute', top: 'auto', left: '50%', right: 'auto',  bottom: 0, 'margin-top': 0, 'margin-left': '-'+(w/2)+'px'});
        }

        if (watermarkPos == 0)
        {
            var w = $('#i_div_watermark').width();
            var h = $('#i_div_watermark').height();
            $('#i_div_watermark').css({position: 'absolute', top: '50%', left: '50%', right: 'auto', bottom: 'auto', 'margin-top': '-'+(h/2)+'px', 'margin-left': '-'+(w/2)+'px'});
        }
        else if (watermarkPos == 1)
        {
            $('#i_div_watermark').css({position: 'absolute', top: 'auto', left: 'auto', right: 0, bottom: 0, 'margin-top': 0, 'margin-left': 0});
        }
        else if (watermarkPos == 2)
        {
            $('#i_div_watermark').css({position: 'absolute', top: 'auto', left: 0, right: 'auto', bottom: 0, 'margin-top': 0, 'margin-left': 0});
        }
        else if (watermarkPos == 3)
        {
            $('#i_div_watermark').css({position: 'absolute', top: 0, left: 0, right: 'auto', bottom: 'auto', 'margin-top': 0, 'margin-left': 0});
        }
        else if (watermarkPos == 4)
        {
            $('#i_div_watermark').css({position: 'absolute', top: 0, left: 'auto', right: 0,  bottom: 'auto', 'margin-top': 0, 'margin-left': 0});
        }
    }
};