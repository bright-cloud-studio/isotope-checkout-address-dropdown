// when making selection with select, change the create new address button
$('#ctrl_billingaddressdropdown').on('change', function() {
  if(this.value == 0)
  {
    $("#opt_billingaddress_71").prop("checked", true);
  } else {
    $("#opt_billingaddress_71").prop("checked", false);
  }
});


// when making selection with select, change the create new address button
$('#ctrl_shippingaddressdropdown').on('change', function() {
  if(this.value == 0)
  {
    $("#opt_shippingaddress_71").prop("checked", true);
  } else {
    $("#opt_shippingaddress_71").prop("checked", false);
  }
});

