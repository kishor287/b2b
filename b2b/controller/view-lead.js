/*

Meeting Table headings

*/

var table_cols = [
  //  'checkbox',
  "S.no.",
  "Meeting Date-Time",
  "Assigned By",
  "Procedure",
  "Remarks",
  "Status",
  "Created",
];
table_head(table_cols, "#navs-justified-schedule");

/*

Followup Table headings

*/

var table_cols = [
  "S.no.",
  "Created",
  "Lead Created By",
  "Next Followup",
  "Added By",
  "Status",
  "Sub Status",
  "Remarks",
  "Recording",
];
table_head(table_cols, "#navs-justified-followups");

edit_data(gpt[4]);

function edit_data(id) {
  var param = new FormData();
  param.append("_action", "get");
  param.append("payload", "view-lead");
  param.append("limit", 1);
  param.append("pagination", 1);
  param.append("filter", JSON.stringify({ id: id }));
  var res = run(param);
  var json = JSON.parse(res.responseText);
  $('#view-lead-edit input[name="companyowner"]').val(
    json.main.data[0]["companyowner"]
  );
  $('#view-lead-edit input[name="companyphone"]').val(
    json.main.data[0]["companyownerphone"]
  );
  $('#view-lead-edit input[name="companyemail"]').val(
    json.main.data[0]["companyowneremail"]
  );
  $('#view-lead-edit input[name="c_name"]').val(json.main.data[0]["name"]);
  $('#view-lead-edit input[name="c_phone"]').val(json.main.data[0]["phone"]);
  $('#view-lead-edit input[name="c_email"]').val(json.main.data[0]["email"]);
  $('#view-lead-edit input[name="committed"]').val(
    json.main.data[0]["committed"]
  );
  $('#view-lead-edit input[name="reward"]').val(json.main.data[0]["reward"]);
  $('#view-lead-edit input[name="credit_card"]').val(
    json.main.data[0]["credit_card"]
  );
  $('#view-lead-edit input[name="benefits"]').val(
    json.main.data[0]["benefits"]
  );
  $('#view-lead-edit input[name="forex"]').val(json.main.data[0]["forex"]);
  $('#view-lead-edit input[name="fintech_otherservice"]').val(
    json.main.data[0]["other"]
  );
  $('#view-lead-edit input[name="organization"]').val(
    json.main.data[0]["organization"]
  );
  $('#view-lead-edit input[name="organization_phone"]').val(
    json.main.data[0]["organization_phone"]
  );
  $('#view-lead-edit input[name="website"]').val(json.main.data[0]["website"]);
  $('#view-lead-edit input[name="organization_email"]').val(
    json.main.data[0]["organization_email"]
  );
  $('#view-lead-edit input[name="state"]').val(json.main.data[0]["state"]);
  $('#view-lead-edit input[name="city"]').val(json.main.data[0]["city"]);
  $('#view-lead-edit textarea[name="address"]').val(
    json.main.data[0]["address"]
  );
  $('#view-lead-edit textarea[name="companytype"]').val(
    json.main.data[0]["companytype"]
  );
  $('#view-lead-edit select[name="crm_users"]').val(
    json.main.data[0]["crm_users"]
  );
  $('#view-lead-edit input[name="other_services"]').val(
    json.main.data[0]["other_services"]
  );
  $('#view-lead-edit select[name="crm_users"]').val(
    json.main.data[0]["crm_users"]
  );
  $('#view-lead-edit select[name="procedure"]').val(
    json.main.data[0]["procedure"]
  );
  $('#view-lead-edit select[name="source"]').val(json.main.data[0]["source"]);
  if (json.main.data[0]["visa"] === 1) {
    $('#view-lead-edit input[name="visa"]').prop("checked", true);
  }
  if (json.main.data[0]["ielts"] === 1) {
    $('#view-lead-edit input[name="ielts"]').prop("checked", true);
  }
  if (json.main.data[0]["vistor_visa"] === 1) {
    $('#view-lead-edit input[name="vistor_visa"]').prop("checked", true);
  }
  if (json.main.data[0]["work_visa"] === 1) {
    $('#view-lead-edit input[name="work_visa"]').prop("checked", true);
  }
  if (json.main.data[0]["pr"] === 1) {
    $('#view-lead-edit input[name="pr"]').prop("checked", true);
  }

  if (json.main.data[0]["crm_soft"] === "no") {
    $('#view-lead-edit input[name="crm_soft"][value="no"]').prop(
      "checked",
      true
    );
  } else if (json.main.data[0]["crm_soft"] !== null) {
    $('#view-lead-edit input[name="crm_soft"][value="yes"]')
      .prop("checked", true)
      .parent()
      .parent()
      .children()
      .eq(3)
      .removeClass("d-none")
      .html(json.main.data[0]["crm_soft"]);
  }

  if (json.main.data[0]["calling_soft"] === "no") {
    $('#view-lead-edit input[name="calling_soft"][value="no"]').prop(
      "checked",
      true
    );
  } else if (json.main.data[0]["calling_soft"] !== null) {
    $('#view-lead-edit input[name="calling_soft"][value="yes"]')
      .prop("checked", true)
      .parent()
      .parent()
      .children()
      .eq(3)
      .removeClass("d-none")
      .html(json.main.data[0]["calling_soft"]);
  }

  if (json.main.data[0]["sms_soft"] === "no") {
    $('#view-lead-edit input[name="sms_soft"][value="no"]').prop(
      "checked",
      true
    );
  } else if (json.main.data[0]["sms_soft"] !== null) {
    $('#view-lead-edit input[name="sms_soft"][value="yes"]')
      .prop("checked", true)
      .parent()
      .parent()
      .children()
      .eq(3)
      .removeClass("d-none")
      .html(json.main.data[0]["sms_soft"]);
  }

  if (json.main.data[0]["whatsapp_soft"] === "no") {
    $('#view-lead-edit input[name="whatsapp_soft"][value="no"]').prop(
      "checked",
      true
    );
  } else if (json.main.data[0]["whatsapp_soft"] !== null) {
    $('#view-lead-edit input[name="whatsapp_soft"][value="yes"]')
      .prop("checked", true)
      .parent()
      .parent()
      .children()
      .eq(3)
      .removeClass("d-none")
      .html(json.main.data[0]["whatsapp_soft"]);
  }

  if (json.main.data[0]["email_soft"] === "no") {
    $('#view-lead-edit input[name="email_soft"][value="no"]').prop(
      "checked",
      true
    );
  } else if (json.main.data[0]["email_soft"] !== null) {
    $('#view-lead-edit input[name="email_soft"][value="yes"]')
      .prop("checked", true)
      .parent()
      .parent()
      .children()
      .eq(3)
      .removeClass("d-none")
      .html(json.main.data[0]["email_soft"]);
  }

  $("#_view_upload_contract").addClass("d-none");
  $("#_view_contract").addClass("d-none");
  if (
    json.main.data[0]["contract"] !== "" &&
    json.main.data[0]["contract"] !== null
  ) {
    $("#_view_contract").removeClass("d-none");
    $("#_contract").attr(
      "href",
      "/uploads/contract/" + json.main.data[0]["contract"]
    );
  } else {
    $("#_view_upload_contract").removeClass("d-none");
  }

  $('#view-lead-edit input[name="location_radio"][value="no"]').prop(
    "checked",
    true
  );
  var newRowAdd = "";
  $.each(json.locations.data, function (k, v) {
    $('#view-lead-edit input[name="location_radio"][value="yes"]').prop(
      "checked",
      true
    );
    var el =
      k == 0
        ? '<span id="_add_location" class="cursor-pointer input-group-text"><i class=" bx bx-plus"></i> </span>'
        : '<span id="_remove_location" class="cursor-pointer input-group-text"><i class=" bx bx-minus"></i> </span>';
    newRowAdd +=
      '<div class="mb-2 col-md-11" id="_remove_locations"><div class="input-group"><input type="text" aria-label="city" placeholder="City" name="location_city[]" value="' +
      v["city"] +
      '" class="form-control"><input type="text" aria-label="state" placeholder="State" name="location_state[]" value="' +
      v["state"] +
      '" class="form-control">' +
      el +
      "</div></div>";
  });

  $("#newinput").html(newRowAdd);
}

