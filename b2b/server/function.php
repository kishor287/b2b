<?php
require_once('Classes/Controller/LeadActivityController.php');

use Panel\Server\Classes\Controller\LeadActivityController;

use PHPMailer\PHPMailer\PHPMailer;

function validateLead($organizationName, $phone, $recordId = null)
{

   $organizationName = str_replace("'", "''", $organizationName);
   $phone = trim($phone);
   if ($recordId) {
      $condition = " where (organization='$organizationName' OR organization_phone='$phone') AND id != $recordId";
   } else {
      $condition = " where organization='$organizationName' OR organization_phone='$phone'";
   }

   $count = select('cp_leads', 'COUNT(id) as count', $condition, '', 'LIMIT 1');
   $count = array_shift($count);
   if ($count['count'] > 0) {
      return "This organization name or phone already exists.";
   }
}


function qupdate($param, $data, $remove)
{
   include('../_con.php');

   if ($remove == 1) {
      $stmt = $con->query('update  ' . $tb_pre . $param['tb'] . ' set ' . $param['col'] . '  ' . $param['where'] . ' limit ' . $param['limit'] . '  ');
      $count = $stmt->rowCount();
      $result = array("status" => 1, "msg" => "success", "data" => "Removed successfully", "count" => $count);
      return $result;
   } else {
      $values = array();
      $cols = '';
      foreach ($data as $name => $value) {
         $cols .= ' ' . $name . ' = :' . $name . ',';
         $values[':' . $name] = $value;
      }
      $cols = substr($cols, 0, -1);

      $stmt = $con->prepare('update ' . $tb_pre . $param['tb'] . ' set  ' . $cols . '  ' . $param['where'] . ' limit ' . $param['limit'] . '  ');
      if ($stmt->execute($values)) {
         $result = array("status" => 1, "msg" => "success", "data" => "Data updated successfully.");
      } else {
         $result = array("status" => 0, "msg" => "error", "data" => "Failed to save the data.");
      }
      return $result;
   }
}



/*
select query
*/
function qselect($param)
{
   if (!isset($param['sort'])) {
      $param['sort'] = '';
   }
   if (empty($param['group_by'])) {
      $param['group_by'] = '';
   }
   include('../_con.php');
   $q = 'select SQL_CALC_FOUND_ROWS ' . $param['col'] . ' from ' . $tb_pre . $param['tb'] . ' ' . $param['where'] . ' ' . $param['group_by'] . '  ' . $param['sort'] . '  limit ' . $param['limit'] . ' ';
   if (isset($param['debug'])) {
      dd($q);
   }
   $stmt = $con->query('select SQL_CALC_FOUND_ROWS ' . $param['col'] . ' from ' . $tb_pre . $param['tb'] . ' ' . $param['where'] . ' ' . $param['group_by'] . '  ' . $param['sort'] . '  limit ' . $param['limit'] . '   ');
   $count = $stmt->rowCount();

   ($param['limit'] == 1) ? ($data = $stmt->fetch(PDO::FETCH_ASSOC)) : ($data = $stmt->fetchAll(PDO::FETCH_ASSOC));

   $stmt = $con->query('SELECT FOUND_ROWS()');
   $totalcount = $stmt->fetchColumn();

   require_once("pagination.class.php");
   $perPage = new PerPage();

   $pagination = $perPage->getAllPageLinks($totalcount, '', (int) substr($param['limit'], strpos($param['limit'], ",") + 1), $param['pagination']);

   $result = array("status" => 1, "msg" => "success", "data" => $data, "count" => $count, "total" => $totalcount, "pagination" => $pagination, "q" => $q);
   return $result;
}
/*
delete query
*/

function qdelete(array $param, $data)
{
   include('../_con.php');
   $stmt = $con->query('delete from ' . $tb_pre . $param['tb'] . ' ' . $param['where'] . ' limit ' . $param['limit'] . '  ');
   $count = $stmt->rowCount();

   $result = array("status" => 1, "msg" => "success", "data" => "Removed successfully", "count" => $count);
   return $result;
}


