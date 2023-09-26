<?

  switch ($page[0]) {
      
    case "":
    $path='index.html';
    break;
    
    case $page[0]:
    $path=$page[0].'.html';
    break;
     
    break; 
    default:
    $path='404.html';
    
}