get_data(1);

function get_users(page, limit) {
  l_event = "tbody";
  var param = new FormData();
  param.append("_action", "get_users");
  param.append("payload", "agreement");
  param.append("limit", limit);
  param.append("pagination", page);
  return run(param);
}
function get_data(page, filter) {
  limit = 15;
  l_event = "tbody";
  var param = new FormData();
  param.append("_action", "get");
  param.append("payload", "agreement");
  param.append("limit", limit);
  param.append("type", 'clients');
  param.append("pagination", page);
  param.append("filter", JSON.stringify({ filter }));
  var res = run(param);
  var json = JSON.parse(res.responseText);

  var res2 = get_users(1, 10000);
  var users = JSON.parse(res2.responseText);
  var arr = [];
  $.each(users.data, function (k, v) {
    arr[v["id"]] = v["fname"] + " " + v["lname"];
  });
  let contracts = 0;
  var jHTML = "";
  $.each(json.data, function (k, v) {
    console.log(v);
    let agreementType = "<span class='bg-warning badge text-dark p-1'>Not Mentioned</span>";
    if (v['lead_type']) {
      if (v['lead_type'] == 'Paid') {
        agreementType = "<span class='bg-success badge text-dark p-1'>" + v["lead_type"] + "</span>";
      } else {
        agreementType = "<span class='bg-primary badge text-dark p-1'>" + v["lead_type"] + "</span>";
      }
    }
    if (v["uploaded_contract_path"]) {
      contracts++;
    }
    let supportPerson = '<span class="badge bg-warning text-dark">Not Assigned</span>';
    if (arr[v["user_id"]]) {
      supportPerson = `<span class=" text-uppercase">${arr[v["user_id"]]}</span>`;
    }
    jHTML +=
      '<tr data-id="client-' +
      v["id"] +
      '" id="' +
      v["id"] +
      '" data-marketerid="' +
      v["marketing_id"] +
      '">';
    jHTML += `<td><input data-id="${v['id']}" value="${v['id']}" class="selectThisClient form-check" type="checkbox" ></td>`;
    jHTML += '<td><div class="d-flex"><a title="Download Agreement" href="server/download-agreement.php?download=' +
      v["id"] +
      '" class="btn btn-success shadow btn-xs sharp me-1"><i class="bx bx-download"></i></a><a title="Edit Record"  href="javascript:" class="btn btn-warning shadow btn-xs sharp me-1"><i class="bx bx-pencil"></i></a><a title="Remove Record"  href="javascript:" class="btn btn-danger d-none deleteButtonForClients shadow btn-xs sharp"><i class="bx bx-trash"></i></a></div></td>';

    jHTML += "<td>" + v["id"] + "</td>";
    jHTML += "<td>" + arr[v["marketing_id"]].toUpperCase() + "</td>";
    jHTML += "<td>" + supportPerson + "</td>";
    jHTML += "<td>" + v["organization"] + "</td>";
    jHTML += "<td>" + v["committed"] + "</td>";
    jHTML += "<td>" + v["reward"] + "</td>";
    jHTML += "<td>" + v["credit_card"] + "</td>";
    jHTML += "<td>" + v["benefits"] + "</td>";
    jHTML += "<td>" + v["forex"] + "</td>";
    jHTML += "<td>" + v["source"] + "</td>";
    jHTML += "<td>" + v["other"] + "</td>";
    jHTML += "<td>" + agreementType + "</td>";
    jHTML += "<td>" + new Date(v["created"]).toLocaleDateString() + "</td>";
    jHTML += "</tr>";
  });

  $("tbody").html(jHTML);
  $(".pagination").html(json.pagination);

  if (getRole() == 1) {
    $(".deleteButtonForClients").removeClass('d-none');
  }
}

