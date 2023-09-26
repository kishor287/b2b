/*

Table headings

*/

var table_cols=[
  //  'checkbox',
    'Action',
    'S.no.',
    'Represented by',
    'ORGANIZATION',
    'ADDRESS',
    'COMMITTED NO. OF GIC(S',
    'REWARD (CAD)',
    'CREDIT CARD',
    'BENEFITS',
    'FOREX',
    'OTHER SERVICES',
  
    ]
table_head(table_cols);


get_data(1);



/*
Save
*/
$('#organization_form').submit(function(e) { 
  e.preventDefault();
  
  var form = $(this)[0]; 
  var param = new FormData(form);
  param.append("_action", "save"); 
  param.append("payload", "agreement"); 
 
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
Users function
*/

function get_data(page,filter){ 

limit=10;    
l_event='tbody';

  var param = new FormData();
  param.append("_action", "get"); 
  param.append("payload", "agreement"); 
 param.append("limit",limit); 
 param.append("pagination", page); 
  var res = run(param); 
  var json = JSON.parse((res.responseText));

     var jHTML='';
     $.each(json.data, function (k, v) {
      jHTML+='<tr data-id="'+v['id']+'" >';
       jHTML+='<td><div class="d-flex"><a title="Download Agreemnet" href="javascript:void();" class="btn btn-success shadow btn-xs sharp me-1"><i class="bx bx-download"></i></a><a title="E-mail Agreemnet"  href="javascript:" class="btn btn-primary shadow btn-xs sharp me-1"><i class="bx bx-mail-send"></i></a><a title="Edit Record"  href="javascript:" class="btn btn-warning shadow btn-xs sharp me-1"><i class="bx bx-pencil"></i></a><a title="Remove Record"  href="javascript:" class="btn btn-danger shadow btn-xs sharp"><i class="bx bx-trash"></i></a></div></td>';
               
         jHTML+='<td>'+((page*limit)-(limit-(k+1)))+'</td>';
         jHTML+='<td>'+v['username']+'</td>';
         jHTML+='<td>'+v['organization']+'</td>';
          jHTML+='<td>'+v['address']+'</td>';
           jHTML+='<td>'+v['committed']+'</td>';
             jHTML+='<td>'+v['reward']+'</td>';
            jHTML+='<td>'+v['credit_card']+''+v['credit_card2']+'</td>';
             jHTML+='<td>'+v['benefits']+'</td>';
              jHTML+='<td>'+v['forex']+'</td>';
               jHTML+='<td>'+v['other']+'</td>';
   
                jHTML+='</tr>';
     })
     
  
          $("tbody").html(jHTML)
          $(".pagination").html(json.pagination)
      
          
      
}



/*
 mail
*/

$("body").on("click","tr .btn-primary", function(){ 
 var id = $(this).parent().parent().parent().attr('data-id');  
 email(id);
})


function email(id){
   $("#basicModalemail").modal('show'); 
    $('#basicModalemail #_id').val(id)
}


$('#organization_form_email').submit(function(e) { 
  e.preventDefault();

  
  var id= $('#basicModalemail #_id').val();
  var form = $(this)[0]; 
  var param = new FormData(form);
  param.append("_action", "email"); 
  param.append("payload", "agreement"); 
  param.append("limit",1); 
  param.append("pagination", 1); 
  param.append("filter",  JSON.stringify({"id":id})); 

  var res = run(param); 
   
  var json = JSON.parse((res.responseText));
  if(json['status']===1){
      toastr.success(json['data'], 'Success')
       $("#basicModalemail").modal('hide'); 
  
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
  param.append("payload", "agreement");
  
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
 Download
*/

$("body").on("click","tr .btn-success", function(){ 
 var id = $(this).parent().parent().parent().attr('data-id');  
 download(id);
})




function download(id){

  var param = new FormData();
  param.append("_action", "get"); 
  param.append("payload", "agreement"); 
  param.append("limit",1); 
  param.append("pagination", 1); 
  param.append("filter",  JSON.stringify({"id":id})); 
  var res = run(param);
  var json = JSON.parse((res.responseText));
    
    $.get("https://cp.innerxcrm.com/view/agreement-pdf.html", function(html_string)
   {
       

   $('#_pdf').html(html_string); ;
  
   // Convert_HTML_To_PDF();
   // $('body').hide()

setTimeout(
  function() 
  { 
     $('#_pdf #date').html(json['date'])
    $('#_pdf #username').html(json.data[0]['username'])
     $('#_pdf #companyowner').html(json.data[0]['companyowner'])
      $('#_pdf #companytype').html(json.data[0]['companytype'])
       $('#_pdf #contact').html(json.data[0]['phone']+' - '+json.data[0]['email'])
    if(json.data[0]['phone']===null){
        $('#_pdf #contact').html('')
    }
    $('#_pdf #organization').html(json.data[0]['organization'])
     $('#_pdf #organization2').html(json.data[0]['organization'])
      $('#_pdf #organization3').html(json.data[0]['organization'])
       $('#_pdf #organization4').html(json.data[0]['organization'])
        $('#_pdf #organization5').html(json.data[0]['organization'])
    $('#_pdf #address').html(json.data[0]['address'])
     $('#_pdf #address2').html(json.data[0]['address'])
    $('#_pdf #committed').html(json.data[0]['committed'])
    $('#_pdf #reward').html(json.data[0]['reward'])
    $('#_pdf #credit_card').html(json.data[0]['credit_card'])
    $('#_pdf #credit_card2').html(json.data[0]['credit_card2'])
    $('#_pdf #benefits').html(json.data[0]['benefits'])
    $('#_pdf #forex').html(json.data[0]['forex'])
    $('#_pdf #other').html(json.data[0]['other'])  
    
  printData()
  }, 1000);
  
  
  setTimeout(
  function() 
  {
   $('#_pdf').html('')
  }, 2000);
 
  })
 
}


 
 function printData()
{
   var divToPrint=document.getElementById("_pdf");
   newWin= window.open("https://cp.innerxcrm.com/agreement");
   newWin.document.write(divToPrint.outerHTML);
   newWin.print();
   newWin.close();
}



/*
Pagination
*/


$("body").on("click","#_pagination", function(){ 
get_data(($(this).attr('data-id')));
})



/*
 Edit
*/


$("body").on("click","tr .btn-warning", function(){ 
 var id = $(this).parent().parent().parent().attr('data-id');  
 edit_data(id);  
})


function edit_data(id){
   $("#basicModaledit").modal('show'); 
   
  var param = new FormData();
  param.append("_action", "get"); 
  param.append("payload", "agreement"); 
  param.append("limit",1); 
  param.append("pagination", 1); 
 param.append("filter",  JSON.stringify({"id":id})); 
   var res = run(param);
   var json = JSON.parse((res.responseText));
  
     
    $('#basicModaledit #_id').val(id)
    $('#basicModaledit input[name="organization"]').val(json.data[0]['organization'])
         $('#basicModaledit input[name="companyowner"]').val(json.data[0]['companyowner'])
      $('#basicModaledit textarea[name="companytype"]').val(json.data[0]['companytype'])
       $('#basicModaledit input[name="email"]').val(json.data[0]['email'])
        $('#basicModaledit input[name="phone"]').val(json.data[0]['phone'])
    $('#basicModaledit textarea[name="address"]').val(json.data[0]['address'])
    $('#basicModaledit input[name="committed"]').val(json.data[0]['committed'])
    $('#basicModaledit input[name="reward"]').val(json.data[0]['reward'])
        $('#basicModaledit input[name="credit_card"]').val(json.data[0]['credit_card'])
            $('#basicModaledit input[name="credit_card2"]').val(json.data[0]['credit_card2'])
                $('#basicModaledit input[name="benefits"]').val(json.data[0]['benefits'])
                    $('#basicModaledit input[name="forex"]').val(json.data[0]['forex'])
                            $('#basicModaledit input[name="other"]').val(json.data[0]['other'])
}


$('#organization_form_edit').submit(function(e) { 
  e.preventDefault();

  
  
  var form = $(this)[0]; 
  var param = new FormData(form);
  param.append("_action", "update"); 
  param.append("payload", "agreement"); 

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



