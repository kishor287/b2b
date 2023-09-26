/*

Table headings

*/

var table_cols=[
  //  'checkbox',
    'S.no.',
    'First Name',
    'Last Name',
    'Username',
    'Password',
    'Image',
    'Email',
    // 'Phone',
    'Role',
    'Action',
    'Status',
    'Created'
    ]
table_head(table_cols);

$("#_role").on('change', function() {
  var selectedOption = $(this).find('option:selected');
  var attributeValue = selectedOption.data('type');

  if (attributeValue === 2) {
    $('#selectReportingManager').hide();
    $('#selectReportingManager').find('select').removeAttr('required');
  } else {
    $('#selectReportingManager').show();
    $('#selectReportingManager').find('select').attr('required', 'true');
  }
});
/*
Saving the data into the users database...
*/
$('#user_form').submit(function(e) { 
  e.preventDefault();
  var form = $(this)[0]; 
  var param = new FormData(form);
  param.append("_action", "save"); 
  param.append("payload", "user"); 
 
  var res = run(param); 
   
  var json = JSON.parse((res.responseText));
  if(json['status']===1){
      $('#basicModal').modal('hide');
      $('#user_form')[0].reset();
      toastr.success("Added", "Data Inserted Successfully.")
      get_data(1);
      
  } else if(json['status']===0){
      toastr.error("Error", json['data'])
     
  }else{
      toastr.error("Error", "Internal Server Error")
     
  }
 
});


/*

Get users

*/

get_data(1);
roles();

function get_roles(page,limit){  
 l_event='tbody';
  var param = new FormData();
  param.append("_action", "get_roles"); 
  param.append("payload", "user"); 
  param.append("limit",limit); 
  param.append("pagination", page); 
  return  run(param); 
  
}

function get_reporting_roles(){
    let param = new FormData();
  param.append("_action", "get_reporting_roles"); 
  param.append("payload", "user"); 
  return  run(param); 
}

function roles(){
page=1;   
limit=10000; 

 var res = get_roles(page,limit); 
 let reportingRoles = get_reporting_roles();
 
 reportingRoles = JSON.parse((reportingRoles.responseText));
 var json = JSON.parse((res.responseText));
     var jHTML='';
     jHTML+='<option value="">'+ "Select role"+' </option>';
     $.each(json.data, function (k, v) {
             type = v['type'];
       jHTML+='<option data-type='+type+'  value='+v['id']+'>'+v['roles_type']+'</option>';
     })
     let roles_html = '<option value="0">Select</option>';
     reportingRoles.forEach((element) => {
        
        roles_html += `<option value="${element.id}">${element.roles_type}</option>`;
     });
        $("#_role").html(jHTML)
        $('select#reportingMan').html(roles_html);
        
        $("#user_form_edit #_role").html(jHTML)
       
        
}
/*
Get Users function
*/

function get_data(page, filter) {
  var limit = 10;
  var l_event = 'tbody';

  var param = new FormData();
  param.append("_action", "get");
  param.append("payload", "user");
  param.append("limit", limit);
  param.append("pagination", page);
  param.append("filter", JSON.stringify({ filter }));

  var res = run(param);

  var json = JSON.parse(res.responseText);

  var res2 = get_roles(1, 10000);
  var roles = JSON.parse(res2.responseText);
  var arr = [];

  // Create an array mapping role IDs to role types
  $.each(roles.data, function(k, v) {
    arr[v['id']] = v['roles_type'];
  });

  var jHTML = '';
  $.each(json.data, function(k, v) {
    let userStatus = '';
    if(v['status'] == 1){
      userStatus = 'checked';
    }
    jHTML += '<tr data-id="' + v['id'] + '" >';
    jHTML += '<td>' + ((page * limit) - (limit - (k + 1))) + '</td>';
    jHTML += '<td>' + v['fname'] + '</td>';
    jHTML += '<td>' + v['lname'] + '</td>';
    jHTML += '<td>' + v['username'] + '</td>';
    jHTML += '<td>' + v['password'] + '</td>';
    jHTML += '<td><div class="d-flex align-items-center"><img src="/uploads/images/' + v['image'] + '" class="rounded me-2" width="50" alt=""></div></td>';
    jHTML += '<td>' + v['email'] + '</td>';
    jHTML += '<td>' + arr[v['role']] + '</td>';
    jHTML += '<td><div class="d-flex"><a title="Edit Record"  href="javascript:" class="btn btn-primary shadow btn-xs sharp me-1"><i class="bx bx-pencil"></i></a><a title="Remove Record"  href="javascript:" class="btn btn-danger shadow btn-xs sharp"><i class="bx bx-trash"></i></a></div></td>';
    jHTML += '<td><div class="form-check form-switch mb-2"><input class="form-check-input status-switch" type="checkbox" id="flexSwitchCheckDefault-' + v['id'] + '" '+userStatus+'></div></td>';
    jHTML += '<td>' + v['created'] + '</td>';
    jHTML += '</tr>';
  });

  $(l_event).html(jHTML);

  $(".pagination").html(json.pagination);
}



