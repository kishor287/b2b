<?
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if ($action == 'dir') {

   $param = array("pagination" => "", "col" => "id,organization", "tb" => "registration", "where" => "", "limit" => 10000);
   $res = qselect($param);
   echo json_encode($res);
}


if ($action == 'save') {
   if ($_FILES['coupon']['name'] != '') {
      //$file['image']=$_FILES['coupon'];
      $d = ['image' => $_FILES['coupon']];
      $file = upload_coupon($d, 'coupon');

      if ($file['status'] == 0) {
         echo json_encode($file);
         exit();
      }
   } else {
      $file['data'] = '';
   }
   $param = array("tb" => "coupon_master");

   $text = isset($_POST['text1']) ? $_POST['text1'] : ' ';
   $gic = 0;
   $forex = 0;
   $sim = isset($_POST['sim']) ? '1' : '0';
   $loan = isset($_POST['loan']) ? '1' : '0';
   $insurance = isset($_POST['insurance']) ? '1' : '0';
   $credit_card = isset($_POST['credit_card']) ? '1' : '0';
   $reward = isset($_POST['reward']) ? $_POST['reward'] : '0';

   $org = (implode(",", $_POST['organization']));
   $forexSubServices = '';
   $gicSubServices = '';
   if (isset($_POST['gic'])) {
      $gic = '1';
      if (isset($_POST['gicSubServices'])) {
         $gicSubServices = implode(",", $_POST['gicSubServices']);
      }
   }
   if (isset($_POST['forex'])) {
      $forex = '1';
      if (isset($_POST['forexSubServices'])) {
         $forexSubServices = implode(",", $_POST['forexSubServices']);
      }
   }
   $data = array(
      "banner_image" => $file['data'],
      "banner_text" => $text,
      "gic" => $gic,
      "forex" => $forex,
      "sim" => $sim,
      "loan" => $loan,
      "insurance" => $insurance,
      "credit_card" => $credit_card,
      "organization" => $org,
      "forex_sub_services" => $gicSubServices,
      "gic_sub_services" => $forexSubServices,
      "amount" => $reward,
   );

   $res = qinsert($param, $data);
   echo json_encode($res);
}

if ($action == 'index') {
   $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
   $param = array("tb" => "coupon_master", "col" => "*", 'short' => 'id desc', "pagination" => $_POST['pagination'], 'where' => '', "limit" => ($_POST['pagination'] - 1) * $_POST['limit'] . "," . $_POST['limit']);

   $coupons = qselect($param);
   $gicBanks = select('cp_gic_bank', 'id,name', '', '', '');
   $forexBanks = select('cp_vendors', 'id,name', '', '', '');
   echo json_encode(compact('coupons', 'gicBanks', 'forexBanks'));
}

if ($action == 'getSubServices') {
   if ($_POST['service'] == 'gic') {
      $gicSubServices = select('cp_gic_bank', 'id,name', '', '', '');
      echo json_encode($gicSubServices);
   }
   if ($_POST['service'] == 'forex') {
      $forexSubServices = select('cp_vendors', 'id,name', '', '', '');
      echo json_encode($forexSubServices);
   }
}