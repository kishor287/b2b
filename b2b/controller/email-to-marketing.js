get_data(1);

function get_data(page, filter) {
  limit = 1;
  l_event = "tbody";

  var param = new FormData();
  param.append("_action", "email");
  param.append("payload", "email-to-marketing");
  param.append("limit", limit);
  param.append("pagination", page);

  var res = run(param);
  var json = JSON.parse(res.responseText);
//   $("#followup_count").html(json.today_followups);

}
