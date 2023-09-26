<?php

  /*
Save
*/
if($action=='get'){
    $filter='where 1=1';
 if(isset($_POST['filter']) && !empty($_POST['filter'])){
    $array=json_decode($_POST['filter'],true);
     if(isset($array['id']) && ($array['id']!='')  ){
        $filter.=" and organization='".$array['id']."' ";  
         $param=array("pagination"=>$_POST['pagination'],"col"=>"*","tb"=>"fintex_services","where"=>"".$filter,"limit"=>(($_POST['pagination']-1) * $_POST['limit']).",".$_POST['limit']);
   
    $res = qselect($param);
    echo json_encode($res);
  exit();
      
    } 
    
 }
 $filter.="  group by organization";
    $param=array("pagination"=>$_POST['pagination'],"col"=>"organization,count(*) as count","tb"=>"fintex_services","where"=>"".$filter,"limit"=>(($_POST['pagination']-1) * $_POST['limit']).",".$_POST['limit']);
    $res = qselect($param);
    echo json_encode($res);
 
}

 /*
Dir
*/
if($action=='dir'){
    
     $param=array("pagination"=>"","col"=>"organization","tb"=>"fintex_services","where"=>"","limit"=>10000);
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
    Data Insert in Database.....
*/

    
if($action=='save'){
    
    
    
    $data=array();
    $services=array('loan'=>0,'credit-card'=>0,'insurance'=>0,'sim'=>0,'forex'=>0,'gic'=>0);
    
    foreach($_POST['country'] as $a ){
        
        foreach($services as $key=>$v){ 
            if(in_array($key, $_POST['services'][$a])){
                
                $services[$key]=1;
            }else{
                $services[$key]=0;
            }
           
        }
         
        $data[]=array("organization"=>$_POST['organization'], "country"=>$a, "loan"=>$services['loan'], "credit_card"=>$services['credit-card'], "insurance"=>$services['insurance'], "sim"=>$services['sim'], "forex"=>$services['forex'], "gic"=>$services['gic']);
                    
    }

   $param=array("tb"=>"fintex_services");
   $res =  qminsert($param,$data);
    echo json_encode($res);
}

// update

if($action=='update'){ 
    $data=array();
    $services=array('loan'=>0,'credit-card'=>0,'insurance'=>0,'sim'=>0,'forex'=>0,'gic'=>0);
    $param=array("col"=>"","tb"=>"fintex_services","where"=>"where organization='".$_POST['id']."' ","limit"=>"10000");
    $res = qdelete($param,'',1);  
    
    foreach($_POST['country'] as $a ){
        
        foreach($services as $key=>$v){ 
            if(in_array($key, $_POST['services'][$a])){
                
                $services[$key]=1;
            }else{
                $services[$key]=0;
            }
           
        }
         
        $data[]=array("organization"=>$_POST['id'], "country"=>$a, "loan"=>$services['loan'], "credit_card"=>$services['credit-card'], "insurance"=>$services['insurance'], "sim"=>$services['sim'],"forex"=>$services['forex'],"gic"=>$services['gic']);
                    
    }

   $param=array("tb"=>"fintex_services");
   $res =  qminsert($param,$data);

    echo json_encode($res);
}

  /*
Remove
*/

if($action=='remove'){ 
    $param=array("col"=>"","tb"=>"fintex_services","where"=>"where organization='".$_POST['id']."' ","limit"=>"10000");
    $res = qdelete($param,'',1);
    echo json_encode($res);
}
