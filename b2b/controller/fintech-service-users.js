$("#forexMaster").on("submit", function (e) {
  // let form = $(this)[0];/
  e.preventDefault();
  let param = new FormData(this);
  param.append("_action", "save");
  param.append("payload", "forex-master");
  let res = run(param);
  console.log(res);
  if (res.status == 200) {
    get_data(1);
    $("#forexMaster")[0].reset();
    $("#forexModal").modal("hide");
  }
});

/*
Table headings
*/

let table_cols = [
  "Date",
  "Organisation",
  "GIC",
  "Forex",
  "Credit Card",
  "SIM",
  "Loan",
  "Insurance",
  // "Payment Status",
];
table_head(table_cols);
get_data(1);
function get_data(page, filter = {}) {
  limit = 15;
  l_event = "tbody";
  var param = new FormData();
  param.append("_action", "getFintechServiceUsers");
  param.append("payload", "fintech-service-users");
  param.append("limit", limit);
  param.append("pagination", page);
  param.append("filter", JSON.stringify({ filter }));
  var res = run(param);
  var response = JSON.parse(res.responseText);
  let fintechServicesHTML = "";
  let fintechServices = response.subdomains.data;
  const year = response.year;
  const month = response.month;
  fintechServices.forEach((element) => {
    console.log(element);
    fintechServicesHTML += `<tr id="record-${element.id}">
    <td class="service text-uppercaseservices sub_domain" data-sub_domain="${element.sub_domain}" data-id=${element.id}>
      ${month}/ ${year}
    </td>
       <td class="service text-uppercaseservices sub_domain" data-sub_domain="${element.sub_domain}" data-id=${element.id}>
       <a type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEnd"
                           aria-controls="offcanvasEnd" >${element.organization}</a>
       </td>
       <td class=" text-uppercaseservices" >
       <a type="button" class="service" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEnd" data-service='gic'
                           aria-controls="offcanvasEnd">
       ${element.gic_count}</a></td>
       <td class="forex_count" >
       <a type="button" class="service" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEnd" data-service='forex'
                           aria-controls="offcanvasEnd">
       ${element.forex_count}</a></td>
       <td class="credit_card_count" >
       <a type="button" class="service" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEnd" data-service='credit_card'
                           aria-controls="offcanvasEnd">
       ${element.credit_card_count}</a></td>
       <td class="sim_count" >
       <a type="button" class="service" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEnd" data-service="sim"
                           aria-controls="offcanvasEnd">
       ${element.sim_count}</a></td>
       <td class="loan_count"  >
       <a type="button" class="service" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEnd" data-service="loan"
                           aria-controls="offcanvasEnd">
       ${element.loan_count}</a></td>
       <td class="insurance_count" >
       <a type="button" class="service" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEnd" data-service="insurance"
                           aria-controls="offcanvasEnd">
       ${element.insurance_count}</a></td>
       </tr>`;
  });

      //  <td>
      //   <select class="form-select">
      //   <option value="${element.payment_status == 'PAID' ? 'selected' : ''}">PAID</option>
      //   <option value="${element.payment_status == 'UNPAID' ? 'selected' : ''}">UNPAID</option>
      //   </select>
      //  </td>
  let organizationsHTML = '<option value="">Select</option>';
  let organizations = response.organizations;
  console.log(response);
  organizations.forEach((element) => {
    organizationsHTML += `<option value="${element.id}">${element.organization}</option>`;
  });
  $("#organizationFilter").html(organizationsHTML);
  if (fintechServices.length === 0) {
    fintechServicesHTML =
      '<tr><td colspan="7" class="text-center">No record found</td></tr>';
  }
  $("tbody").html(fintechServicesHTML);
  let pagination = response.subdomains.pagination;
  $(".pagination").html(pagination);

  // todays requests
  let todaysRequests = response.todayRequests.data[0];
  
  $("#todaysGicRequests").html(`${todaysRequests.gic_count}`);
  $("#todaysForexRequests").html(`${todaysRequests.forex_count}`);
  $("#todaysCreditCardRequests").html(`${todaysRequests.credit_card_count}`);
  $("#todaysLoanRequests").html(`${todaysRequests.loan_count}`);
  $("#todaysInsuranceRequests").html(`${todaysRequests.insurance_count}`);
  $("#todaysSimCardRequests").html(`${todaysRequests.sim_count}`);

  $('#month').val(month);
  $('#year').val(year);
}

