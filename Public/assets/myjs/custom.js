// Add new product row
$('#addproduct').on('click', function () {
    var taxOn = $('#tax_status').val();
    var disOn = $('#discount_handle').val();
    var ganakChun = $('#ganak');
    var ganak = ganakChun.val();
    var cvalue = parseInt(ganak) + 1;
    var functionNum = "'" + cvalue + "'";
    count = $('#saman-row div').length;
    var currency = $('#currency').val(); // Ensure currency is defined

    // Product row HTML with standardized product-search class
    var data = `
        <tr>
            <td><input type="text" class="form-control text-center product-search" name="product_name[]" placeholder="Enter Product name or Code" id="productname-${cvalue}" autocomplete="off"></td>
            <td><input type="text" class="form-control req amnt" name="product_qty[]" id="amount-${cvalue}" onkeypress="return isNumber(event)" onkeyup="rowTotal(${functionNum}); billUpyog();" autocomplete="off" value="1"></td>
            <td><input type="text" class="form-control req prc" name="product_price[]" id="price-${cvalue}" onkeypress="return isNumber(event)" onkeyup="rowTotal(${functionNum}); billUpyog();" autocomplete="off"></td>
            <td><input type="text" class="form-control vat" name="product_tax[]" id="vat-${cvalue}" onkeypress="return isNumber(event)" onkeyup="rowTotal(${functionNum}); billUpyog();" autocomplete="off"></td>
            <td id="texttaxa-${cvalue}" class="text-center">0</td>
            <td><input type="text" class="form-control discount" name="product_discount[]" onkeypress="return isNumber(event)" id="discount-${cvalue}" onkeyup="rowTotal(${functionNum}); billUpyog();" autocomplete="off"></td>
            <td><span class="currenty">${currency}</span> <strong><span class="ttlText" id="result-${cvalue}">0</span></strong></td>
            <td class="text-center"><button type="button" data-rowid="${cvalue}" class="btn btn-danger removeProd" title="Remove"><i class="icon-minus-square"></i></button></td>
            <input type="hidden" name="taxa[]" id="taxa-${cvalue}" value="0">
            <input type="hidden" name="disca[]" id="disca-${cvalue}" value="0">
            <input type="hidden" class="ttInput" name="product_subtotal[]" id="total-${cvalue}" value="0">
            <input type="hidden" class="pdIn" name="pid[]" id="pid-${cvalue}" value="0">
        </tr>
        <tr>
            <td colspan="8"><textarea class="form-control" id="dpid-${cvalue}" name="product_description[]" placeholder="Enter Product description" autocomplete="off"></textarea><br></td>
        </tr>`;

    $('tr.last-item-row').before(data);

    // Initialize autocomplete for the new row
    $(`#productname-${cvalue}`).autocomplete({
        source: function (request, response) {
            $.ajax({
                url: baseurl + 'search_products/' + billtype,
                dataType: "json",
                method: 'post',
                data: {
                    name_startsWith: request.term,
                    type: 'product_list',
                    row_num: cvalue,
                    wid: $("#warehouses option:selected").val()
                },
                success: function (data) {
                    response($.map(data, function (item) {
                        var product_d = item[0];
                        return {
                            label: product_d,
                            value: product_d,
                            data: item
                        };
                    }));
                }
            });
        },
        autoFocus: true,
        minLength: 0,
        select: function (event, ui) {
            var id_arr = $(this).attr('id');
            var id = id_arr.split("-")[1];
            $(`#amount-${id}`).val(1);
            $(`#price-${id}`).val(ui.item.data[1]);
            $(`#pid-${id}`).val(ui.item.data[2]);
            $(`#vat-${id}`).val(ui.item.data[3] || 0);
            $(`#discount-${id}`).val(ui.item.data[4] || 0);
            $(`#dpid-${id}`).val(ui.item.data[5] || '');
            rowTotal(id);
            billUpyog();
        },
        create: function (e) {
            $(this).prev('.ui-helper-hidden-accessible').remove();
        }
    });

    ganakChun.val(cvalue);
    billUpyog();
    var sideh2 = document.getElementById('rough').scrollHeight;
    var opx3 = sideh2 + 50;
    document.getElementById('rough').style.height = opx3 + "px";
});

