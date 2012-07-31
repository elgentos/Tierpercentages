function calculatePrice(el) {
    id = el.id;
    normal_price = $('price').value;
    special_price = $('special_price').value;
    if(special_price.length===0) { price = normal_price; priceType="normal price"; } else { price = special_price; priceType = "special price"; }
    percentage = el.value;
    priceInput = id.replace('percentage','price');
    newPrice = price*(percentage/100);
    currPrice = $(priceInput).value;
    if(newPrice!=currPrice && confirm("Do you want to update this tier price from "+currPrice+" to "+newPrice+" based on the "+priceType+"?")) {
        $(priceInput).value = newPrice;
    }
}

document.observe("dom:loaded", function() {
    normal_price = $('price').value;
    special_price = $('special_price').value;

    $('price').observe('blur',function(event) {
        newPrice = $('price').value;
        if(normal_price!='' && normal_price!=newPrice && confirm("Do you want to update the tier prices?")) {
            $$('input.percentage').each(function (el) {
                id = el.id;
                price = $('price').value;
                percentage = el.value;
                priceInput = id.replace('percentage','price');
                $(priceInput).value = price*(percentage/100);
            });
        }
    });
    $('special_price').observe('blur',function(event) {
        newPrice = $('special_price').value;
        if(newPrice.length!==0 && special_price!=newPrice && confirm("Do you want to update the tier prices to adhere to the special price?")) {
            $$('input.percentage').each(function (el) {
                id = el.id;
                price = $('special_price').value;
                percentage = el.value;
                priceInput = id.replace('percentage','price');
                $(priceInput).value = price*(percentage/100);
            });
        }
    });
});