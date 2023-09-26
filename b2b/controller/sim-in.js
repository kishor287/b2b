
$(document).on('submit','#addSimInventoryForm',function(e){
    e.preventDefault();
   let formRequest =  new FormData(this);
   let result = run(formRequest);
   if(result.responseJSON.statusCode == 200){
     $("#addSimInventoryForm")[0].reset();
   }
});