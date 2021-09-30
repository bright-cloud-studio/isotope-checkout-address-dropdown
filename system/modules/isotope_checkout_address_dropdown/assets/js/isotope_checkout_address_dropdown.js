// when making selection with select, change the create new address button
$('#ctrl_billingaddressdropdown').on('change', function() {
  if(this.value == 0)
  {
     //check button
    $("#opt_billingaddress_71").prop("checked", true);
  }
});

