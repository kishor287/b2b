<?
if ($action == 'get') {
   $filter = 'where 1=1';
   $limit = $_POST['limit'];
   if (isset($_POST['filter']) && !empty($_POST['filter'])) {
      $array = json_decode($_POST['filter'], true);
      if (isset($array['id']) && ($array['id'] != '')) {
         $filter .= " and id='" . $array['id'] . "'  ";
      }
      if (!empty($array['dateRange'])) {
         $date = explode('-', $array['dateRange']);
         $firstDate = $date[0];
         $lastDate = $date[1];
         $filter .= " and created BETWEEN $firstDate AND $lastDate ";
      }

   }
   $param = array("pagination" => $_POST['pagination'], "col" => "*", "tb" => "fintech_requests", "where" => "" . $filter, "limit" => (($_POST['pagination'] - 1) * $limit) . "," . $limit, 'sort' => ' ORDER BY id desc');
   $res = qselect($param);
   echo json_encode($res);

}

if ($action == 'update') {
   require_once('../_con.php');
   $id = $_POST['id'];
   $param = array("tb" => "fintech_requests", "where" => "where id=" . $_POST['id'] . " ", "limit" => "1");
   $data = array(
      "status" => ($_POST['status'] == 0) ? 1 : 0,
   );


   $res = qupdate($param, $data, 0);
   if ($res) {
      $selectUser = "SELECT * FROM cp_fintech_requests WHERE id = :requestId";
      $prep = $con->prepare($selectUser);
      $prep->bindParam(':requestId', $id);
      $prep->execute();
      $result = $prep->fetch(PDO::FETCH_ASSOC);
      $recipient = ['name' => $result['sname'], 'phone' => $result['phone']];
      simActivationStatus($recipient, $result['sname']);
   }
   echo json_encode($res);
}

function simActivationStatus(array $recipient, string $studentName)
{

   $data = array(
      "channelId" => '63e206b7fce0e80b431d639d',
      "channelType" => "whatsapp",
      "recipient" => array(
         "name" => $recipient['name'],
         "phone" => "91" . $recipient['phone']
      ),
      "whatsapp" => array(
         "type" => "template",
         "template" => array(
            "templateName" => "new_sim_activation_completion_2",
            "bodyValues" => array(
               'student_name' => $studentName,
            )
         )
      )
   );

   $curl = curl_init();
   curl_setopt_array(
      $curl,
      array(
         CURLOPT_URL => 'server.gallabox.com/devapi/messages/whatsapp',
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_ENCODING => '',
         CURLOPT_MAXREDIRS => 10,
         CURLOPT_TIMEOUT => 0,
         CURLOPT_FOLLOWLOCATION => true,
         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
         CURLOPT_CUSTOMREQUEST => 'POST',
         CURLOPT_POSTFIELDS => json_encode($data),
         CURLOPT_HTTPHEADER => array(
            'apiKey: 63e22876583a7318bbc19e96',
            'apiSecret: edab7b1e312543b894d708f5a9526ef3',
            'Content-Type: application/json',
         ),
      )
   );

   $response = curl_exec($curl);
   curl_close($curl);
   error_log('service request approval response:' . date('Y-md-') . ': ' . $response);
}