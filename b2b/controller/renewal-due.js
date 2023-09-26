const userRole = getRole();
if (userRole == 1) {
  $('.filters').removeClass('d-none');
}

function getRenewals(filter = {}) {
  let formRequest = new FormData();
  formRequest.append("_action", "getRenewals");
  formRequest.append("payload", "renewal-due");
  formRequest.append("filters", JSON.stringify(filter));
  let res = run(formRequest);
  let pricingHtml = "";
  if (res.responseJSON.data.length > 0) {
    let inc = 1;
    Object.entries(res.responseJSON.data).forEach(function ([key, value]) {
      let marketingPerson = '<b>Not Assigned</b>';
      if (value.marketingPersonFname) {
        marketingPerson = value.marketingPersonFname;
      }
      let supportPerson = '<b>Not Assigned</b>';
      if (value.fname) {
        supportPerson = value.fname;
      }
      pricingHtml += `<tr id="renewalRow_${value.id}">
          <td>${inc++}</td>
          <td>${value.organization}</td>
          <td class="contactPerson">${value.companyowner}</td>
          <td class="organizationPhone">${value.organization_phone}</td>
          <td class="marketingPerson">${marketingPerson}</td>
          <td class="supportPerson">${supportPerson}</td>
          <td class="planName"><span class="badge bg-warning p-1 text-dark">${value.planName.toUpperCase()}</span></td>
          <td class="renewDate">${new Date(value.to_date).toLocaleDateString()}</td>
          <td class="paymentMode">${value.payment_mode}</td>
          <td class="joiningDate">${new Date(value.agreement_signed_at).toLocaleDateString()}</td>
          </tr>`;
    });
  }
  if(!pricingHtml){
    pricingHtml = "<tr><th colspan='9'>Not Found</th></tr>";
  }
  $("#renewalDueBody").html(pricingHtml);
}

setTimeout(function () {
  getRenewals();
}, 500);

function getManagers() {
  let formRequest = new FormData();
  formRequest.append("_action", "get_managers");
  formRequest.append("payload", "all-lead");
  let response = run(formRequest);
  return response;
}

function getTrainers() {
  let formRequest = new FormData();
  formRequest.append("_action", "get_trainers");
  formRequest.append("payload", "all-lead");
  return run(formRequest);
}

const marketingManagers = getManagers().responseJSON;
const trainers = getTrainers().responseJSON;

let managersHtml = `<option value="">All</option>`;
Object.entries(marketingManagers).forEach(([key, value]) => {
  managersHtml += `<option value="${value.id}">${value.fname} ${value.lname}</option>`;
});
$("#managerFilter").html(managersHtml);

let trainersHtml = `<option value="">All</option>`;
Object.entries(trainers).forEach(([key, value]) => {
  trainersHtml += `<option value="${value.id}">${value.fname} ${value.lname}</option>`;
});
$("#trainerFilter").html(trainersHtml);

// Filter 
$(document).on('click', '#applyFilters', function () {
  let marketingPersonId = $("#managerFilter").val();
  let supportPersonId = $("#trainerFilter").val();

  let filter = {
    'marketingId': marketingPersonId,
    'supportId': supportPersonId
  };

  getRenewals(filter);
}); 

$(document).on('click','#resetFilters',function(){
  getRenewals();
})

$(document).on('click','#export',function(){
  exportTableToExcel('renewalsDataTable','renewals');
});