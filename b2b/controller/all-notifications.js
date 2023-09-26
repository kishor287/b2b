var param = new FormData();
param.append("_action", "get");
param.append("payload", "all-notifications");
let response = run(param);
let data = response.responseText;
data = JSON.parse(data);
let notifications = data.notifications;

let html = '';
notifications.forEach(element => {

    let tag = '';
    if(element.tag){
        tag = `<span class="badge bg-secondary py-2" style="font-size: 8px;padding: 7px 7px 7px 8px !important;">${element.tag}</span>`;
    }
    html += `<li class="list-group-item list-group-item-action dropdown-notifications-item">
    <div class="d-flex">
      <div class="flex-shrink-0 me-3">
        <div class="avatar">
          <span class="avatar-initial rounded-circle bg-label-success"><i class="bx bx-${element.icon}"></i></span>
        </div>
      </div>
      <div class="flex-grow-1">
        <h6 class="mb-1">${element.message}</h6>
        ${tag}
        <small class="text-muted">${element.created_at}</small>
      </div>
    </div>
  </li>`;
});
$('#all-notifications-group').html(html);