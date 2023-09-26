/*

Table headings

*/


var table_cols=[
  //  'checkbox',
    'S.no.',
    'Organization',
    'Services',
    'Action'
    ]
table_head(table_cols);



/*
organisation
*/
dir();

function dir(){
  var param = new FormData();
  param.append("_action", "dir"); 
  param.append("payload", "fintex-services"); 
  var res = run(param); 
  var json = JSON.parse((res.responseText));
     jHTML=''
     jHTML+='<option value="">ORGANIZATION</option>'
   $.each(json.data, function (k, v) { 
       
        jHTML+='<option value="'+v+'" >'+v+'</option>'
   })
      $("#_dir").html(jHTML); 
      $("#services_form_edit #_dirr").html(jHTML);
}

/*
Dynamic_form
*/

$("body").on("change","#_country", function(){ 
 d_form($(this).val()); 
})

function d_form(countries){
    var dhtml="";
       $.each(countries, function (k, v) { 
   dhtml+='<div class="col-md-12 mt-3"><label class="form-label d-block">'+v+'</label><div class="form-check form-check-inline"><input name="services['+v+'][]" class="form-check-input" type="checkbox" id="loan'+k+'" value="loan"><label class="form-check-label" for="loan'+k+'">Loan</label></div><div class="form-check form-check-inline"><input name="services['+v+'][]" class="form-check-input" type="checkbox" id="credit-card'+k+'" value="credit-card"><label class="form-check-label" for="credit-card'+k+'">Credit Card</label></div><div class="form-check form-check-inline"><input name="services['+v+'][]" class="form-check-input" type="checkbox" id="insurance'+k+'" value="insurance"><label class="form-check-label" for="insurance'+k+'">Insurance</label></div><div class="form-check form-check-inline"><input name="services['+v+'][]" class="form-check-input" type="checkbox" id="sim'+k+'" value="sim"><label class="form-check-label" for="sim'+k+'">Sim-Card</label></div><div class="form-check form-check-inline"><input name="services['+v+'][]" class="form-check-input" type="checkbox" id="forex'+k+'" value="forex"><label class="form-check-label" for="forex'+k+'">Forex</label></div><div class="form-check form-check-inline"><input name="services['+v+'][]" class="form-check-input" type="checkbox" id="gic'+k+'" value="gic"><label class="form-check-label" for="gic'+k+'">GIC</label></div></div>';
       })
       $("#_d_form").html(dhtml);
   
       
}

$("body").on("click","#basicModaledit  #_country option", function(){ 
 dn_form($(this).val()); 
 
 
})

function dn_form(v){
    var arr =($("#basicModaledit #_country").val());  
    var dhtml="";
   var k=v
   dhtml+='<div  data-id="'+k+'" class="col-md-12 mt-3 c_chckk"><label  class="form-label d-block">'+v+'</label><div class="form-check form-check-inline"><input name="services['+v+'][]" class="form-check-input" type="checkbox" id="loan'+k+'" value="loan"><label class="form-check-label" for="loan'+k+'">Loan</label></div><div class="form-check form-check-inline"><input name="services['+v+'][]" class="form-check-input" type="checkbox" id="credit-card'+k+'" value="credit-card"><label class="form-check-label" for="credit-card'+k+'">Credit Card</label></div><div class="form-check form-check-inline"><input name="services['+v+'][]" class="form-check-input" type="checkbox" id="insurance'+k+'" value="insurance"><label class="form-check-label" for="insurance'+k+'">Insurance</label></div><div class="form-check form-check-inline"><input name="services['+v+'][]" class="form-check-input" type="checkbox" id="sim'+k+'" value="sim"><label class="form-check-label" for="sim'+k+'">Sim-Card</label></div><div class="form-check form-check-inline"><input name="services['+v+'][]" class="form-check-input" type="checkbox" id="forex'+k+'" value="forex"><label class="form-check-label" for="forex'+k+'">Forex</label></div><div class="form-check form-check-inline"><input name="services['+v+'][]" class="form-check-input" type="checkbox" id="gic'+k+'" value="gic"><label class="form-check-label" for="gic'+k+'">GIC</label></div></div>';
      
    
       $("#services_form_edit #__dform").append(dhtml);
       
       $(".c_chckk").each(function() {
          var thiss= $(this);
        var c= $(this).attr('data-id'); 
          if(jQuery.inArray(c, arr) !== -1){
              
          }else{
              thiss.remove();
          }
      });
 
       
}


/*
Save
*/
$('#services_form').submit(function(e) { 
  e.preventDefault();
  
  var form = $(this)[0]; 
  var param = new FormData(form);
  param.append("_action", "save"); 
  param.append("payload", "fintex-services"); 
 
  var res = run(param); 
   
  var json = JSON.parse((res.responseText));
  if(json['status']===1){
      $('#basicModal').modal('hide');
      $('#services_form')[0].reset();
      toastr.success("Added", "Data Inserted Successfully.")
      get_data(1);
  }else{
      toastr.error("Error", "Internal Server Error")
     
  }
 
});

/*
Users function
*/
get_data(1)
function get_data(page,filter){ 

limit=10;    
l_event='tbody';

  var param = new FormData();
  param.append("_action", "get"); 
  param.append("payload", "fintex-services"); 
  param.append("limit",limit); 
  param.append("pagination", page); 
  var res = run(param); 
  var json = JSON.parse((res.responseText));

     var jHTML='';
     $.each(json.data, function (k, v) {
       
      jHTML+='<tr data-id="'+v['organization']+'" >';
      jHTML+='<td class="w60">'+((page*limit)-(limit-(k+1)))+'</td>';
      jHTML+='<td class="str_short">'+v['organization']+'</td>';
      jHTML+='<td class="str_short"><a class="btn btn-secondary" id="_get_detail" href="javascript:">'+v['count']+' Service(s)</a></td>';
      
      jHTML+='<td><div class="d-flex"><a title="Edit Record"  href="javascript:" class="btn btn-primary shadow btn-xs sharp me-1"><i class="bx bx-pencil"></i></a><a title="Remove Record"  href="javascript:" class="btn btn-danger shadow btn-xs sharp"><i class="bx bx-trash"></i></a></div></td>';
      jHTML+='</tr>';
     })
     
  
          $("tbody").html(jHTML)
          $(".pagination").html(json.pagination)
        dir();
          
      
}


