{include:header}
  <div id="main-content">
    <div class="container padding-adjust">
      {if category:banner}
      <div class="category-banner">
        <img src="{site:url}static/uploads/{category:banner}" alt="{category:title}" />
      </div>
      {/if}
      {include:left-sidebar}
      <div class="twelve columns">
		<!--
        <form name="" method="POST" action="" id="filter-results">
          <input type="submit" name="filter_results" class="button blue-green" value="Filter Results">
          {form:prod_cat}
          {form:prod_sizes}
          <div class="checkbox">
            {form:is_sale}
          </div>
          <div class="checkbox">
            {form:newly_listed}
          </div>
          <div class="clearfix"></div>
          <div class="border-bottom"></div>
        </form>
          -->
        
          
          
          
          {if shop:products}
            {shop:products}
            <form name="" method="POST" action="{site:url}shop/cart/add" id="form-data">
                <div class="four columns alpha product-wrapper">
                  <input type="hidden" name="productID" value="{product:id}" class="product-data" /> 
				  <input type="hidden" name="variation1" class="variation1" value="" />
				  <input type="hidden" name="variation2" class="variation2" value="" />
				  <input type="hidden" name="variation3" class="variation3" value="" />
                  <div class="product">
                    {product:is_sale}
                    {product:is_deal}
                    <a href="{product:link}" title="{product:tooltip}">
                      <img alt="" src="{product:thumb-path}" alt="{product:tooltip}">
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
                    <input type="submit" name="add_to_cart" class="button add-to-cart-sc" value="Add to Cart" id="{product:id}" />
					<div class="sizes" id="{product:id}">
						<select name="variation2" id="{product:id}" style="width: 150px; padding: 5px; margin-bottom: 10px;">{variation2}</select>
						<input type="submit" name="add_to_cart" class="button add-to-cart-sc" value="Add to Cart" id="{product:id}" />
					</div>
                  </div>
                </div>
              </form>
            {/shop:products}
			
			<div class="clearfix"></div>
			{pagination}
			
          {else}
			<h2>Coming Soon!</h2>
          {/if}
          <div class="clearfix"></div>
          
          
          
        
        

      </div>
    </div>
  </div>
  <script type="text/javascript" src="{site:url}static/js/jquery.lightbox.js"></script>
  <link rel="stylesheet" type="text/css" href="{site:url}static/css/lightbox.css" />
{include:footer}