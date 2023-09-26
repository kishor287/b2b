
$(document).on('submit','#saveLoan',function(e){
    e.preventDefault();
   let formRequest =  new FormData(this);
   let result = run(formRequest);
   if(result.responseJSON.statusCode == 200){
     $("#saveLoan")[0].reset();
   }
});