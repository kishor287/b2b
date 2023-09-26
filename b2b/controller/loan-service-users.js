get_data(1, {});

function get_data(page, filter) {
  limit = 15;

  var param = new FormData();
  param.append("_action", "get_loan_users");
  param.append("payload", "loan-service-users");
  param.append("limit", limit);
  param.append("pagination", page);
  param.append("filter", JSON.stringify({ filter }));
  var res = run(param);
  let result = JSON.parse(res.responseText);
  result = result.data;
  let bodyHTML = "";
  let pagination = result.pagination;
  result.forEach((element) => {
    if (element.offer_letter == null || element.offer_letter == "") {
      element.offer_letter = "No file uploaded";
    } else {
      element.offer_letter = `<a href="https://demo2.innerxcrm.com/crm/${element.offer_letter}" target="_blank">Offer Letter</a>`;
    }

    if (element.passport_file == null || element.passport_file == "") {
      element.passport_file = "No file uploaded";
    } else {
      element.passport_file = `<a href="https://demo2.innerxcrm.com/crm/${element.passport_file}" target="_blank"> Passport File</a>`;
    }
    if (element.visa_copy == null || element.visa_copy == "") {
      element.visa_copy = "No file uploaded";
    } else {
      element.visa_copy = `<a href="https://demo2.innerxcrm.com/crm/${element.visa_copy}" target="_blank"> Visa File</a>`;
    }

    var createdAt = new Date(element.created_at);
    createdAt = createdAt.toLocaleString();
    bodyHTML += `<tr><td>
    <button type="button" class="btn btn-primary bg-primary btn-sm" data-bs-toggle="modal" data-status="${element.requeststatus}" data-id="${element.service_id}" data-remarks="${element.remarks}" data-service="LOAN"
        data-bs-target="#myModal" id="addRemarks">Action</button>
  </td>
  <td>${element.organization}</td>
  <td>${element.first_name}</td>
  <td>${element.last_name}</td>
  <td>${element.date_of_birth}</td>
  <td>${element.email}</td>
  <td>${element.passport}</td>
  <td>${element.college}</td>
  <td>${element.city}</td>
  <td>${element.country}</td>
  <td>${element.offer_letter}</td>
  <td>${element.passport_file}</td>
  <td>${element.visa_copy}</td>
  <td class='recordRemarks'>${element.remarks}</td>
  <td>${createdAt}</td></tr>`;
  });
  if (result.length == 0) {
    $("tbody").html(
      '<tr><td colspan="25" class="text-center">No data found</td></tr>'
    );
    return;
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