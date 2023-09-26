/*

Table headings

*/

var table_cols = [
  '<input class="_admin_access d-none" type="checkbox" >',
  "Edit",
  "Call",
  "Lead Id",
  "Organization Name",
  "Name",
  "Phone No.",
  "City/State",
  "Create By",
  "Trainer",
  "Manager",
  "Status",
  "Sub Status",
  "Next Followup",
  "Source",
  "Remarks",
  "Created",
];
table_head(table_cols);

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

let getSearch = localStorage.getItem('search');
let getTrainer = localStorage.getItem('trainer');
let getManager = localStorage.getItem('manager'); 
let getSource = localStorage.getItem('source'); 
let getStatus = localStorage.getItem('status'); 
let getSubStatus = localStorage.getItem('subStatus');
let getPage = localStorage.getItem('page');
let pageToFetch = 1;
if(getPage){
    pageToFetch = getPage;
}
get_data_with_filters(pageToFetch,"",getSearch,getTrainer,getManager,getSource,getStatus,getSubStatus);


function get_data(page, filters = {}) {
  limit = 15;
  l_event = "tbody";
  
  var param = new FormData();
  param.append("_action", "get");
  param.append("payload", "all-lead");
  param.append("limit", limit);
  param.append("pagination", page);
  param.append("filter", JSON.stringify(filters));
  var res = run(param);
  var json = JSON.parse(res.responseText);

  var res2 = get_users(1, 10000);
  var users = JSON.parse(res2.responseText);
  var arr = [];
  $.each(users.data, function (k, v) {
    arr[v["id"]] = v["fname"];
  });

  var jHTML = "";
  $.each(json.data, function (k, v) {
    jHTML +=
      '<tr data-id="' +
      v["id"] +
      '" id="lead-' +
      v["id"] +
      '" data-marketerid="' +
      v["marketing_id"] +
      '">';
    jHTML +=
      '<td><input type="checkbox" class="_admin_access d-none all_checkboxes"  value="' +
      v["id"] +
      '" ></td>';
    jHTML +=
      '<td style="width: 100px"><a href="view-lead/' +
      v["id"] +
      '" class="btn rounded-pill btn-sm  btn-icon btn-primary"><span  class="tf-icons bx bx-edit"></span></a></td>';
    jHTML +=
      '<td style="width: 100px"><div class="form-switch d-none d-inline-block"><input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault"></div><button type="button" class="btn rounded-pill btn-sm  btn-icon btn-success"><span  class="tf-icons bx bxs-phone"></span></button> </td>';
    jHTML += "<td>" + v["id"] + "</td>";
    jHTML +=
      '<td class="text-truncate" style="max-width: 200px;">' +
      v["organization"] +
      "</td>";
    jHTML += "<td>" + v["name"] + "</td>";
    jHTML += "<td>" + v["phone"] + "</td>";
    jHTML += "<td>" + v["city"] + "-" + v["state"] + "</td>";
    jHTML += "<td></td>";
    jHTML += "<td>" + arr[v["user_id"]] + "</td>";
    jHTML += "<td>" + arr[v["marketing_id"]] + "</td>";
    jHTML += "<td>" + v["followup_status"] + "</td>";
    jHTML += "<td>" + v["followup_substatus"] + "</td>";
    jHTML += "<td>" + v["next_followup_date"] + "</td>";
    jHTML += "<td>" + v["source"] + "</td>";
    jHTML +=
      '<td class="text-truncate" style="max-width: 250px;">' + v["remarks"] + "</td>";
    jHTML += "<td>" + v["created"] + "</td>";
    jHTML += "</tr>";
  });
  $("tbody").html(jHTML);
  $(".pagination").html(json.pagination);
  profile();
}

$("body").on("click", ".all_checkboxes", function () {
  var lead_ids = [];
  const manager_lead_id = [];
  var value = $('input[type="checkbox"].all_checkboxes:checked')
    .map(function () {
      return $(this).val();
    })
    .toArray();
  lead_ids.push(value);
  let ids = $('input[type="checkbox"].all_checkboxes:checked')
    .map(function () {
      return $(this).closest("tr").data("marketerid") + ":" + $(this).val();
    })
    .toArray();
  $("input:hidden[name=lead_id]").val(lead_ids);
  $("input:hidden[name=manager_lead_id]").val(ids);
});

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
    get_data_with_filters(currentPage, "");
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
    get_data_with_filters(currentPage, "");
    toastr.success("Added", "Lead Ressign Successfully.");
  } else {
    toastr.error("Error", "Internal Server Error");
  }
});

