var table_cols = [
  "Actions",
  "Created At",
  "Lead Id",
  "Organization",
  "Status",
  "Sub Status",
  "Followup Date/<span class='text-primary'>Time<span>",
  // "Updated At",
  "Remarks",
];

table_head(table_cols);

get_data(1);
$("body").on("change", "#_assign_modal select[name='usertype']", function () {
  var usertype = $(this).val();
  limit = 1000;

  var param = new FormData();
  param.append("_action", "get");
  param.append("payload", "user");
  param.append("limit", limit);
  param.append("pagination", 1);
  param.append("filter", JSON.stringify({ usertype: usertype }));
  var res = run(param);
  var json = JSON.parse(res.responseText);
  var jHTML = '<option value="" >Select User</option>';
  $.each(json.data, function (k, v) {
    jHTML += '<option value="' + v["id"] + '" >' + v["username"] + "</option>";
  });

  $("#_assign_modal select[name='user']").html(jHTML);
});
$("body").on(
  "change",
  "#_reassign_modal select[name='usertypeee']",
  function () {
    var usertype = $(this).val();
    limit = 1000;

    var param = new FormData();
    param.append("_action", "get");
    param.append("payload", "user");
    param.append("limit", limit);
    param.append("pagination", 1);
    param.append("filter", JSON.stringify({ usertype: usertype }));
    var res = run(param);
    var json = JSON.parse(res.responseText);
    var jHTML = '<option value="" >Select User</option>';
    $.each(json.data, function (k, v) {
      jHTML +=
        '<option value="' + v["id"] + '" >' + v["username"] + "</option>";
    });
    $("#_reassign_modal select[name='userss']").html(jHTML);
  }
);

$("body").on("change", ".offcanvas select[name='usertypee']", function () {
  var usertype = $(this).val();
  limit = 1000;

  var param = new FormData();
  param.append("_action", "get");
  param.append("payload", "user");
  param.append("limit", limit);
  param.append("pagination", 1);
  param.append("filter", JSON.stringify({ usertype: usertype }));
  var res = run(param);
  var json = JSON.parse(res.responseText);
  var jHTML = '<option value="" >Select User</option>';
  $.each(json.data, function (k, v) {
    jHTML += '<option value="' + v["id"] + '" >' + v["username"] + "</option>";
  });

  $(".offcanvas select[name='users']").html(jHTML);
});

function get_users(page, limit) {
  l_event = "tbody";
  var param = new FormData();
  param.append("_action", "get_users");
  param.append("payload", "all-lead");
  param.append("limit", limit);
  param.append("pagination", page);
  return run(param);
}
$(document).on("submit", "#followUpFilter", function (e) {
  e.preventDefault();
  let searchValue = $("#searchOrganization").val();
  let filter = {
    'search': searchValue,
  };

  let page = get_pagination();
  get_data(page, filter);
});

$(document).on("click", "#applyFilters", function () {
  $("#followUpFilter").submit();
});
$(document).on("click", "#reset", function () {
  $("#searchOrganization").val("");
  let page = get_pagination();
  let filter = {};
  get_data(page, filter);
});

function get_data(page, filter = {}) {
  var limit = 10;
  var l_event = "tbody";

  var param = new FormData();
  param.append("_action", "get");
  param.append("payload", "today-followup");
  param.append("limit", limit);
  param.append("pagination", page);
  param.append("filter", JSON.stringify(filter));

  var res = run(param);
  var json = JSON.parse(res.responseText);

  if (json.status === 1) {
    var res2 = get_users(1, 10000);
    var users = JSON.parse(res2.responseText);
    var arr = [];
    $.each(users.data, function (k, v) {
      arr[v.id] = v.fname;
    });

    var jHTML = "";
    var selectAllChecked = false;

    json.data.forEach(function (v) {
      jHTML += '<tr data-id="' + v.id + '">';
      jHTML += `<td>
            <a title="Add Followup" href="javascript:void(0)" class="btn rounded-pill btn-sm btn-icon btn-primary me-2 addFollowup rotate-icon" data-id="${v['lead_id']}">
              <span class="tf-icons bx bx-plus"></span>
            </a>
    `;
      jHTML += `<div class="form-switch d-none d-inline-block"><input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault"></div><button type="button" class="btn rounded-pill btn-sm  btn-icon btn-success"><span class="tf-icons bx bxs-phone" data-id="${v['lead_id']}"></span></button> </td>`;
      jHTML += `<td>${new Date(v.created).toLocaleDateString()} <span class="text-primary">${new Date(v.created).toLocaleTimeString()}</span></td>`;
      jHTML += "<td>" + v.lead_id + "</td>";
      jHTML += "<td>" + v.organization + "</td>";
      jHTML += "<td>" + v.status + "</td>";
      jHTML += "<td>" + v.sub_status + "</td>";
      jHTML += `<td>${new Date(v.followup_date).toLocaleDateString()} <span class="text-primary">${new Date(v.followup_date).toLocaleTimeString()}</span></td>`;
      // jHTML += `<td>${new Date(v.updated).toLocaleDateString()} <span class="text-primary">${new Date(v.updated).toLocaleTimeString()}</span></td>`;
      jHTML += "<td>" + v.remarks + "</td>";
      jHTML += "</tr>";
    });

    $("tbody").html(jHTML);
    $(".pagination").html(json.pagination);
  } else {
    console.error(json.msg);
  }
}

