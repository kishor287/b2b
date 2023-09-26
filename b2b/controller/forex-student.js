
/*
 mail
*/

  var id= $('#basicModalemail #_id').val();
  var form = $(this)[0]; 
  var param = new FormData(form);
  param.append("_action", "email"); 
  param.append("payload", "forex-student"); 
  param.append("limit",1); 
  param.append("pagination", 1); 
  param.append("filter",  JSON.stringify({"id":id})); 
  var res = run(param); 
  var json = JSON.parse((res.responseText));
 
 




 



 
 






