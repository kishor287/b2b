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
    
    $email = "kulbhushan@innerxcrm.com,atul@innerxcrm.com";
    $to = explode(",",$email);
     if(count($to)>0){
          $content = "";
          $subject = "Email";
     $content=str_replace( "{{date}}", date('d M Y'), $content);
     $content=str_replace( "{{marketing_admin}}", $res['data']['passport'], $content);
     $content=str_replace( "{{t_client_name}}", $res['data']['phone'], $content);
     $content=str_replace( "{{request_type}}", $res['data']['college'], $content);
     $content=str_replace( "{{date}}", $res['data']['service'], $content);
     $content=str_replace( "{{final_amount}}", $res['data']['phone'], $content);
     
    $param=array("to"=>$to,"subject"=>$subject,"content"=>$content,"attachment"=>"");
    mailer($param);
     }
    
    echo json_encode(array("status"=>1,"msg"=>"success","data"=>"Mail Sent successfully."));
       
}

