{include:header}
  <div id="main-content">
    <div class="container padding-adjust">
      
      {include:account-menu}
      
      <div class="twelve columns">
       

		<h1>My Wishlist</h1>

		<form name="" method="POST" action="{site:url}shop/cart/add">
          
          
          
          {if shop:products}
            {shop:products}
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
                  <input type="submit" name="add_to_cart" class="button add-to-cart" value="Add to Cart" id="{product:id}" />
                </div>
              </div>
            {/shop:products}
          {else}
            No Products yet
          {/if}
          <div class="clearfix"></div>
          
          {pagination}
          
        </form>


		
        

      </div>
    </div>
  </div>

{include:footer}