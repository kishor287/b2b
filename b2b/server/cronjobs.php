<?

  /*
Save
*/
if($action=='get'){
    $filter='where 1=1';
 if(isset($_POST['filter']) && !empty($_POST['filter'])){
    $array=json_decode($_POST['filter'],true);
     if(isset($array['id']) && ($array['id']!='')  ){
      $filter.=" and organization='".$array['id']."'  ";  
   $param=array("pagination"=>$_POST['pagination'],"col"=>"report","tb"=>"cron_reports","where"=>"".$filter,"limit"=>(($_POST['pagination']-1) * $_POST['limit']).",".$_POST['limit']);
    $res = qselect($param);
    echo json_encode($res);
  exit();
      
    } 
    
 }
 $filter.="  group by organization  ";
    $param=array("pagination"=>$_POST['pagination'],"col"=>"is_set,organization,count(*) as report","tb"=>"cron_reports","where"=>"".$filter,"limit"=>(($_POST['pagination']-1) * $_POST['limit']).",".$_POST['limit']);
    $res = qselect($param);
    echo json_encode($res);
 
}

 /*
Dir
*/
if($action=='dir'){
    
     $param=array("pagination"=>"","col"=>"organization","tb"=>"cron_reports","where"=>"","limit"=>10000);
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
Reports
*/
if($action=='reports'){
    
     $param=array("pagination"=>"","col"=>"report","tb"=>"reports","where"=>"","limit"=>10000);
     $res = qselect($param); 
    echo json_encode($res);
}


 /*
Save
*/
if($action=='save'){
 
         
    $data=array();
    foreach($_POST['reports'] as $a){
    $data[]=array("organization"=>$_POST['organization'], "report"=>$a);
    }
   
   $param=array("tb"=>"cron_reports");
   $res =  qminsert($param,$data);
    echo json_encode($res);
}

  /*
Remove
*/

if($action=='remove'){ 
    $param=array("col"=>"","tb"=>"cron_reports","where"=>"where organization='".$_POST['id']."' ","limit"=>"10000");
    $res = qdelete($param,'',1);
    echo json_encode($res);
}



if($action=='update'){ 
    
    $param=array("col"=>"","tb"=>"cron_reports","where"=>"where organization='".$_POST['id']."' ","limit"=>"10000");
    $res = qdelete($param,'',1);  
    
    $data=array();
    foreach($_POST['reports'] as $a){
    $data[]=array("organization"=>$_POST['id'], "report"=>$a);
    }
   
   $param=array("tb"=>"cron_reports");
   $res =  qminsert($param,$data);

    echo json_encode($res);
}



if($action=='set'){ 
    
     $param=array("tb"=>"cron_reports","where"=>"where organization='".$_POST['id']."' ","limit"=>"100000");
     $data=array(
         "is_set"=>1,
         );
     
   
   qupdate($param,$data,0);
  
    
    $param=array("col"=>"","tb"=>"cron_period","where"=>"where organization='".$_POST['id']."' ","limit"=>"10000");
    $res = qdelete($param,'',1);  
    
   $param=array("pagination"=>$_POST['pagination'],"col"=>"report","tb"=>"cron_reports","where"=>"where organization='".$_POST['id']."' ","limit"=>(($_POST['pagination']-1) * $_POST['limit']).",".$_POST['limit']);
   $res = qselect($param);
   
   $data=array();
   foreach($res['data'] as $a){
    $data[]=array("report"=>$a['report'],"organization"=>$_POST['id'], "period"=>$_POST['period'][$a['report']][0], "time"=>$_POST['time'][$a['report']][0]);
    }
   
   $param=array("tb"=>"cron_period");
   $res =  qminsert($param,$data);
   echo json_encode($res);
   
}


if($action=='get_period'){ 
    
   $param=array("pagination"=>$_POST['pagination'],"col"=>"*","tb"=>"cron_period","where"=>"where organization='".$_POST['id']."' ","limit"=>(($_POST['pagination']-1) * $_POST['limit']).",".$_POST['limit']);
   $res = qselect($param);
     echo json_encode($res);
}