/*
Insert query
*/
function qinsert($param, $data)
{

   include('../_con.php');
   $data = array_map('trim', $data);
   $cols = implode('`,`', array_keys($data));
   $comma = str_repeat("?,", count($data));
   $comma = rtrim($comma, ",");

   $stmt = $con->prepare('insert into ' . $tb_pre . $param['tb'] . '  (`' . $cols . '`) VALUES  (' . $comma . ')  ');

   if ($stmt->execute(array_values($data))) {
      $result = array("status" => 1, "msg" => "success", "data" => "Data inserted successfully.", "message" => "Data inserted successfully.", "success_id" => $con->lastInsertId());
   } else {
      $result = array("status" => 0, "msg" => "error", "data" => "Failed to save the data.", "message" => "Failed to save the data.");
   }
   return $result;
}

/*
Insert multi query
*/

function qminsert($param, $data)
{
   include('../_con.php');

   if (empty($data)) {
      return array("status" => 1, "msg" => "success", "data" => "Data inserted successfully.");
   }

   $columns = array_keys($data[0]);
   $columnCount = !empty($columns) ? count($columns) : count(reset($data));
   $columnList = !empty($columns) ? '(' . implode(', ', $columns) . ')' : '';
   $rowPlaceholder = ' (' . implode(', ', array_fill(1, $columnCount, '?')) . ')';

   $query = sprintf('INSERT INTO %s%s VALUES %s', $tb_pre . $param['tb'], $columnList, implode(', ', array_fill(1, count($data), $rowPlaceholder)));
   $statement = $con->prepare($query);

   $data_arr = array();
   foreach ($data as $rowData) {
      $data_arr = array_merge($data_arr, array_values($rowData));
   }

   if ($statement->execute($data_arr)) {
      $result = array("status" => 1, "msg" => "success", "data" => "Data inserted successfully.");
   } else {
      $result = array("status" => 0, "msg" => "error", "data" => "Failed to save the data.");
   }
   return $result;
}


/*
Random String
*/

function random_str($n)
{
   $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
   $randomString = '';

   for ($i = 0; $i < $n; $i++) {
      $index = rand(0, strlen($characters) - 1);
      $randomString .= $characters[$index];
   }

   return $randomString;
}


/*
Upload Img
*/


function upload_img($data, $dir)
{
   $name = random_str(25);
   $errors = array();
   $file_name = $data['image']['name'];
   $file_size = $data['image']['size'];
   $file_tmp = $data['image']['tmp_name'];
   $file_ext = explode('.', $data['image']['name']);
   $file_ext = end($file_ext);
   $file_ext = strtolower($file_ext);
   //  $extensions = array("jpeg", "jpg", "png", );

   //  if (in_array($file_ext, $extensions) === false) {
   //      $errors[] = "Please choose a JPEG or PNG file.";
   //  }

   if ($file_size > 2097152) {
      $errors[] = 'File size must be less than 2 MB';
   }

   // Initialize the 'filename' and 'path' keys in the $result array
   $result = array(
      "status" => 0,
      "data" => array(),
      "msg" => "error",
      "filename" => "",
      // Initialize 'filename'
      "path" => "" // Initialize 'path'
   );

   if (empty($errors)) {
      // Modify the file path to point to crm/uploads directory
      $filepath = "crm/uploads/" . $dir . "/" . $name . '.' . $file_ext;
      move_uploaded_file($file_tmp, "../crm/uploads/" . $dir . "/" . $name . '.' . $file_ext);
      $result = array(
         "status" => 1,
         "data" => $name . '.' . $file_ext,
         "msg" => "success",
         "filename" => $file_name,
         // Set 'filename' on success
         "path" => $filepath,
         // Set 'path' on success
         "exactFilePath" => "uploads/$dir/$name.$file_ext"
      );
   } else {
      $result["data"] = $errors;
   }

   return $result;
}