$("body").on("click", "#_pagination", function () {
  get_data($(this).attr("data-id"), filters(false, true, true, true));
});

function changeStatus() {
  let status = 1;
  if ($(this).prop("checked")) {
    status = 1;
  } else {
    status = 0;
  }

  let id = $(this).attr("data-id");
  console.log(id);
  let param = new FormData();
  param.append("_action", "update");
  param.append("payload", "forex-master");
  param.append("id", id);
  param.append("status", status);
  let res = run(param);
}

function deleteRecord() {
  if (confirm("Are you sure you want to delete this record?")) {
    let id = $(this).attr("data-id");
    let param = new FormData();
    param.append("_action", "deleteRecord");
    param.append("payload", "fintech-service-users");
    param.append("id", id);
    let res = run(param);
    $(`#record-${id}`).remove();
  }
}

// $(document).on("click", ".service", function () {
//   let service = $(this).attr("data-service");
//   let id = $(this).parent().parent().find(".sub_domain").attr("data-id");
//   let sub_domain = $(this)
//     .parent()
//     .parent()
//     .find(".sub_domain")
//     .attr("data-sub_domain");
//   if ($(this).text() == 0) {
//     sub_domain = "";
//   }
//   let param = new FormData();
//   param.append("_action", "getServices");
//   param.append("payload", "fintech-service-users");
//   param.append("service", service);
//   param.append("sub_domain", sub_domain);
//   param.append("id", id);
//   let res = run(param);
//   let response = JSON.parse(res.responseText);
//   response = response.data;
//   let servicesHTML = "";
//   let count = 1;
//   if (response.length == 0) {
//     servicesHTML = '<p class="text-center">No record found</p>';
//   }
//   response.forEach((element) => {
//     let remarks = element.remarks ?? "";
//     let selected = "";
//     if (element.fintech_status == "UNDER REVIEW") {
//       selected = ` <option value="">Select</option>
//             <option value="APPROVED">APPROVED</option>
//             <option value="UNDER REVIEW" selected>UNDER REVIEW</option>
//             <option value="REJECTED">REJECTED</option>`;
//     } else if (element.fintech_status == "APPROVED") {
//       selected = ` <option value="">Select</option>
//             <option value="APPROVED" selected>APPROVED</option>
//             <option value="UNDER REVIEW">UNDER REVIEW</option>
//             <option value="REJECTED">REJECTED</option>`;
//     } else if (element.fintech_status == "REJECTED") {
//       selected = ` <option value="">Select</option>
//             <option value="APPROVED">APPROVED</option>
//             <option value="UNDER REVIEW">UNDER REVIEW</option>
//             <option value="REJECTED" selected>REJECTED</option>`;
//     } else {
//       selected = ` <option value="">Select</option>
//             <option value="APPROVED">APPROVED</option>
//             <option value="UNDER REVIEW">UNDER REVIEW</option>
//             <option value="REJECTED">REJECTED</option>`;
//     }
//     count = count + 1;
//     let uuid = element.crm_request_id;
//     if (service == "gic" || service == "GIC") {
//       servicesHTML += `<div class="card accordion-item">
//             <h2 class="accordion-header" id="headingOne">
//                <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
//                   data-bs-target="#accordionOne-${count}" aria-expanded="false" aria-controls="accordionOne-${count}">
//                   ${element.first_name}
//                </button>
//             </h2>
//             <div id="accordionOne-${count}" class="accordion-collapse collapse" data-bs-parent="#accordionExample" style="">
//                <div class="accordion-body">
//                <div class="row">
//                  <div class="col-md-4">
//                <p class="mb-1"><b class="text-mute"> First Name :</b> <span id="_selected_vendor">${element.first_name}</span></p>
//                      <p class="mb-1"><b>Last Name :</b> <span id="_selected_vendor_rate">${element.last_name}</span></p>
//                      <p class="mb-1"><b>Date Of Birth :</b> <span id="_selected_vendor_date">${element.date_of_birth}</span></p>
//                      <p class="mb-1"><b>Email :</b> <span id="_selected_vendor_date">${element.email}</span></p>
//                      <p class="mb-1"><b>Passport :</b> <span id="_selected_vendor_date">${element.passport}</span></p>
//                      <p class="mb-1"><b>College :</b> <span id="_selected_vendor_date">${element.college}</span></p>
//                      <p class="mb-1"><b>City :</b> <span id="_selected_vendor_date">${element.city}</span></p>
//                      <p class="mb-1"><b>Country :</b> <span id="_selected_vendor_date">${element.country}</span></p>
//                   </div>
//                    <div class="col-md-4">
//                      <p class="mb-1"><b class="text-mute"> GIC Bank :</b> <span id="_selected_vendor">${element.gic_bank}</span></p>
//                      <p class="mb-1"><b>GIC Account Info :</b> ₹ <span id="_selected_vendor_rate">${element.gic_acc_info}</span></p>
//                      <p class="mb-1"><b>GIC Account Certificate :</b> <span id="_selected_vendor_date">${element.gic_acc_certificate}</span></p>
//                      <p class="mb-1"><b>GIC Id :</b> <span id="_selected_vendor_date">${element.gic_id}</span></p>
//                      <p class="mb-1"><b>GIC Pass :</b> <span id="_selected_vendor_date">${element.gic_pass}</span></p>
//                   </div>
                
