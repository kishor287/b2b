var table_cols = [
  //  'checkbox',
  "S.no.",
  "Action",
  "Status",
  "Request Id",
  "Organization",
  "Student Name",
  "Phone",
  "Email",
  "Service",
  "Sim Number",
  "Sim Attachment",
  "Passport File",
  "Visa Copy",
  "Offer Letter",
  "Arrival Date",
  "CRM ID",
  "Country",
  "Passport",
  "City",
  "Address",
  "Course",
  "College",
  "Intake",
  "Father Name",
  "Created",
  "Updated",
];
table_head(table_cols);

/*
Users function
*/
get_data(1);
function get_data(page, filter) {
  limit = 10;
  l_event = "tbody";

  var param = new FormData();
  param.append("_action", "get");
  param.append("payload", "fintech-requests");
  param.append("limit", limit);
  param.append("pagination", page);
  var res = run(param);
  var json = JSON.parse(res.responseText);

  var jHTML = "";
  $.each(json.data, function (k, v) {
    let simNumber = "No";
    if (v["sim_number"]) {
      simNumber = v["sim_number"];
    }
    let simAttachment = "No";
    if (v["sim_path"]) {
      simAttachment = `<a target="_blank" href="https://innerxcrm.com/website/${v["sim_path"]}">View</a> | <a class="ms-1"  href="server/download-fintechdocs.php?file=https://innerxcrm.com/website/${v["sim_path"]}" download>Download</a>`;
    }
    let passportFile = "No";
    if (v["passport_file"]) {
      const fileArray = JSON.parse(v["passport_file"]);
      if(fileArray.length > 1){
        passportFile = '';
        fileArray.forEach(element => {
          // passportFile += `<a target="_blank"href="https://innerxcrm.com/website/${element}">View</a> <a target="_blank" href="server/download-fintechdocs.php?file=${element}" download >Download</a> <span class="me-2">|</span>`;
          passportFile += `<a target="_blank"href="https://innerxcrm.com/website/${element}">View</a> <a href="server/download-fintechdocs.php?file=https://innerxcrm.com/website/${element}" >Download</a> <span class="me-2">|</span>`;

        });
      }else{
        passportFile = `<a target="_blank" href="https://innerxcrm.com/website/${fileArray[0]}" class="me-1">View</a> | <a class="ms-1"  href="server/download-fintechdocs.php?file=https://innerxcrm.com/website/${fileArray[0]}">Download</a>`;
      }
    }
    let arrivalDate = "No";
    if (v["arrival_date"]) {
      arrivalDate = `${v["arrival_date"]}`;
    }
    let visaCopy = "No";
    if (v["visa_copy"]) {
      visaCopy = `<a target="_blank" href="https://innerxcrm.com/website/${v["visa_copy"]}" class="me-1">View</a> | <a class="ms-1"  href="server/download-fintechdocs.php?file=https://innerxcrm.com/website/${v['visa_copy']}" download>Download</a>`;
    }
    let offerLetter = "No";
    if (v["offer_letter"]) {
      offerLetter = `<a target="_blank" href="https://innerxcrm.com/website/${v["offer_letter"]}">View</a> | <a class="ms-1"  href="server/download-fintechdocs.php?file=https://innerxcrm.com/website/${v['offer_letter']}" download>Download</a>`;
    }
    jHTML +=
      '<tr data-id="' + v["id"] + '"  data-status="' + v["status"] + '" >';
    jHTML += '<td class="w60">' + (page * limit - (limit - (k + 1))) + "</td>";
    jHTML +=
      '<td class="str_short"><a title="Update Status" href="javascript:" class="btn btn-primary shadow btn-xs sharp me-1">' +
      (v["status"] === 0 ? "Mark As Done" : "Cancel") +
      "</a></td>";
    jHTML +=
      '<td class="str_short">' +
      (v["status"] === 0
        ? '<span title="Request Pending"   class="btn btn-warning shadow btn-xs sharp me-1">Pending</span>'
        : '<span title="Request Accepted" style="background-color:#386f1a;" class="btn btn-success shadow btn-xs sharp me-1">Accepted</span>') +
      "</td>";
      
      jHTML += '<td class="str_short">' + v["request_id"] + "</td>";
      jHTML += '<td class="str_short">' + v["organization"] + "</td>";
      jHTML += '<td class="str_short">' + v["sname"] + "</td>";
      jHTML +='<td class="str_short">' + v["phone"] + "<br>" + v["rphone"] + "</td>";
      jHTML += '<td class="str_short">' + v["email"] + "</td>";
    jHTML +=
      '<td class="str_short">' +
      v["services"].replace(/_/g, " ").toUpperCase() +
      "</td>";
    jHTML += `<td>${simNumber}</td>`;
    jHTML += `<td>${simAttachment}</td>`;
    jHTML += `<td>${passportFile}</td>`;
    jHTML += `<td>${visaCopy}</td>`;
    jHTML += `<td>${offerLetter}</td>`;
    jHTML += `<td>${arrivalDate}</td>`;
    jHTML +=
      '<td class="str_short">' +
      (v["crm_id"] === 0 ? "" : v["crm_id"]) +
      "</td>";
    jHTML += '<td class="str_short">' + v["country"] + "</td>";
    jHTML += '<td class="str_short">' + v["passport"] + "</td>";
    jHTML += '<td class="str_short">' + v["city"] + "</td>";
    jHTML += '<td class="str_short">' + v["address"] + "</td>";
    jHTML += '<td class="str_short">' + v["course"] + "</td>";
    jHTML += '<td class="str_short">' + v["college"] + "</td>";
    jHTML += '<td class="str_short">' + v["intake"] + "</td>";
    jHTML +=
      '<td class="str_short">' + v["fname"] + "<br>" + v["fphone"] + "</td>";
    jHTML += '<td class="str_short">' + v["created"] + "</td>";
    jHTML += '<td class="str_short">' + v["updated"] + "</td>";
    jHTML += "</tr>";
  });

  $("tbody").html(jHTML);
  $(".pagination").html(json.pagination);
}

/*
 Update
*/

$("body").on("click", "tr .btn-primary", function () {
  var id = $(this).parent().parent().attr("data-id");
  var status = $(this).parent().parent().attr("data-status");
  update_request(id, status);
});

function update_request(id, status) {
  var param = new FormData();
  param.append("_action", "update");
  param.append("id", id);
  param.append("status", status);
  param.append("payload", "fintech-requests");
  var res = run(param);
  var json = JSON.parse(res.responseText);
  if (json["status"] === 1) {
    toastr.success(json["data"], "Success");
    get_data(1);
  } else {
    toastr.error(json["data"], "Error");
  }
}

$("body").on("click", "#_pagination", function () {
  get_data($(this).attr("data-id"));
});

function exportToExcel() {
  const table = document.getElementById('fintechSIMRequestsTable');
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
  downloadLink.download = 'table_data.xls';
  downloadLink.click();
}
