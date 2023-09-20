//branch
function quickAddBranch(obj) {
    $(obj).closest('.branch').find('.new_branch').toggle('fast');
}

function saveBranch(obj) {
    let branchInput = $(obj).closest('.new_branch').find('input:text');
    if (branchInput.val().length < 1) {
        alert("Enter valid branch name");
        branchInput.focus();
        return;
    }

    let spinner = $(obj).closest('.new_branch').find('.loading_spinner');
    spinner.show();
    let name = branchInput.val();
    $.post('?module=branches&action=saveNewBranch&format=json', {name: name}, function (data) {
        let result = JSON.parse(data);
        console.log(result);
        if (result[0].status == "error") {
            triggerError(result[0].details);
            branchInput.focus();
        } else {
            triggerMessage(result[0].details);
            setTimeout(function () {
                branchInput.val('');
                $(obj).closest('.new_branch').toggle('fast');
            }, 1200);

        }
        spinner.hide();
    });
}

//department
function quickAddDepartment(obj) {
    $(obj).closest('.department').find('.new_department').toggle('fast');
}

function saveDepartment(obj) {
    let departmentInput = $(obj).closest('.new_department').find('input:text');
    if (departmentInput.val().length < 1) {
        alert("Enter valid department name");
        departmentInput.focus();
        return;
    }

    let spinner = $(obj).closest('.new_department').find('.loading_spinner');
    spinner.show();
    let name = departmentInput.val();
    $.post('?module=departments&action=saveNewDepartment&format=json', {name: name}, function (data) {
        let result = JSON.parse(data);
        console.log(result);
        if (result[0].status == "error") {
            triggerError(result[0].details);
            departmentInput.focus();
        } else {
            triggerMessage(result[0].details);
            setTimeout(function () {
                departmentInput.val('');
                $(obj).closest('.new_department').toggle('fast');
            }, 1200);

        }
        spinner.hide();
    });
}

//brand
function quickAddBrand(obj) {
    $(obj).closest('.brand').find('.new_brand').toggle('fast');
}

function saveBrand(obj) {
    let brandInput = $(obj).closest('.new_brand').find('input:text');
    if (brandInput.val().length < 1) {
        alert("Enter valid brand name");
        brandInput.focus();
        return;
    }

    let spinner = $(obj).closest('.new_brand').find('.loading_spinner');
    spinner.show();
    let name = brandInput.val();
    $.post('?module=model&action=saveNewModel&format=json', {name: name}, function (data) {
        let result = JSON.parse(data);
        console.log(result);
        if (result[0].status == "error") {
            triggerError(result[0].details);
            brandInput.focus();
        } else {
            triggerMessage(result[0].details);
            setTimeout(function () {
                brandInput.val('');
                $(obj).closest('.new_brand').toggle('fast');
            }, 1200);

        }
        spinner.hide();
    });
}

//category
function quickAddProductCategory(obj) {
    $(obj).closest('.productcategory').find('.new_product_category').toggle('fast');
}

function saveProductCategory(obj) {
    let categoryInput = $(obj).closest('.new_product_category').find('input:text');
    if (categoryInput.val().length < 1) {
        alert("Enter valid category name");
        categoryInput.focus();
        return;
    }

    let spinner = $(obj).closest('.new_product_category').find('.loading_spinner');
    spinner.show();
    let name = categoryInput.val();
    $.post('?module=product_categories&action=saveNewCategory&format=json', {name: name}, function (data) {
        let result = JSON.parse(data);
        console.log(result);
        if (result[0].status == "error") {
            triggerError(result[0].details);
            categoryInput.focus();
        } else {
            triggerMessage(result[0].details);
            setTimeout(function () {
                categoryInput.val('');
                $(obj).closest('.new_product_category').toggle('fast');
            }, 1200);

        }
        spinner.hide();
    });
}

//fetch subcategories
function fetchSubcategories(obj) {
    $("#subcategories").empty();

    let categoryId = $(obj).val();

    $.get("?module=product_categories&action=getSubcategories&format=json&categoryId=" + categoryId, null, function (data) {
        let results = JSON.parse(data);
        console.log(results);

        $("#subcategories").append(`<option selected disabled > --Choose Subcategory-- </option>`);
        $.each(results[0], function (index, item) {
            let option = `<option value="${item.id}">${item.name}</option>`;
            $("#subcategories").append(option);
        });

        triggerMessage('Choose subcategory', 4000);
        $("#subcategories").focus();
    });
}

//fetch bulk units
function checkbulk(obj) {
    $(".bulk_units").empty();
    let unit = $(obj).val();

    $.get("?module=bulk_units&action=getbulk&format=json&unit=" + unit, null, function (data) {
        let bulkunits = eval(data);
        $.each(bulkunits[0].bulkunits, function (index, bulkunit) {
            let option = "<option value='" + bulkunit.id + "'>" + bulkunit.name + " (" + bulkunit.rate + ")</option>";
            $(".bulk_units").append(option);
        });
    });
}

//init ajax select2
let initSelectAjax = function (selector, url, placeholder = '', miniInputLength = 2) {
    return $(selector).select2({
        placeholder: placeholder,
        width: '100%', minimumInputLength: miniInputLength,
        ajax: {
            url: url,
            dataType: 'json',
            delay: 250,
            quietMillis: 200,
            data: function (term) {
                return {search: term};
            },
            results: function (data, page) {
                return {result: data};
            }
        }
    });
};

//init product ajax select2
let initSelectAjaxWithDesc = function (selector, url, placeholder = '', miniInputLength = 2) {
    return $(selector).select2({
        placeholder: placeholder,
        width: '100%', minimumInputLength: miniInputLength,
        language: {
            searching: function() {
                let spinner = $('#select2-ajax-custom-loading-spinner').clone().css({
                    'height':'',
                    'width':'',
                    'visibility':''
                });
                return $(spinner);
            }
        },
        ajax: {
            url: url,
            dataType: 'json',
            delay: 250,
            quietMillis: 200,
            data: function (term) {
                return {search: term};
            }
        },
        templateResult: function (state) {
            if (!state.id) return state.text;
            let barcode_text = '', stock_text = '';
            if (state.barcode !=null) barcode_text = `<p class="p-none m-none">Barcode: <span class="text-weight-semibold">${state.barcode}</span></p>`;
            if (state.stock_qty !=null) stock_text = `<p class="p-none m-none">Stock qty: <span class="text-weight-semibold">${state.stock_qty}</span></p>`;
            // console.log(state);
            let template = `<div class="mb-md">
                             <p class="p-none m-none text-weight-semibold">${state.text}</p>
                             ${barcode_text}
                             ${stock_text}
                             <p class="p-none m-none">${state.description}</p>
                            </div>`;
            return $(template);
        }
    });
};