$("body").on("click","#_get_detail", function(){ 
 var id = $(this).parent().parent().attr('data-id');
 show_country_data(id);  
})


function show_country_data(id){
   $("#basicModaldata").modal('show'); 
   
  var param = new FormData();
  param.append("_action", "get"); 
  param.append("payload", "fintex-services"); 
  param.append("limit",10000); 
  param.append("pagination", 1); 
 param.append("filter",  JSON.stringify({"id":id})); 
   var res = run(param);
   var json = JSON.parse((res.responseText));
   
    $("#services_data #_id").val(id)
 
       
    var jHTML='';
     $.each(json.data, function (k, v) {
         $("#_country_name").html(v['organization'])
    var service =((v['loan']===1)?"<span class='badge bg-primary'>Loan</span>":"")+' '+((v['credit_card']===1)?"<span class='badge bg-primary'>Credit Card</span>":"")+' '+((v['insurance']===1)?"<span class='badge bg-primary'>Insurance</span>":"")+' '+((v['sim']===1)?"<span class='badge bg-primary'>Sim Card</span>":"")+' '+((v['forex']===1)?"<span class='badge bg-primary'>forex</span>":"")+' '+((v['gic']===1)?"<span class='badge bg-primary'>gic</span>":"");

   
      jHTML+='<tr class="mb-2"><td style="width: 100px;">'+v['country']+'</td>';
      jHTML+='<td>'+service+'</td></tr>';


  
     })
     
   $("#services_data .modal-body").html(jHTML)
}

$('#services_data').submit(function(e) { 
  e.preventDefault();
  var form = $(this)[0]; 
  var param = new FormData(form);
  param.append("_action", "set"); 
  param.append("payload", "fintex-services"); 
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


/*
 Edit
*/


$("body").on("click","tr .btn-primary", function(){ 
 var id = $(this).parent().parent().parent().attr('data-id');

 edit_data(id);  
})


function edit_data(id){
   $("#basicModaledit").modal('show'); 
   $("#services_form_edit")[0].reset(); 
  var param = new FormData();
  param.append("_action", "get"); 
  param.append("payload", "fintex-services"); 
  param.append("limit",10000); 
  param.append("pagination", 1); 
 param.append("filter",  JSON.stringify({"id":id})); 
 
   var res = run(param);
   var json = JSON.parse((res.responseText));
   var dhtml="";
    // $("#services_form_edit #_id").val(v['organization'])
    $.each(json.data, function (k, v) { 
        $("#_org_name").html(v['organization'])
        $("#services_form_edit #_id").val(v['organization'])
         var loan= (v['loan']===0)?'':'checked';
         var card= (v['credit_card']===0)?'':'checked';
         var insurance= (v['insurance']===0)?'':'checked';
         var sim= (v['sim']===0)?'':'checked';
         var gic= (v['gic']===0)?'':'checked';
         var forex= (v['forex']===0)?'':'checked';
      
        $("#services_form_edit  select option[value='"+v['organization']+"']").prop('selected',true)
        $("#services_form_edit  select option[value='"+v['country']+"']").prop('selected',true)
       dhtml+='<div data-id="'+v['country']+'" class="col-md-12 mt-3 c_chckk"><label class="form-label d-block">'+v['country']+'</label><div class="form-check form-check-inline"><input '+loan+' name="services['+v['country']+'][]" class="form-check-input" type="checkbox" id="loan'+k+'" value="loan"><label class="form-check-label" for="loan'+k+'">Loan</label></div><div class="form-check form-check-inline"><input '+card+' name="services['+v['country']+'][]" class="form-check-input" type="checkbox" id="credit-card'+k+'" value="credit-card"><label class="form-check-label" for="credit-card'+k+'">Credit Card</label></div><div class="form-check form-check-inline"><input '+insurance+'  name="services['+v['country']+'][]" class="form-check-input" type="checkbox" id="insurance'+k+'" value="insurance"><label class="form-check-label" for="insurance'+k+'">Insurance</label></div><div class="form-check form-check-inline"><input '+sim+' name="services['+v['country']+'][]" class="form-check-input" type="checkbox" id="sim'+k+'" value="sim"><label class="form-check-label" for="sim'+k+'">Sim-Card</label></div><div class="form-check form-check-inline"><input '+gic+' name="services['+v['country']+'][]" class="form-check-input" type="checkbox" id="gic'+k+'" value="gic"><label class="form-check-label" for="gic'+k+'">GIC</label></div><div class="form-check form-check-inline"><input '+forex+' name="services['+v['country']+'][]" class="form-check-input" type="checkbox" id="forex'+k+'" value="forex"><label class="form-check-label" for="forex'+k+'">Forex</label></div></div>';
        
    })
    
     $("#services_form_edit #__dform").html(dhtml)

}


$('#services_form_edit').submit(function(e) { 
  e.preventDefault();

  
  
  var form = $(this)[0]; 
  var param = new FormData(form);
  param.append("_action", "update"); 
  param.append("payload", "fintex-services"); 

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
  param.append("payload", "fintex-services");
  
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

