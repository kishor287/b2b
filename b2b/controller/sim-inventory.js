// Fetching assigned sim inventory
const getAssignedSimInventory = (page = 1, filter = {}, limit = 25) => {
  $("#simInventoryTbody").html(
    `<tr class="text-center"><td colspan="10">Loading...</td></tr>`
  );
  let formRequest = new FormData();
  formRequest.append("_action", "getAssignedSimInventory");
  formRequest.append("payload", "sim-inventory");
  formRequest.append("page", page);
  formRequest.append("limit", limit);
  formRequest.append("filter", JSON.stringify(filter));
  let result = run(formRequest);
  let detailsHtml = "";
  if (result.responseJSON.statusCode == 200) {
    let key = 1;
    result.responseJSON.data.forEach((element) => {
      detailsHtml += `<tr>
          <td class="simNumber">${key++}</td>
          <td class="simNumber">${element.sim_number}</td>
          <td class="marketingPerson">${element.marketingPersonFname} ${
        element.marketingPersonLname
      }</td>
          <td class="agentName">${element.company_name}</td>
          <td class="agentName">${element.backend_team}</td>
          <td class="agentName">${element.student_name}</td>
      </tr>`;
    });
    if (detailsHtml == "") {
      detailsHtml = `<tr><td colspan="5">Not Found</td></tr>`;
    }
    $("#simInventoryTbody").html(detailsHtml);
    $("#pagination").html(result.responseJSON.pagination);
  }
};

// giving a space before loading data 
setTimeout(function () {
  getAssignedSimInventory();
}, 300);

// get pagination current page
function getCurrentPage() {
  if ($(".page-item.active").text() !== "") {
    return $(".page-item.active").text();
  } else {
    return 1;
  }
}

const changeRecordLimit = (event) => {
  let limit = $(event.target).val();
  getAssignedSimInventory(getCurrentPage(), {}, limit);
};

$("body").on("click", "#_pagination", function () {
  getAssignedSimInventory($(this).attr("data-id"));
});

// Get Company Names html {<option> Company Name </option>}
const getAgents = () => {
  let formRequest = new FormData();
  formRequest.append("_action", "getAgents");
  formRequest.append("payload", "sim-inventory");
  let response = run(formRequest);
  response = response.responseJSON;
  let optionHTML = "<option value='' >All</option>";
  Object.entries(response.data).forEach(([value, data]) => {
    optionHTML += `<option value="${data.organization}" data-id="${data.id}">${data.organization}</option>`;
  });
  return optionHTML;
};

// Get Marketing Person Names html {<option> Marekting Person </option>}
const getMarketingPeople = () => {
    let formRequest = new FormData();
    formRequest.append("_action", "getMarketingPeople");
    formRequest.append("payload", "sim-inventory");
    let response = run(formRequest);
    response = response.responseJSON;
    let optionHTML = "<option value='' >All</option>";
    Object.entries(response.data).forEach(([value, data]) => {
      optionHTML += `<option value="${data.id}">${data.username}</option>`;
    });
    return optionHTML;
};

// Get Student Names html {<option> Student Name </option>}
const getStudents = () => {
    let formRequest = new FormData();
    formRequest.append("_action", "getStudents");
    formRequest.append("payload", "sim-inventory");
    let response = run(formRequest);
    response = response.responseJSON;
    let optionHTML = "<option value='' >All</option>";
    Object.entries(response.data).forEach(([value, data]) => {
      optionHTML += `<option value="${data.student_name}" data-id"${data.student_name}">${data.student_name}</option>`;
    });
    return optionHTML;
}

$(document).ready(function(){

    const agents = getAgents();
    const marketingManagers = getMarketingPeople();
    const backendTeam = ['Madhu','Satinder','Deepika'];
    const studentNames = getStudents();

    let backendTeamHtml = '`<option value="">All</option>`';
    backendTeam.forEach(function (value) {
        backendTeamHtml+= `<option value="${value}">${value}</option>`;
    });
    $("#marketingPeople").html(marketingManagers);
    $("#backendTeam").html(backendTeamHtml);
    $("#studentNames").html(studentNames);
    $("#companyNames").html(agents);
});

// Getting Applied filters by getting the values from all inputs
const getFilters = () => {
    let selectedMarketingFilter = $("#marketingPeople").val();
    let selectedBackendTeamPerson = $("#backendTeam").val();
    let selectedStudent = $("#studentNames").val();
    let selectedCompanyName = $("#companyNames").val();

    return {
        marketing_id: selectedMarketingFilter,
        backend_team: selectedBackendTeamPerson,
        student_name: selectedStudent,
        company_name: selectedCompanyName
    };
};

$('#daterange').on('change',function(){
    let dateRange = $(this).val();
    let filters = getFilters();
    filters.dateRange = dateRange;
    getAssignedSimInventory(getCurrentPage(), filters);
});

$(document).on('click','#applyFilters',function(){
    $(this).text('Filtring...');
    setTimeout(function(){
        getAssignedSimInventory(getCurrentPage(),getFilters());
    },500);
    $("#applyFilters").text('Apply Filters');
});