// =============================================================== Assign Client Functionality ========================================================================
$(document).on('click', '#assignClientBtn', function () {
  let checked = false;
  $(".selectThisClient").each(function (value) {
    if ($(this).is(':checked')) {
      checked = true;
    }
  });
  if (checked == true) {
    $("#_assign_modal").modal('show');
  } else {
    toastr.warning('Select [✓] atleast one client ');
  }
  let ids = $('.selectThisClient:checked').map(function () {
    return $(this).closest("tr").data("marketerid") + ":" + $(this).val();
  }).toArray();
  $("input:hidden[name=manager_lead_id]").val(ids);

  let leads = [];
  $('.selectThisClient').each(function () {
    if ($(this).is(':checked')) {
      leads.push($(this).attr('data-id'));
    }
  });
  $("#lead_id").val(leads);

});
//  Submit Forsm
$("#assigning_lead").submit(function (e) {
  e.preventDefault();
  const currentPage = get_pagination();
  const form = $(this)[0];
  const leads = $("#lead_id").val();

  let param = new FormData(form);
  param.append("_action", "update");
  param.append("payload", "all-lead");

  const idsArr = leads.split(",");
  const marketers = [];
  let marketerId;

  idsArr.forEach((id) => {
    marketerId = $(`#client-${id}`).attr("data-marketerid");
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

// =============================================================== End Assign Client Functionality ========================================================================

$("body").on("click", "tr .btn-primary", function () {
  var id = $(this).parent().parent().parent().attr("data-id");
  email(id);
});

function email(id) {
  $("#basicModalemail").modal("show");
  $("#basicModalemail #_id").val(id);
}

$("#organization_form_email").submit(function (e) {
  e.preventDefault();

  var id = $("#basicModalemail #_id").val();
  var form = $(this)[0];
  var param = new FormData(form);
  param.append("_action", "email");
  param.append("payload", "agreement");
  param.append("limit", 1);
  param.append("pagination", 1);
  param.append("filter", JSON.stringify({ id: id }));
  var res = run(param);
  var json = JSON.parse(res.responseText);

  if (json["status"] === 1) {
    toastr.success(json.data, "Success");
    $("#basicModalemail").modal("hide");
  } else {
    toastr.error(json.data, "Error");
  }
});

/*
 Delete
*/

$("body").on("click", "tr .btn-danger", function () {
  var id = $(this).parent().parent().parent().attr("data-id");
  remove_modal(id);
});

$("#_delete_form").submit(function (e) {
  e.preventDefault();

  if ($('#_delete_form input[name="id"]').val().length === 0) {
    toastr.error("Something Went Wrong", "Error");
    return false;
  }

  var form = $(this)[0];
  var param = new FormData(form);
  param.append("_action", "remove");
  param.append("payload", "agreement");

  l_event = '#_delete_form button[type="submit"]';
  l_data = "Confirm";
  loader(l_event);
  var res = run(param);
  loader(l_event, l_data);

  var json = JSON.parse(res.responseText);
  if (json["status"] === 1) {
    toastr.success(json["data"], "Success");
    $("#_delete_modal").modal("hide");
    get_data(1);
  } else {
    toastr.error(json["data"], "Error");
  }
});


$("body").on("click", "#_pagination", function () {
  get_data_with_filters($(this).attr("data-id"));
});

$("body").on("click", "tr .btn-warning", function () {
  var id = $(this).parent().parent().parent().attr("id");
  edit_data(id);
});

function edit_data(id) {
  $("#basicModaledit").modal("show");

  var param = new FormData();
  param.append("_action", "get");
  param.append("payload", "agreement");
  param.append("limit", 1);
  param.append("pagination", 1);
  param.append("getPlan", 1);
  param.append("filter", JSON.stringify({ id: id }));
  var res = run(param);
  var json = JSON.parse(res.responseText);

  $("#basicModaledit #_id").val(id);
  $('#basicModaledit input[name="companyowner"]').val(
    json.data[0]["companyowner"]
  );
  $('#basicModaledit input[name="companyemail"]').val(
    json.data[0]["companyowneremail"]
  );
  $('#basicModaledit input[name="companyphone"]').val(
    json.data[0]["companyownerphone"]
  );
  $("#basicModaledit #nature").html(json.data[0]["companytype"]);
  $('#basicModaledit input[name="c_name"]').val(json.data[0]["name"]);
  $('#basicModaledit input[name="c_phone"]').val(json.data[0]["phone"]);
  $('#basicModaledit input[name="c_email"]').val(json.data[0]["email"]);
  $('#basicModaledit input[name="committed"]').val(json.data[0]["committed"]);
  $('#basicModaledit input[name="reward"]').val(json.data[0]["reward"]);
  $('#basicModaledit input[name="credit_card"]').val(
    json.data[0]["credit_card"]
  );
  $('#basicModaledit input[name="benefits"]').val(json.data[0]["benefits"]);
  $('#basicModaledit input[name="forex"]').val(json.data[0]["forex"]);
  $('#basicModaledit input[name="fintech_otherservice"]').val(
    json.data[0]["other"]
  );
  $('#basicModaledit input[name="organization"]').val(
    json.data[0]["organization"]
  );
  $('#basicModaledit input[name="organization_phone"]').val(
    json.data[0]["organization_phone"]
  );
  $('#basicModaledit input[name="organization_email"]').val(
    json.data[0]["organization_email"]
  );
  $('#basicModaledit input[name="website"]').val(json.data[0]["website"]);
  $('#basicModaledit input[name="state"]').val(json.data[0]["state"]);
  $('#basicModaledit input[name="city"]').val(json.data[0]["city"]);
  $('#basicModaledit textarea[name="address"]').val(json.data[0]["address"]);
  $('#basicModaledit input[name="other_services"]').val(
    json.data[0]["other_services"]
  );
  $('#basicModaledit input[name="source"]').val(json.data[0]["source"]);
  if (json.data[0]["visa"] === 1) {
    $('#basicModaledit input[name="visa"]').prop("checked", true);
  }
  if (json.data[0]["ielts"] === 1) {
    $('#basicModaledit input[name="ielts"]').prop("checked", true);
  }
  if (json.data[0]["vistor_visa"] === 1) {
    $('#basicModaledit input[name="vistor_visa"]').prop("checked", true);
  }
  if (json.data[0]["work_visa"] === 1) {
    $('#basicModaledit input[name="work_visa"]').prop("checked", true);
  }
  if (json.data[0]["pr"] === 1) {
    $('#basicModaledit input[name="pr"]').prop("checked", true);
  }
  if (json.data[0]["account_email_id"] !== '' || json.data[0]["account_email_id"] == null) {
    $('#basicModaledit input[name="account_email_id"]').val(json.data[0]["account_email_id"]);
  }

  if (json.data[0]["crm_soft"] === "no") {
    $('#basicModaledit input[name="crm_soft"][value="no"]').prop(
      "checked",
      true
    );
  } else if (json.data[0]["crm_soft"] !== null) {
    $('#basicModaledit input[name="crm_soft"][value="yes"]')
      .prop("checked", true)
      .parent()
      .parent()
      .children()
      .eq(3)
      .removeClass("d-none")
      .html(json.data[0]["crm_soft"]);
  }

  if (json.data[0]["calling_soft"] === "no") {
    $('#basicModaledit input[name="calling_soft"][value="no"]').prop(
      "checked",
      true
    );
  } else if (json.data[0]["calling_soft"] !== null) {
    $('#basicModaledit input[name="calling_soft"][value="yes"]')
      .prop("checked", true)
      .parent()
      .parent()
      .children()
      .eq(3)
      .removeClass("d-none")
      .html(json.data[0]["calling_soft"]);
  }

  if (json.data[0]["sms_soft"] === "no") {
    $('#basicModaledit input[name="sms_soft"][value="no"]').prop(
      "checked",
      true
    );
  } else if (json.data[0]["sms_soft"] !== null) {
    $('#basicModaledit input[name="sms_soft"][value="yes"]')
      .prop("checked", true)
      .parent()
      .parent()
      .children()
      .eq(3)
      .removeClass("d-none")
      .html(json.data[0]["sms_soft"]);
  }

  if (json.data[0]["whatsapp_soft"] === "no") {
    $('#basicModaledit input[name="whatsapp_soft"][value="no"]').prop(
      "checked",
      true
    );
  } else if (json.data[0]["whatsapp_soft"] !== null) {
    $('#basicModaledit input[name="whatsapp_soft"][value="yes"]')
      .prop("checked", true)
      .parent()
      .parent()
      .children()
      .eq(3)
      .removeClass("d-none")
      .html(json.data[0]["whatsapp_soft"]);
  }

  if (json.data[0]["email_soft"] === "no") {
    $('#basicModaledit input[name="email_soft"][value="no"]').prop(
      "checked",
      true
    );
  } else if (json.data[0]["email_soft"] !== null) {
    $('#basicModaledit input[name="email_soft"][value="yes"]')
      .prop("checked", true)
      .parent()
      .parent()
      .children()
      .eq(3)
      .removeClass("d-none")
      .html(json.data[0]["email_soft"]);
  }
  $("#numberOfForexsEdit").val(json.data[0]["number_of_forex_commited"]);
  if (
    json.data[0]["uploaded_contract_path"] === null ||
    json.data[0]["uploaded_contract_path"] === ""
  ) {
    $("#contractUpload").show();
    $("#contractFileUploaded").hide();
  } else {
    $("#contractFileUploaded").show();
    $("#contractUpload").hide();
    let fileName = json.data[0]["contract_filename"];
    $("#contractFileName").text(fileName);
    $("#view_contract").attr("href", json.data[0]["uploaded_contract_path"]);
    $("#remove_contract").attr("data-id", id);
  }
  if (
    json.data[0]["org_logo_path"] === null ||
    json.data[0]["org_logo_path"] === ""
  ) {
    $("#logoUpload").show();
    $("#logoFileUploaded").hide();
  } else {
    $("#logoFileUploaded").show();
    $("#logoUpload").hide();
    let fileName = json.data[0]["org_logo_filename"];
    $("#logoFileName").text(fileName);
    $("#view_logo").attr("href", json.data[0]["org_logo_path"]);
    $("#remove_logo").attr("data-id", id);
  }
  if (json.data[0]['lead_type'] == 'Paid') {
    let planData = json.plan;
    $("#editSelectPackage").removeClass('d-none');
    $("#editselectPackageSelect").val(planData.title);
    $("#editedPlanId").val(planData.plan_id);
    $("#editPlanPrice").val(planData.amount);
    $("#editSelectPaymentType").val(planData.payment_type.toLowerCase());
    $("#editPaymentMode").val(planData.payment_mode);
    $("#editPricingDiscount").val(planData.given_discount);
    $("#editAmountPaid").val(planData.amount - planData.given_discount ?? 0);
    $("#viewPlanReciept").html(`<a target="_blank" href="https://team.innerxcrm.com/${planData.reciept_attachment}">View</a>`)
  }

  if (json.data[0]['lead_type'] !== 'Free' && json.data[0]['lead_type'] !== 'Paid') {
    $("#clientTypeFree").prop('checked', false);
    $("#clientTypePaid").prop('checked', false);
    $("#editSelectPackage").addClass('d-none');
  }
  $("#editClientCategory").val(json.data[0]["client_category"]);
  $('#basicModaledit select[name="crm_users"]').val(json.data[0]["crm_users"]);
 
}

$(document).on("click", "#remove_contract", function () {
  if (confirm("Are you sure? You want to delete it !")) {
    let id = $(this).data("id");
    var param = new FormData();
    param.append("_action", "deleteContract");

    param.append("id", id);
    param.append("payload", "agreement");
    $(this).text("Removing...");
    let res = run(param);
    $(this).text("Remove");
    if (res.responseJSON.status == "success") {
      $("#contractUpload").value = "";
      $("#contractFileUploaded").css("display", "none");
    }
  }
});

$(document).on("click", "#remove_logo", function () {
  if (confirm("Are you sure? You want to delete it !")) {
    let id = $(this).data("id");
    var param = new FormData();
    param.append("_action", "deleteLogo");

    param.append("id", id);
    param.append("payload", "agreement");
    $(this).text("Removing...");
    let res = run(param);
    $(this).text("Remove");
    if (res.responseJSON.status == "success") {
      $("#logoUpload").value = "";
      $("#logoFileUploaded").css("display", "none");
    }
  }
});

$("#organization_form_edit").submit(function (e) {
  e.preventDefault();

  var form = $(this)[0];
  var param = new FormData(form);
  param.append("_action", "update");
  param.append("payload", "agreement");

  var res = run(param);
  var json = JSON.parse(res.responseText);
  console.log(json);
  if (json["status"] === 1) {
    toastr.success(json["data"], "Success");
    $("#basicModaledit").modal("hide");
    get_data_with_filters(get_pagination());
  }
});

$("body").on("click", "input[type='radio']", function () {
  if ($(this).val() === "yes") {
    $(this).parent().parent().children().eq(3).removeClass("d-none");
  } else {
    $(this).parent().parent().children().eq(3).addClass("d-none");
  }
});

$("body").on("click", "input[name='location_radio']", function () {
  if ($(this).val() === "yes") {
    $("#_more_locations").removeClass("d-none");
  } else {
    $("#_more_locations").addClass("d-none");
  }
});

$("#_add_location").click(function () {
  newRowAdd =
    '<div class="mb-2 col-md-11" id="_remove_locations"><div class="input-group"><input type="text" aria-label="city" placeholder="City" name="location_city[]" class="form-control"><input type="text" aria-label="state" placeholder="State" name="location_state[]" class="form-control"><span id="_remove_location" class="cursor-pointer input-group-text"><i class=" bx bx-minus"></i> </span></div></div>';

  $("#newinput").append(newRowAdd);
});

$("body").on("click", "#_remove_location", function () {
  $(this).parents("#_remove_locations").remove();
});

$(document).on("submit", "#filterAgreement", function (e) {
  e.preventDefault();

  let contractVal = $("#contractFilter").val();
  let searchValue = $("#searchOrganization").val();
  let filter = {
    contract: contractVal,
    search: searchValue,
  };

  if (contractVal === "2") {
    filter.upload_contract_path = null;
  } else if (contractVal === "1") {
    filter.upload_contract_path = { $ne: null };
  }
  let page = get_pagination();
  get_data(page, filter, true);

});

$(document).on("click", "#applyFilters", function () {
  get_data_with_filters(get_pagination());
});

$(document).on("click", "#reset", function () {
  $("#searchOrganization").val("");
  $("#contractFilter").val("");
  let page = get_pagination();
  let filter = {};
  get_data(page, filter);
});

function get_pagination() {
  if ($(".page-item.active").text() !== "") {
    return $(".page-item.active").text();
  } else {
    return 1;
  }
}


function get_data_with_filters(page = 1, dateRange = "", search = '', trainer = '', manager = '', source = '', status = '', subStatus = '') {

  let searchFilter = $("#searchOrganization").val();
  let trainerFilter = $("#trainerFilter").val();
  let managerFilter = $("#managerFilter").val();
  let sourceFilter = $("#sourceFilter").val();

  let filter = {
    search: searchFilter,
    trainer: trainerFilter,
    manager: managerFilter,
    source: sourceFilter,
  };
  if (dateRange) {
    filter.dateRange = dateRange;
  }
  console.log('sdfsdf');
  get_data(page, filter);
}

$(document).on('click', '.clientType', function () {
  const clientType = $(this).val();
  if (clientType === 'Paid') {
    $("#selectPackage").removeClass('d-none');
    $("#selectPackageSelect").attr('required', true);
    $("#selectPaymentType").attr('required', true);
    $("#amountPaid").attr('required', true);
    $("#receiptAttached").attr('required', true);
  } else {
    $("#selectPackage").addClass('d-none');
    $("#selectPackageSelect").attr('required', false);
    $("#selectPaymentType").attr('required', false);
    $("#amountPaid").attr('required', false);
    $("#receiptAttached").attr('required', false);
  }
});

$(document).on('click', '.editClientType', function () {
  const clientType = $(this).val();
  if (clientType === 'Paid') {
    $("#editSelectPackage").removeClass('d-none');
    $("#selectPackageSelect").attr('required', true);
    $("#selectPaymentType").attr('required', true);
    $("#amountPaid").attr('required', true);
    $("#receiptAttached").attr('required', true);
  } else {
    $("#editSelectPackage").addClass('d-none');
    $("#selectPackageSelect").attr('required', false);
    $("#selectPaymentType").attr('required', false);
    $("#amountPaid").attr('required', false);
    $("#receiptAttached").attr('required', false);
  }
});

$(document).on('change', '#selectPackageSelect', function (e) {
  let planName = $(this).val();
  let selector = $(`#${planName}_plan`);
  let planPrice = selector.attr('data-price');
  let discountPrice = selector.attr('data-discount');
  let planId = selector.attr('data-id');

  $("#planPrice").val(planPrice);
  $("#amountPaid").val(planPrice);
  $("#planId").val(planId);
});
$(document).on('change', '#editselectPackageSelect', function (e) {
  let planName = $(this).val();
  let selector = $(`#${planName}_plan`);
  let planPrice = selector.attr('data-price');
  let planId = selector.attr('data-id');

  $("#editPlanPrice").val(planPrice);
  $("#editAmountPaid").val(planPrice);
  $("#editedPlanId").val(planId);
});



$(document).on("keyup", "#pricingDiscount", function () {
  let packageAmount = $("#planPrice").val();
  let discountValue = $(this).val();
  let planName = $('#selectPackageSelect').val();
  if (discountValue && !planName) {
    $(this).val('');
    alert('Select a package first');
  }
  let maxDiscountAmount = $(`#${planName}_plan`).attr('data-discount');
  $("#amountPaid").val(packageAmount - discountValue);
  if (!isNaN(discountValue) && parseInt(discountValue) > parseInt(maxDiscountAmount)) {
    $(this).val(maxDiscountAmount);
    $("#amountPaid").val(packageAmount - maxDiscountAmount);
    $("#discountErrorText").removeClass("d-none");
    $("#errorMaxDiscount").text(maxDiscountAmount);
  } else {
    $("#paid").val(packageAmount - discountValue);
    $("#discountErrorText").addClass("d-none");
  }
});
$(document).on("keyup", "#editPricingDiscount", function () {
  let packageAmount = $("#editPlanPrice").val();
  let discountValue = $(this).val();
  let planName = $('#editselectPackageSelect').val();
  if (discountValue && !planName) {
    $(this).val('');
    alert('Select a package first');
  }
  let maxDiscountAmount = $(`#${planName}_plan`).attr('data-discount');
  $("#editAmountPaid").val(packageAmount - discountValue);
  if (!isNaN(discountValue) && parseInt(discountValue) > parseInt(maxDiscountAmount)) {
    $(this).val(maxDiscountAmount);
    $("#editAmountPaid").val(packageAmount - maxDiscountAmount);
    $("#editDiscountErrorText").removeClass("d-none");
    $("#editErrorMaxDiscount").text(maxDiscountAmount);
  } else {
    $("#editAmountPaid").val(packageAmount - discountValue);
    $("#editDiscountErrorText").addClass("d-none");
  }
});

function restrictInputToNumbers(inputElement) {
  inputElement.addEventListener("input", function (event) {
    let inputValue = event.target.value.replace(/^0+/, "");
    inputValue = inputValue.replace(/[^0-9]/g, "");
    event.target.value = inputValue;
  });
}



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



$(document).on('click', '#selectAllClients', function () {
  if ($(this).is(':checked')) {
    $(".selectThisClient").each(function (value) {
      $(this).prop('checked', true);
    });
  } else {
    $(".selectThisClient").each(function (value) {
      $(this).prop('checked', false);
    });
  }
});


// Assign Clients Functionality =========================================
//=======================================================================
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

function getPricingPlans() {
  let formRequest = new FormData();
  formRequest.append("_action", "pricingPlans");
  formRequest.append("payload", "view-lead");
  let res = run(formRequest);
  let pricingHtml = "<option value=''>Select</option>";
  let plansHtml = '';
  Object.entries(res.responseJSON).forEach(function ([key, value]) {
    pricingHtml += `<option value="${value.title}" >${value.title}</option>`;
    plansHtml += `<input id="${value.title}_plan" data-price="${value.price}" data-id="${value.id}" data-discount="${value.max_discount}" type="hidden" />`;
  });
  $("#selectPackageSelect").html(pricingHtml);
  $("#editselectPackageSelect").html(pricingHtml);
  $("#planDetails").html(plansHtml);
}

getPricingPlans();