//                   <div class="col-md-4">
//                   <form class="addRemarks">
//                   <div class="row">
//                     <div class="col-md-12">
//                         <label class="form-label">Status</label>
//                         <select class="form-select form-select-sm" name="status">
//                            ${selected}
//                         </select>
//                   </div>
//                   <input type="hidden" name="id" value="${element.id}">
//                   <input type="hidden" name="sub_domain" value="${element.sub_domain}">
//                   <input type="hidden" name="uuid" value="${uuid}">
//                   <input type="hidden" name="service" value="${element.service}">
//                   <div class="col-md-12">
//                         <label> Remarks</label>
//                         <textarea class="form-control" name="remarks">${remarks}</textarea>
//                   </div>
//                   <div class="col-md-12">
//                         <button class="btn btn-primary btn-sm mt-2">Submit</button>
//                         </div>
//                </div>
               
//                </form>
//                </div>
//                </div>
//                  </div>
//             </div>
//          </div>`;
//     } else if (service == "forex" || service == "FOREX") {
//       console.log(element);
//       servicesHTML += `<div class="card accordion-item">
//             <h2 class="accordion-header" id="headingOne">
//                <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
//                   data-bs-target="#accordionOne-${count}" aria-expanded="false" aria-controls="accordionOne-${count}">
//                  ${element.first_name}
//                </button>
//             </h2>
//             <div id="accordionOne-${count}" class="accordion-collapse collapse" data-bs-parent="#accordionExample" style="">
//                <div class="accordion-body">
//                  <div class="row">
//                    <div class="col-md-4">
//                <p class="mb-1"><b class="text-mute"> First Name :</b> <span id="_selected_vendor">${
//                  element.first_name
//                }</span></p>
//                   <p class="mb-1"><b>Last Name :</b> <span id="_selected_vendor_rate">${
//                     element.last_name
//                   }</span></p>
//                   <p class="mb-1"><b>Date Of Birth :</b> <span id="_selected_vendor_date">${
//                     element.date_of_birth
//                   }</span></p>
//                   <p class="mb-1"><b>Email :</b> <span id="_selected_vendor_date">${
//                     element.email
//                   }</span></p>
//                   <p class="mb-1"><b>Passport :</b> <span id="_selected_vendor_date">${
//                     element.passport
//                   }</span></p>
//                   <p class="mb-1"><b>College :</b> <span id="_selected_vendor_date">${
//                     element.college
//                   }</span></p>
//                   <p class="mb-1"><b>City :</b> <span id="_selected_vendor_date">${
//                     element.city
//                   }</span></p>
//                   <p class="mb-1"><b>Country :</b> <span id="_selected_vendor_date">${
//                     element.country
//                   }</span></p>
//                   </div>
//                 <div class="col-md-4">
//                   <p class="mb-1"><b class="text-mute"> Forex Type :</b> <span id="_selected_vendor">${
//                     element.service
//                   }</span></p>
//                   <p class="mb-1"><b>Total Fees :</b> ₹ <span id="_selected_vendor_rate">${
//                     element.fee
//                   }</span></p>
//                   <p class="mb-1"><b>Margin :</b> <span id="_selected_vendor_date">${
//                     element.margin
//                   }</span></p>
//                   <p class="mb-1"><b>Conversion Rate :</b> <span id="_selected_vendor_date">${
//                     element.conversion_rate
//                   }</span></p>
//                   <p class="mb-1"><b>Total Amount :</b> <span id="_selected_vendor_date">${
//                     element.total_payable
//                   }</span></p>
//                   <p class="mb-1"><b>GST :</b> <span id="_selected_vendor_date">${
//                     element.gst
//                   }</span></p>
//                   <p class="mb-1"><b>Processing Charge :</b> <span id="_selected_vendor_date">${
//                     element.processing_charges
//                   }</span></p>
//                   <p class="mb-1"><b>TCS :</b> <span id="_selected_vendor_date">${
//                     element.tcs
//                   }</span></p>
//                   </div>
//                     <div class="col-md-4">
//                   <form class="addRemarks">
//                   <div class="row">
//                     <div class="col-md-12">
//                         <label class="form-label">Status</label>
//                         <select class="form-select form-select-sm" name="status">
//                            ${selected}
//                         </select>
//                   </div>
//                   <input type="hidden" name="id" value="${element.id}">
//                   <input type="hidden" name="sub_domain" value="${
//                     element.sub_domain
//                   }">
//                   <input type="hidden" name="uuid" value="${
//                     element.crm_forex_id
//                   }">
//                   <input type="hidden" name="service" value="${
//                     element.service == "gic" ? "TT" : element.service
//                   }">
//                   <div class="col-md-12">
//                         <label> Remarks     </label>
//                         <textarea class="form-control" name="remarks">${remarks}</textarea>
//                   </div>
//                   <div class="col-md-12">
//                         <button class="btn btn-primary btn-sm  mt-2">Submit</button>
//                         </div>
//                </div>
//                </form>
//                </div>
//                </div>
//                </div>
//             </div>
//          </div>`;
//     } else if (service == "credit_card" || service == "CREDIT_CARD") {
//       servicesHTML += `<div class="card accordion-item">
//             <h2 class="accordion-header" id="headingOne">
//                <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
//                   data-bs-target="#accordionOne-${count}" aria-expanded="false" aria-controls="accordionOne-${count}">
//                  ${element.first_name}
//                </button>
//             </h2>
//             <div id="accordionOne-${count}" class="accordion-collapse collapse" data-bs-parent="#accordionExample" style="">
//                <div class="accordion-body">
//                <p class="mb-1"><b class="text-mute"> First Name :</b> <span id="_selected_vendor">${element.first_name}</span></p>
//                   <p class="mb-1"><b>Last Name :</b> <span id="_selected_vendor_rate">${element.last_name}</span></p>
//                   <p class="mb-1"><b>Date Of Birth :</b> <span id="_selected_vendor_date">${element.date_of_birth}</span></p>
//                   <p class="mb-1"><b>Email :</b> <span id="_selected_vendor_date">${element.email}</span></p>
//                   <p class="mb-1"><b>Passport :</b> <span id="_selected_vendor_date">${element.passport}</span></p>
//                   <p class="mb-1"><b>College :</b> <span id="_selected_vendor_date">${element.college}</span></p>
//                   <p class="mb-1"><b>City :</b> <span id="_selected_vendor_date">${element.city}</span></p>
//                   <p class="mb-1"><b>Country :</b> <span id="_selected_vendor_date">${element.country}</span></p>
//                   <hr>
//                   <p class="mb-1"><b>Status</b> <span id="_selected_vendor_date">APPLIED</span></p>
//                   <hr>
//                   <form class="addRemarks">
//                   <div class="row">
//                     <div class="col-md-12">
//                         <label class="form-label">Status</label>
//                         <select class="form-control" name="status">
//                            ${selected}
//                         </select>
//                   </div>
//                   <input type="hidden" name="id" value="${element.id}">
//                   <input type="hidden" name="sub_domain" value="${element.sub_domain}">
//                   <input type="hidden" name="uuid" value="${uuid}">
//                   <input type="hidden" name="service" value="${element.service}">
//                   <div class="col-md-12">
//                         <label> Remarks     </label>
//                         <textarea class="form-control" name="remarks">${remarks}</textarea>
//                   </div>
//                   <div class="col-md-12">
//                         <button class="btn btn-primary mt-2">Submit</button>
//                         </div>
//                </div>
//                </form>
//                </div>
//             </div>
//          </div>`;
//     } else if (service == "sim" || service == "SIM") {
//       servicesHTML += `<div class="card accordion-item">
//             <h2 class="accordion-header" id="headingOne">
//                <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
//                   data-bs-target="#accordionOne-${count}" aria-expanded="false" aria-controls="accordionOne-${count}">
//                  ${element.first_name}
//                </button>
//             </h2>
//             <div id="accordionOne-${count}" class="accordion-collapse collapse" data-bs-parent="#accordionExample" style="">
//                <div class="accordion-body">
//                <p class="mb-1"><b class="text-mute"> First Name :</b> <span id="_selected_vendor">${element.first_name}</span></p>
//                   <p class="mb-1"><b>Last Name :</b> <span id="_selected_vendor_rate">${element.last_name}</span></p>
//                   <p class="mb-1"><b>Date Of Birth :</b> <span id="_selected_vendor_date">${element.date_of_birth}</span></p>
//                   <p class="mb-1"><b>Email :</b> <span id="_selected_vendor_date">${element.email}</span></p>
//                   <p class="mb-1"><b>Passport :</b> <span id="_selected_vendor_date">${element.passport}</span></p>
//                   <p class="mb-1"><b>College :</b> <span id="_selected_vendor_date">${element.college}</span></p>
//                   <p class="mb-1"><b>City :</b> <span id="_selected_vendor_date">${element.city}</span></p>
//                   <p class="mb-1"><b>Country :</b> <span id="_selected_vendor_date">${element.country}</span></p>
//                   <hr>
//                   <p class="mb-1"><b>Status</b> <span id="_selected_vendor_date">APPLIED</span></p>
//                   <hr>
//                   <form class="addRemarks">
//                   <div class="row">
//                     <div class="col-md-12">
//                         <label class="form-label">Status</label>
//                         <select class="form-control" name="status">
//                            ${selected}
//                         </select>
//                   </div>
//                   <input type="hidden" name="id" value="${element.id}">
//                   <input type="hidden" name="sub_domain" value="${element.sub_domain}">
//                   <input type="hidden" name="uuid" value="${uuid}">
//                   <input type="hidden" name="service" value="${element.service}">
//                   <div class="col-md-12">
//                         <label> Remarks     </label>
//                         <textarea class="form-control" name="remarks">${remarks}</textarea>
//                   </div>
//                   <div class="col-md-12">
//                         <button class="btn btn-primary mt-2">Submit</button>
//                         </div>
//                </div>
//                </form>
//                </div>
//             </div>
//          </div>`;
//     } else if (service == "loan" || service == "LOAN") {
//       let amount = element.loan_disbursed_amount ?? 0;
//       servicesHTML += `<div class="card accordion-item">
//             <h2 class="accordion-header" id="headingOne">
//                <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
//                   data-bs-target="#accordionOne-${count}" aria-expanded="false" aria-controls="accordionOne-${count}">
//                  ${element.first_name}
//                </button>
//             </h2>
//             <div id="accordionOne-${count}" class="accordion-collapse collapse" data-bs-parent="#accordionExample" style="">
//                <div class="accordion-body">
//                <p class="mb-1"><b class="text-mute"> First Name :</b> <span id="_selected_vendor">${element.first_name}</span></p>
//                   <p class="mb-1"><b>Last Name :</b> <span id="_selected_vendor_rate">${element.last_name}</span></p>
//                   <p class="mb-1"><b>Date Of Birth :</b> <span id="_selected_vendor_date">${element.date_of_birth}</span></p>
//                   <p class="mb-1"><b>Email :</b> <span id="_selected_vendor_date">${element.email}</span></p>
//                   <p class="mb-1"><b>Passport :</b> <span id="_selected_vendor_date">${element.passport}</span></p>
//                   <p class="mb-1"><b>College :</b> <span id="_selected_vendor_date">${element.college}</span></p>
//                   <p class="mb-1"><b>City :</b> <span id="_selected_vendor_date">${element.city}</span></p>
//                   <p class="mb-1"><b>Country :</b> <span id="_selected_vendor_date">${element.country}</span></p>
//                   <hr>
//                   <p class="mb-1"><b>Status</b> <span id="_selected_vendor_date">APPLIED</span></p>
//                   <hr>
//                   <form class="addRemarks">
//                   <div class="row">
//                     <div class="col-md-12">
//                         <label class="form-label">Status</label>
//                         <select class="form-control" name="status">
//                            ${selected}
//                         </select>
//                   </div>
//                   <input type="hidden" name="id" value="${element.id}">
//                   <input type="hidden" name="sub_domain" value="${element.sub_domain}">
//                   <input type="hidden" name="uuid" value="${uuid}">
//                   <input type="hidden" name="service" value="${element.service}">
//                   <div class="col-md-12">
//                         <label> Remarks     </label>
//                         <textarea class="form-control" name="remarks">${remarks}</textarea>
//                   </div>
//                   <div class="col-md-12">
//                   <label>Loan Disbursed</label>
//                   <input class="form-control" name="loan_disbursed_amount" value="${amount}"/>
//             </div>
//                   <div class="col-md-12">
//                         <button class="btn btn-primary mt-2">Submit</button>
//                         </div>
//                </div>
//                </form>
//                </div>
//             </div>
//          </div>`;
//     } else if (service == "insurance" || service == "INSURANCE") {
//       servicesHTML += `<div class="card accordion-item">
//             <h2 class="accordion-header" id="headingOne">
//                <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
//                   data-bs-target="#accordionOne-${count}" aria-expanded="false" aria-controls="accordionOne-${count}">
//          ${element.first_name}
//                </button>
//             </h2>
//             <div id="accordionOne-${count}" class="accordion-collapse collapse" data-bs-parent="#accordionExample" style="">
//                <div class="accordion-body">
//                <p class="mb-1"><b class="text-mute"> First Name :</b> <span id="_selected_vendor">${element.first_name}</span></p>
//                   <p class="mb-1"><b>Last Name :</b> <span id="_selected_vendor_rate">${element.last_name}</span></p>
//                   <p class="mb-1"><b>Date Of Birth :</b> <span id="_selected_vendor_date">${element.date_of_birth}</span></p>
//                   <p class="mb-1"><b>Email :</b> <span id="_selected_vendor_date">${element.email}</span></p>
//                   <p class="mb-1"><b>Passport :</b> <span id="_selected_vendor_date">${element.passport}</span></p>
//                   <p class="mb-1"><b>College :</b> <span id="_selected_vendor_date">${element.college}</span></p>
//                   <p class="mb-1"><b>City :</b> <span id="_selected_vendor_date">${element.city}</span></p>
//                   <p class="mb-1"><b>Country :</b> <span id="_selected_vendor_date">${element.country}</span></p>
//                   <hr>
//                   <p class="mb-1"><b>Status</b> <span id="_selected_vendor_date">APPLIED</span></p>
//                   <hr>
//                   <form class="addRemarks">
//                   <div class="row">
//                     <div class="col-md-12">
//                         <label class="form-label">Status</label>
//                         <select class="form-control" name="status">
//                            ${selected}
//                         </select>
//                   </div>
//                   <input type="hidden" name="id" value="${element.id}">
//                   <input type="hidden" name="sub_domain" value="${element.sub_domain}">
//                   <input type="hidden" name="uuid" value="${uuid}">
//                   <input type="hidden" name="service" value="${element.service}">
//                   <div class="col-md-12">
//                         <label> Remarks     </label>
//                         <textarea class="form-control" name="remarks">${remarks}</textarea>
//                   </div>
//                   <div class="col-md-12">
//                         <button class="btn btn-primary mt-2">Submit</button>
//                         </div>
//                </div>
//                </form>
//                </div>
//             </div>
//          </div>`;
//     }
//   });
//   $("#servicesBox").html(servicesHTML);
// });

$(document).on("submit", ".addRemarks", function (e) {
  e.preventDefault();

  let form = new FormData(this);
  form.append("_action", "addRemarks");
  form.append("payload", "fintech-service-users");
  run(form);
});

$(document).on("submit", "#filterFintech", function (e) {
  e.preventDefault();
  get_data(1, filters());
});

function filters() {
  let organization = $("#organizationFilter").val();
  let month = $("#month").val();
  let year = $("#year").val();
  let filter = {
    organization: organization,
    month: month,
    year: year,
  };
  return filter;
}


