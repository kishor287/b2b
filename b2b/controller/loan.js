const getLoanSimDetails = (page = 1, filter = {}) => {
  $("#loanBody").html(`<tr><td colspan="10">Fetching...</td></tr>`);
  let formRequest = new FormData();
  formRequest.append("_action", "getLoanDetails");
  formRequest.append("payload", "add-loan");
  formRequest.append("page", page);
  formRequest.append("filter", JSON.stringify(filter));
  let result = run(formRequest);
  let detailsHtml = "";
  if (result.responseJSON.statusCode == 200) {
    let key = 1;
    result.responseJSON.data.forEach((element) => {
      detailsHtml += `<tr id="loanRow_${element.id}">  
         <td>
          <button data-id="${
            element.id
          }" class="btn rounded-pill btn-sm  btn-icon btn-primary editLoanBtn"><span class="tf-icons bx bx-edit"></span></button>
          <button data-id="${
            element.id
          }" class="btn rounded-pill btn-sm  btn-icon btn-danger deleteLoanBtn"><span class="tf-icons bx bx-trash"></span></button>
         </td>
         <td class="date" data-date="${element.created_at}">${new Date(
        element.created_at
      ).toLocaleDateString()}</td>
         <td class="agent_name">${element.agent_name}</td>
         <td class="student_name">${element.student_name}</td>
         <td class="contact_number">${element.contact_number}</td>
         <td class="location">${element.location}</td>
         <td class="amount">${element.amount}</td>
         <td class="status">${element.status}</td>
         <td class="bank_name">${element.bank_name}</td>
       </tr>`;
    });
    if (detailsHtml == "") {
      detailsHtml = `<tr><td colspan="10">Not Found</td></tr>`;
    }
    $("#loanBody").html(detailsHtml);
    $("#pagination").html(result.responseJSON.pagination);
  }
};

getLoanSimDetails();

function getCurrentPage() {
  if ($(".page-item.active").text() !== "") {
    return $(".page-item.active").text();
  } else {
    return 1;
  }
}

$("body").on("click", "#_pagination", function () {
  getLoanSimDetails($(this).attr("data-id"));
});

// Edit Details
$(document).on("click", ".editLoanBtn", function () {
  let recordId = $(this).attr("data-id");
  let agentName = $(this).closest("tr").find(".agent_name").text();
  let date = $(this).closest("tr").find(".date").data("date");
  let studentName = $(this).closest("tr").find(".student_name").text();
  let contactNumber = $(this).closest("tr").find(".contact_number").text();
  let location = $(this).closest("tr").find(".location").text();
  let amount = $(this).closest("tr").find(".amount").text();
  let status = $(this).closest("tr").find(".status").text();
  let bankName = $(this).closest("tr").find(".bank_name").text();

  $("#agentName").val(agentName);
  $("#bankName").val(bankName);
  $("#recordId").val(recordId);
  $("#location").val(location);
  $("#studentName").val(studentName);
  $("#contactNumber").val(contactNumber);
  $("#amount").val(amount);
  $("#status").val(status);

  $("#editLoanModal").modal("show");
});

$(document).on("submit", "#updateLoan", function (e) {
  e.preventDefault();
  let formRequest = new FormData(this);
  let result = run(formRequest);
  if (result.responseJSON.statusCode == 200) {
    getLoanSimDetails(getCurrentPage());
    $("#editLoanModal").modal("hide");
  }
});

$(document).on("click", ".deleteLoanBtn", function () {
  if (confirm("Are you sure! You want to delete this record?")) {
    let id = $(this).data("id");
    let formRequest = new FormData();
    formRequest.append("id", id);
    formRequest.append("payload", "add-loan");
    formRequest.append("_action", "deleteLoan");
    let res = run(formRequest);
    if (res.responseJSON.statusCode == 200) {
      getLoanSimDetails();
    }
  }
});

$(document).on("change", "#daterange", function () {
  let dateRange = $(this).val();
  getLoanSimDetails(getCurrentPage(), { dateRange: dateRange });
});
