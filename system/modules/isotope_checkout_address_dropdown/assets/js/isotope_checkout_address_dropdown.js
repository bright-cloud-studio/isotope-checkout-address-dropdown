// when changing the billing address dropdown
$('#ctrl_billingaddressdropdown').on('change', function() {

    // if our value is "Create New Address"
    if(this.value === 0)
    {
        // tick our radio and trigger our onlick event
        $("#opt_billingaddress_71").prop("checked", true);
        $("#opt_billingaddress_71")[0].onclick();
    } else {
        $("#opt_billingaddress_71").prop("checked", false);
    }
});

// when changing the shipping address dropdown
$('#ctrl_shippingaddressdropdown').on('change', function() {
    
    // if our value is "Create New Address"
    if(this.value === 0)
    {
        // tick our radio and trigger our onclick event
        $("#opt_shippingaddress_71").prop("checked", true);
        $("#opt_shippingaddress_71")[0].onclick();
    } else {
        $("#opt_shippingaddress_71").prop("checked", false);
    }
});

// make our billing select match our radio button
$('input:radio[name="billingaddress"]').change(
    function(){
        if ($(this).is(':checked')) {
        $("#ctrl_billingaddressdropdown").val("0").change();
    }
});

// make our shipping select match our radio button
$('input:radio[name="shippingaddress"]').change(
    function(){
    if ($(this).is(':checked')) {
        $("#ctrl_shippingaddressdropdown").val("0").change();
        $("#opt_shippingaddress_71")[0].onclick();
    }
});
