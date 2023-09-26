/*
Saving the data into the users database...
*/
$('#add_lead_form').submit(function(e) { 
  e.preventDefault();
  
  var form = $(this)[0]; 
  var param = new FormData(form);
  param.append("_action", "save"); 
  param.append("payload", "add-lead"); 
 
  var res = run(param); 
   
  var json = JSON.parse((res.responseText));
  if(json['status']===1){
      $('#add_lead_form')[0].reset();
      toastr.success("Added", "Data Inserted Successfully.")
      
  } else if(json['status']===0){
      toastr.error("Error", json['data'])
     
  }else{
      toastr.error("Error", "Internal Server Error")
     
  }
 
});




$("body").on("click","input[type='radio']", function(){  
 if($(this).val()==='yes'){
	$(this).parent().parent().children().eq(3).removeClass("d-none");
}else{
$(this).parent().parent().children().eq(3).addClass("d-none");
}
})




 $("body").on("click","input[name='location_radio']", function(){ 
 if($(this).val()==='yes'){
	$("#_more_locations").removeClass("d-none");
}else{
	$("#_more_locations").addClass("d-none");
}
 })
 
 
 
 
        $("#_add_location").click(function () {
            newRowAdd =
            '<div class="mb-2 col-md-11" id="_remove_locations"><div class="input-group"><input type="text" aria-label="city" placeholder="City" name="location_city[]" class="form-control"><input type="text" aria-label="state" placeholder="State" name="location_state[]" class="form-control"><span id="_remove_location" class="cursor-pointer input-group-text"><i class=" bx bx-minus"></i> </span></div></div>';
 
            $('#newinput').append(newRowAdd);
        });
 
        $("body").on("click", "#_remove_location", function () {
            $(this).parents("#_remove_locations").remove();
        })
