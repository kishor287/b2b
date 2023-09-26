
const getRemittance = (page = 1,filter = {}) => {
    $("#agentBaseDetailsTbody").html(`<tr><td colspan="10">Fetching...</td></tr>`);
    let formRequest = new FormData();
    formRequest.append("_action", "getRemittance");
    formRequest.append("payload", "remittance");
    formRequest.append("page", page);
    formRequest.append("filter", JSON.stringify(filter));
    let result = run(formRequest);
    let detailsHtml = '';
    if(result.responseJSON.statusCode == 200){
      let key =1;
      if(result.responseJSON.data.length > 0){
        result.responseJSON.data.forEach(element => {
          detailsHtml += `<tr>
           <td>
           <button data-id="${element.id}" data-organization_id="${element.organization_id}" class="btn rounded-pill btn-sm  btn-icon btn-primary editRemittanceBtn"><span class="tf-icons bx bx-edit"></span></button>
           <button data-id="${element.id}" class="btn rounded-pill btn-sm  btn-icon btn-danger deleteRemittanceBtn"><span class="tf-icons bx bx-trash"></span></button>
          </td>
           <td>${element.organization}</td>
           <td>${element.fname}</td>
           <td>${new Date(element.created_at).toLocaleDateString()}</td>
           <td class="agent_name">${element.agent_name}</td>
           <td class="remittance_name">${element.remittance_name}</td>
           <td class="email_id">${element.email_id}</td>
           <td class="contact_number">${element.contact_number}</td>
           <td class="agent_margin">${element.agent_margin}</td>
           <td class="student_name">${element.student_name}</td>
           <td class="country">${element.country}</td>
           <td class="currency">${element.currency}</td>
           <td class="amount">${element.amount}</td>
           <td class="conversion_rate">${element.conversion_rate}</td>
           <td class="amount_inr">${element.amount_inr}</td>
           <td class="forex_company">${element.forex_company}</td>
           <td class="comission">${element.comission}</td>
           </tr>`;
          });
      }
        // <td class="number_of_accounts">${element.number_of_accounts}</td>
      if(detailsHtml == ''){
        detailsHtml = `<tr><td colspan="19">Not Found</td></tr>`;
      }
      $("#remittanceTbody").html(detailsHtml);
      $("#pagination").html(result.responseJSON.pagination);
    }
};
  
getRemittance();
function getCurrentPage() {
  if ($(".page-item.active").text() !== "") {
    return $(".page-item.active").text();
  } else {
    return 1;
  }
}
  
$("body").on("click", "#_pagination", function () {
  getRemittance($(this).attr("data-id"));
});

$(document).on("click",".editRemittanceBtn", function () {
  let recordId = $(this).attr('data-id');
  let organizationId = $(this).data('organization_id');
  let agentName = $(this).closest("tr").find(".agent_name").text();
  let studentName = $(this).closest("tr").find(".student_name").text();
  let emailId = $(this).closest("tr").find(".email_id").text();
  let contactNumber = $(this).closest("tr").find(".contact_number").text();
  let country = $(this).closest("tr").find(".country").text();
  let currency = $(this).closest("tr").find(".currency").text();
  let agentMargin = $(this).closest("tr").find(".agent_margin").text();
  let conversionRate = $(this).closest("tr").find(".conversion_rate").text();
  let amount = $(this).closest("tr").find(".amount").text();
  let amountInr = $(this).closest("tr").find(".amount_inr").text();
  let numberOfAccounts = $(this).closest("tr").find(".number_of_accounts").text();
  let forexCompany = $(this).closest("tr").find(".forex_company").text();
  let comission = $(this).closest("tr").find(".comission").text();
  let remittanceName = $(this).closest("tr").find(".remittance_name").text();
  $("#organizationId").val(organizationId);
  $("#recordId").val(recordId);
  $("#agentName").val(agentName);
  $("#studentName").val(studentName);
  $("#emailId").val(emailId);
  $("#contactNumber").val(contactNumber);
  $("#country").val(country);
  $("#currency").val(currency);
  $("#agentMargin").val(agentMargin);
  $("#conversionRate").val(conversionRate);
  $("#amountInr").val(amountInr);
  $("#amount").val(amount);
  $("#numberOfAccounts").val(numberOfAccounts);
  $("#forexCompany").val(forexCompany);
  $("#comission").val(comission);
  $("#remittanceName").val(remittanceName);

  $("#editRemittanceModal").modal("show");

});

$(document).on("submit", "#updateRemittance", function (e) {
  e.preventDefault();
  let formRequest = new FormData(this);
  let result = run(formRequest);
  result = JSON.parse(result.responseText);
  if (result.statusCode == 200) {
    $("#editRemittanceModal").modal("hide");
    getRemittance(getCurrentPage());
  }
});

$(document).on('click','.deleteRemittanceBtn',function(){
  if(confirm('Are you sure! You want to delete this record?')){
    let id = $(this).data('id');
    let formRequest = new FormData();
    formRequest.append('id',id);
    formRequest.append('payload','add-agent-base');
    formRequest.append('_action','deleteRemittance');
    let res = run(formRequest);
    if(res.responseJSON.statusCode == 200){
      getAgentBaseDetails();
    }
  }

});

const getAgencies = () => {
  let formRequest = new FormData();
  formRequest.append('_action','getOrganizations');
  formRequest.append('payload','save-gic');
  let res = run(formRequest);
  res  = JSON.parse(res.responseText);
  let agenciesOption = '';
  res.data.forEach(element => {
      agenciesOption += `<option value="${element.id}">${element.organization}</option>`;
  });
  $("#organizationId").html(agenciesOption);
}

$("#daterange").on('change',function(){
  let value = $(this).val();
  getRemittance(getCurrentPage(),{dateRange:value});
});

getAgencies();