function storeImg($data, $dir)
{
   $name = random_str(25);
   $errors = array();
   $file_name = $data['image']['name'];
   $file_size = $data['image']['size'];
   $file_tmp = $data['image']['tmp_name'];
   $file_ext = explode('.', $data['image']['name']);
   $file_ext = end($file_ext);
   $file_ext = strtolower($file_ext);
   $extensions = array("jpeg", "jpg", "png");

   //  if (in_array($file_ext, $extensions) === false) {
   //      $errors[] = "Please choose a JPEG or PNG file.";
   //  }

   if ($file_size > 2097152) {
      $errors[] = 'File size must be less than 2 MB';
   }

   // Initialize the 'filename' and 'path' keys in the $result array
   $result = array(
      "status" => 0,
      "data" => array(),
      "msg" => "error",
      "filename" => "",
      // Initialize 'filename'
      "path" => "" // Initialize 'path'
   );

   if (empty($errors)) {
      // Modify the file path to point to crm/uploads directory
      $filepath = "uploads/" . $dir . "/" . $name . '.' . $file_ext;
      move_uploaded_file($file_tmp, "../uploads/" . $dir . "/" . $name . '.' . $file_ext);
      $result = array(
         "status" => 1,
         "data" => $name . '.' . $file_ext,
         "msg" => "success",
         "filename" => $file_name,
         // Set 'filename' on success
         "path" => $filepath,
         // Set 'path' on success
         "exactFilePath" => "uploads/$dir/$name.$file_ext"
      );
   } else {
      $result["data"] = $errors;
   }

   return $result;
}
function upload_coupon($data, $dir)
{

   $name = random_str(25);
   $errors = array();
   $file_name = $data['image']['name'];
   $file_size = $data['image']['size'];
   $file_tmp = $data['image']['tmp_name'];
   $file_type = $data['image']['type'];
   $file_ext = explode('.', $data['image']['name']);
   $file_ext = end($file_ext);
   $file_ext = strtolower($file_ext);
   $extensions = array("jpeg", "jpg", "png");

   if (in_array($file_ext, $extensions) === false) {
      $errors[] = "Please choose a JPEG or PNG file.";
   }

   if ($file_size > 2097152) {
      $errors[] = 'File size must be less then 2 MB';
   }

   if (empty($errors) == true) {
      $filepath = "/uploads/" . $dir . "/" . $name . '.' . $file_ext;
      move_uploaded_file($file_tmp, "../uploads/" . $dir . "/" . $name . '.' . $file_ext);
      $result = array("status" => 1, "data" => $name . '.' . $file_ext, "msg" => 'success', 'filename' => $file_name, 'path' => $filepath);
   } else {
      $result = array("status" => 0, "data" => $errors, "msg" => 'error');
   }

   return $result;
}



/*selecc
Upload pdf
*/


function upload_pdf($data, $dir)
{
   $name = random_str(25);
   $errors = array();
   $file_name = $data['file']['name'];
   $exten = pathinfo($file_name, PATHINFO_EXTENSION);

   $file_size = $data['file']['size'];
   $file_tmp = $data['file']['tmp_name'];
   $file_type = $data['file']['type'];
   $file_ext = explode('.', $data['file']['name']);
   $file_ext = end($file_ext);
   $file_ext = strtolower($file_ext);

   $extensions = array("pdf");

   if (in_array($file_ext, $extensions) === false) {
      $errors[] = "Please choose a PDF file.";
   }

   if ($file_size > 5097152) {
      $errors[] = 'File size must be less then 2 MB';
   }

   if (empty($errors) == true) {
      $path = "uploads/" . $dir . "/" . $name . '.' . $file_ext;
      move_uploaded_file($file_tmp, "../uploads/" . $dir . "/" . $name . '.' . $file_ext);
      $result = array("status" => 1, "data" => $name . '.' . $file_ext, 'path' => $path, 'filename' => $file_name, "msg" => 'success');
   } else {
      $result = array("status" => 0, "data" => $errors, 'path' => '', "msg" => 'error');
   }

   return $result;
}




/*
mailer
*/