/*
Pagination
*/

$("body").on("click", "#_pagination", function () {
  get_data_with_filters($(this).attr("data-id"), "");
});

/*
Call
*/

$("body").on("click", ".bxs-phone", function () {
  var id = $(this).parent().parent().parent().attr("data-id");
  call(id);
});

function call(id) {
  var param = new FormData();
  param.append("_action", "call");
  param.append("payload", "all-lead");
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

/*
Searching Unassigned leads
*/
$("body").on("click", "#_show_result", function () {
  var check = $("#check_lead").val();
  var search = $("#sname").val();
  var lead_id = $("#usertypee").val();
  var lead = $("#users").val();
  $(".btn-close").trigger("click");
  get_data(1, search, check, lead_id, lead);
});

$("body").on("click", "#_reset_", function () {
  $("#check_lead").val("");
  $("#sname").val("");
  $(".btn-close").trigger("click");
  get_data(1, "", "", "", "");
});

function get_pagination() {
  return $(".page-item.active").text();
}

function get_data_with_filters(page = 1, dateRange = "", search = '', trainer='',manager='',source='',status='',subStatus='') {
    
  removeLocalStorageItem('search');
  removeLocalStorageItem('trainer');
  removeLocalStorageItem('manager');
  removeLocalStorageItem('source');
  removeLocalStorageItem('status');
  removeLocalStorageItem('subStatus');

  
    let searchFilter = !search ? $("#searchOrganization").val() : search;
    let trainerFilter = !trainer ? $("#trainerFilter").val() : trainer ;   
    let managerFilter = !manager ? $("#managerFilter").val() :manager ;
    let sourceFilter = !source ? $("#sourceFilter").val() : source ;
    let statusFilter = !status? $("#statusFilter").val() : status;
    let subStatusFilter = !subStatus ? $("#substatusFilter").val() : subStatus ;
    let dateRangeFilter = !dateRange ? "" : dateRange;
 
 localStorage.setItem('search',searchFilter);
 localStorage.setItem('trainer',trainerFilter);
 localStorage.setItem('manager',managerFilter);
 localStorage.setItem('source',sourceFilter);
 localStorage.setItem('status',statusFilter);
 localStorage.setItem('subStatus',subStatusFilter);
 localStorage.setItem('page',page);
  let filter = {
    search: searchFilter,
    trainer: trainerFilter,
    manager: managerFilter,
    source: sourceFilter,
    status: statusFilter,
    subStatus: subStatusFilter,
  };
  if(dateRangeFilter){
      filter.dateRange = dateRangeFilter;
  }
  get_data(page, filter);
}


$(document).on("click", "#applyFilters", function () {
   get_data_with_filters(1, "");
});

$(document).on("click", "#reset", function () {
   removeLocalStorageItem('search');
   removeLocalStorageItem('trainer');
   removeLocalStorageItem('manager');
   removeLocalStorageItem('source');
   removeLocalStorageItem('status');
   removeLocalStorageItem('subStatus');
   removeLocalStorageItem('page');
   
  $("#searchOrganization").val("");
  $("#contractFilter").val("");
  let page = get_pagination();
  let filter = {};
  get_data(page, filter);
});

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

$(document).on("change", "#dateRange", function () {
  let dateRange = $(this).val(); //output format 05/01/2023 - 21/01/2023
  get_data_with_filters(1, dateRange);
});

function removeLocalStorageItem(key) {
    localStorage.removeItem(key);
}

// set filtered data in inputs from localstorage
$('#searchOrganization').val(getSearch);
$("#trainerFilter").val(getTrainer);
$("#managerFilter").val(getManager);
$("#sourceFilter").val(getSource);
$("#statusFilter").val(getStatus);
$('#substatusFilter').val(getSubStatus);

