
$(document).on('submit','#addSales',function(e){
    e.preventDefault();
   let formRequest =  new FormData(this);
   let result = run(formRequest);
   if(result.responseJSON.statusCode == 200){
     $("#addSales")[0].reset();
   }
});