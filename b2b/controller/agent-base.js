const getAgentBaseDetails = (page = 1,filter = {}) => {
  $("#agentBaseDetailsTbody").html(
    `<tr><td colspan="10">Fetching...</td></tr>`
  );
  let formRequest = new FormData();
  formRequest.append("_action", "getAgentBaseDetails");
  formRequest.append("payload", "add-agent-base");
  formRequest.append("page", page);
  formRequest.append("filter",JSON.stringify(filter));
  let result = run(formRequest);
  let detailsHtml = "";
  if (result.responseJSON.statusCode == 200) {
    let key = 1;
    result.responseJSON.data.forEach((element) => {
      detailsHtml += `<tr id="agentBaseDetailRow_${element.id}">  
       <td>
        <button data-id="${element.id}" class="btn rounded-pill btn-sm  btn-icon btn-primary editAgentBaseDetailsBtn"><span class="tf-icons bx bx-edit"></span></button>
        <button data-id="${element.id}" class="btn rounded-pill btn-sm  btn-icon btn-danger deleteAgentBaseDetailsBtn"><span class="tf-icons bx bx-trash"></span></button>
       </td>
       <td>${element.fname}</td>
       <td class="organizationId" data-organization_id="${element.organization_id}">${element.organization}</td>
       <td >${element.contactPerson}</td>
       <td >${element.contactPersonEmail}</td>
       <td >${element.contactPersonCity}</td>
       <td >${element.contactPersonState}</td>
       <td>${new Date(element.created_at).toLocaleDateString()}</td>
       <td class="agent_name">${element.agent_name}</td>
       <td class="location">${element.location}</td>
       <td class="email_id">${element.email_id}</td>
       <td class="contact_number">${element.contact_number}</td>
       <td class="remarks">${element.remarks}</td>
       </tr>`;
    });
      //  <td class="agreement_status">${element.agreement_status}</td>
      //  <td class="meeting">${element.meeting}</td>
      //  <td class="visit_type">${element.visit_type}</td>
    if (detailsHtml == "") {
      detailsHtml = `<tr><td colspan="10">Not Found</td></tr>`;
    }
    $("#agentBaseDetailsTbody").html(detailsHtml);
    $("#pagination").html(result.responseJSON.pagination);
  }
};

getAgentBaseDetails();

function getCurrentPage() {
  if ($(".page-item.active").text() !== "") {
    return $(".page-item.active").text();
  } else {
    return 1;
  }
}

$("body").on("click", "#_pagination", function () {
  getAgentBaseDetails($(this).attr("data-id"));
});

// Edit Details
$(document).on("click",".editAgentBaseDetailsBtn", function () {
  let recordId = $(this).attr('data-id');
  let agentName = $(this).closest("tr").find(".agent_name").text();
  let location = $(this).closest("tr").find(".location").text();
  let emailId = $(this).closest("tr").find(".email_id").text();
  let contactNumber = $(this).closest("tr").find(".contact_number").text();
  let agreementStatus = $(this).closest("tr").find(".agreement_status").text();
  let meeting = $(this).closest("tr").find(".meeting").text();
  let visitType = $(this).closest("tr").find(".visit_type").text();
  let remarks = $(this).closest("tr").find(".remarks").text();
  let organizationId = $(this).closest("tr").find(".organizationId").data('organization_id');
  
  $("#agentName").val(agentName);
  $("#recordId").val(recordId);
  $("#location").val(location);
  $("#emailId").val(emailId);
  $("#contactNumber").val(contactNumber);
  $("#agreementStatus").val(agreementStatus);
  $("#meeting").val(meeting);
  $("#visitType").val(visitType);
  $("#remarks").val(remarks);
  $("#organizationId").val(organizationId);

  $("#editAgentBase").modal("show");
});


$(document).on("submit", "#agentBase", function (e) {
  e.preventDefault();
  let formRequest = new FormData(this);
  let result = run(formRequest);
  if (result.responseJSON.statusCode == 200) {
    getAgentBaseDetails(getCurrentPage());
    $("#editAgentBase").modal("hide");
  }
});


$(document).on('click','.deleteAgentBaseDetailsBtn',function(){
  if(confirm('Are you sure! You want to delete this record?')){
    let id = $(this).data('id');
    let formRequest = new FormData();
    formRequest.append('id',id);
    formRequest.append('payload','add-agent-base');
    formRequest.append('_action','deleteAgentBaseDetail');
    let res = run(formRequest);
    if(res.responseJSON.statusCode == 200){
      getAgentBaseDetails();
    }
  }

});


$(document).on('change','#daterange',function(){
    let dateRange = $(this).val();
    getAgentBaseDetails(getCurrentPage(),{dateRange:dateRange})
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
getAgencies();