function test_mailer($data)
{
   require("../var.php");

   require("plugins/PHPMailer/src/Exception.php");
   require("plugins/PHPMailer/src/PHPMailer.php");
   require("plugins/PHPMailer/src/SMTP.php");

   $mail = new PHPMailer();
   $mail->IsSMTP();
   $mail->Mailer = "smtp";
   $mail->Host = trim($data['host']);
   $mail->Port = trim($data['port']);
   //$mail->SMTPDebug = 2;
   $mail->SMTPAuth = true;
   $mail->SMTPSecure = trim($data['encryptt']);
   $mail->Username = trim($data['email']);
   $mail->Password = trim($data['pass']);
   $mail->setFrom(trim($data['email']));
   $mail->isHTML(true);
   $mail->Subject = "Hi!";
   $mail->Body = "Hi! This is for Config";
   $mail->addAddress('lakshay@innerxcrm.com');
   return $mail->Send();
}


function mailer($data)
{
   require("../var.php");
   require("plugins/mailer/class.phpmailer.php");
   foreach ($data['to'] as $to) {
      $maili = new PHPMailer();
      $maili->IsSMTP();
      $maili->Host = $email_host;
      $maili->SMTPAuth = true;
      $maili->Port = 25;
      $maili->Username = $email_username;
      $maili->Password = $email_password;
      $maili->From = $email_username;
      $maili->FromName = $Website;
      $maili->AddAddress($to);
      if (!empty($data['attachment'])) {
         $maili->AddAttachment($data['attachment']);
      }
      $maili->IsHTML(true);
      $maili->Subject = $data['subject'];
      $maili->Body = $data['content'];
      $response = $maili->Send();
      logMessage('Mail is sending with params' . json_encode($data));
      logMessage('and Response' . $response);
      return $response;
   }
}

function test2_mailer($data)
{
   require("../var.php");

   require("plugins/PHPMailer/src/Exception.php");
   require("plugins/PHPMailer/src/PHPMailer.php");
   require("plugins/PHPMailer/src/SMTP.php");

   $maili = new PHPMailer();
   $maili->IsSMTP();
   $maili->Host = $email_host;
   $maili->SMTPAuth = true;
   $maili->Port = 25;
   $maili->Username = $email_username;
   $maili->Password = $email_password;
   $maili->From = $email_username;
   $maili->FromName = $Website;
   $maili->AddAddress($data['to']);
   $maili->Subject = $data['subject'];
   $maili->Body = $data['content'];
   logMessage('Mail is sending with params' . json_encode($data));
   $maili->Send();
}


function reg_mail($data)
{
   require("../var.php");

   require("plugins/PHPMailer/src/Exception.php");
   require("plugins/PHPMailer/src/PHPMailer.php");
   require("plugins/PHPMailer/src/SMTP.php");

   foreach ($data['to'] as $to) {
      $maili = new PHPMailer();
      $maili->IsSMTP();
      $maili->Host = $email_host;
      $maili->SMTPAuth = true;
      $maili->Port = 25;
      $maili->Username = $email_username;
      $maili->Password = $email_password;
      $maili->From = $email_username;
      $maili->FromName = $Website;
      $maili->addAddress($to['email']);

      if (!empty($data['attachment'])) {
         $maili->addAttachment($data['attachment']);
      }
      $maili->isHTML(true);
      $maili->Subject = $data['subject'];
      $maili->Body = $to['content'];
      return $maili->Send();
   }
}



 

function getIPDetails($ip) {
   $url = "https://ipinfo.io/{$ip}/json";
   $response = file_get_contents($url);
   return $response;
}

/*
Dialer
*/
function getClientIP() {
   if (isset($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
       return $_SERVER['HTTP_CLIENT_IP'];
   } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
       return $_SERVER['HTTP_X_FORWARDED_FOR'];
   } elseif (isset($_SERVER['REMOTE_ADDR']) && filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP)) {
       return $_SERVER['REMOTE_ADDR'];
   }
   return 'UNKNOWN'; // Default value if no valid IP address is found
}

function dialer($campaign, $agent, $phone)
{
   require("../var.php");
   $dialer_api = str_replace("{{campaign}}", $campaign, $dialer_api);
   $dialer_api = str_replace("{{agent}}", $agent, $dialer_api);
   $dialer_api = str_replace("{{phone}}", $phone, $dialer_api);
   //echo $dialer_api;
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $dialer_api);
   curl_setopt($ch, CURLOPT_HEADER, 0);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   $result = curl_exec($ch);
   curl_close($ch);
   return $result;
}

