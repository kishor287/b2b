gpt=($(location).attr('href')).split("/");
xr = gpt[3].split('?')[0];
xr=xr.split('#')[0].replace(/[&\/\\#@_,+()$~%.'":*?<>{}]/g,'');
hash=(!xr)?'root':xr;
$.getScript('/b2b/controller/functions.js',function(){
$.getScript('/b2b/controller/settings.js',function(){ 
$.getScript('/b2b/controller/'+hash+'.js');
});});
