jQuery(function(){
  jQuery(document).on('submit', '.submit_url', function(){
    jQuery('p.error').remove();
     
    event.preventDefault();
     jQuery.ajax({
      url: 'https://wordcount.weglot.com/data',
      type: "GET",
      data:jQuery('.submit_url').serialize(),
      beforeSend: function(){
        jQuery('.result_section').show();
        jQuery('.ajax_loader').show();
        jQuery('.count_response').html('');
        jQuery('.links_response').html('');
        jQuery('.text_information').hide();
        jQuery('.subscribe-form').css('margin-top','0');
      },
      success: function(response){
        var res = JSON.parse(response);
        jQuery('.ajax_loader').hide();
        if(res.success == true){
          jQuery('.count_response').html('<h2 style="text-align:center">'+res.total_words+'</h2>');
          jQuery('.links_response').html(res.data);
          jQuery('.text_information').show();
        }else{
          jQuery('.form-search').prepend(res.message);
          jQuery('.result_section').hide();
          jQuery('.ajax_loader').hide();
          jQuery('.count_response').html('');
          jQuery('.links_response').html('');
           jQuery('.subscribe-form').css('margin-top','100px');
        }
      }
    });
  });
});
(function(){
  var test = document.createElement('div');
  test.innerHTML = '&nbsp;';
  test.className = 'adsbox';
  document.body.appendChild(test);
  window.setTimeout(function() {
  if (test.offsetHeight === 0) {
    // alert("active");    
    $.confirm({
      'title'   : 'Adblocker active!',
      'message' : 'You are running an adblocker extension in your browser. You made a kitten cry. If you wish to continue to this website you might consider disabling it.',
      'buttons' : {
       /* 'I will!' : {
          'class' : 'blue',
          'action': function(){
            // Do nothing
            return;
          }
        },
        'Never!'  : {
          'class' : 'gray',
          'action': function(){
            // Redirect to some page
            window.location = 'http://tutorialzine.com/';
          }
        }*/
      }
    });  
  } else {
    // alert("not active");
  }
  test.remove();
  }, 100);
})();

