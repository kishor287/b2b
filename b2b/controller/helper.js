// Write functions which needs to be rendered on all application
function getNotifications(){
    var param = new FormData();
    param.append("_action", "getNotifications");
    param.append("payload", "notifications");
    let response = run(param);
    let data = response.responseText;
    data = JSON.parse(data);
    let notifications = data.notifications;
    let html = '';
    notifications.forEach(element => {
      let tag = '';
        if(element.tag){
          tag = `<span class="badge bg-dark py-1 px-1">${element.tag}</span>`;
        }
        html += `<li class="list-group-item list-group-item-action dropdown-notifications-item">
        <div class="d-flex">
          <div class="flex-shrink-0 me-3">
            <div class="avatar">
              <span class="avatar-initial rounded-circle bg-label-success"><i class="bx bx-${element.icon}"></i></span>
            </div>
          </div>
          <div class="flex-grow-1">
            <h6 class="mb-1"><a href="${element.url}">${element.message}</a></h6>
            <small class="text-muted">${element.created_at}</small>
            ${tag}
          </div>
        </div>
      </li>`;
    });
    if(data.totalNotifications){
      $('#totalNotifications').show();
      let notifications = data.totalNotifications;
      if(parseInt(data.totalNotifications) > 100){
          notifications = '99+';
      }
      $('#totalNotifications').text(notifications);
    }else{
      $('#totalNotifications').hide();
    }
    $('#notificationBarBell').html(html);
}

function timeSince(date) {
    let isoDateStr = new Date(date).toISOString();
    isoDateStr = new Date(isoDateStr);
    const seconds = Math.floor((new Date() - isoDateStr) / 1000);
    let interval = Math.floor(seconds / 31536000);
    if (interval > 1) {
      return interval + " years ago";
    }
    interval = Math.floor(seconds / 2592000);
    if (interval > 1) {
      return interval + " months ago";
    }
}
  
// setInterval(() => {
//   getNotifications();
// }, 15000);
getNotifications();
