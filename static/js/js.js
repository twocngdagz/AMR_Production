$(document).ready(function() {
  
  var site_url = 'https://motherrunnerstore.com/';
  
  $(".success, .error").animate(
  {
    opacity: 1.0
  }, 
  3000).fadeOut("slow");
  
  var pull        = $('#pull');
      menu        = $('#navigation ul');
      menuHeight  = menu.height();

  $(pull).on('click', function(e) {
    e.preventDefault();
    menu.slideToggle();
  });
  
  
  $('.close-btn img').click(function() {
    $('#shopping-cart-notification').slideUp('slow');
  })
 
  $('.categories-title').click(function() {
    $('#shopping-cart-notification').slideDown('slow');
  });
  
  /*
  $('.add-to-cart').hover(function(){
	$(this).prev().show();
  }, function(){
	$(this).prev().hide();
  });
  */
  
  $('.add-to-cart').click(function() {
     var productID = $(this).attr('id');
	 var quantity = 1;
	 var var1 = "";
	 var var2 = "";
	 var var3 = "";
	 if($('input[name="quantity"]').length > 0) { quantity = $('input[name="quantity"]').val(); }
	 if($('select.variation[name="variation1"]').length > 0) { var1 = $('select[name="variation1"]').val(); }
	 if($('select.variation[name="variation2"]').length > 0) { var2 = $('select[name="variation2"]').val(); }
	 if($('select.variation[name="variation3"]').length > 0) { var3 = $('select[name="variation3"]').val(); }
		
     $.ajax({ 
        type:     "GET",
        url:      site_url+"shop/cart_process",
        data:     "productID="+productID+"&quantity="+quantity+"&variation1="+var1+"&variation2="+var2+"&variation3="+var3,
        dataType: "JSON",
        cache:    false,
        beforeSend: function() {
          
          var aTag = $('#header');
          
          $('html,body').animate({
            scrollTop: aTag.offset().top
          },'slow');
        },
        success: function(data) {
            
         // var data       = $.parseJSON(data);
          $('.product-name').html(data.productName);
          $('.price').html(data.productPrice);
          $('.thumb').html(data.productImage);
          $('.cart-items').html(data.cartItems);
          $('.cart-subtotal').html(data.subtotal);
		  $('.quantity').html(data.quantity);
          
          $('#shopping-cart-notification').slideDown('slow');
          $('#shopping-cart-notification').delay(15000).slideUp('slow');
          
          $('.close-btn img').click(function() {
            $('#shopping-cart-notification').slideUp('slow');
          })
            
        },
        error: function(x,e) {}
        
      });
      
      return false;
    });
    
	$('.add-to-cart-sc').click(function(e) {
		e.preventDefault();
		var productID = $(this).attr('id');
		var quantity = 1;
		
		var1 = $('select#'+productID+'[name="variation1"] option:selected').val();
		var2 = $('select#'+productID+'[name="variation2"] option:selected').val();
		var3 = $('select#'+productID+'[name="variation3"] option:selected').val();
		
		if(var2!='')
		{
		 $.ajax({ 
			type:     "GET",
			url:      site_url+"shop/cart_process",
			data:     "productID="+productID+"&quantity="+quantity+"&variation1="+var1+"&variation2="+var2+"&variation3="+var3,
			dataType: "JSON",
			cache:    false,
			beforeSend: function() {
			  
			  var aTag = $('#header');
			  
			  $('html,body').animate({
				scrollTop: aTag.offset().top
			  },'slow');
			},
			success: function(data) {
				
			 // var data       = $.parseJSON(data);
			  $('.product-name').html(data.productName);
			  $('.price').html(data.productPrice);
			  $('.thumb').html(data.productImage);
			  $('.cart-items').html(data.cartItems);
			  $('.cart-subtotal').html(data.subtotal);
			  $('.quantity').html(data.quantity);
			  
			  $('#shopping-cart-notification').slideDown('slow');
			  $('#shopping-cart-notification').delay(15000).slideUp('slow');
			  
			  $('.close-btn img').click(function() {
				$('#shopping-cart-notification').slideUp('slow');
			  })
				
			},
			error: function(x,e) {}
			
		  });
	  }
	  else
	  {
		$(this).next().find('select').addClass('redborder');
	  }
	
    });
	
    $('.my-cart-hover').hover(function() {
      $('#shopping-cart-summary').slideDown('slow');
    });
    
    
    
});