$("#view-lead-edit").submit(function (e) {
  e.preventDefault();
  var form = $(this)[0];
  var param = new FormData(form);
  param.append("_action", "update");
  param.append("lead_id", gpt[4]);
  param.append("payload", "view-lead");

  var res = run(param);

  var json = JSON.parse(res.responseText);
  if (json["status"] === 1) {
    toastr.success(json["data"], "Success");
    edit_data(gpt[4]);
  } else {
    toastr.error(json["data"], "Error");
  }
});

$("body").on("click", "#_add_location", function () {
  newRowAdd =
    '<div class="mb-2 col-md-11" id="_remove_locations"><div class="input-group"><input type="text" aria-label="city" placeholder="City" name="location_city[]" class="form-control"><input type="text" aria-label="state" placeholder="State" name="location_state[]" class="form-control"><span id="_remove_location" class="cursor-pointer input-group-text"><i class=" bx bx-minus"></i> </span></div></div>';
  $("#newinput").append(newRowAdd);
});

$("body").on("click", "#_remove_location", function () {
  $(this).parents("#_remove_locations").remove();
});

$("body").on("click", "input[name='location_radio']", function () {
  if ($(this).val() === "yes") {
    $("#newinput").removeClass("d-none");
    if ($("#newinput").html() === "") {
      $("#newinput").html(
        '<div class="mb-2 col-md-11" id="_add_locations"><div class="input-group"><input type="text" aria-label="city" placeholder="City" name="location_city[]" class="form-control"><input type="text" aria-label="state" placeholder="State" name="location_state[]" class="form-control"><span id="_add_location" class="cursor-pointer input-group-text"><i class=" bx bx-plus"></i> </span></div></div>'
      );
    }
  } else {
    $("#newinput").addClass("d-none");
  }
});

