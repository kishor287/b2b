$('#vendorBank').on('submit', function (e) {
    // let form = $(this)[0];/
    e.preventDefault();
    let param = new FormData(this);
    param.append('_action', 'save');
    param.append('payload', 'vendor-bank');
    let res = run(param);
    console.log(res);
    if (res.status == 200) {
        get_data(1);
        $('#vendorBank')[0].reset();
        $('#vendorBankModal').modal('hide');
    }
});

/*

Table headings

*/

let table_cols = [
    'Action',
    'Vendor Name',
    'Bank Name',
    'Branch Name',
    'Account Number',
    'IFSC Code',
    'MICR Code'

];
table_head(table_cols);
get_data(1);
function get_data(page, filter = {}) {
    limit = 15;
    l_event = 'tbody';

    var param = new FormData();
    param.append("_action", "get");
    param.append("payload", "vendor-bank");
    param.append("limit", limit);
    param.append("pagination", page);
    var res = run(param);
    var response = JSON.parse((res.responseText));
    let vendorsHtml = '<option value="">Select</option>';
    let vendors = response.vendors;
    let banks = response.bankDetails;
    let banksDetailListing = '';
    vendors.forEach(element => {
        vendorsHtml += `<option value="${element.id}">${element.name}</option>`;
    });
    banks.forEach(element => {
        let status = '';
        if (element.is_active == 1) {
            status = 'checked';
        }
        banksDetailListing += `<tr id="record-${element.id}">
        <td>
        <button class="editForexRecord btn btn-sm btn-warning"
        data-id="${element.id}"
        data-vendor_id="${element.vendor_id}"
        data-name="${element.name}"
        data-branch="${element.branch}"
        data-account_no="${element.account_no}"
        data-ifsc="${element.ifsc}"
        data-url="${element.url}"
        data-micr="${element.micr}"
        >
        <i class="bx bx-edit"></i>
        </button>
        <button class="deleteRecord btn btn-sm btn-danger" onclick="deleteRecord.call(this)" data-id="${element.id}"><i class="bx bx-trash"></i></button>
        </td>
        <td id="vendor_name">${element.vendor_name}</td>
        <td id="bank_name">${element.name}</td>
        <td id="branch">${element.branch}</td>
        <td id="account_number">${element.account_no}</td>
        <td id="ifsc">${element.ifsc}</td>
        <td id="micr">${element.micr}</td>`;
    });

    $('#banksListing').html(banksDetailListing);
    $('.vendorsSelect').html(vendorsHtml);
}

$("body").on("click", "#_pagination", function () {
    get_data(($(this).attr('data-id')), filters(false, true, true, true));
});

$(document).on('click', '.editForexRecord', function () {
    $("#editForexModal").modal("show");
    let id = $(this).attr('data-id');
    let name = $(this).attr('data-name');
    let branch = $(this).attr('data-branch');
    let account_no = $(this).attr('data-account_no');
    let ifsc = $(this).attr('data-ifsc');
    let micr = $(this).attr('data-micr');
    let url = $(this).attr('data-url');
    let vendor_id = $(this).attr('data-vendor_id');

    $('#edit_id').val(id);
    $('#edit_name').val(name);
    $('#edit_branch').val(branch);
    $('#edit_account_number').val(account_no);
    $('#edit_ifsc').val(ifsc);
    $('#edit_micr').val(micr);
    $('#edit_bank_url').val(url);
    $('#edit_vendor_id').val(vendor_id);
    $("#editVendor").val(vendor_id).trigger('change');

});

$('#editVendorBank').on('submit', function (e) {

    e.preventDefault();
    let param = new FormData(this);
    param.append('_action', 'update');
    param.append('payload', 'vendor-bank');

    run(param);
    $('#editVendorBank')[0].reset();
    $('#editForexModal').modal('hide');
    get_data(1);
});


function deleteRecord(){
    console.log($(this).attr('data-id'));
    let id = $(this).attr('data-id');

    let param = new FormData();
    param.append('_action', 'delete');
    param.append('payload', 'vendor-bank');
    param.append('id', id);
    
    run(param);

    $(`#record-${id}`).remove();
}