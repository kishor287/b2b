/*

Table headings

*/

var table_cols=[
  //  'checkbox',
    'S.no.',
    'Organization',
    'SMS API',
    'Dialer API',
    'Click2Dial API',
    'Whatsapp API',
    'Action'
    ]
table_head(table_cols);

 


/*
Subdomain
*/


function dir(){
  var param = new FormData();
  param.append("_action", "dir"); 
  param.append("payload", "apis"); 
  var res = run(param); 
  var json = JSON.parse((res.responseText));
     jHTML=''
     jHTML+='<option value="">ORGANIZATION</option>'
   $.each(json.data, function (k, v) { 
        jHTML+='<option value="'+v+'" >'+v+'</option>'
   })
      $("#_dir").html(jHTML); 
}

/*
Api Copy
*/
$("body").on("click","#_apicopy", function(){ 
var content = $(this).parent().text();
content = content.substr(content.indexOf(": ") + 1)
copyToClipboard(content);
})


function copyToClipboard(text) {
  navigator.clipboard.writeText(text)
.then(() => {
toastr.success("", "API Copied.")
})
.catch((error) => {
  console.error(`Could not copy text: ${error}`);
});
}

/*
Save
*/
$('#organization_form').submit(function(e) { 
  e.preventDefault();
  
  var form = $(this)[0]; 
  var param = new FormData(form);
  param.append("_action", "save"); 
  param.append("payload", "apis"); 
 
  var res = run(param); 
   
  var json = JSON.parse((res.responseText));
  if(json['status']===1){
      $('#basicModal').modal('hide');
      $('#organization_form')[0].reset();
      toastr.success("Added", "Data Inserted Successfully.")
      get_data(1);
  }else{
      toastr.error("Error", "Internal Server Error")
     
  }
 
});




/*

Get users

*/

get_data(1);


/*
Users function
*/

function get_data(page,filter){ 

limit=10;    
l_event='tbody';

  var param = new FormData();
  param.append("_action", "get"); 
  param.append("payload", "apis"); 
 param.append("limit",limit); 
 param.append("pagination", page); 
  var res = run(param); 
  var json = JSON.parse((res.responseText));

     var jHTML='';
     $.each(json.data, function (k, v) {
      jHTML+='<tr data-id="'+v['id']+'" >';
         jHTML+='<td class="w60">'+((page*limit)-(limit-(k+1)))+'</td>';
          jHTML+='<td>'+v['organization']+'</td>';
           jHTML+='<td class="str_short">'+v['sms_api']+'</td>';
              jHTML+='<td class="str_short">'+v['dialer_api']+'</td>';
               jHTML+='<td class="str_short">'+v['click2dial_api']+'</td>';
               jHTML+='<td class="str_short">'+v['w_channel_id']+'</td>';
              jHTML+='<td><div class="d-flex"><a title="Edit Record"  href="javascript:" class="btn btn-primary shadow btn-xs sharp me-1"><i class="bx bx-pencil"></i></a><a title="Remove Record"  href="javascript:" class="btn btn-danger shadow btn-xs sharp"><i class="bx bx-trash"></i></a></div></td>';
                  jHTML+='</tr>';
     })
     
  
          $("tbody").html(jHTML)
          $(".pagination").html(json.pagination)
        dir();
          
      
}





/*
 Delete
*/

$("body").on("click","tr .btn-danger", function(){ 
 var id = $(this).parent().parent().parent().attr('data-id');  
 remove_modal(id);
})

 
 
 


$('#_delete_form').submit(function(e) { 
  e.preventDefault();
  
     if($('#_delete_form input[name="id"]').val().length===0){
        toastr.error("Something Went Wrong", "Error")
        return false
   }  
  
  var form = $(this)[0]; 
  var param = new FormData(form);
  param.append("_action", "remove"); 
  param.append("payload", "apis");
  
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
 var id = $(this).parent().parent().parent().attr('data-id');  
 edit_data(id);  
})


function edit_data(id){
   $("#basicModaledit").modal('show'); 
   
  var param = new FormData();
  param.append("_action", "get"); 
  param.append("payload", "apis"); 
  param.append("limit",1); 
  param.append("pagination", 1); 
 param.append("filter",  JSON.stringify({"id":id})); 
   var res = run(param);
   var json = JSON.parse((res.responseText));
     
    $('#basicModaledit #_id').val(id)
    $('#basicModaledit input[name="sms_api"]').val(json.data[0]['sms_api'])
    $('#basicModaledit input[name="dialer_api"]').val(json.data[0]['dialer_api'])
    $('#basicModaledit input[name="click2dial_api"]').val(json.data[0]['click2dial_api'])
    $('#basicModaledit input[name="w_channel_id"]').val(json.data[0]['w_channel_id'])
    $('#basicModaledit input[name="w_api"]').val(json.data[0]['w_api'])
    $('#basicModaledit input[name="w_secret"]').val(json.data[0]['w_secret'])
}


$('#organization_form_edit').submit(function(e) { 
  e.preventDefault();

  
  
  var form = $(this)[0]; 
  var param = new FormData(form);
  param.append("_action", "update"); 
  param.append("payload", "apis"); 

  var res = run(param); 
   
  var json = JSON.parse((res.responseText));
  if(json['status']===1){
      toastr.success(json['data'], 'Success')
       $("#basicModaledit").modal('hide'); 
      get_data(1)
  }else{
      toastr.error(json['data'], 'Error')

  }
 
});


