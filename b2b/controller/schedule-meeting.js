
/*

Table headings

*/

let table_cols = [
   //  'checkbox',
   'Name Of Company',
   'Name Of Person',
   'Contact Number',
   'Datetime',
   'Status',
   'Location',
   'Upload',
];
table_head(table_cols);
get_data(1);
function get_data(page, filter = {}) {


   limit = 15;
   l_event = 'tbody';

   var param = new FormData();
   param.append("_action", "get");
   param.append("payload", "schedule-meeting");
   param.append("limit", limit);
   param.append("pagination", page);
   param.append("filter", JSON.stringify(filter));
   var res = run(param);

   var response = JSON.parse((res.responseText));
   let meetingHtml = '';
   let meetings = response.data.data;

   let statusHtml = '';
   meetings.forEach((element) => {
      let visitedSelected = '';
      let rescheduledSelected = '';
      let scheduledSelected = '';
      let status = element.status.toLowerCase();

      if (status == 'visited') {
         visitedSelected = 'selected';
      } else if (status == 'rescheduled') {
         rescheduledSelected = 'selected';
      } else if (status == 'scheduled') {
         scheduledSelected = 'selected';
      }

      statusHtml = `<div class="form-group">
      <select class="form-control scheduledMeetingStatus" data-fieldid=${element.id}>
         <option value="">Select</option>
         <option ${visitedSelected} value="visited">Visited</option>
         <option ${rescheduledSelected} value="rescheduled">Rescheduled</option>
         <option ${scheduledSelected} value="scheduled">Scheduled</option>
      </select>
   </div>`;
      meetingHtml += `<tr>
      <td>${element.organization}</td>
      <td>${element.name}</td>
      <td>${element.organization_phone}</td>
      <td>${element.meetingDate}</td>
      <td>
         ${statusHtml}
      </td>
      <td id="#location-${element.id}">...</td>
      <td>
      <input type="file" class="form-control" id="upload-${element.id}" data-fieldid=${element.id}>
      </td>
      <td><button type="submit" class="btn btn-primary btn-sm">Done</button></td>
   </tr>`;
   });
   if(meetings.length == 0){
      meetingHtml = '<tr><td colspan="8">No data found</td></tr>';
   }
   $('#meetingsList').html(meetingHtml);
   let salesManagerHtml = '<option value="">Select</option>';
   let salesManagers = response.marketers;
   let pagination = response.data.pagination;
   salesManagers.forEach((element) => {
      salesManagerHtml += `<option value="${element.id}">${element.fname}</option>`;
   });

   
   $('#saleManagerFilter').html(salesManagerHtml);
   $(".pagination").html(pagination)
}

// function handleDoneButtonClick(buttonElement, organization) {
//    const id = buttonElement.getAttribute('data-fieldid');
//    const locationField = document.querySelector(`#location-${id}`);

//    // Check if the location field has a value
//    if (locationField.value) {
//       // Update the status of the meeting
//       const status = 'Visited';
//       const param = new FormData();
//       param.append("_action", "update");
//       param.append("payload", "schedule-meeting");
//       param.append("id", id);
//       param.append("status", status);
//       param.append("location", locationField.value);
//       const res = run(param);

//       // Check if the status was updated successfully
//       if (res.status === 200) {
//          // Update the table
//          get_data(1);
//       } else {
//          console.log("Error updating status");
//       }
//    } else {
//       console.log("Please enter the location");
//    }
// }

function filters(daterange = true, search = true, salemanager = true, status = true) {
   let filters = {};
   if (daterange) {
      filters.daterange = $('#dateRange').val();
   }
   if (search) {
      filters.search = $('#searchOrganization').val();
   }
   if (salemanager) {
      filters.salemanager = $('#saleManagerFilter').val();
   }
   if (status) {
      filters.status = $('#statusFilter').val();
   }
   return filters;
}

$("#meetingFilters").on('submit', function (e) {
   e.preventDefault();
   get_data(1, filters(false, true, true, true));
});
$('#resetFilters').on('click', function () {
   console.log('working reset filters');
   $('#meetingFilters')[0].reset();
   get_data(1, filters(false, false, false, false));
});


$("body").on("click", "#_pagination", function () {
   get_data(($(this).attr('data-id')), filters(false, true, true, true));
});
