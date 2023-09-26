/*
ajax
*/
// $(document).ajaxStart(function () {
//   $(".loading").removeClass("d-none");
// });

// $(document).ajaxStop(function () {
//   $(".loading").addClass("d-none");
// });

function run(param, l_event) {
  $(document).ajaxStart(function () {
    $(".loading").removeClass("d-none");
  });
  $(document).ajaxStop(function () {
    $(".loading").addClass("d-none");
  });
  var jqXHR = $.ajax("/b2b/server/", {
    beforeSend: function () {
      if (l_event) {
        loader(l_event);
      }
    },
    dataType: "json",
    data: param,
    type: "POST",
    async: false,
    timeout: 10000,
    cache: false,
    contentType: false,
    processData: false,

    success: function (response, status, xhr) {
      if (response.message) {
        toastr.success(response.message, "Success", {
          timeOut: 2e3,
          closeButton: !0,
          newestOnTop: !0,
          progressBar: !0,
          onclick: null,
          showEasing: "swing",
          hideEasing: "linear",
          showMethod: "fadeIn",
          hideMethod: "fadeOut",
          tapToDismiss: !1,
        });
        return false;
      }
    },
    error: function (jqXhr, textStatus, errorMessage) {
      let response = JSON.parse(jqXhr.responseText);
      if (response.message) {
        toastr.error(response.message, "Error", {
          timeOut: 2e3,
          closeButton: !0,
          newestOnTop: !0,
          progressBar: !0,
          onclick: null,
          showEasing: "swing",
          hideEasing: "linear",
          showMethod: "fadeIn",
          hideMethod: "fadeOut",
          tapToDismiss: !1,
        });
        return false;
      } else {
        toastr.error("Please try again later", "Internal Server Error", {
          timeOut: 2e3,
          closeButton: !0,
          newestOnTop: !0,
          progressBar: !0,
          onclick: null,
          showEasing: "swing",
          hideEasing: "linear",
          showMethod: "fadeIn",
          hideMethod: "fadeOut",
          tapToDismiss: !1,
        });
        return false;
      }
    },
  });
  return jqXHR;
}

/*
Loader
*/

function loader(event, data) {
  if (!data) {
    data = '<i class="fa fa-spinner fa-spin"></i>';
    prop = true;
  } else {
    prop = false;
  }
  $(event).html(data);
  $(event).prop("disabled", prop);
}

/*

Get roles

*/

function get_roles(page, limit) {
  l_event = "tbody";
  var param = new FormData();
  param.append("_action", "get_roles");
  param.append("payload", "user");
  param.append("limit", limit);
  param.append("pagination", page);
  return run(param);
}

/*

Get users

*/

function get_users(page, limit) {
  l_event = "tbody";
  var param = new FormData();
  param.append("_action", "get_users");
  param.append("payload", "user");
  param.append("limit", 10000);
  param.append("pagination", 1);
  var users = run(param);
  var json = JSON.parse(users.responseText);
  var user = [];
  $.each(json.data, function (k, v) {
    user[v["id"]] = v["username"];
  });
  return user;
}

/*
Profile
*/

function profile() {
  var tabs = ["/agreement", "/dashboard"];
  var param = new FormData();
  param.append("_action", "profile");
  param.append("payload", "settings");
  var res = run(param);
  var json = JSON.parse(res.responseText);
  $("#_username").html(json["data"]["fname"] + " " + json["data"]["lname"]);

  var res2 = get_roles(1, 10000);
  var roles = JSON.parse(res2.responseText);

  var arr = [];
  $.each(roles.data, function (k, v) {
    arr[v["id"]] = v["roles_type"];
  });

  $("#_usertype").html(arr[json["data"]["role"]]);
  //var rola=arr[json['data']['role']];

  if (gpt[3] == "dashboard") {
    $("#_dashboard_").load(
      "/view/includes/" + arr[json["data"]["role"]] + ".html"
    );
  }
  if (arr[json["data"]["role"]] == "Trainer") {
    $(".trainerDontHaveAccess").each(function () {
      $(this).addClass("d-none");
    });
  }

  if (arr[json["data"]["role"]] == "Admin") {
    setTimeout(function () {
      $("._admin_access").each(function () {
        $(this).removeClass("d-none");
      });
    }, 1000);
  }
}

/*
Table Head
*/
function table_head(table_cols, el) {
  var jHTML = "<tr>";
  $.each(table_cols, function (k, v) {
    if (v === "checkbox") {
      jHTML +=
        '<th style="width:50px;"><div class="form-check custom-checkbox checkbox-success check-lg me-3"><input type="checkbox" class="form-check-input" id="checkAll" required=""><label class="form-check-label" for="checkAll"></label></div></th>';
    } else {
      jHTML += "<th>" + v + "</th>";
    }
  });
  jHTML += "</tr>";
  if (!el) {
    el = "";
  }
  $(el + " thead").html(jHTML);
}

function remove_modal(id) {
  if (!id) {
    toastr.error("Something Went Wrong!", "Error");
    return false;
  }
  $("#_delete_modal form input[type='hidden']").remove();
  $("#_delete_modal form").append(
    '<input type="hidden" name="id" value="' + id + '" >'
  );
  $("#_delete_modal").modal("show");
}

function addRemarksToFintechRequests(id, service, remarks = null, status,subdomain) {
  let form = new FormData();
  form.append("_action", "addRemarks");
  form.append("payload", "fintech-service-users");
  form.append("id", id);
  form.append("service", service);
  form.append("remarks", remarks);
  form.append("status", status);
  form.append("subdomain", subdomain);
  return run(form);
}

function getRole(){
  let formRequest = new FormData();
  formRequest.append('_action','getRole');
  formRequest.append('payload','settings');

  let res = run(formRequest);
  res = JSON.parse(res.responseText)
  return res.role;
}

function exportToExcel(tableId,customName='table_data') {
  const table = document.getElementById(tableId);
  const rows = table.querySelectorAll('tr');

  // Prepare the Excel data in a format suitable for download
  let excelData = 'data:application/vnd.ms-excel;charset=utf-8,' + encodeURIComponent('<table>');

  rows.forEach(row => {
     const columns = row.querySelectorAll('td, th');
     let rowContent = '<tr>';
     columns.forEach(column => {
        rowContent += '<td>' + column.innerText + '</td>';
     });
     rowContent += '</tr>';
     excelData += encodeURIComponent(rowContent);
  });
  excelData += encodeURIComponent('</table>');
  // Trigger the file download
  const downloadLink = document.createElement('a');
  downloadLink.href = excelData;
  downloadLink.download = `${customName}.xls`;
  downloadLink.click();
}

function exportTableToExcel(tableId,customName='table_data') {
  const table = document.getElementById(tableId);
  const rows = table.querySelectorAll('tr');
  let csvData = [];

  for (let i = 0; i < rows.length; i++) {
    const row = [];
    const cols = rows[i].querySelectorAll('td, th');

    for (let j = 0; j < cols.length; j++) {
      row.push(cols[j].textContent);
    }

    csvData.push(row.join(','));
  }

  // Create a Blob object containing the CSV data
  const blob = new Blob([csvData.join('\n')], { type: 'text/csv' });

  // Create a download link and trigger the click event to download the CSV file
  const a = document.createElement('a');
  a.href = window.URL.createObjectURL(blob);
  a.download = `${customName}.csv`;
  a.style.display = 'none';
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a); 
}