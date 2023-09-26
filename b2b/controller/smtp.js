/*

Table headings

*/

var table_cols=[
  //  'checkbox',
    'S.no.',
    'Organization ',
    'Primary Email',
    'Secondary Email',
    'Action'
    ]
table_head(table_cols);


get_data(1);



/*
Subdomain
*/


function dir(){
  var param = new FormData();
  param.append("_action", "dir"); 
  param.append("payload", "smtp"); 
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
Save
*/
$('#organization_form').submit(function(e) { 
  e.preventDefault();
  
  var form = $(this)[0]; 
  var param = new FormData(form);
  param.append("_action", "save"); 
  param.append("payload", "smtp"); 
 
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

 
/*
Users function
*/

function get_data(page,filter){ 

limit=10;    
l_event='tbody';

  var param = new FormData();
  param.append("_action", "get"); 
  param.append("payload", "smtp"); 
 param.append("limit",limit); 
 param.append("pagination", page); 
  var res = run(param); 
  var json = JSON.parse((res.responseText));

     var jHTML='';
     $.each(json.data, function (k, v) {
      jHTML+='<tr data-id="'+v['id']+'" >';
         jHTML+='<td class="w60">'+((page*limit)-(limit-(k+1)))+'</td>';
          jHTML+='<td>'+v['organization']+'</td>';
           jHTML+='<td class="str_short">'+v['p_email']+'<br>('+v['p_host']+') <br><a title="Test Primary SMTP" id="_p_test" href="javascript:" class="btn btn-warning shadow btn-xs sharp me-1">Test Connection</a></td>';
           jHTML+='<td class="str_short">'+v['s_email']+'<br>('+v['s_host']+') <br><a title="Test Secondary SMTP" id="_s_test" href="javascript:" class="btn btn-warning shadow btn-xs sharp me-1">Test Connecion</a></td>';
              jHTML+='<td><div class="d-flex"><a title="Edit Record"  href="javascript:" class="btn btn-primary shadow btn-xs sharp me-1"><i class="bx bx-pencil"></i></a><a title="Remove Record"  href="javascript:" class="btn btn-danger shadow btn-xs sharp"><i class="bx bx-trash"></i></a></div></td>';
                  jHTML+='</tr>';
     })
     
  
          $("tbody").html(jHTML)
          $(".pagination").html(json.pagination)
        dir();
          
      
}


/*
 Test Connection
*/

$("body").on("click","#_p_test", function(){ 
 var id = $(this).parent().parent().attr('data-id');  
 test_connection(id,'p_test'); 
})

$("body").on("click","#_s_test", function(){ 
 var id = $(this).parent().parent().attr('data-id');  
 test_connection(id,'s_test'); 
})

function  loader(type ,param){
      $("#_"+type).html('<span class="spinner-border spinner-border-sm mt-1 text-primary" role="status"><span class="visually-hidden">Loading...</span></span>'); 
    return run(param)
} 

 function  test_connection(id,type){
  var param = new FormData();
  param.append("_action", "test_connection"); 
  param.append("payload", "smtp");
  param.append("id", id);
  param.append("type", type);  

   var res =loader(type,param);

  var json = JSON.parse((res.responseText));

  if(json['status']===1){
      toastr.success(json['data'], 'Success')
     $("#_"+type).html('Test Connecion');
  
     
  }else{
      toastr.error(json['data'], 'Error')
    $("#_"+type).html('Test Connecion');
  }
  
     
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
  param.append("payload", "smtp");
  
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
  param.append("payload", "smtp"); 
  param.append("limit",1); 
  param.append("pagination", 1); 
 param.append("filter",  JSON.stringify({"id":id})); 
   var res = run(param);
   var json = JSON.parse((res.responseText));
     
    $('#basicModaledit #_id').val(id)
    $('#basicModaledit input[name="p_host"]').val(json.data[0]['p_host'])
    $('#basicModaledit input[name="p_email"]').val(json.data[0]['p_email'])
    $('#basicModaledit input[name="p_pass"]').val(json.data[0]['p_pass'])
    $('#ptype').val(json.data[0]['p_type'])
    $('#basicModaledit input[name="p_port"]').val(json.data[0]['p_port'])
    
    $('#basicModaledit input[name="s_host"]').val(json.data[0]['s_host'])
    $('#basicModaledit input[name="s_email"]').val(json.data[0]['s_email'])
    $('#basicModaledit input[name="s_pass"]').val(json.data[0]['s_pass'])
    $('#stype').val(json.data[0]['s_type'])
    $('#basicModaledit input[name="s_port"]').val(json.data[0]['s_port'])

}


$('#organization_form_edit').submit(function(e) { 
  e.preventDefault();

  
  
  var form = $(this)[0]; 
  var param = new FormData(form);
  param.append("_action", "update"); 
  param.append("payload", "smtp"); 

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