/**
 * third join $foreign_table_child
 */
// Query Foreign Table
function query_foreign_table(
   string $main_table,
   string $foreign_key,
   string $foreign_table,
   string|array $columns = "*",
   string|array $order_by = null,
   string|array $where = null,
   string $third_join_table = null,
   string $third_join_table_key = null,
) {
   //include db file 
   include('../_con.php');

   // Build the SQL query string
   $query = "SELECT ";
   // Add the columns to select
   if (is_array($columns)) {
      foreach ($columns as $column) {
         if ($column === end($columns)) {
            $query .= $column . " ";
         } else {
            $query .= $column . ",";
         }
      }
   } else {
      $query .= $main_table . ".*, " . $foreign_table . ".* ";
      if ($third_join_table) {
         $query .= "," . $third_join_table . ".*";
      }
   }

   $query .= " FROM " . $main_table . " LEFT JOIN " . $foreign_table . " ON " . $main_table . "." . $foreign_key . " = " . $foreign_table . ".id ";
   if ($third_join_table) {
      $query .= " LEFT JOIN " . $third_join_table . " ON " . $foreign_table . "." . $third_join_table_key . " = " . $third_join_table . ".id";
   }
   if (!empty($where)) {
      $where_conditions = array_map(function ($w) use ($main_table) {
         return $w;
      }, $where);
      $query .= " WHERE " . join(" AND ", $where_conditions);
   }

   // Add the order by clause
   if (is_array($order_by)) {
      $query .= " ORDER BY " . implode(", ", array_map(function ($o) use ($main_table, $foreign_table) {
         if (strpos($o, ".") === false) {
            return $main_table . "." . $o . " ASC";
         } else {
            return $o . " ASC";
         }
      }, $order_by));
   } else if (!empty($order_by)) {
      $query .= " ORDER BY " . $order_by . " ASC";
   }
   // Prepare and execute the query
   $stmt = $con->prepare($query);
   // print_r($stmt);
   // die;
   $stmt->execute();

   $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
   return $results;
}
function select(
   string $table,
   string $columns = "*",
   string $where = "",
   string $orderBy = "",
   string $limit = "",
   bool $debug = false
) {
   include('../_con.php');

   $sql = "SELECT $columns FROM $table";

   if (!empty($where)) {
      $sql .= " $where";
   }

   if (!empty($orderBy)) {
      $sql .= " $orderBy";
   }

   if (!empty($limit)) {
      $sql .= " $limit";
   }
   if (!empty($debug)) {
      echo $sql;
      die;
   }
   $stmt = $con->prepare($sql);
   $stmt->execute();

   return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function notify(
   int $userid,
   string $message,
   string $icon,
   string $url = null,
   string $tag = null,
): bool {
   $notification_data = array(
      'user_id' => $userid,
      "message" => $message,
      // the message content of the notification
      "icon" => $icon,
      // the name of the icon to display with the notification
      "url" => $url,
      "tag" => $tag,
      "created_at" => date('Y-m-d H:i:s'),
      // the current datetime for the created_at field
      "updated_at" => date('Y-m-d H:i:s') // the current datetime for the updated_at field
   );
   $qinsert_param = array(
      "tb" => "notifications"
   );
   $result = qinsert($qinsert_param, $notification_data);
   if ($result['status']) {
      return true;
   } else {
      return false;
   }
}

function adminId(): int
{
   $res = select('cp_users', 'id', 'WHERE role=1', '', 'LIMIT 1');
   $response = array_shift($res);
   return $response['id'];
}
/**
 * Select User First Name by ID
 */
function userName(int $id): string
{
   $userid = $id;
   $result = select('cp_users', 'fname', "WHERE id=$userid", '', 'LIMIT 1');

   if ($result && count($result) > 0) {
      $username = $result[0]['fname'];
      return $username;
   } else {
      return 'User';
   }
}

function getLeadName(
   int $id
): string {
   $result = select('cp_leads', 'name', "WHERE id=$id", '', 'LIMIT 1');
   $username = array_shift($result);
   return $username['name'] ?? 'Lead';
}
function getAdminId()
{
   $result = select('cp_users', 'id', "WHERE role=1", '', 'LIMIT 1');
   $admin = array_shift($result);
   return $admin['id'];
}
function dd($data)
{
   echo '<pre>';
   print_r($data);
   echo '<pre>';
   die;
}

function update(
   string $table,
   array $data,
   string $condition,
   $debug = false
) {
   include('../_con.php');
   // Build the UPDATE query
   $updateQuery = "UPDATE $table SET ";

   // Prepare the update values
   $updateValues = array();
   foreach ($data as $column => $value) {
      $updateValues[] = "$column = '$value'";
   }

   $updateQuery .= implode(", ", $updateValues);
   $updateQuery .= " WHERE $condition";
   if ($debug) {
      echo $updateQuery;
      die;
   }
   // Execute the update query
   $result = $con->query($updateQuery);
   $result = $result->rowCount();
   // Return true if the update was successful, false otherwise

   return $result;
}


function getAgreementPdfPath($leadId)
{
   $getPath = select('cp_leads', 'pdf_path', "WHERE id=$leadId", '', 'LIMIT 1');
   $getPath = array_shift($getPath);
   if (!$leadId) {
      throw new \Exception('Cannot Proceed Without Id');
   }
   if (!$getPath['pdf_path']) {
      require_once '../vendor/autoload.php';

      $html = file_get_contents('../view/agreement-pdf.html');

      $datePlaceholder = "{{date}}";
      $orgPlaceholder = "{{organization}}";
      $addressPlaceholder = "{{address}}";
      $companyownerPlaceholder = "{{companyowner}}";
      $organizationPlaceholder = "{{organization}}";
      $companyTypePlaceholder = "{{companytype}}";
      $committed = "{{committed}}";
      $reward = "{{reward}}";
      $creditCard = "{{credit_card}}";
      $benifits = "{{benefits}}";
      $forex = "{{forex}}";
      $other = "{{other}}";
      $phonePlaceholder = "{{phone}}";
      $emailPlaceholder = "{{email}}";
      $image = "{{image}}";
      $committedForex = "{{forexcommitted}}";

      $imagePath = 'https://innerxcrm.com/website/assets/images/logo.png';
      $imageExtension = pathinfo($imagePath, PATHINFO_EXTENSION);
      // Read image file
      $imageData = file_get_contents($imagePath);
      // Encode image data to base64
      $base64Image = base64_encode($imageData);
      // Generate the data URI
      $dataUri = 'data:image/' . $imageExtension . ';base64,' . $base64Image;
      $getLeadDate = select('cp_leads', '*', "WHERE id=$leadId", '', 'LIMIT 1');
      $leadData = array_shift($getLeadDate);

      $data = [
         $datePlaceholder => date('Y-m-d'),
         $orgPlaceholder => $leadData['organization'] ?? '',
         $addressPlaceholder => $leadData['address'] ?? '',
         $companyownerPlaceholder => $leadData['companyowner'] ?? '',
         $organizationPlaceholder => $leadData['organization'] ?? '',
         $companyTypePlaceholder => $leadData['companytype'] ?? '',
         $committed => $leadData['committed'] ?? '',
         $reward => $leadData['reward'] ?? '',
         $creditCard => $leadData['credit_card'] ?? '',
         $benifits => $leadData['benefits'] ?? '',
         $forex => $leadData['forex'] ?? '',
         $other => $leadData['other'] ?? '',
         $phonePlaceholder => $leadData['phone'] ?? '',
         $emailPlaceholder => $leadData['email'] ?? '',
         $image => $dataUri,
         $committedForex => $leadData['number_of_forex_commited'] ?? 0
      ];
      foreach ($data as $placeholder => $replacement) {
         if ($placeholder == $committedForex) {
            $replacement = number_format($replacement);
         }
         $html = str_replace($placeholder, $replacement, $html);
      }
      // Instantiate Dompdf
      $dompdf = new Dompdf\Dompdf();

      $dompdf->loadHtml($html);
      $dompdf->setPaper('A4', 'portrait');
      $dompdf->setPaper(array(0, 0, 1200, 1200)); // Width: 600mm, Height: 800mm
      $dompdf->render();
      $orgname = str_replace(' ', '_', $leadData['organization']);

      $filename = 'agreement' . '-' . uniqid() . '.pdf';
      $folder = '../storage/agreements/';
      $filePath = $folder . $filename;

      // Save the PDF to a file
      $pdfOutput = $dompdf->output();

      file_put_contents($filePath, $pdfOutput);
      update('cp_leads', ['pdf_path' => "/storage/agreements/$filename"], "id=$leadId");
      // Return the file path or do something else with it
      return $filePath;
   } else {
      return '..' . $getPath['pdf_path'];
   }
}

// $param = array(
//     'col' => 'f.*, l.*, u.username',
//     'tb' => 'cp_followups AS f',
//     'join' => array(
//         array(
//             'table' => 'cp_leads AS l',
//             'condition' => 'f.lead_id = l.id'
//         ),
//         array(
//             'table' => 'users AS u',
//             'condition' => 'f.user_id = u.id'
//         )
//     ),
//     'where' => 'WHERE f.some_column = 1',
//     'sort' => 'ORDER BY f.date DESC',
//     'limit' => '0, 10',
//     'pagination' => 10
// );

function joinSelect($param)
{

   if (!isset($param['sort'])) {
      $param['sort'] = '';
   }
   include('../_con.php');
   $q = 'SELECT SQL_CALC_FOUND_ROWS ' . $param['col'] . ' FROM ' . $param['tb'];
   if (isset($param['join'])) {
      foreach ($param['join'] as $join) {
         $joinType = isset($join['type']) ? $join['type'] : 'INNER JOIN';
         $joinTable = $join['table'];
         $joinCondition = $join['condition'];
         $q .= " $joinType $joinTable ON $joinCondition";
      }
   }
   $q .= ' ' . $param['where'] . ' ' . $param['sort'];
   if (!empty($param['group'])) {
      $q .= ' GROUP BY ' . $param['group'];
   }
   if (!empty($param['limit'])) {
      $q .= ' LIMIT ' . $param['limit'];
   }
   if (isset($param['debug'])) {
      echo $q;
      exit;
   }

   // Execute the query
   $stmt = $con->query($q);
   $count = $stmt->rowCount();

   // Fetch the data
   $data = ($param['limit'] == 1) ? $stmt->fetch(PDO::FETCH_ASSOC) : $stmt->fetchAll(PDO::FETCH_ASSOC);

   // Get the total count of rows
   $stmt = $con->query('SELECT FOUND_ROWS()');
   $totalcount = $stmt->fetchColumn();
   $pagination = '';
   if (!empty($param['limit'])) {
      require_once("pagination.class.php");
      $perPage = new PerPage();

      $pagination = $perPage->getAllPageLinks($totalcount, '', (int) substr($param['limit'], strpos($param['limit'], ",") + 1), $param['pagination']);
   }

   $result = array(
      "status" => 1,
      "msg" => "success",
      "data" => $data,
      "count" => $count,
      "total" => $totalcount,
      "pagination" => $pagination,
      "q" => $q
   );

   return $result;
}

function logMessage($message)
{
   $logDirectory = 'logs';
   $logFile = $logDirectory . '/' . date('Y-m-d') . '.log';
   if (!is_dir($logDirectory)) {
      mkdir($logDirectory, 0777, true);
   }

   if (!file_exists($logFile)) {
      touch($logFile);
   }

   $logMessage = date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL;

   file_put_contents($logFile, $logMessage, FILE_APPEND);
}

if (!function_exists('captureLeadActivity')) {
   function captureLeadActivity(int $leadId, string $description, string $tag, string $url = '')
   {
      $leadActivity = new LeadActivityController();

      $result = $leadActivity->setActivity([
         'leadId' => $leadId,
         'description' => $description,
         'tag' => $tag,
         'url' => $url
      ]);
      return $result['id'];
   }
}
