const getTravelPlanData = (page = 1, filter = {}) => {
  $("#travelPlanTbody").html(`<tr><td colspan="10">Fetching...</td></tr>`);
  let formRequest = new FormData();
  formRequest.append("_action", "getTravelPlanData");
  formRequest.append("payload", "add-travel-plan");
  formRequest.append("page", page);
  formRequest.append("filter", JSON.stringify(filter));
  let result = run(formRequest);
  let detailsHtml = "";
  if (result.responseJSON.statusCode == 200) {
    if(result.responseJSON.data.length > 0){
      result.responseJSON.data.forEach((element) => {
        detailsHtml += `<tr id="travelPlanRow_${element.id}">  
           <td>
            <button data-id="${
              element.id
            }" class="btn rounded-pill btn-sm  btn-icon btn-primary editTravelPlanBtn"><span class="tf-icons bx bx-edit"></span></button>
            <button data-id="${
              element.id
            }" class="btn rounded-pill btn-sm  btn-icon btn-danger deleteTravelPlanBtn"><span class="tf-icons bx bx-trash"></span></button>
           </td>
           <td>${new Date(element.created_at).toLocaleDateString()}</td>
           <td class="agent_name">${element.agent_name}</td>
           <td class="city">${element.city}</td>
           <td class="location">${element.location}</td>
           <td class="revisit">${element.revisit}</td>
           <td class="silver">${element.silver}</td>
           <td class="remarks">${element.remarks}</td>
         </tr>`;
      });
    }
    if (detailsHtml == "") {
      detailsHtml = `<tr><td colspan="10">Not Found</td></tr>`;
    }
    $("#travelPlanTbody").html(detailsHtml);
    $("#pagination").html(result.responseJSON.pagination);
  }
};

getTravelPlanData();

function getCurrentPage() {
  if ($(".page-item.active").text() !== "") {
    return $(".page-item.active").text();
  } else {
    return 1;
  }
}

$("body").on("click", "#_pagination", function () {
  getTravelPlanData($(this).attr("data-id"));
});

// Edit Details
$(document).on("click", ".editTravelPlanBtn", function () {
  let recordId = $(this).attr("data-id");
  let agentName = $(this).closest("tr").find(".agent_name").text();
  let location = $(this).closest("tr").find(".location").text();
  let city = $(this).closest("tr").find(".city").text();
  let revisit = $(this).closest("tr").find(".revisit").text();
  let silver = $(this).closest("tr").find(".silver").text();
  let remarks = $(this).closest("tr").find(".remarks").text();

  $("#agentName").val(agentName);
  $("#recordId").val(recordId);
  $("#location").val(location);
  $("#city").val(city);
  $("#revisit").val(revisit);
  $("#silver").val(silver);
  $("#remarks").val(remarks);

  $("#editTravelPlanModal").modal("show");
});

$(document).on("submit", "#updateTravelPlan", function (e) {
  e.preventDefault();
  let formRequest = new FormData(this);
  let result = run(formRequest);
  if (result.responseJSON.statusCode == 200) {
    getTravelPlanData(getCurrentPage());
    $("#editTravelPlanModal").modal("hide");
  }
});

$(document).on("click", ".deleteTravelPlanBtn", function () {
  if (confirm("Are you sure! You want to delete this record?")) {
    let id = $(this).data("id");
    let formRequest = new FormData();
    formRequest.append("id", id);
    formRequest.append("payload", "add-travel-plan");
    formRequest.append("_action", "deleteTravelPlan");
    let res = run(formRequest);
    if (res.responseJSON.statusCode == 200) {
      getTravelPlanData();
    }
  }
});

$(document).on("change", "#dateRange", function () {
  let dateRange = $(this).val();
  getTravelPlanData(getCurrentPage(), { dateRange: dateRange });
});

