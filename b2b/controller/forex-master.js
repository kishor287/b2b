$('#forexMaster').on('submit', function (e) {
    // let form = $(this)[0];/
    e.preventDefault();
    let param = new FormData(this);
    param.append('_action', 'save');
    param.append('payload', 'forex-master');
    let res = run(param);
    console.log(res);
    if (res.status == 200) {
        get_data(1);
        $('#forexMaster')[0].reset();
        $('#forexModal').modal('hide');
    }
});

/*

Table headings

*/

let table_cols = [
    'Action',
    'Status',
    'Currency',
    'Vendor Name',
    'Today Rate',
];
table_head(table_cols);
get_data(1);
function get_data(page, filter = {}) {


    limit = 15;
    l_event = 'tbody';

    var param = new FormData();
    param.append("_action", "get");
    param.append("payload", "forex-master");
    param.append("limit", limit);
    param.append("pagination", page);
    var res = run(param);
    var response = JSON.parse((res.responseText));
    let forexsHtml = '';
    let forexs = response.data;

    forexs.forEach((element) => {
        let status = '';
        if (element.status == 1) {
            status = 'checked';
        } else {
            status = '';
        }
        forexsHtml += `<tr id="record-${element.id}">
       <td>
       <button class="editForexRecord btn btn-sm btn-warning" data-bankname="${element.name}" data-rate="${element.rate}" data-id="${element.id}" data-currency="${element.currency}"><i class="bx bx-edit"></i></button>
       <button class="removeForexRecord btn btn-sm btn-danger" data-id="${element.id}" onclick="deleteRecord.call(this)" ><i class="bx bx-trash"></i></button>
       </td>
       <td class="status">
       <div class="form-check form-switch">
       <input class="form-check-input" data-id="${element.id}" type="checkbox" onchange="changeStatus.call(this)" ${status} id="statusSwitchButton">
     </div>
       </td>
       <td class="currency">${element.currency}</td>
       <td class="bank_name">${element.name}</td>
       <td class="rate">${element.rate}</td>
    </tr>`;
    });
    if (forexs.length == 0) {
        forexsHtml = '<tr><td colspan="3">No data found</td></tr>';
    }
    $('#forexListing').html(forexsHtml);
    let pagination = response.data.pagination;
    $(".pagination").html(pagination)
}

$("body").on("click", "#_pagination", function () {
    get_data(($(this).attr('data-id')), filters(false, true, true, true));
});

$(document).on('click', '.editForexRecord', function () {
    $("#editForexModal").modal("show");
    let bankName = $(this).attr('data-bankname');
    let currency = $(this).attr('data-currency');
    let rate = $(this).attr('data-rate');
    let id = $(this).attr('data-id');
    
    console.log(bankName, currency, rate, id);
    $('#editCurrency').val(currency);
    $('#editBankName').val(bankName);
    $('#editRate').val(rate);
    $('#editForexId').val(id);
    $('#editForexModal').modal('show');
});

$('#editforexMaster').on('submit', function (e) {
    // let

    e.preventDefault();
    let param = new FormData(this);
    param.append('_action', 'update');
    param.append('payload', 'forex-master');

    let res = run(param);

    if (res.status == 200) {
        $('#editforexMaster')[0].reset();
        $('#editForexModal').modal('hide');
        $(`#record-${param.get('id')} .currency`).html(`${param.get('currency')}`);
        $(`#record-${param.get('id')} .rate`).html(`${param.get('today_rate')}`);
    }
});

function changeStatus() {
    let status = 1;
    if ($(this).prop('checked')) {
        status = 1;
    } else {
        status = 0;
    }

    let id = $(this).attr('data-id');
    console.log(id);
    let param = new FormData();
    param.append('_action', 'update');
    param.append('payload', 'forex-master');
    param.append('id', id);
    param.append('status', status);
    let res = run(param);
    console.log(res);
}

function deleteRecord(){
console.log('its here');
    let id = $(this).attr('data-id');
    let param = new FormData();
    param.append('_action', 'deleteRecord');
    param.append('payload', 'forex-master');
    param.append('id', id);
    param.append('status', 0);
    console.log('made a request');
    let res = run(param);
    console.log('got a response');
    console.log(res);
    $(`#record-${id}`).remove();
}