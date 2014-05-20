{include:header}
  <div id="main-content">
    <div class="container product-page-wrapper">
      {if errors}
        <div class="error">
          {errors}
        </div>
      {/if}
      
      
      <div class="eight columns">
        <div id="slider" class="flexslider">
          <ul class="slides">
            {single_product:image_1_large}
  	    	{single_product:image_2_large}
            {single_product:image_3_large}
            {single_product:image_4_large}
            {single_product:image_5_large}
          </ul>
        </div>
        <div id="carousel" class="flexslider">
          <ul class="slides">
            {single_product:image_1_thumb}
  	    	{single_product:image_2_thumb}
            {single_product:image_3_thumb}
            {single_product:image_4_thumb}
            {single_product:image_5_thumb}
          </ul>
        </div>
      </div>
      <div class="eight columns">
        <div class="product-data">
       
          {if success}
          <div class="success">
            <p>{success}</p>
          </div>
          {/if}
          
          <form method="post" action="{site:url}shop/cart/add">
            
            <input type="hidden" name="productID" value="{single_product:id}" />
            
            <div class="product-data-top">
              
              
              <h1>{single_product:title}</h1>

              {if single_product:subtitle}
                <p class="product-subcaption">{single_product:subtitle}</p>
              {else}
                <p>&nbsp;</p>
              {/if}

              <div class="product-page-price">
                {single_product:price}
              </div>
            </div>

            <div class="product-page-desc">
              {single_product:body}
            </div>

            <div class="product-meta">
              {if single_product:variations}
              <div class="four columns alpha">
                <div class="variations">
                  {single_product:variations}
                </div>
              </div>
              {/if}
              <div class="four columns alpha">
                <div class="quantity">
                  <label>Quantity</label>
                  <input type="text" name="quantity" value="1" />
                </div>
              </div>
              <div class="clearfix"></div>
            </div>

            <hr />

            <div class="product-button">
              <div class="four columns alpha">
                
                  {wishlist:btn}
                
              </div>
              <div class="four columns alpha">
                <div class="add-to-cart">
                  <input type="submit" name="add_to_cart" value="Add to Cart" class="add-to-cart" id="{single_product:id}" />
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
            
          </form>
          
          <div class="social-icons">
            <ul>
              <li class="two columns alpha omega">
                Share the Love
              </li>
              <li class="one-half columns alpha omega twitter">
                <a href="https://twitter.com/share" class="twitter-share-button" data-count="vertical" data-url="{single_product:link}">Tweet</a>
                <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
              </li>
              <li class="one-half columns alpha omega facebook">
                <iframe src="//www.facebook.com/plugins/like.php?href={single_product:link}&amp;width&amp;layout=box_count&amp;action=like&amp;show_faces=true&amp;share=false&amp;height=65&amp;width=50" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:65px; width: 50px;" allowTransparency="true"></iframe>
              </li>
              <li class="one-half columns alpha omega pinterest">
                <a href="//www.pinterest.com/pin/create/button/?url={single_product:link}&media={single_product:thumb-path}&description={single_product:title}" data-pin-do="buttonPin" data-pin-config="above" data-pin-color="white" data-pin-height="28">
                  <img src="//assets.pinterest.com/images/pidgets/pinit_fg_en_rect_white_28.png" />
                </a>
                <!-- Please call pinit.js only once per page -->
                <script type="text/javascript" async src="//assets.pinterest.com/js/pinit.js"></script>
              </li>
              
            </ul>
            <div class="clearfix"></div>
          </div>
          
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="sixteen columns">
        <h2>Products You'll Love: </h2>
        
        <form name="" method="POST" action="{site:url}shop/cart/add">
        
        <div id="related-products">
          {if shop:related_products}
            {shop:related_products}
              <div class="four columns alpha product-wrapper">
                <input type="hidden" name="productID" value="{product:id}" />
                <div class="product">
                  {product:is_sale}
                  <a href="{product:link}">
                    <img alt="" src="{product:thumb-path}">
                    <div class="layer"></div>
                  </a>
                  <div class="product-price">
                    {product:price}
                  </div>
                </div>
                
                <div class="product-name">
                  {product:title}
                </div>
                <div class="product-view">
                  <a href="{product:link}">View Product</a>
                </div>
                <div class="product-add-to-cart">
                  <input type="submit" name="add_to_cart" class="button add-to-cart-sc" value="Add to Cart" id="{product:id}">
				  <div class="sizes" id="{product:id}">
						<select name="variation2" id="{product:id}" style="width: 150px; padding: 5px; margin-bottom: 10px;">{variation2}</select>
						<input type="submit" name="add_to_cart" class="button add-to-cart-sc" value="Add to Cart" id="{product:id}" />
					</div>
                </div>
              </div>
            {/shop:related_products}
            <div class="clearfix"></div>
          {else}
            <p>No products yet</p>
          {/if}
          
        </div>
        </form>
      </div>
      
      
        
    </div>
  </div>
<link rel="stylesheet" href="{site:url}static/css/flexslider/flexslider.css" type="text/css" media="screen" />
<script type="text/javascript" src="{site:url}static/js/flexslider/jquery.flexslider-min-2.2.2.js"></script>
<script type="text/javascript">
  
  $(document).ready(function() {
    $('#carousel').flexslider({
      animation: "slide",
      controlNav: false,
      animationLoop: false,
      slideshow: false,
      move: 1,
      minItems: 1,
      maxItems: 5,
      itemWidth: 150,
      itemMargin: 10,
      touch: false,
      smoothHeight: false,
      asNavFor: '#slider'
      
    });
 
    $('#slider').flexslider({
      animation: "slide",
      controlNav: false,
      animationLoop: false,
      slideshow: false,
      touch: false,
      smoothHeight: true,
      sync: "#carousel"
    });
	
	$('div.flex-viewport').css('height','auto !important');
	
  });
  
    
</script>
{include:footer}