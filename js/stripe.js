var handler = StripeCheckout.configure({
  key: 'pk_test_6pRNASCoBOKtIshFeQd4XMUh',
  image: 'https://stripe.com/img/documentation/checkout/marketplace.png',
  locale: 'auto',
  currency:'GBP',
  token: function(token) {
    // You can access the token ID with `token.id`.
    // Get the token ID to your server-side code for use.
    jQuery.post(ajaxurl,{action:'stripe_payment',token:token.id,email:token.email,ip:token.client_ip, price:jQuery('#stripepackage').val(),description:jQuery('#stripepackage option:selected').text()},function(data){
      if(data.type == 'success') {
        alert('data.msg');
      } else{
        alert('data.msg');
      }
    },'json')

  }
});

document.getElementById('stripe_button').addEventListener('click', function(e) {
  var selectedprice = jQuery('#stripepackage').val();
  var selectedpackage = jQuery('#stripepackage option:selected').text();
  // Open Checkout with further options:
  handler.open({
    name: 'Wp image compression upgrade',
    description: selectedpackage,
    zipCode: true,
    amount: selectedprice*1
  });
  e.preventDefault();
});

// Close Checkout on page navigation:
window.addEventListener('popstate', function() {
  handler.close();
});