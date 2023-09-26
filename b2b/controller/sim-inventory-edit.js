const getSimInventory = (page = 1, filter = {}, limit = 25) => {
  $("#simInventoryTbody").html(
    `<tr class="text-center"><td colspan="10">Loading...</td></tr>`
  );
  let formRequest = new FormData();
  formRequest.append("_action", "getSimInventory");
  formRequest.append("payload", "sim-inventory");
  formRequest.append("page", page);
  formRequest.append("limit", limit);
  formRequest.append("filter", JSON.stringify(filter));
  let result = run(formRequest);
  let detailsHtml = "";
  if (result.responseJSON.statusCode == 200) {
    let key = 1;
    let marketingPoeple = getMarketingPeople();
    let agents = getAgents();
    result.responseJSON.data.forEach((element) => {
      detailsHtml += `<tr>
        <td><input class="childCheckBoxs" data-id="${element.id}" type="checkbox" value="" name="record[${element.id}]"></td>
        <td class="simNumberTd">${element.sim_number}</td>
        <td class="organizationName">
        <input class="form-control organization" list="datalistOptions" name="student_name[]" id="exampleDataList_${element.id}" placeholder="Type to search...">
        <datalist id="datalistOptions">
          ${agents}
        </datalist>
        </td>
        <td class="marketingManager">
            <select class="form-select marketing_manager" name="relation_manager">
                ${marketingPoeple}
            </select>
        </td>
        <td class="backendTeam">
            <select class="form-select backend_team" id="sel1" name="backend_team">
            <option value=""> Select </option>
            <option value="Madhu"> Madhu  </option>
            <option value="Satinder"> Satinder  </option>
            </select>                       
        </td>
        <td class="studentName">
            <input type="text" class="form-control" name="student_name[]" />
        </td>
    </tr>`;
    });
    if (detailsHtml == "") {
      detailsHtml = `<tr><td colspan="10">Not Found</td></tr>`;
    }
    $("#simInventoryTbody").html(detailsHtml);
    $("#pagination").html(result.responseJSON.pagination);
  }
};

setTimeout(function () {
  getSimInventory(1, {}, 10);
}, 300);

function selectAllCheckBoxs(event) {
  if ($(event.target).is(":checked")) {
    $(".childCheckBoxs").prop("checked", true);
  } else {
    $(".childCheckBoxs").prop("checked", false);
  }
}

const getMarketingPeople = () => {
  let formRequest = new FormData();
  formRequest.append("_action", "getMarketingPeople");
  formRequest.append("payload", "sim-inventory");
  let response = run(formRequest);
  response = response.responseJSON;
  let optionHTML = "";
  Object.entries(response.data).forEach(([value, data]) => {
    optionHTML += `<option value="${data.id}">${data.username}</option>`;
  });
  return optionHTML;
};
getMarketingPeople();
const changeRecordLimit = (event) => {
  let limit = $(event.target).val();
  getSimInventory(getCurrentPage(), {}, limit);
};

function getCurrentPage() {
  if ($(".page-item.active").text() !== "") {
    return $(".page-item.active").text();
  } else {
    return 1;
  }
}

const getAgents = () => {
  let formRequest = new FormData();
  formRequest.append("_action", "getAgents");
  formRequest.append("payload", "sim-inventory");
  let response = run(formRequest);
  response = response.responseJSON;
  let optionHTML = "";
  Object.entries(response.data).forEach(([value, data]) => {
    optionHTML += `<option value="${data.organization}" data-id="${data.id}">${data.organization}</option>`;
  });
  return optionHTML;
};

$(document).on("click", "#assignSimInventory", function () {
  $("#assignSimInventory").text("Assigning...");
  setTimeout(function () {
    let data = [];
    let valid = true;
    let totalCheckbox = "";
    $(".childCheckBoxs:checked").each(function () {
      totalCheckbox = 1;
      const row = $(this).closest("tr");
      const organizationName = row
        .find(".organizationName input.organization")
        .val();
      const marketingManager = row
        .find(".marketingManager select.marketing_manager")
        .val();
      const backendTeam = row.find(".backendTeam select.backend_team").val();
      const studentName = row.find(".studentName input").val();
      const id = $(this).data("id");
      if (!organizationName) {
        toastr.error("Organization name should not be empty");
        valid = false;
      } else if (!marketingManager) {
        toastr.error("Marketing manager should be assigned");
        valid = false;
      } else if (!backendTeam) {
        toastr.error("Assign backend team");
        valid = false;
      } else if (!studentName) {
        toastr.error("Student name should not be empty");
        valid = false;
      }
      const rowData = {
        id: id,
        organizationName: organizationName,
        marketingManager: marketingManager,
        backendTeam: backendTeam,
        studentName: studentName,
      };
      if (valid) {
        data.push(rowData);
      }
    });

    if (!valid) {
      return; // Stop the action if any validation error occurred
    }
    if (!totalCheckbox) {
      toastr.error("select atleast one record");
      return;
    }

    let formRequest = new FormData();
    formRequest.append("_action", "assignSimNumbers");
    formRequest.append("payload", "sim-inventory");
    formRequest.append("data", JSON.stringify(data));
    let response = run(formRequest);
    if (response.responseJSON.statusCode == 200) {
      getSimInventory(1, {}, 10);
      $("#assignSimInventory").text("Assign");
    } else {
      $("#assignSimInventory").text("Assign");
    }
  }, 200);

});

$("body").on("click", "#_pagination", function () {
  getSimInventory($(this).attr("data-id"));
});