/*

Followups 

*/
get_followups(1);
function get_followups(page, filter) {
  limit = 10;
  l_event = "tbody";

  var param = new FormData();
  param.append("_action", "get_followups");
  param.append("payload", "view-lead");
  param.append("limit", limit);
  param.append("pagination", page);
  param.append("lead_id", gpt[4]);
  var res = run(param);
  var json = JSON.parse(res.responseText);
  var jHTML = "";
  $.each(json.data, function (k, v) {
    jHTML += '<tr  data-id="' + v["lead_id"] + '" >';
    jHTML += "<td>" + (page * limit - (limit - (k + 1))) + "</td>";
    jHTML += "<td>" + v["created"] + "</td>";

    let lname = "";
    if (v["created_by_lname"] == null || v["created_by_lname"] == "") {
      lname = "";
    }
    let fname = "";
    if (v["created_by_fname"] == null || v["created_by_fname"] == "") {
      fname = "";
    }
    let audio = "";
    if (v["recording"]) {
      audio = `<td><audio preload="auto" controls><source src="${v["recording"]}"></audio></td>`;
    }
    console.log(v["recording"]);
    jHTML += "<td><b>" + fname + lname + "</b></td>";
    jHTML += "<td>" + v["next_followup_date"] + "</td>";
    jHTML += "<td>" + v["addedByName"] + "</td>";
    jHTML += "<td>" + v["status"] + "</td>";
    jHTML += "<td>" + v["sub_status"] + "</td>";
    jHTML += "<td>" + v["remarks"] + "</td>";
    jHTML += audio;
    jHTML += "</tr>";
  });
  $("#followupTbody").html(jHTML);
  $("#followUpsPagination").html(json.pagination);
}

/*
Saving the data into the users database...
*/

$("#followup_form").submit(function (e) {
  e.preventDefault();
  var form = $(this)[0];
  var param = new FormData(form);
  param.append("_action", "save_followups");
  param.append("lead_id", gpt[4]);
  param.append("payload", "view-lead");

  var res = run(param);

  var json = JSON.parse(res.responseText);
  if (json["status"] === 1) {
    $("#basicModal").modal("hide");
    $("#followup_form")[0].reset();
    toastr.success("Added", "Data Inserted Successfully.");
    get_followups(1);
  } else if (json["status"] === 0) {
    toastr.error("Error", json["data"]);
  } else {
    toastr.error("Error", "Internal Server Error");
  }
});

/*
get_followups Pagination
*/

$("body").on("click", "#navs-justified-followups #_pagination", function () {
  get_followups($(this).attr("data-id"));
});

/*

Meeting 

*/

