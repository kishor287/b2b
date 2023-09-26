get_data(1);
function get_data(page, filter) {
  limit = 1;
  l_event = "tbody";

  var param = new FormData();
  param.append("_action", "get_count");
  param.append("payload", "dashboard");
  param.append("limit", limit);
  param.append("pagination", page);
  var res = run(param);
  var json = JSON.parse(res.responseText);

  let totalLeads = json.dashboard.leads;
  let totalFollowups = json.dashboard.followups;
  let totalDoneFollowups = json.dashboard.totalDoneFollowups;
  let todayLeads = json.dashboard.todayLeads;
  let totalTodayMeetings = json.dashboard.totalMeetings;
  let monthlyAgreements = json.dashboard.monthlyAgreementsResult;
  let weeklyAgreements = json.dashboard.weeklyAgreementsResult  ;
  let yesterdayAgreements = json.dashboard.yesterdayAgreementsResult;
  let todayAgreement = json.dashboard.todayAgreementsResult;
  let rescheduledMeetings = json.dashboard.totalRescheduledMeetings;
  let totalDoneMeetings = json.dashboard.totalDoneMeetings;
  let todayTraining =  json.dashboard.todayTraining;
  let todayTrainingHtml = '';
  let totalMeetingsCount = 0;
  todayTraining.forEach((element) => {
      totalMeetingsCount = +totalMeetingsCount+ + +element.meetings;
      todayTrainingHtml += `<p class="text-primary  mb-1">${element.fname} : ${element.meetings}</p>`
  });
  $("#todayTrainingPeopleAndMeetings").html(todayTrainingHtml);
  $("#todayTrainingPeopleAndMeetingsCount").html(totalMeetingsCount);
  $('#totalLeads').text(totalLeads);
  $('#totalDoneMeetings').text(totalDoneMeetings);
  $('#totalPendingMeetings').text(totalTodayMeetings - totalDoneMeetings);
  $('#totalMeetings').text(totalTodayMeetings);
  $("#todayLeads").text(todayLeads);
  $('#totalFollowups').text(totalFollowups);
  $('#todayDoneFollowups').text(totalDoneFollowups);
  $('#todayPendingFollowups').text(totalFollowups - totalDoneFollowups);
  $("#lead_count").html(json.total_leads);
  $("#followup_count").html(json.today_followups);
  $('#todayAgreements').text(todayAgreement);
  $('#yesterdayAgreements').text(yesterdayAgreements);
  $('#weeklyAgreements').text(weeklyAgreements);
  $('#monthlyAgreements').text(monthlyAgreements);
  // $("#totalContracts")
  $('#totalRescheduledMeetings').text(rescheduledMeetings);
  // ;et todayAgreement,et
  const meetings = json.meetings;
  let meetingHtml = "";
  let count = 0;
  meetings.forEach((result) => {
    if (count % 2 === 0) {
      if (count === 0) {
        meetingHtml += '<div class="carousel-item active">';
      } else {
        meetingHtml += '</div></div><div class="carousel-item">';
      }
    }
    let fulldate = result.meeting_date;
    const [dateStr, timeStr] = fulldate.split(' ');
    
    meetingHtml += `
      <div class="col-md-6">
        <div class="card border bg-primary text-white p-2 m-2">
          <table class="tx-12">
            <tr>
              <td class="p-0 w-50">Company Name</td>
              <td class="p-0">${result.organization}</td>
            </tr>
            <tr>
              <td class="p-0 w-50">Contact</td>
              <td class="p-0">${result.phone}</td>
            </tr>
            <tr>
              <td class="p-0">Sale Manager</td>
              <td class="p-0">${result.sales_manager}</td>
            </tr>
            <tr>
              <td class="p-0">Date</td>
              <td class="p-0">${dateStr}</td>
            </tr>
            <tr>
              <td class="p-0">Time</td>
              <td class="p-0">${convertTimeTo12Hour(timeStr)}</td>
            </tr>
          </table>
        </div>
      </div>
    `;
    count++;
  });
  
    $("#upcomingMeetings").html(meetingHtml);
}

show_msg();
function show_msg() {
  var hours = new Date().getHours();
  var message;
  var morning = "Good morning";
  var afternoon = "Good afternoon";
  var evening = "Good evening";

  if (hours >= 0 && hours < 12) {
    message = morning;
  } else if (hours >= 12 && hours < 17) {
    message = afternoon;
  } else if (hours >= 17 && hours < 24) {
    message = evening;
  }

  $(".greeting").append(message);
}

function convertTimeTo12Hour(timeStr) {
  // Create a new date object with the time string
  const time = new Date(`2000-01-01T${timeStr}`);

  // Get the hours and minutes from the date object
  let hours = time.getHours();
  let minutes = time.getMinutes();

  // Determine if it's AM or PM
  const ampm = hours >= 12 ? 'PM' : 'AM';

  // Convert to 12-hour format
  hours = hours % 12;
  hours = hours ? hours : 12; // the hour '0' should be '12'

  // Add leading zeros to minutes
  minutes = minutes < 10 ? '0' + minutes : minutes;

  // Return the formatted time string
  return `${hours}:${minutes} ${ampm}`;
}


