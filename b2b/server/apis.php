<?

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
    $param=array("pagination"=>$_POST['pagination'],"col"=>"*","tb"=>"apis","where"=>"".$filter,"limit"=>(($_POST['pagination']-1) * $_POST['limit']).",".$_POST['limit']);
    $res = qselect($param);
    echo json_encode($res);
 
}

 /*
Dir
*/
if($action=='dir'){
    
     $param=array("pagination"=>"","col"=>"organization","tb"=>"apis","where"=>"","limit"=>10000);
     $res = qselect($param); 
     $organization=array(); foreach($res['data'] as $d){ $organization[]=$d['organization']; } 
     
     $param=array("pagination"=>"","col"=>"organization","tb"=>"registration","where"=>"","limit"=>10000);
     $res = qselect($param); 
     $dirs=array(); foreach($res['data'] as $d){ $dirs[]=$d['organization']; } 

   $data=array(); foreach($dirs as $d){ if (!in_array($d, $organization)){ $data[]=$d; }}
    $api=array("status"=>1,"msg"=>"success","data"=>$data); 
    echo json_encode($api);
}


 /*
Save
*/
if($action=='save'){
 

  /*
Data Insert
*/
     $param=array("tb"=>"apis");
     $data=array(
        "organization"=>$_POST['organization'],
        "sms_api"=>$_POST['sms_api'],
        "dialer_api"=>$_POST['dialer_api'],
         "click2dial_api"=>$_POST['click2dial_api'],
                "w_channel_id"=>$_POST['w_channel_id'],
                       "w_api"=>$_POST['w_api'],
                              "w_secret"=>$_POST['w_secret'],
         );
         
   
    $res = qinsert($param,$data);
    echo json_encode($res);
}

  /*
Remove
*/

if($action=='remove'){ 
    $param=array("col"=>"","tb"=>"apis","where"=>"where id=".$_POST['id']." ","limit"=>"1");
    $res = qdelete($param,'',1);
    echo json_encode($res);
}



if($action=='update'){ 
    
  /*
Data Update
*/
     $param=array("tb"=>"apis","where"=>"where id=".$_POST['id']." ","limit"=>"1");
     $data=array(
         "sms_api"=>$_POST['sms_api'],
         "dialer_api"=>$_POST['dialer_api'],
         "click2dial_api"=>$_POST['click2dial_api'],
          "w_channel_id"=>$_POST['w_channel_id'],
                       "w_api"=>$_POST['w_api'],
                              "w_secret"=>$_POST['w_secret'],
         );
     
   
    $res = qupdate($param,$data,0);
    echo json_encode($res);
}



