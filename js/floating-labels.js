$(function(){
  $("body").on('focus', '.form-span', function(){
    var span = $('<label class="control-label"></label>');
    span.html($(this).attr('placeholder'));
    span.attr('id', $(this).attr('id') + '_span');
    span.css({
      position: 'absolute',
      top: '-10px',
      left: '25px',
      background: '#ffffff',
      'font-weight': 'bold',
      padding: '0 5px'
    });

    $(this).before(span);
    $(this).attr('placeholder', '');
  }).on("blur", ".form-span", function(){
    if ($(this).val() == '' || $(this).val() == '+7 (___) ___-__-__')
    {
      $(this).attr('placeholder', $('#' + $(this).attr('id') + '_span').html());
      $('#' + $(this).attr('id') + '_span').remove();
      $(this).parent().removeClass('has-error');
    }
  }).on('keypress', '.form-span', function(e){
    if (e.keyCode == 13)
    {
      found = false;
      var id = $(this).attr('id');
      $('.form-span').each(function()
      {
        if (found)
        {
          found = false;
          $(this).focus();
        }
        if ($(this).attr('id') == id)
        {
          found = true;
        }
      });
    }
  });
});

function refreshLabels()
{
  $('.form-span').each(function(){
    if ($(this).val() == '' || $(this).val() == '+7 (___) ___-__-__')
    {
      if($('#' + $(this).attr('id') + '_span').length)
      {
        $(this).attr('placeholder', $('#' + $(this).attr('id') + '_span').html());
        $('#' + $(this).attr('id') + '_span').remove();
        $(this).parent().removeClass('has-error');
      }
    }
    else
    {
      if(!$('#' + $(this).attr('id') + '_span').length)
      {
        var span = $('<label class="control-label"></label>');
        span.html($(this).attr('placeholder'));
        span.attr('id', $(this).attr('id') + '_span');
        span.css({
          position: 'absolute',
          top: '-10px',
          left: '25px',
          background: '#ffffff',
          'font-weight': 'bold',
          padding: '0 5px'
        });
        $(this).before(span);
        $(this).next('.c_line').removeClass('c_line_error').addClass('c_line_active');
        $(this).attr('placeholder', '');
      }
    }

  });
}

$(document).ready(function(){
  refreshLabels();
});