get_meetings(1, gpt["4"]);
function get_meetings(page, id) {
  limit = 10;
  l_event = "tbody";

  var param = new FormData();
  param.append("_action", "get_meetings");
  param.append("payload", "view-lead");
  param.append("limit", limit);
  param.append("pagination", page);
  param.append("filter", JSON.stringify({ id: id }));
  var res = run(param);
  var json = JSON.parse(res.responseText);
  var users = get_users();

  var jHTML = "";
  $.each(json.data, function (k, v) {
    jHTML += '<tr data-id="' + v["lead_id"] + '" >';
    jHTML += "<td>" + (page * limit - (limit - (k + 1))) + "</td>";
    jHTML += "<td><b>" + v["new_meeting_date"] + "</b></td>";
    jHTML += "<td>" + users[v["assigned_by"]] + "</td>";
    jHTML += "<td>" + v["procedure"] + "</td>";
    //  jHTML+='<td>'+v['assigned_to']+'</td>';
    jHTML += "<td>" + v["remarks"] + "</td>";
    jHTML += "<td>" + v["status"] + "</td>";
    jHTML += "<td>" + v["created"] + "</td>";
    jHTML +=
      '<td><a href="javascript:void();" class="btn btn-primary shadow btn-xs sharp me-1"><i  class="bx bxl-whatsapp"></i> </a> <a href="javascript:"  class="btn btn-primary shadow btn-xs sharp me-1"><i class="bx bx-envelope"></i></a></td>';

    jHTML += "</tr>";
  });

  $("#navs-justified-schedule tbody").html(jHTML);
  $("#navs-justified-schedule .pagination").html(json.pagination);
}

/*
Saving the data into the users database...
*/
$("#meeting_form").submit(function (e) {
  e.preventDefault();
  var form = $(this)[0];
  var param = new FormData(form);
  param.append("_action", "save_meeting");
  param.append("payload", "view-lead");
  param.append("id", gpt[4]);
  var res = run(param);

  var json = JSON.parse(res.responseText);
  if (json["status"] === 1) {
    $("#meetingModal").modal("hide");
    $("#meeting_form")[0].reset();
    toastr.success("Added", "Data Inserted Successfully.");
    get_meetings(1, gpt["4"]);
  } else if (json["status"] === 0) {
    toastr.error("Error", json["data"]);
  } else {
    toastr.error("Error", "Internal Server Error");
  }
});

/*
get_meetings Pagination
*/

$("body").on("click", "#navs-justified-schedule #_pagination", function () {
  get_meetings($(this).attr("data-id"));
});

/*
adding class d-none to the textarea
*/

$("body").on("click", "input[type='radio']", function () {
  if ($(this).val() === "yes") {
    $(this).parent().parent().children().eq(3).removeClass("d-none");
  } else {
    $(this).parent().parent().children().eq(3).addClass("d-none");
  }
});

/*
Email link
*/
$("body").on("click", "tr .btn-primary", function () {
  var id = $(this).parent().parent().attr("data-id");
  email(id);
});

function email(id) {
  $("#meeting-detail").modal("show");
  $("#meeting-detail #_id").val(id);
}

$("#meeting_link_form").submit(function (e) {
  e.preventDefault();

  var id = $("#meeting-detail #_id").val();
  var form = $(this)[0];
  var param = new FormData(form);
  param.append("_action", "email");
  param.append("payload", "view-lead");
  param.append("limit", 1);
  param.append("pagination", 1);
  param.append("filter", JSON.stringify({ id: id }));

  var res = run(param);

  var json = JSON.parse(res.responseText);
  if (json["status"] === 1) {
    toastr.success(json["data"], "Success");
    $("#meeting-detail").modal("hide");
  } else {
    toastr.error(json["data"], "Error");
  }
});

$(document).ready(function () {
  $("#followup_status_selector").on("change", function () {
    let selectorVal = $(this).val();
    if (selectorVal.toUpperCase() === "NOT INTERESTED") {
      $("#nextFollowUpDate").hide().removeAttr("required");
    } else {
      $("#nextFollowUpDate").show().prop("required", true);
    }
  });
});

function getPricingPlans() {
  let formRequest = new FormData();
  formRequest.append("_action", "pricingPlans");
  formRequest.append("payload", "view-lead");
  let res = run(formRequest);
  console.log(res.responseJSON);
  let pricingHtml = "";

  Object.entries(res.responseJSON).forEach(function ([key, value]) {
    pricingHtml += `<tr id="pricing_row_${value.id}">
    <td>${value.id}</td>
    <td class="planTitle">${value.title}</td>
    <td class="planPrice">${value.price}</td>
    <td class="discountPrice">${value.max_discount}</td>
    <td>${value.description}</td>
    <td data-id="${value.id}" data-price="${value.price}" class="selectPricingPlan  d-flex"><div class="p-2 bg-primary border-1 rounded text-white cursor-pointer" >Select Plan</div></td>
    </tr>`;
  });
  $("#pricingPlansBody").html(pricingHtml);
}

