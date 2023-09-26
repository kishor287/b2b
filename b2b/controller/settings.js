if(hash==="root"){ 
    window.location.href="/dashboard";
}
var hash= (hash==='root')?'dashboard':hash;
$('#_page_title').text(hash.toUpperCase());  



/*
Logout
*/

$('#_logout').click(function() { 
  var param = new FormData();
  param.append("_action", "logout"); 
  param.append("payload", "settings"); 
  var res = run(param); 
  var json = JSON.parse((res.responseText));
  if(json['status']===1){
      window.location.href="/"
  }else{
    toastr.error("Please try again later", "Internal Server Error")
  }
})


/*
login
*/
$('#_login_form').submit(function(e) { 
  e.preventDefault();
  
  if($('input[name="username"]').val().length===0){  
        toastr.error("Please enter your username", "Invalid Username")
        return false
   }
      
 if($('input[name="password"]').val().length===0){
        toastr.error("Please enter your password", "Invalid Password")
        return false
   }  
  
  
  var form = $(this)[0]; 
  var param = new FormData(form);
  param.append("_action", "login"); 
  param.append("payload", "settings"); 
  
  l_event='button[type="submit"]'
  l_data='Sign Me In'
  loader(l_event)
  var res = run(param); 
  loader(l_event,l_data)
   
  var json = JSON.parse((res.responseText));
  if(json['status']===1){
      if(hash===''){
        window.location.href="/dashboad";
      }else{
         window.location.href="/"+hash; 
      }
  }else{
      toastr.error("Please verify the login details", "Invalid Credentials/Not Authorized")
      $('input[name="password"]').val('')
  }
 
});

/*

new navbar.....

*/

profile();
navbar();
function navbar(){
  var param = new FormData(); 
  param.append("_action", "navbar"); 
  param.append("payload", "settings"); 
  var res = run(param); 
  var json = JSON.parse((res.responseText));
    var jHTML='';
    
     $.each(json.data, function (k, v) {
         
    if(v['sub_title']==='' || v['sub_title']===null ){
        
       var clas= (v['link']==='javascript:void(0)')?'menu-toggle':'';
       var subclas= (v['link']==='javascript:void(0)')?'<ul class="menu-sub" id="_nav_'+v['title'].replace(/ /g,"_")+'"></ul>':'';
            
          jHTML+='<li class="menu-item"><a href="'+v['link']+'" class="menu-link '+clas+' " aria-expanded="false">';
    	  jHTML+='<i class="menu-icon tf-icons bx '+v['icon']+'"></i>';
          jHTML+='<div data-i18n="Analytics">'+v['title']+'</div>';
    	  jHTML+='</a>';
    	  jHTML+=subclas;
    	  jHTML+='</li>';
	
     }})
     $("#menu").append(jHTML)
     
      $.each(json.data, function (k, v) {
      if(v['sub_title']!=='' && v['sub_title']!==null ){ 
          jHTML='';
          jHTML+='<li class="menu-item"><a href="'+v['link']+'" class="menu-link">';
    	  
          jHTML+=' <div>'+v['sub_title']+'</div>';
    	  jHTML+='</a>';
    	  jHTML+='</li>';
    	  $('#_nav_'+v['title'].replace(/ /g,"_")).append(jHTML);
    	  $('#_nav_form_'+v['title'].replace(/ /g,"_")).append(jHTML);
    	 
      }
     })
     

   
  
  
}




