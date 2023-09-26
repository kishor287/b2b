/*

Table headings

*/

var table_cols=[
  //  'checkbox',
    'S.no.',
    'Role Type',
    'Action'
    ]
table_head(table_cols);

// $('#roleType').on('change',function(){
//   if($(this).val() == 2){
//       $('#selectPagesForRole').hide();
//       $('#_nav_form').hide();
//       $('#selectPagesForRole').find('select').removeAttr('required');
//   }else{
//       $('#_nav_form').show();
//       $('#selectPagesForRole').show();
//       $('#selectPagesForRole').find('select').attr('required','true');
//   }
// });


// Displaying sub-menu while adding a new role

$("body").on("change","#_navbar", function(e){ 
var id = $(this).val();
get_navbar( 1,id);
})


/*

get navbar

*/



/*
Saving the data into the users database...
*/
$('#user_role_form').submit(function(e) { 
  e.preventDefault();
  
  var form = $(this)[0]; 
  var param = new FormData(form);
  param.append("_action", "save"); 
  param.append("payload", "roles"); 
 
  var res = run(param); 
   
  var json = JSON.parse((res.responseText));
  if(json['status']===1){
      $('#basicModal').modal('hide');
      $('#user_role_form')[0].reset();
      toastr.success("Added", "Data Inserted Successfully.")
      get_data(1);
  }else{
      toastr.error("Error", "Internal Server Error")
     
  }
 
});


/*

get navbar

*/

get_navbar(1);



function get_navbar(page,id){ 

limit=1000;    
l_event='tbody';

  var param = new FormData();
  param.append("_action", "getnav"); 
  param.append("payload", "roles"); 
 param.append("limit",limit); 
 param.append("pagination", page); 
 param.append("filter",  JSON.stringify({"id":id})); 
  var res = run(param); 
  var json = JSON.parse((res.responseText));

     if(!id){
         
         var jHTML='';
        $.each(json.data, function (k, v) {
            jHTML+='<option value='+v['id']+'>'+v['title']+'</option>';
        })
        $("#_navbar").html(jHTML);
        $("#_navbar_").html(jHTML);
     }else{ 
          var jHTML='';
          
             $.each(json.data, function (k, v) {
           
         
              if(v['sub_title']===null){ 
                  jHTML+='<p class="form-label d-block ">   <i class=" bx bx-check-square text-primary"></i> '+v['title']+'</p>';
                 return true;
               }
              
                  jHTML+='<div class="form-check form-check-inline"><input name="navbar[]" class="form-check-input" type="checkbox" id="'+v['sub_title']+'" value="'+v['id']+'"><label class="form-check-label" for="'+v['sub_title']+'">'+v['sub_title']+'</label></div>';
              
               
        })
       
     $("#_nav_form").html(jHTML);
      $("#_nav_form_").html(jHTML);
    
      
}
}


/*

Get users roles

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
  param.append("payload", "roles"); 
 param.append("limit",limit); 
 param.append("pagination", page); 
  var res = run(param); 
  var json = JSON.parse((res.responseText));

     var jHTML='';
     $.each(json.data, function (k, v) {
      jHTML+='<tr data-id="'+v['id']+'" >';
         jHTML+='<td>'+((page*limit)-(limit-(k+1)))+'</td>';
          jHTML+='<td>'+v['roles_type']+'</td>';
              jHTML+='<td><div class="d-flex"><a title="Edit Record"  href="javascript:" class="btn btn-primary shadow btn-xs sharp me-1" data-type="'+v['type']+'"><i class="bx bx-pencil"></i></a><a title="Remove Record"  href="javascript:" class="btn btn-danger shadow btn-xs sharp"><i class="bx bx-trash"></i></a></div></td>';
              jHTML+='</tr>';
     })
     
  
          $("tbody").html(jHTML)
          $(".pagination").html(json.pagination)
        // dir();
          
      
}

/*
 Delete
*/

$("body").on("click","tr .btn-danger", function(){ 
 var id = $(this).parent().parent().parent().attr('data-id');  
 remove_modal(id);
})

 
 
 $('#_delete_roles_form').submit(function(e) { 
  e.preventDefault();
  
     if($('#_delete_roles_form input[name="id"]').val().length===0){
        toastr.error("Something Went Wrong", "Error")
        return false
   }  
  
  var form = $(this)[0]; 
  var param = new FormData(form);
  param.append("_action", "remove"); 
  param.append("payload", "roles");
  
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
 edit_data(id,$(this).attr('data-type'));  
})

// function edit_nav(id){
// var param = new FormData();
//   param.append("_action", "get"); 
//   param.append("payload", "roles"); 
//   param.append("limit",1); 
//   param.append("pagination", 1); 
//  param.append("filter",  JSON.stringify({"id":id})); 
//   var res = run(param);
//   var json = JSON.parse((res.responseText));
    
// }



function edit_data(id,type = null){
   $("#basicModaledit").modal('show'); 
   $('#user_role_form_edit').attr('data-editingtype',type);
  var param = new FormData();
  param.append("_action", "get"); 
  param.append("payload", "roles"); 
  param.append("limit",1); 
  param.append("pagination", 1); 
 param.append("filter",  JSON.stringify({"id":id})); 
   var res = run(param);
   var json = JSON.parse((res.responseText));
     
    $('#basicModaledit #_id').val(id)
    $('#basicModaledit input[name="assigned_role"]').val(json.data[0]['roles_type'])
    // if(type && type == 2){
    // $('#pagePermissionSelectionEdit').hide();
    //  $('#pagePermissionSelectionEdit').find('select').removeAttr('required');
    // }else{
    // $('#pagePermissionSelectionEdit').show();    
    // $('#pagePermissionSelectionEdit').find('select').attr('required','true');
    // }
    
}


$('#user_role_form_edit').submit(function(e) {
  e.preventDefault();

  
  
  var form = $(this)[0]; 
  var param = new FormData(form);
  let editingtype = $(this).attr('data-editingtype');
  param.append("_action", "update"); 
  param.append("payload", "roles"); 
  param.append("type", editingtype); 
 
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