function get_data_with_filters(page = 1) {
  var check = $("#check_lead").val();
  var search = $("#sname").val();
  var lead_id = $("#usertypee").val();
  var lead = $("#users").val();

  get_data(page, search, check, lead_id, lead);
}

$("body").on("click", "#_pagination", function () {
  get_data_with_filters($(this).attr("data-id"));
});

$("#trainers").on("click", function () {
  createTrainerDropdown();
});

function createTrainerDropdown() {
  var param = new FormData();
  param.append("_action", "get_users");
  param.append("payload", "today-followup");

  var res = run(param);
  var users = JSON.parse(res.responseText);

  var res2 = get_users(1, 10000);
  var users = JSON.parse(res2.responseText);
  var arr = [];
  $.each(users.data, function (k, v) {
    arr[v["id"]] = v["fname"];
  });

  if (Array.isArray(users)) {
    var dropdownHTML = '<select class="form-select">';
    users.forEach(function (user) {
      dropdownHTML +=
        '<option value="' + user.id + '">' + user.fname + "</option>";
    });
    dropdownHTML += "</select>";

    $("#trainerDropdown").html(dropdownHTML);
  }
}

$("#assigning_lead").submit(function (e) {
  e.preventDefault();
  var currentPage = get_pagination();
  var form = $(this)[0];
  let leads = $("#lead_id").val();
  const salesManagerLeads = $("#manager_lead_id").val();
  let limit = 1000;
  var param = new FormData(form);
  param.append("_action", "update");
  param.append("payload", "all-lead");
  const idsArr = leads.split(","); // convert to array
  const marketers = [];
  let marketerId;
  idsArr.forEach((id) => {
    marketerId = $(`#lead-${id}`).attr("data-marketerid");
    marketers.push(marketerId);
  });
  param.append("sales_manager_ids", marketers);

  var res = run(param);
  var json = JSON.parse(res.responseText);
  if (json["status"] === 1) {
    $("#_assign_modal").modal("hide");
    $("#assigning_lead")[0].reset();
    get_data_with_filters(currentPage);
    toastr.success("Added", "Data Inserted Successfully.");
    getNotifications();
  } else {
    toastr.error("Error", "Internal Server Error");
  }
});

$("#reassigning_lead").submit(function (e) {
  e.preventDefault();
  let currentPage = get_pagination();
  var form = $(this)[0];

  limit = 1000;
  var param = new FormData(form);
  param.append("_action", "reassign");
  param.append("payload", "all-lead");
  let leadid = $("#leadid").val();
  let marketerId = $(`#lead-${leadid}`).data("marketerid");
  param.append("marketer_id", marketerId);
  var res = run(param);
  var json = JSON.parse(res.responseText);
  if (json["status"] === 1) {
    $("#_reassign_modal").modal("hide");
    $("#reassigning_lead")[0].reset();
    get_data_with_filters(currentPage);
    toastr.success("Added", "Data Inserted Successfully.");
  } else {
    toastr.error("Error", "Internal Server Error");
  }
});

function get_pagination() {
  return $(".page-item.active").text();
}

// $(document).on('submit', '#followUpFilter', function (e) {
//   e.preventDefault();
//   console.log("Submit button clicked");
//   let searchValue = $("#searchOrganization").val();
//   console.log("Search value:", searchValue);
//   // Rest of the code...
// });

// $(document).on('click', '#applyFilters', function () {
//   console.log("Apply Filters button clicked");
//   // Rest of the code...
// });
//   $("#reassigning_lead").submit(function (e) {
//   e.preventDefault();
//   let currentPage = get_pagination();
//   var form = $(this)[0];

//   limit = 1000;
//   var param = new FormData(form);
//   param.append("_action", "reassign");
//   param.append("payload", "today-followup");
//   let leadid = $('#leadid').val();
//   let marketerId = $(`#lead-${leadid}`).data('marketerid');
//   param.append("marketer_id", marketerId);
//   var res = run(param);
//   var json = JSON.parse(res.responseText);
//   if (json["status"] === 1) {
//     $("#_reassign_modal").modal("hide");
//     $("#reassigning_lead")[0].reset();
//     get_data_with_filters(currentPage);
//     toastr.success("Added", "Data Inserted Successfully.");
//   } else {
//     toastr.error("Error", "Internal Server Error");
//   }
// });

$("body").on("click", ".bxs-phone", function () {
  var id = $(this).attr("data-id");
  call(id);
});

function call(id) {
  var param = new FormData();
  param.append("_action", "call");
  param.append("payload", "today-followup");
  param.append("id", id);
  param.append("limit", 1);
  param.append("pagination", 1);
  var res = run(param);
  var json = JSON.parse(res.responseText);
  if (json["status"] === 1) {
    toastr.success(json["data"], "Success");
  } else {
    toastr.error(json["data"], "Error");
  }
}


$(document).on('click','.addFollowup',function(){
  let leadId = $(this).attr('data-id');
  if(leadId == null || leadId == undefined || leadId == ''){
    alert('Lead Not Found');
    return false;
  }
  $("#leadId").val(leadId);
  $('#basicModal').modal('show');
});


$(document).on('submit','#followup_form',function(e){
  e.preventDefault();
  let formRequest = new FormData(this);
  formRequest.append('_action','save_followup');
  formRequest.append('payload','today-followup');

  let res = run(formRequest);
  if(res.responseJSON.statusCode == 200){
      $("#followup_form")[0].reset();
      $('#basicModal').modal('hide');
      get_data(1,{});
  }
});