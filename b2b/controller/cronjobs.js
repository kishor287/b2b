/*

Table headings

*/

var table_cols=[
  //  'checkbox',
    'S.no.',
    'Organization',
    'Reports',
    'Time Period',
    'Action'
    ]
table_head(table_cols);



/*
Subdomain
*/


function dir(){
  var param = new FormData();
  param.append("_action", "dir"); 
  param.append("payload", "cronjobs"); 
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
Reports
*/

reports();
function reports(){
  var param = new FormData();
  param.append("_action", "reports"); 
  param.append("payload", "cronjobs"); 
  var res = run(param); 
  var json = JSON.parse((res.responseText));
     jHTML=''

   $.each(json.data, function (k, v) { 
        jHTML+='<option value="'+v['report']+'" >'+(k+1)+'. '+v['report']+'</option>'
   })
      $("#_reports").html(jHTML); 
      $("#organization_form_edit #_reports").html(jHTML); 
}


/*
Save
*/
$('#organization_form').submit(function(e) { 
  e.preventDefault();
  
  var form = $(this)[0]; 
  var param = new FormData(form);
  param.append("_action", "save"); 
  param.append("payload", "cronjobs"); 
 
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
get_data(1)
function get_data(page,filter){ 

limit=10;    
l_event='tbody';

  var param = new FormData();
  param.append("_action", "get"); 
  param.append("payload", "cronjobs"); 
 param.append("limit",limit); 
 param.append("pagination", page); 
  var res = run(param); 
  var json = JSON.parse((res.responseText));

     var jHTML='';
     $.each(json.data, function (k, v) {
         var is_set = (v['is_set']==='0')?'Set Time':'Update Time';
         var clas = (v['is_set']==='0')?'warning':'success';
      jHTML+='<tr data-id="'+v['organization']+'" >';
      jHTML+='<td class="w60">'+((page*limit)-(limit-(k+1)))+'</td>';
      jHTML+='<td class="str_short">'+v['organization']+'</td>';
      jHTML+='<td class="str_short">'+v['report']+' Report(s)</td>';
      jHTML+='<td ><a title="Update Time Period" id="_sett" href="javascript:" class="btn btn-'+clas+' shadow btn-xs sharp me-1">'+is_set+'</a></td>';
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
  param.append("payload", "cronjobs");
  
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
 reports();
 edit_data(id);  
})


function edit_data(id){
   $("#basicModaledit").modal('show'); 
   
  var param = new FormData();
  param.append("_action", "get"); 
  param.append("payload", "cronjobs"); 
  param.append("limit",10000); 
  param.append("pagination", 1); 
 param.append("filter",  JSON.stringify({"id":id})); 
   var res = run(param);
   var json = JSON.parse((res.responseText));
    $("#organization_form_edit #_id").val(id)
  $.each(json.data, function (k, v) {
    $("#organization_form_edit select option[value='"+v['report']+"']").attr('selected','selected')
  
})
}


$('#organization_form_edit').submit(function(e) { 
  e.preventDefault();

  
  
  var form = $(this)[0]; 
  var param = new FormData(form);
  param.append("_action", "update"); 
  param.append("payload", "cronjobs"); 

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



/*
 set
*/

function get_period(id){
  var param = new FormData();
  param.append("_action", "get_period"); 
  param.append("payload", "cronjobs"); 
  param.append("limit",10000); 
  param.append("pagination", 1); 
  param.append("id",  id); 
   return res = run(param);
 
}

$("body").on("click","#_sett", function(){ 
 var id = $(this).parent().parent().attr('data-id');
 set_data(id);  
})


function set_data(id){
   $("#basicModalset").modal('show'); 
   
  var param = new FormData();
  param.append("_action", "get"); 
  param.append("payload", "cronjobs"); 
  param.append("limit",10000); 
  param.append("pagination", 1); 
 param.append("filter",  JSON.stringify({"id":id})); 
   var res = run(param);
   var json = JSON.parse((res.responseText));
    $("#organization_form_set #_id").val(id)
 
       
 var jHTML='';
     $.each(json.data, function (k, v) {
         //sel1=(v['period']==select[v['report']]['period'])?'selected':'';
         
      jHTML+='<div class="col-12 mb-0"><label for="dobBasic" class="form-label">'+v['report']+'</label><div class="input-group"><select  name="period['+v['report']+'][]" required="" class="form-select"><option value="">Select Period</option><option value="Daily">Daily</option><option value="Weekly">Weekly (Sat)</option><option value="Semi-Monthly">Semi-Monthy (1st,15th)</option><option value="Monthly">Monthy (30th)</option><option value="Sem-Yearly">Sem-Yearly (Jan,Jun)</option><option value="Yearly">Yearly (Dec)</option></select><select name="time['+v['report']+'][]" required="" class="form-select">'+hours()+'</select></div></div>';
     })
    
   $("#organization_form_set .modal-body").html(jHTML)
}


$('#organization_form_set').submit(function(e) { 
  e.preventDefault();
  var form = $(this)[0]; 
  var param = new FormData(form);
  param.append("_action", "set"); 
  param.append("payload", "cronjobs"); 
   param.append("limit",10000); 
  param.append("pagination", 1); 
  var res = run(param); 
   
  var json = JSON.parse((res.responseText));
  if(json['status']===1){
      toastr.success(json['data'], 'Success')
       $("#basicModalset").modal('hide'); 
      get_data(1)
  }else{
      toastr.error(json['data'], 'Error')

  }
 
});


function hours(sel2){
    
     var jHTML='<option value="">Select Time</option>';
     for(var i=1;i<=24;i++) {
     //   var  sel2 = (i==select[v['report']]['time'])?'selected':'';
      jHTML+='<option '+sel2+' value="'+i+'">'+i+':00</option>';
     }
 return jHTML;
}