// Calculations
var precentCalc = function (total, percentageVal) {
    return (total / 100) * (parseFloat(percentageVal) || 0);
};

var deciFormat = function (minput) {
    return parseFloat(minput || 0).toFixed(2);
};

var formInputGet = function (iname, inumber) {
    var inputId = iname + "-" + inumber;
    var inputValue = $(inputId).val();
    return parseFloat(inputValue) || 0;
};

var shipTot = function () {
    var shipVal = $('.shipVal').val();
    return deciFormat(shipVal);
};

var samanYog = function () {
    var itempriceList = [];
    $('.ttInput').each(function () {
        var vv = parseFloat($(this).val()) || 0;
        itempriceList.push(vv);
    });

    var sum = 0, taxc = 0, discs = 0;
    var ganak = parseInt($("#ganak").val()) + 1;
    for (var z = 0; z < ganak; z++) {
        if (parseFloat(itempriceList[z]) > 0) {
            sum += parseFloat(itempriceList[z]);
        }
        if (parseFloat($(`#taxa-${z}`).val()) > 0) {
            taxc += parseFloat($(`#taxa-${z}`).val());
        }
        if (parseFloat($(`#disca-${z}`).val()) > 0) {
            discs += parseFloat($(`#disca-${z}`).val());
        }
    }
    $("#discs").html(deciFormat(discs));
    $("#taxr").html(deciFormat(taxc));
    return deciFormat(sum);
};

var updateTotal = function () {
    var totalBillVal = deciFormat(parseFloat(samanYog()) + parseFloat(shipTot()));
    $("#invoiceyoghtml").val(totalBillVal);
    $("#mahayog").html(totalBillVal);
    return totalBillVal;
};

var billUpyog = function () {
    $("#subttlform").val(samanYog());
    $("#invoiceyoghtml").val(updateTotal());
};

var rowTotal = function (numb) {
    var amountVal = formInputGet("#amount", numb);
    var priceVal = formInputGet("#price", numb);
    var discountVal = formInputGet("#discount", numb);
    var vatVal = formInputGet("#vat", numb);
    var tax_status = $("#tax_status").val();
    var disFormat = $("#discount_format").val();

    var totalPrice = amountVal * priceVal;
    var taxo = 0, disco = 0, totalValue;

    if (disFormat == '%' || disFormat == 'flat') {
        // Discount after tax
        if (tax_status == 'yes') {
            taxo = precentCalc(totalPrice, vatVal);
            totalValue = parseFloat(totalPrice) + parseFloat(taxo);
        } else {
            totalValue = totalPrice;
        }
        if (disFormat == 'flat') {
            disco = deciFormat(discountVal);
            totalValue = parseFloat(totalValue) - parseFloat(discountVal);
        } else if (disFormat == '%') {
            disco = precentCalc(totalValue, discountVal);
            totalValue = parseFloat(totalValue) - parseFloat(disco);
        }
    } else {
        // Discount before tax
        if (disFormat == 'bflat') {
            disco = deciFormat(discountVal);
            totalValue = parseFloat(totalPrice) - parseFloat(discountVal);
        } else if (disFormat == 'b_p') {
            disco = precentCalc(totalPrice, discountVal);
            totalValue = parseFloat(totalPrice) - parseFloat(disco);
        } else {
            totalValue = totalPrice;
        }
        if (tax_status == 'yes') {
            taxo = precentCalc(totalValue, vatVal);
            totalValue = parseFloat(totalValue) + parseFloat(taxo);
        }
    }

    totalValue = deciFormat(totalValue);
    taxo = deciFormat(taxo);
    disco = deciFormat(disco);

    $(`#result-${numb}`).html(totalValue);
    $(`#taxa-${numb}`).val(taxo);
    $(`#texttaxa-${numb}`).text(taxo);
    $(`#disca-${numb}`).val(disco);
    $(`#total-${numb}`).val(totalValue);
    billUpyog();
};