const getUserRegistrationPlan = () => {
  $(".optionBtns").removeClass("d-none"); // showing {add payments } and {upgrade plan } btns if hidden
  let formRequest = new FormData();
  formRequest.append("_action", "userRegistrationPlan");
  formRequest.append("userId", gpt[4]);
  formRequest.append("payload", "view-lead");
  let res = run(formRequest);
  let planHTML = "";
  let inc = 1;
  Object.entries(res.responseJSON).forEach(function ([key, value]) {
    let date = new Date(value.created_at);
    let recieptAttchment = 'Not Uploaded';
    if (value.reciept_attachment) {
      recieptAttchment = `<a class="btn btn-sm btn-warning" href="https://team.innerxcrm.com/${value.reciept_attachment}" download >View</a>`;
    }
    let trColor = '';

    var toDate = new Date(value.to_date).toLocaleDateString(); // Convert the 'to_date' string to a Date object
    var currentDate = new Date().toLocaleDateString(); // Get the current date

    if (toDate >= currentDate) {
      trColor = ' bg bg-danger ';
    }
    date = date.toDateString();
    planHTML += `<tr ${trColor} id="plan_row_${value.id}">
    <td>${inc++}</td>
    <td>${recieptAttchment}</td>
    <td>${value.title}</td>
    <td>${value.price}</td>
    <td>${value.given_discount}</td>
    <td>${value.amount}</td>
    <td><span class="bg-primary p-1 rounded" style="color:white;">${value.payment_type
      }</span></td>
    <td>${new Date(value.from_date).toLocaleDateString()}</td>
    <td>${new Date(value.to_date).toLocaleDateString()}</td>
    <td>${value.payment_mode}</td>
    <td>${value.remarks}</td>
    </tr>`;
  });
  // <td>
  // <a title="Remove Record" data-id="${value.payment_id}" href="javascript:" class="btn btn-danger shadow btn-xs sharp" id="deleteUserPricingPlan"><i class="bx bx-trash"></i></a>
  // </td>
  $("#registredPlanTable").removeClass("d-none");
  $(".addPlanRow").removeClass("d-none");
  $("#registredPlanTBody").html(planHTML);
  if (planHTML == "") {
    $(".addPlanRow").addClass("d-none");
    $("#registredPlanTable").addClass("d-none");
    $("#pricingPlanTable").removeClass("d-none");
    getPricingPlans();
  }
};

getUserRegistrationPlan();

$(document).on("click", ".selectPricingPlan", function () {
  $("#pay_form")[0].reset();
  $("#planTitle").text($(this).closest("tr").find(".planTitle").text());
  $("#planPrice").val($(this).closest("tr").find(".planPrice").text());
  $("#discountAmount").val($(this).closest("tr").find(".discountPrice").text());
  $("#recordId").val($(this).data("id"));
  $("#planId").val($(this).data("id"));
  $("#pricingPlanTable").addClass("d-none");
  let price = $(this).data("price");
  $("#paid").val(price);
  $("#pay_form").removeClass("d-none");
});

$(document).on("click", "#pricingPlanBackBtn", function () {
  $("#pricingPlanTable").removeClass("d-none");
  $("#pay_form").addClass("d-none");
});

$(document).on("keyup", "#pricingDiscount", function () {
  let packageAmount = $("#planPrice").val();
  let discountValue = $(this).val();
  let maxDiscountAmount = $("#discountAmount").val();
  if (!isNaN(discountValue) || discountValue !== "") {
    if (parseInt(discountValue) > parseInt(maxDiscountAmount)) {
      $(this).val(maxDiscountAmount);
      console.log(packageAmount, maxDiscountAmount);
      $("#paid").val(packageAmount - maxDiscountAmount);
      $("#discountErrorText").removeClass("d-none");
      $("#errorMaxDiscount").text(maxDiscountAmount);
    } else {
      $("#paid").val(packageAmount - discountValue);
      $("#discountErrorText").addClass("d-none");
    }
  }
});

