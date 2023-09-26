<?



  /*
Email
*/
if($action=='email'){
       $filter='where 1=1';
 if(isset($_POST['filter']) && !empty($_POST['filter'])){
    $array=json_decode($_POST['filter'],true);
     if(isset($array['id']) && ($array['id']!='')  ){
      $filter.=" and id='".$array['id']."'  ";  
    } 
    
 }
    $param=array("pagination"=>"","col"=>"*","tb"=>"agreements","where"=>"".$filter,"limit"=>"1");
    $res = qselect($param);
    print_r($res);die;
    
    
     $to = explode(",",$_POST['emails']);
     if(count($to)>0){
     $subject="Agreement | InnerxCRM";
     $content=file_get_contents('https://innerxcrm.com/agreement');
     $content=str_replace( "{{date}}", date('d M Y'), $content);

     $content=str_replace( "{{username}}", $res['data']['username'], $content);
     $content=str_replace( "{{organization}}", $res['data']['organization'], $content);
     $content=str_replace( "{{address}}", $res['data']['address'], $content);
     $content=str_replace( "{{committed}}", $res['data']['committed'], $content);
     $content=str_replace( "{{reward}}", $res['data']['reward'], $content);
     $content=str_replace( "{{credit_card}}", $res['data']['credit_card'], $content);
     $content=str_replace( "{{credit_card2}}", $res['data']['credit_card2'], $content);
     $content=str_replace( "{{benefits}}", $res['data']['benefits'], $content);
     $content=str_replace( "{{forex}}", $res['data']['forex'], $content);
     $content=str_replace( "{{other}}", $res['data']['other'], $content);
     $content=str_replace( "{{companyowner}}", $res['data']['companyowner'], $content);
     $content=str_replace( "{{companytype}}", $res['data']['companytype'], $content);
          $content=str_replace( "{{email}}", $res['data']['email'], $content);
               $content=str_replace( "{{phone}}", $res['data']['phone'], $content);
     
     $param=array("to"=>$to,"subject"=>$subject,"content"=>$content,"attachment"=>"");
    mailer($param);
     }
    
    echo json_encode(array("status"=>1,"msg"=>"success","data"=>"Mail Sent successfully."));
       
}


  /*
Save
*/
if($action=='get'){
    $filter='where 1=1';
 if(isset($_POST['filter']) && !empty($_POST['filter'])){
    $array=json_decode($_POST['filter'],true);
     if(isset($array['id']) && ($array['id']!='')  ){
      $filter.=" and id='".$array['id']."'  ";  
    } 
    
 }
    $param=array("sort"=>"order by id desc","pagination"=>$_POST['pagination'],"col"=>"*","tb"=>"agreements","where"=>"".$filter,"limit"=>(($_POST['pagination']-1) * $_POST['limit']).",".$_POST['limit']);
    $res = qselect($param);
    echo json_encode($res+array('date'=>date('d M Y')));
 
}



 /*
Save
*/
if($action=='save'){
 
    $param=array("pagination"=>"","col"=>"*","tb"=>"leads","where"=>"where id=".$_SESSION['uId'],"limit"=>" 1");
    $r = qselect($param);
    // $username = $r['data']['prefix'].' '.$r['data']['fname'].' '.$r['data']['lname'];
  /*
Data Insert
*/
     $param=array("tb"=>"agreements");
     $data=array(
        //  "marketing_id"=>$_SESSION['uId'],
        "organization"=>$_POST['organization'],
        "phone"=>$_POST['phone'],
        "email"=>$_POST['email'],
        "address"=>$_POST['address'],
        "companyowner"=>$_POST['companyowner'],
      "companytype"=>$_POST['companytype'],
        // "username"=>$username,
        "committed"=>$_POST['committed'],
        "reward"=>$_POST['reward'],
        "credit_card"=>$_POST['credit_card'],
        // "credit_card2"=>$_POST['credit_card2'],
        "benefits"=>$_POST['benefits'],
        "forex"=>$_POST['forex'],
        "other"=>$_POST['other'],
        );
         
   
    $res = qinsert($param,$data);
    echo json_encode($res);
}

  /*
Remove
*/

if($action=='remove'){ 
    $param=array("col"=>"","tb"=>"agreements","where"=>"where id=".$_POST['id']." ","limit"=>"1");
    $res = qdelete($param,'',1);
    echo json_encode($res);
}



if($action=='update'){ 
    
  /*
Data Update
*/
     $param=array("tb"=>"agreements","where"=>"where id=".$_POST['id']." ","limit"=>"1");
     $data=array(
       "organization"=>$_POST['organization'],
        "address"=>$_POST['address'],
        "committed"=>$_POST['committed'],
        "reward"=>$_POST['reward'],
        "credit_card"=>$_POST['credit_card'],
        "credit_card2"=>$_POST['credit_card2'],
        "benefits"=>$_POST['benefits'],
        "forex"=>$_POST['forex'],
        "other"=>$_POST['other'],
       "companyowner"=>$_POST['companyowner'],
      "companytype"=>$_POST['companytype'],   
           "email"=>$_POST['email'],
      "phone"=>$_POST['phone'],
         );
     
   
    $res = qupdate($param,$data,0);
    echo json_encode($res);
}