var changeTaxFormat = function (getSelectv) {
    if (getSelectv == 'on') {
        $(".tax_col").show();
        $("#tax_status").val('yes');
        $("#tax_format").val('%');
    } else {
        $("#tax_status").val('no');
        $("#tax_format").val('off');
        $(".tax_col").hide();
        $('.vat').val(0); // Reset tax inputs when tax is off
    }
    var discount_handle = $("#discount_format").val();
    formatRest($("#tax_status").val(), discount_handle);
};

var changeDiscountFormat = function (getSelectv) {
    if (getSelectv != '0') {
        $(".disCol").show();
        $("#discount_handle").val('yes');
        $("#discount_format").val(getSelectv);
    } else {
        $("#discount_format").val(getSelectv);
        $(".disCol").hide();
        $("#discount_handle").val('no');
        $('.discount').val(0); // Reset discount inputs when discount is off
    }
    var tax_status = $("#tax_status").val();
    formatRest(tax_status, getSelectv);
};

var formatRest = function (taxFormat, disFormat) {
    var amntArray = [], prcArray = [], vatArray = [], discountArray = [];
    $('.amnt').each(function () { amntArray.push(parseFloat($(this).val()) || 0); });
    $('.prc').each(function () { prcArray.push(parseFloat($(this).val()) || 0); });
    $('.vat').each(function () { vatArray.push(parseFloat($(this).val()) || 0); });
    $('.discount').each(function () { discountArray.push(parseFloat($(this).val()) || 0); });

    var taxr = 0, discsr = 0;
    for (var i = 0; i < amntArray.length; i++) {
        var amtVal = amntArray[i];
        var prcVal = prcArray[i];
        var vatVal = vatArray[i];
        var discountVal = discountArray[i];
        var result = amtVal * prcVal;

        var taxo = 0, disco = 0;
        if (disFormat == '%' || disFormat == 'flat') {
            // Discount after tax
            if (taxFormat == 'yes') {
                taxo = precentCalc(result, vatVal);
                result = parseFloat(result) + parseFloat(taxo);
                taxr += parseFloat(taxo);
            } else {
                taxo = 0;
            }
            if (disFormat == '%') {
                disco = precentCalc(result, discountVal);
                result = parseFloat(result) - parseFloat(disco);
                discsr += parseFloat(disco);
            } else if (disFormat == 'flat') {
                disco = discountVal;
                result = parseFloat(result) - parseFloat(discountVal);
                discsr += parseFloat(discountVal);
            }
        } else {
            // Discount before tax
            if (disFormat == 'b_p') {
                disco = precentCalc(result, discountVal);
                result = parseFloat(result) - parseFloat(disco);
                discsr += parseFloat(disco);
            } else if (disFormat == 'bflat') {
                disco = discountVal;
                result = parseFloat(result) - parseFloat(discountVal);
                discsr += parseFloat(discountVal);
            }
            if (taxFormat == 'yes') {
                taxo = precentCalc(result, vatVal);
                result = parseFloat(result) + parseFloat(taxo);
                taxr += parseFloat(taxo);
            } else {
                taxo = 0;
            }
        }

        result = deciFormat(result);
        taxo = deciFormat(taxo);
        disco = deciFormat(disco);

        $(`#total-${i}`).val(result);
        $(`#result-${i}`).html(result);
        $(`#texttaxa-${i}`).html(taxFormat == 'yes' ? taxo : 'Off');
        $(`#taxa-${i}`).val(taxo);
        $(`#disca-${i}`).val(disco);
    }

    var sum = deciFormat(samanYog());
    var itemSum = shipTot();
    var totl = deciFormat(parseFloat(sum) + parseFloat(itemSum));
    $("#subttlform").val(sum);
    $("#subttlid").html(sum);
    $("#mahayog").html(totl);
    $("#taxr").html(deciFormat(taxr));
    $("#discs").html(deciFormat(discsr));
    $("#invoiceyoghtml").val(totl);
};