const cannotTypeNumber = (event) => {
  const keyCode = event.keyCode || event.which;
  if (
    keyCode === 8 || // Backspace
    keyCode === 9 || // Tab
    keyCode === 46 || // Delete
    (keyCode >= 37 && keyCode <= 40) // Arrow keys
  ) {
    return;
  }
  if (keyCode < 48 || keyCode > 57) {
    event.preventDefault(); // Prevent input of non-numeric characters
  }
};

$(document).on("click", "#addMorePayments", function () {
  addPayment();
  $("#addPaymentModal").modal("show");
});

const addPayment = (userId = gpt[4]) => {
  let formRequest = new FormData();
  formRequest.append("user_id", userId);
  formRequest.append("_action", "getUserPlan");
  formRequest.append("payload", "view-lead");

  let result = run(formRequest);
  if (result.responseJSON.statusCode == 200) {
    let data = result.responseJSON.data[0];
    console.log(data);
    let totolPackageAmount = data.price;
    let discount = data.given_discount;
    let amountPaid = totolPackageAmount - discount;
    let planId = data.plan_id;
    $("#planPriceInModal").val(totolPackageAmount);
    $("#pricingDiscountInModal").val(discount);
    $("#paidInModal").val(amountPaid);
    $("#planIdInModal").val(planId);
  }
};

$(document).on("submit", "#pay_form", function (e) {
  e.preventDefault();

  if (!$("#discountErrorText").hasClass("d-none")) {
    toastr.error(
      "Discount cannot be greater than available discount on package"
    );
    return false;
  }

  let formRequest = new FormData(this);
  formRequest.append("_action", "savePaymentDetails");
  formRequest.append("payload", "view-lead");
  formRequest.append("lead_id", gpt[4]);
  let request = run(formRequest);
  let response = request.responseJSON;
  if (response.statusCode == 200) {
    $("#pay_form").addClass("d-none");
    getUserRegistrationPlan();
  }
});
$(document).on("submit", "#paymentFormInModal", function (e) {
  e.preventDefault();
  if (!$("#discountErrorText").hasClass("d-none")) {
    toastr.error(
      "Discount cannot be greater than available discount on package"
    );
    return false;
  }

  let formRequest = new FormData(this);
  formRequest.append("_action", "savePaymentDetails");
  formRequest.append("payload", "view-lead");
  formRequest.append("lead_id", gpt[4]);
  let request = run(formRequest);
  let response = request.responseJSON;
  if (response.statusCode == 200) {
    $("#addPaymentModal").modal("hide");
    getUserRegistrationPlan();
  }
});

$(document).on("click", "#deleteUserPricingPlan", function () {
  if (confirm("Are you sure? You want to delete it")) {
    let leadId = $(this).data("id");
    let formRequest = new FormData();
    formRequest.append("_action", "deleteUserRegistredPlan");
    formRequest.append("payload", "view-lead");
    formRequest.append("id", leadId);
    let request = run(formRequest);
    if (request.responseJSON.statusCode == 200) {
      $(`#plan_row_${leadId}`).remove();
      $("#pricingPlanTable").removeClass("d-none");
      $("#registredPlanTable").addClass("d-none");
      getPricingPlans();
    }
  }
});

const getActivities = () => {
  let formRequest = new FormData();
  formRequest.append("_action", "getActivities");
  formRequest.append("payload", "view-lead");
  formRequest.append("lead_id", gpt[4]);
  let request = run(formRequest);
  let response = request.responseJSON;
  let HTML = "";

  response.activities.forEach((element) => {
    let date = new Date(element.created_at);
    date = date.toDateString();
    HTML += `<tr>
    <td>${date}</td>
    <td>${element.tag}</td>
    <td>${element.description}</td>
    </tr>`;
  });
  if (HTML == "") {
    $("#leadActivityTbody").html(
      '<tr ><td colspan="3">No Activity Found</td></tr>'
    );
  } else {
    $("#leadActivityTbody").html(HTML);
  }
};

$(document).on("click", "#leadActivityTab", function () {
  $("#leadActivityTbody").html(
    '<tr class="text-center"><td colspan="3">Loading...</td></tr>'
  );
  setTimeout(function () {
    getActivities();
  }, 1000);
});

$(document).on("click", "#upgradePlan", function () {
  $("#registredPlanTable").addClass("d-none");
  $("#pricingPlanTable").removeClass("d-none");
  $("#backFromUpgrade").removeClass("d-none");
  $(".optionBtns").addClass("d-none");
  setTimeout(function () {
    getPricingPlans();
  }, 400);
});

