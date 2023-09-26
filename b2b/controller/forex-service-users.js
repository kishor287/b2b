get_data(1, {});

function get_data(page, filter) {
  limit = 15;

  var param = new FormData();
  param.append("_action", "get_forex_users");
  param.append("payload", "forex-service-users");
  param.append("limit", limit);
  param.append("pagination", page);
  param.append("filter", JSON.stringify({ filter }));
  var res = run(param);
  let result = JSON.parse(res.responseText);
  result = result.data;
  let pagination = JSON.parse(res.responseText);
  pagination = pagination.pagination;
  let bodyHTML = "";
  let inc = 1;
  result.forEach((element) => {
    if (element.offer_letter == null || element.offer_letter == "") {
      element.offer_letter = "No file uploaded";
    } else {
      element.offer_letter = `<a href="https://demo2.innerxcrm.com/crm/${element.offer_letter}" target="_blank">Student Offer Letter</a>`;
    }
    if (element.adhar_card_copy == null || element.adhar_card_copy == "") {
      element.adhar_card_copy = "No file uploaded";
    } else {
      element.adhar_card_copy = `<a href="https://demo2.innerxcrm.com/crm/${element.adhar_card_copy}" target="_blank">Student Adhar Card</a>`;
    }
    if (element.passport_file == null || element.passport_file == "") {
      element.passport_file = "No file uploaded";
    } else {
      element.passport_file = `<a href="https://demo2.innerxcrm.com/crm/${element.passport_file}" target="_blank">Student Passport File</a>`;
    }
    if (element.visa_copy == null || element.visa_copy == "") {
      element.visa_copy = "No file uploaded";
    } else {
      element.visa_copy = `<a href="https://demo2.innerxcrm.com/crm/${element.visa_copy}" target="_blank">Student Visa File</a>`;
    }
    if (element.pan_card_copy == null || element.pan_card_copy == "") {
      element.pan_card_copy = "No file uploaded";
    } else {
      element.pan_card_copy = `<a href="https://demo2.innerxcrm.com/crm/${element.pan_card_copy}" target="_blank">Student Visa File</a>`;
    }
    if (element.cheque_copy == null || element.cheque_copy == "") {
      element.cheque_copy = "No file uploaded";
    } else {
      element.cheque_copy = `<a href="https://demo2.innerxcrm.com/crm/${element.cheque_copy}" target="_blank">Student Visa File</a>`;
    }


    var createdAt = new Date(element.created_at);
    createdAt = createdAt.toLocaleString();
    bodyHTML += `<tr><td>
    <button type="button" class="btn btn-primary bg-primary btn-sm" data-subdomain="${element.sub_domain}" data-bs-toggle="modal" data-status="${element.requeststatus}" data-id="${element.service_id}" data-remarks="${element.remarks}" data-service="FOREX"
        data-bs-target="#myModal" id="addRemarks">Action</button>
  </td>
  <td>${inc++}</td>
  <td>${element.organization}</td>
  <td>${element.sub_domain}</td>
  <td>${element.phone}</td>
  <td>${element.first_name}</td>
  <td>${element.last_name}</td>
  <td>${element.date_of_birth}</td>
  <td>${element.email}</td>
  <td>${element.passport}</td>
  <td>${element.college}</td>
  <td>${element.city}</td>
  <td>${element.country}</td>
  <td>${element.vendor_name}</td>
  <td>${element.ebix_order_id}</td>
  <td>${element.generated_order_id}</td>
  <td>${element.offer_letter}</td>
  <td>${element.passport_file}</td>
  <td>${element.visa_copy}</td>
  <td>${element.relationship.toUpperCase()}</td>
  <td>${element.adhar_card_copy}</td>
  <td>${element.pan_card_copy}</td>
  <td>${element.cheque_copy}</td>
  <td>${element.service}</td>
  <td>$ ${element.fee}</td>
  <td>${element.margin}</td>
  <td>${element.base_conversion_rate}</td>
  <td>${element.forex_conversion_rate}</td>
  <td>${element.conversion_rate}</td>
  <td>${element.calculated_fee}</td>
  <td>${element.gst}</td>
  <td>${element.processing_charges}</td>
  <td>${element.tcs}</td>
  <td>${element.total_payable}</td>
  <td class='recordRemarks'>${element.remarks}</td>
  <td>${element.currency}</td>
  <td>${element.account_num}</td>
  <td>${element.bank_branch_add}</td>
  <td>${element.swift_code}</td>
  <td>${element.msg}</td>
  <td>${element.iban_num}</td>
  <td>${element.bank_name}</td>
  <td>${element.branch}</td>
  <td>${element.beneficiary_name}</td>
  <td>${element.beneficiary_add}</td>
  <td>${createdAt}</td></tr>`;
  });
  if (result.length == 0) {
    $("tbody").html(
      '<tr><td colspan="25" class="text-center">No data found</td></tr>'
    );
  }
  $("tbody").html(bodyHTML);
  $("#pagination").html(pagination);
}

$(document).on("click","#addRemarks", function () {
  $("#requestRemarks").val('');
  $("#requestStatus").val('');
  $("#requestSubdomain").val('');
  $("#requestService").val('');
  let id = $(this).data("id");
  let remarks = $(this).closest('tr').find('.recordRemarks').text();
  let service = $(this).data("service");
  let status = $(this).data("status");
  let subdomain = $(this).data("subdomain");
  if (!status) {
    status = "UNDER REVIEW";
  }
  $("#requestRemarks").val(remarks);
  $("#requestStatus").val(status);
  $("#requestSubdomain").val(subdomain);
  $("#requestService").val(service);
  $("#requestId").val(id);
});

$("#saveRemarks").on("click", function () {
  let remarks = $("#requestRemarks").val();
  let status = $("#requestStatus").val();
  let service = $("#requestService").val();
  let id = $("#requestId").val();
  let subdomain = $("#requestSubdomain").val();
  let result = addRemarksToFintechRequests(id, service, remarks, status, subdomain);
  result = result.responseJSON.statusCode;
  if (result == 200) {
    $("#myModal").modal("hide");
    toastr.success("Remarks added successfully");
    get_data(1, {});
  }
});

$("body").on("click", "#_pagination", function () {
  get_data(($(this).attr('data-id')));
})