const getSalesDetails = (page = 1, filter = {}) => {
  $("#salesTbody").html(`<tr><td colspan="10">Fetching...</td></tr>`);
  let formRequest = new FormData();
  formRequest.append("_action", "getSalesDetails");
  formRequest.append("payload", "add-sales");
  formRequest.append("page", page);
  formRequest.append("filter", JSON.stringify(filter));
  let result = run(formRequest);
  let detailsHtml = "";
  //   <td>
  //   <button data-id="${element.id}" class="btn rounded-pill btn-sm  btn-icon btn-primary editSalesBtn"><span class="tf-icons bx bx-edit"></span></button>
  //   <button data-id="${element.id}" class="btn rounded-pill btn-sm  btn-icon btn-danger deleteSalesBtn"><span class="tf-icons bx bx-trash"></span></button>
  //  </td>
  if (result.responseJSON.statusCode == 200) {
    let increamentKey = 1;
    if (result.responseJSON.data.length > 0) {
      result.responseJSON.data.forEach((element) => {
        let agreementStatus = "UNSIGNED";
        let trainingDateTime = "";
        let contractedAt = "";
        if (element.contracted_at) {
          agreementStatus = "SIGNED";
        }
        if (element.next_followup_date) {
          trainingDateTime = new Date(
            element.next_followup_date
          ).toLocaleDateString();
        }
        if (element.contracted_at) {
          contractedAt = new Date(element.contracted_at).toLocaleDateString();
        }
        detailsHtml += `<tr id="agentBaseDetailRow_${element.id}">  
         <td class="agent_name">${increamentKey++}</td>
         <td class="agent_name">${element.organization}</td>
         <td>${element.fname}</td>
         <td>${new Date(element.created_at).toLocaleDateString()}</td>
         <td class="location">${element.address}</td>
         <td class="email_id">${element.organizatio_email}</td>
         <td class="contact_number">${element.organization_phone}</td>
         <td class="agreement_status">${agreementStatus}</td>
         <td class="contract_signed_at" >${contractedAt}</td>
         <td class="training_date" >${trainingDateTime}</td>
         <td class="purpose">${element.crm_soft}</td>
         <td class="gic_bank">${element.prefered_bank_name}</td>
         <td class="gic_commitment">${element.committed}</td>
         <td class="gic_reward">${element.reward}</td>
         <td class="total_gic">${element.total_gic}</td>
         <td class="remarks">${element.remarks}</td>
       </tr>`;
      });
    }
    if (detailsHtml == "") {
      detailsHtml = `<tr><td colspan="10">Not Found</td></tr>`;
    }
    $("#salesTbody").html(detailsHtml);
    $("#pagination").html(result.responseJSON.pagination);
  }
};

getSalesDetails();

function getCurrentPage() {
  if ($(".page-item.active").text() !== "") {
    return $(".page-item.active").text();
  } else {
    return 1;
  }
}

$("body").on("click", "#_pagination", function () {
  getSalesDetails($(this).attr("data-id"));
});

// Edit Details
$(document).on("click", ".editSalesBtn", function () {
  let recordId = $(this).attr("data-id");
  let agentName = $(this).closest("tr").find(".agent_name").text();
  let location = $(this).closest("tr").find(".location").text();
  let emailId = $(this).closest("tr").find(".email_id").text();
  let contactNumber = $(this).closest("tr").find(".contact_number").text();
  let agreementStatus = $(this).closest("tr").find(".agreement_status").text();
  let contractSignedAt = $(this)
    .closest("tr")
    .find(".contract_signed_at")
    .data("date");
  let trainingDate = $(this).closest("tr").find(".training_date").data("date");
  let trainingTime = $(this).closest("tr").find(".training_time").text();
  let purpose = $(this).closest("tr").find(".purpose").text();
  let gicBank = $(this).closest("tr").find(".gic_bank").text();
  let gicCommitment = $(this).closest("tr").find(".gic_commitment").text();
  let gicReward = $(this).closest("tr").find(".gic_reward").text();
  let totalGic = $(this).closest("tr").find(".total_gic").text();
  let remittanceCommitment = $(this)
    .closest("tr")
    .find(".remittance_commitment")
    .text();
  let remittanceReward = $(this)
    .closest("tr")
    .find(".remittance_reward")
    .text();
  let followUpDate = $(this).closest("tr").find(".followup_date").data("date");
  let remarks = $(this).closest("tr").find(".remarks").text();

  $("#agentName").val(agentName);
  $("#recordId").val(recordId);
  $("#location").val(location);
  $("#emailId").val(emailId);
  $("#contactNumber").val(contactNumber);
  $("#agreementStatus").val(agreementStatus);
  $("#contractSignedAt").val(contractSignedAt);
  $("#trainingDate").val(trainingDate);
  $("#trainingTime").val(trainingTime);
  $("#purpose").val(purpose);
  $("#gicBank").val(gicBank);
  $("#gicCommitment").val(gicCommitment);
  $("#gicReward").val(gicReward);
  $("#totalGic").val(totalGic);
  $("#remittanceCommitement").val(remittanceCommitment);
  $("#remittanceReward").val(remittanceReward);
  $("#followUpDate").val(followUpDate);
  $("#remarks").val(remarks);

  $("#editSalesModal").modal("show");
});

$(document).on("submit", "#updateSales", function (e) {
  e.preventDefault();
  let formRequest = new FormData(this);
  let result = run(formRequest);
  if (result.responseJSON.statusCode == 200) {
    getSalesDetails(getCurrentPage());
    $("#editSalesModal").modal("hide");
  }
});

$(document).on("click", ".deleteSalesBtn", function () {
  if (confirm("Are you sure! You want to delete this record?")) {
    let id = $(this).data("id");
    let formRequest = new FormData();
    formRequest.append("id", id);
    formRequest.append("payload", "add-sales");
    formRequest.append("_action", "deleteSales");
    let res = run(formRequest);
    if (res.responseJSON.statusCode == 200) {
      getSalesDetails();
    }
  }
});

$(document).on("change", "#daterange", function () {
  let dateRange = $(this).val();
  getSalesDetails(getCurrentPage(), { dateRange: dateRange });
});
