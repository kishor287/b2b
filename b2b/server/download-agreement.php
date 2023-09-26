<?php 
// ini_set('display_errors',1);
// ini_set('allow_url_fopen',1);

require_once '../vendor/autoload.php';
include('function.php');
if (isset($_GET['download'])) {
  $leadId = $_GET['download'];
  if(!$leadId){
    throw new \Exception('Cannot Proceed Without Id');
  }
  $html = file_get_contents('../view/agreement-pdf.html');

  $datePlaceholder = "{{date}}";
  $orgPlaceholder = "{{organization}}";
  $addressPlaceholder  = "{{address}}";
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
  // Replace placeholders with data
  // Get the image file extension
  $imagePath = 'https://innerxcrm.com/website/assets/images/logo.png';
  $imageExtension = pathinfo($imagePath, PATHINFO_EXTENSION);
  // Read image file
  $imageData = file_get_contents($imagePath);
  // Encode image data to base64
  $base64Image = base64_encode($imageData);
  // Generate the data URI
  $dataUri = 'data:image/' . $imageExtension . ';base64,' . $base64Image;
  $getLeadDate = select('cp_leads','*',"WHERE id=$leadId",'','LIMIT 1');
  $leadData = array_shift($getLeadDate);

  $data = [
    $datePlaceholder => date('Y-m-d'),
    $orgPlaceholder => $leadData['organization'] ?? '',
    $addressPlaceholder => $leadData['address'] ?? '',
    $companyownerPlaceholder => $leadData['companyowner'] ?? '',
    $organizationPlaceholder => $leadData['organization'] ?? '',
    $companyTypePlaceholder => $leadData['companytype'] ?? '',
    $committed =>  $leadData['committed'] ?? '',
    $reward =>  $leadData['reward'] ?? '',
    $creditCard =>  $leadData['credit_card'] ?? '',
    $benifits =>  $leadData['benefits'] ?? '',
    $forex => $leadData['forex'] ?? '',
    $other =>  $leadData['other'] ?? '',
    $phonePlaceholder =>  $leadData['phone'] ?? '',
    $emailPlaceholder =>  $leadData['email'] ?? '',
    $image => $dataUri,
    $committedForex => $leadData['number_of_forex_commited'] ?? 0

  ];
  foreach ($data as $placeholder => $replacement) {
    $html = str_replace($placeholder, $replacement, $html);
  }
  // Instantiate Dompdf
  $dompdf = new Dompdf\Dompdf();
  $dompdf->loadHtml($html);
  $dompdf->setPaper('A4','portrait');
  $dompdf->setPaper(array(0, 0, 1200, 1130)); // Width: 600mm, Height: 800mm
  $dompdf->render();

  $filename = 'generated_pdf_' . uniqid() . '.pdf';

  header('Content-Type: application/pdf');
  header('Content-Disposition: attachment; filename="' . $filename . '"');

  echo $dompdf->output();
}