/*
 Delete
*/

$("body").on("click","tr .btn-danger", function(){ 
 var id = $(this).parent().parent().parent().attr('data-id');  
 remove_modal(id);
})

 
 
 


$('#_delete_user_form').submit(function(e) { 
  e.preventDefault();
  
     if($('#_delete_user_form input[name="id"]').val().length===0){
        toastr.error("Something Went Wrong", "Error")
        return false
   }  
  
  var form = $(this)[0]; 
  var param = new FormData(form);
  param.append("_action", "remove"); 
  param.append("payload", "user");
  
  l_event='#_delete_form button[type="submit"]';
  l_data='Confirm'
  loader(l_event)
  var res = run(param); 
  loader(l_event,l_data)
   
  var json = JSON.parse((res.responseText));
  if(json['status']===1){
      toastr.success(json['data'], 'Success')
      $("#_delete_modal").modal('hide');
      get_data(1);
      
  }else{
      toastr.error(json['data'], 'Error')

  }
  
})

/*
Pagination
*/


$("body").on("click","#_pagination", function(){ 
get_data(($(this).attr('data-id')));
})



/*
 Edit
*/


$("body").on("click","tr .btn-primary", function(){ 
 var id = $(this).closest('tr').attr('data-id');  
 edit_data(id);  
 
})


function edit_data(id){
  
  var param = new FormData();
  param.append("_action", "get"); 
  param.append("payload", "user"); 
  param.append("limit",1); 
  param.append("pagination", 1); 
  param.append("filter",  JSON.stringify({"id":id})); 
   var res = run(param);
   var json = JSON.parse((res.responseText));
   
 
   
    $('#basicModaledit #_id').val(id)
    $('#basicModaledit input[name="first_name"]').val(json.data[0]['fname'])
    $('#basicModaledit input[name="last_name"]').val(json.data[0]['lname'])
    $('#basicModaledit input[name="user_name"]').val(json.data[0]['username'])
    $('#basicModaledit input[name="password"]').val(json.data[0]['password'])
    $('#basicModaledit input[name="phone"]').val(json.data[0]['phone'])
    $('#basicModaledit input[name="email"]').val(json.data[0]['email'])
    // $('#basicModaledit input[name="pimage"]').val(json.data[0]['image'])
    $('#basicModaledit select[name="role"]').val(json.data[0]['role'])
    $('#basicModaledit input[name="campaign"]').val(json.data[0]['campaign'])
   $('#basicModaledit input[name="meeting_link"]').val(json.data[0]['meeting_link'])
   $('#basicModaledit input[name="dob"]').val(json.data[0]['dob'])
     $("#basicModaledit").modal('show'); 
   
}


$('#user_form_edit').submit(function(e) { 
  e.preventDefault();

  
  
  var form = $(this)[0]; 
  var param = new FormData(form);
  param.append("_action", "update"); 
  param.append("payload", "user"); 

  var res = run(param); 
   
  var json = JSON.parse((res.responseText));
  if(json['status']===1){
      toastr.success(json['data'], 'Success')
       $("#basicModaledit").modal('hide'); 
      get_data(1);
  }else{
      toastr.error(json['data'], 'Error')

  }
 
});

  $("body").on("change", "input[type='checkbox']", function() {
  var col = 'status';
  var val = $(this).is(':checked') ? 1 : 0;
   var id = $(this).closest('tr').data('id');
  
  $.ajax({
    type: 'POST',
    url: 'server/user.php',
    dataType: 'json',
    data: { action: 1, col: col, val: val , id: id },
    success: function(response) {
      // Handle the response from the server if needed
       toastr.success("Updated", "Status Updated Successfully.");
      console.log('Status updated successfully');
    }
  });
});



$(document).on('submit', '#followUpFilter', function (e) {
  e.preventDefault();
  let statusFilter = $("#statusFilter").val();
  let dateRange = $('#dateRange').val();
  let searchValue = $("#searchOrganization").val();
  let filter = {
    'statusFilter': statusFilter,
    'daterange': dateRange,
    'search': searchValue,
  };
  if (statusFilter === "1") {
    filter.status = 1;
  } else if (statusFilter === "0") {
    filter.status = 0;
  }
  let page = get_pagination();
  get_data(page, filter,true);
});

$(document).on('click', '#applyFilters', function () {
  $('#followUpFilter').submit();
});


$(document).on('click', '#resetFilters', function() {
  $("#searchOrganization").val('');
  $("#statusFilter").val('')
  let page = get_pagination();
  let filter = {};
  get_data(page, filter);
});

function get_pagination() {
  if ($('.page-item.active').text() !== "") {
    return $('.page-item.active').text();
  } else {
    return 1;
  }
}