$(document).on('click', '#backFromUpgrade', function () {
  $("#registredPlanTable").removeClass("d-none");
  $("#pricingPlanTable").addClass("d-none");
  $(".optionBtns").removeClass("d-none");
  $(this).addClass('d-none');
});

$(document).on("click", ".yesToService", function () {
  if ($(this).is(":checked")) {
    $(this).parent("td").next("td").removeClass("d-none");
  }
});
$(document).on("click", ".noToService", function () {
  if ($(this).is(":checked")) {
    $(this).parent("td").next("td").addClass("d-none");
    console.log("checked");
  }
});

$("#checklistForm").on("submit", function (e) {
  e.preventDefault();
  let formRequest = new FormData(this);
  formRequest.append("id", gpt[4]);
  run(formRequest);
});

const getChecklistServices = () => {
  let formRequest = new FormData();
  formRequest.append("_action", "getChecklistServices");
  formRequest.append("payload", "view-lead");
  formRequest.append("id", gpt[4]);
  let response = run(formRequest);
  return response.responseJSON.data ?? [];
};

$("#checkListTab").on("click", function () {
  let data = getChecklistServices();
  let result = JSON.parse(data[0].checklist_services);
  let subDomain = result.domain ?? {};
  let database = result.database ?? {};
  let multiservices = result.multiservices ?? {};
  let country = result.country ?? {};
  let institute = result.institute ?? {};
  let registeredPlan = result.plan ?? {};
  let expenses = result.expenses ?? {};
  let zapier = result.zapier ?? {};
  let departments = result.users ?? {};
  let emails = result.emails ?? {};
  let whatsaap = result.whatsaap ?? {};
  let sms = result.sms ?? {};
  let click2Dial = result.click2dial ?? {};

  if (multiservices.accepted == "yes") {
    $("#services-yes").prop("checked", true);
    $("#services-yes").parent("td").next("td").removeClass("d-none");
    console.log(multiservices.visa, multiservices.ielts);
    if (multiservices.visa) {
      $("#visaService").prop("checked", true);
    }
    if (multiservices.ielts) {
      $("#ieltsService").prop("checked", true);
    }
    if (multiservices.vistor_visa) {
      $("#vistorVisaService").prop("checked", true);
    }
    if (multiservices.work_visa) {
      $("#workVisaService").prop("checked", true);
    }
    if (multiservices.pr) {
      $("#prService").prop("checked", true);
    }
  }

  checkCheckListServices(
    "#domain-yes",
    "#domain-no",
    subDomain.url,
    subDomain.accepted
  );
  checkCheckListServices(
    "#domain-yes",
    "#domain-no",
    subDomain.url,
    subDomain.accepted
  );
  checkCheckListServices(
    "#database-yes",
    "#database-no",
    database.name,
    database.accepted
  );
  checkCheckListServices(
    "#institute-yes",
    "#institute-no",
    institute.name,
    institute.accepted
  );
  checkCheckListServices(
    "#plan-yes",
    "#plan-no",
    registeredPlan.name,
    registeredPlan.accepted
  );
  checkCheckListServices(
    "#expenses-yes",
    "#expenses-no",
    expenses.name,
    expenses.accepted
  );
  checkCheckListServices(
    "#zapier-yes",
    "#zapier-no",
    zapier.name,
    zapier.accepted
  );
  checkCheckListServices(
    "#users-yes",
    "#users-no",
    departments.name,
    departments.accepted
  );
  checkCheckListServices(
    "#country-yes",
    "#country-no",
    country.name,
    country.accepted
  );
  checkCheckListServices(
    "#emails-yes",
    "#emails-no",
    emails.name,
    emails.accepted
  );
  checkCheckListServices("#sms-yes", "#sms-no", sms.name, sms.accepted);
  checkCheckListServices(
    "#whatsapp-yes",
    "#whatsapp-no",
    whatsaap.name,
    whatsaap.accepted
  );
  checkCheckListServices(
    "#click2dial-yes",
    "#click2dial-no",
    click2Dial.name,
    click2Dial.accepted
  );
});

function checkCheckListServices(yesElemId, noElemId, name, status) {
  if (status == "yes") {
    $(yesElemId).prop("checked", true);
    $(yesElemId).parent("td").next("td").find("input").val(name);
    $(yesElemId).parent("td").next("td").removeClass("d-none");
  } else {
    $(noElemId).prop("checked", true);
  }
}
