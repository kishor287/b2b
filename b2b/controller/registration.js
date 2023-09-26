$.getScript(
  "https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"
);
var table_cols = [
  //  'checkbox',
  "S.no.",
  "URL",
  "Sim Form URL",
  "Qr Code",
  "Organization",
  "Logo",
  "Email",
  "Phone",
  "Website",
  "Action",
];
table_head(table_cols);

function dir() {
  var param = new FormData();
  param.append("_action", "dir");
  param.append("payload", "registration");
  var res = run(param);
  var json = JSON.parse(res.responseText);
  jHTML = "";
  jHTML += '<option value="">Sub Domian</option>';
  $.each(json.data, function (k, v) {
    jHTML += '<option value="' + v.slice(6) + '" >' + v.slice(6) + "</option>";
  });
  $("#_dir").html(jHTML);
}

/*
Save
*/
$("#organization_form").submit(function (e) {
  e.preventDefault();

  var form = $(this)[0];
  var param = new FormData(form);
  let uid = generateLongKey(15);
  const qrCodeData = generateQrCode(`https://innerxcrm.com/sim-request/${uid}`);
  param.append("_action", "save");
  param.append("payload", "registration");
  param.append("uid", uid);
  param.append("qrdata", qrCodeData);
  var res = run(param);

  var json = JSON.parse(res.responseText);
  if (json["status"] === 1) {
    $("#basicModal").modal("hide");
    $("#organization_form")[0].reset();
    toastr.success("Added", "Data Inserted Successfully.");
    get_data(1);
  } else {
    toastr.error("Error", "Internal Server Error");
  }
});

function generateLongKey(length) {
  const array = new Uint8Array(length);
  window.crypto.getRandomValues(array);
  return Array.from(array, (byte) => byte.toString(16).padStart(2, "0")).join(
    ""
  );
}

function generateQrCode(data) {
  const qrCode = new QRCode(document.getElementById("qrcode"), {
    text: data,
    width: 128,
    height: 128,
  });
  const imageData = qrCode._el.firstChild.toDataURL();
  return imageData;
}

/*

Get users

*/

get_data(1);

/*
Users function
*/

function get_data(page, filter) {
  limit = 10;
  l_event = "tbody";

  var param = new FormData();
  param.append("_action", "get");
  param.append("payload", "registration");
  param.append("limit", limit);
  param.append("pagination", page);
  var res = run(param);
  var json = JSON.parse(res.responseText);

  var jHTML = "";
  $.each(json.data, function (k, v) {
    jHTML += '<tr data-id="' + v["id"] + '" data-organization="' + v["organization"] + '" >';
    jHTML += "<td>" + (page * limit - (limit - (k + 1))) + "</td>";
    jHTML +=
    '<td><a target="_blank" href="https://' +
    v["sub_domain"] +
    '.innerxcrm.com" >' +
    v["sub_domain"] +
    ".innerxcrm.com</a></td>";
    if (v["uid"]) {
      jHTML += `<td><a href="https://innerxcrm.com/sim-request/${v['uid']}" target="_blank">https://innerxcrm.com/sim-request/${v['uid']}</a></td>`;
    } else {
      jHTML += `<td>--</td>`;
    }
    if (v["qr_code"]) {
      jHTML += `<td><a href="${v["qr_code"]}" target="_blank">View</a></td>`;
    } else {
      jHTML += `<td>--</td>`;
    }
    jHTML += "<td>" + v["organization"] + "</td>";
    jHTML +=
      '<td><div class="d-flex align-items-center"><img src="/uploads/logo/' +
      v["logo"] +
      '" class="rounded me-2" width="50" alt=""></div></td>';
    jHTML += "<td>" + v["email"] + "</td>";
    jHTML += "<td>" + v["phone"] + "</td>";
    jHTML += "<td>" + v["website"] + "</td>";
    jHTML +=
      '<td><div class="d-flex"><a title="Edit Record"  href="javascript:" class="btn btn-primary shadow btn-xs sharp me-1" data-record-id="' +
      v["id"] +
      '"><i class="bx bx-pencil"></i></a><a title="Remove Record"  href="javascript:" class="btn btn-danger shadow btn-xs sharp"><i class="bx bx-trash"></i></a></div></td>';
    jHTML += "</tr>";
  });

  $("tbody").html(jHTML);
  $(".pagination").html(json.pagination);
  dir();
}

/*
 Delete
*/

$("body").on("click", "tr .btn-danger", function () {
  var id = $(this).parent().parent().parent().attr("data-id");
  let organization = $(this).closest('tr').data('organization');
  $("#organizationName").text(`!! ${organization} !!`);
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
  param.append("payload", "registration");

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

/*
Pagination
*/

$("body").on("click", "#_pagination", function () {
  get_data($(this).attr("data-id"));
});

/*
 Edit
*/

$("body").on("click", "tr .btn-primary", function () {
  var id = $(this).parent().parent().parent().attr("data-id");
  edit_data(id);
});

function edit_data(id) {
  $("#basicModaledit").modal("show");

  var param = new FormData();
  param.append("_action", "get");
  param.append("payload", "registration");
  param.append("limit", 1);
  param.append("pagination", 1);
  var res = run(param);
  var json = JSON.parse(res.responseText);

  $("#basicModaledit #_id").val(id);
  $('#basicModaledit input[name="organization"]').val(
    json.data[0]["organization"]
  );
  $('#basicModaledit input[name="phone"]').val(json.data[0]["phone"]);
  $('#basicModaledit input[name="email"]').val(json.data[0]["email"]);
  $('#basicModaledit input[name="website"]').val(json.data[0]["website"]);
}

$("#organization_form_edit").submit(function (e) {
  e.preventDefault();

  var form = $(this)[0];
  var param = new FormData(form);
  param.append("_action", "update");
  param.append("payload", "registration");

  var res = run(param);

  var json = JSON.parse(res.responseText);
  if (json["status"] === 1) {
    toastr.success(json["data"], "Success");
    $("#basicModaledit").modal("hide");
    get_data(1);
  } else {
    toastr.error(json["data"], "Error");
  }
});

$(document).ready(function () {
  $("#applyFiltersBtn").click(function () {
    var searchTerm = $("#searchOrganization").val().trim().toLowerCase();

    $("tbody tr").each(function () {
      var organizationName = $(this)
        .find("td:nth-child(3)")
        .text()
        .toLowerCase();
      if (organizationName.includes(searchTerm)) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });
  });
});

document.getElementById("resetBtn").addEventListener("click", function () {
  location.reload();
});

// qr code functionalitiy

$(document).on("click", "#qrCodeGeneratorBtn", function () {
  let id = $("#_id").val();

  let uid = getOrganizationUid(id);
  const qrCodeData = generateQrCode(`https://innerxcrm.com/sim-request/${uid}`);
  var param = new FormData();
  param.append("_action", "generateQrCode");
  param.append("payload", "registration");
  param.append("uid", uid);
  param.append("qrdata", qrCodeData);
  var res = run(param);
  let response = res.responseJSON;
  if(response.statusCode == 200){
    toastr.success(response.message);
    return;
  }else{
    toastr.error(response.message);
    return;
  }
});

function getOrganizationUid(id){
  let form = new FormData();
  let uid = generateLongKey(15);
  form.append('_action','getUid');
  form.append('payload','registration');
  form.append('id',id);
  form.append('uid',uid);
  let res = run(form);
  let response = res.responseJSON;

  if(response.statusCode == 200){
      return response.uid;
  }else{
    toastr.error(response.message);
    return false;
  }

}