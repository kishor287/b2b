<?
  /*
Save
*/
if($action=='getnav'){
    $filter='where 1=1 ';
 if(isset($_POST['filter']) && !empty($_POST['filter'])){
    $array=json_decode($_POST['filter'],true);
     if(isset($array['id']) && ($array['id']!='')  ){
         
    $param=array("pagination"=>$_POST['pagination'],"col"=>"title","tb"=>"navbar","where"=>"where id in (".implode(',',$array['id']).") ","limit"=>(($_POST['pagination']-1) * $_POST['limit']).",".$_POST['limit']);
    $res = qselect($param);
    $title=array(); foreach($res['data'] as $r){ $title[]=$r['title']; }
    
     $filter.="and title in ('".implode("','",$title)."')  "; 
    }else{ 
      $filter.="and sub_title is null"; 
 }
 }
      
 

    $param=array("pagination"=>$_POST['pagination'],"col"=>"*","tb"=>"navbar","where"=>"".$filter,"limit"=>(($_POST['pagination']-1) * $_POST['limit']).",".$_POST['limit']);
    $res = qselect($param);
    echo json_encode($res); 

}



  /*
Save
*/
if($action=='get'){
    $filter='where 1=1 and id!=1';
 if(isset($_POST['filter']) && !empty($_POST['filter'])){
    $array=json_decode($_POST['filter'],true);
     if(isset($array['id']) && ($array['id']!='')  ){
      $filter.=" and id='".$array['id']."'  ";  
    } 
    
 }
    $param=array("pagination"=>$_POST['pagination'],"col"=>"*","tb"=>"roles","where"=>"".$filter,"limit"=>(($_POST['pagination']-1) * $_POST['limit']).",".$_POST['limit']);
    $res = qselect($param);
    echo json_encode($res);
 
}


 /*
Save
*/
if($action=='save'){

    $param=array("tb"=>"roles");
    $role = $_POST['assigned_role'];
    $checkIfNameExists = select('cp_roles','COUNT(id) as count'," where roles_type='$role'",'','LIMIT 1');
    $roles = array_shift($checkIfNameExists);
    if($roles['count'] > 0){
        http_response_code(500);
        echo json_encode(['message' => "{$role} role already exists"]);
        exit;
    }
   
     $data=array(
        'type' => $_POST['roleType'],
        "roles_type"=>$_POST['assigned_role'],
    );

    $res = qinsert($param,$data);
    $last_id= $res['success_id'];
    
    $data2=array();
    $total = count($_POST['navbar']);
    for ($x = 0; $x < $total; $x++) {
        $param2=array("tb"=>"navbar_assign");
         
        $data2[]=array(
            "role_id"=>$last_id,
            "navbar_id" => $_POST['navbar'][$x],
        );
        
    }
     $res2 = qminsert($param2,$data2);
     echo json_encode($res2);
}

if($action=='update'){ 
    $type = $_POST['type'];
    
    $param=array("tb"=>"roles");
    $role = $_POST['assigned_role'];
    $checkIfNameExists = select('cp_roles','COUNT(id) as count'," where roles_type='$role'",'','LIMIT 1');
    $roles = array_shift($checkIfNameExists);
    if($roles['count'] > 0){
        http_response_code(500);
        echo json_encode(['message' => "{$role} role already exists"]);
        exit;
    }
     
    $param=array("tb"=>"roles","where"=>"where id=".$_POST['id']." ","limit"=>"1");
    $data=array(
            "roles_type"=>$_POST['assigned_role'],
    );
    $res = qupdate($param,$data,0);
    echo json_encode($res);
}



if($action=='remove'){ 
    $param=array("col"=>"","tb"=>"roles","where"=>"where id=".$_POST['id']." ","limit"=>"1");
    $res = qdelete($param,'',1);
    echo json_encode($res);
}


    
