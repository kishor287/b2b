get_data(1);

function get_data(page, filter) {
  limit = 1;
  l_event = "tbody";

  var param = new FormData();
  param.append("_action", "get_count");
  param.append("payload", "sale-manager");
  param.append("limit", limit);
  param.append("pagination", page);

  var res = run(param);
  var json = JSON.parse(res.responseText);
  $("#followup_count").html(json.today_followups);
  $("#lead_count").html(json.total_leads);
  $("#today_lead_count").html(json.today_total_leads);
  $("#today_meetings").html(json.today_meeting);
  $("#today_trainings").html(json.today_training);
  $("#today_agreements").html(json.today_agreement);
}

