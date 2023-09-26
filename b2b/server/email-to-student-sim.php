<?

if($action=='email'){
 
$filter='where 1=1';
 if(isset($_POST['filter']) && !empty($_POST['filter'])){
    $array=json_decode($_POST['filter'],true);
     if(isset($array['id']) && ($array['id']!='')  ){
      $filter.=" and id='".$array['id']."'  ";  
    } 
    
 }
    $param=array("pagination"=>"","col"=>"*","tb"=>"user_has_services","where"=>"".$filter,"limit"=>"1");
    $res = qselect($param);
    
  $email = $res['data']['email'];
     $to = explode(",",  $email);
    
     if(count($to)>0){
          $content = "";
          $subject = "Email";
     $content=str_replace( "{{date}}", date('d M Y'), $content);
     $first_name = $res['data']['first_name'];
     $last_name = $res['data']['last_name'];
     $name = $first_name . " " . $last_name;
     $content=str_replace( "{{student_name}}", $name, $content);
     $content=str_replace( "{{passport_number}}", $res['data']['passport'], $content);
     $content=str_replace( "{{phone_number}}", $res['data']['phone'], $content);
     $content=str_replace( "{{college}}", $res['data']['college'], $content);
     $content=str_replace( "{{request_type}}", $res['data']['service'], $content);
    $content=str_replace( "{{phone}}", $res['data']['phone'], $content);
     
    $param=array("to"=>$to,"subject"=>$subject,"content"=>$content,"attachment"=>"");
    mailer($param);
     }
    
    echo json_encode(array("status"=>1,"msg"=>"success","data"=>"Mail Sent successfully."));
       
}

