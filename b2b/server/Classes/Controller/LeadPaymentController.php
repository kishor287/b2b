<?php

namespace Panel\Server\Classes\Controller;

require_once('Classes/Controller/BaseController.php');

use Carbon\Carbon;
use Panel\Server\Classes\Config\Response;
use Panel\Server\Classes\Controller\BaseController;

class LeadPaymentController extends BaseController
{

  public function __construct(
    protected string $table = 'cp_lead_payments'
  ) {

    parent::__construct();
    $this->table = $table;
  }

  /**
   * @param array $data
   * Required: [plan_id,lead_id,amount,given_discount,payment_mode,payment_type,reciept_attachment,reciept_name,from_date,to_date]
   * Optional: [remarks]
   */
  public function store(array $data)
  {

    $leadId = $data['leadId'];
    $today = Carbon::today();
    $paymentType = strtoupper($data['paymentType']);

    $getLastRenewDate = select($this->table, 'to_date', " where lead_id='$leadId'", ' ORDER BY id DESC LIMIT 1');
    $existedfromDate = !empty($getLastRenewDate[0]['to_date']) ? $getLastRenewDate[0]['to_date'] : '';

    if ($paymentType == 'QUARTERLY') {
      $fromDate = $today->format('Y-m-d');
      $toDate = $today->copy()->addMonths(3)->format('Y-m-d');
      if (!empty($existedfromDate)) {
        $fromDate = Carbon::parse($existedfromDate)->addDay()->format('Y-m-d');
        $toDate = Carbon::parse($existedfromDate)->copy()->addMonths(3)->format('Y-m-d');
      }
    } else {
      $fromDate = $today->format('Y-m-d');
      $toDate = $today->copy()->addMonths(12)->format('Y-m-d');
      if (!empty($existedfromDate)) {
        $fromDate = Carbon::parse($existedfromDate)->addDay()->format('Y-m-d');
        $toDate = Carbon::parse($existedfromDate)->copy()->addMonths(12)->format('Y-m-d');
      }
    }

    if (empty($data['receiptImage']['name'])) {
      return Response::error('Reciept Required');
    } else {
      $reciept = $data['receiptImage'];
      $img = storeImg(['image' => $reciept], 'payment-attachments');
      if ($img['status'] = 1) {
        $recieptName = $img['filename'];
        $recieptPath = $img['path'];
      } else {
        return Response::error('Reciept Image has some errors, Please try again');
      }
    }

    try {
      qinsert(
        ['tb' => ltrim($this->table, 'cp_')],
        [
          'lead_id' => $data['leadId'],
          'plan_id' => $data['planId'],
          'amount' => $data['amountPaid'],
          'given_discount' => $data['givenDiscount'],
          'payment_mode' => $data['paymentMode'],
          'payment_type' => strtoupper($paymentType),
          'reciept_attachment' => $recieptPath,
          'reciept_name' => $recieptName,
          'from_date' => $fromDate,
          'to_date' => $toDate,
          'remarks' => !isset($data['remarks']) ? "" : $data['remarks'],
        ]
      );
      return $this->connection->lastInsertId();
    } catch (\Throwable $th) {
      return 0;
    }
  }

  public function show($page = 1, $limit = 10)
  {
    // $renewalDate = date('Y-m-d', strtotime('+15 days'));
    $param = [
      'tb' => "{$this->table} as lp",
      'where' => " ",
      // 'where' => "where DATE(lp.to_date) <= '$renewalDate'",
      'col' => 'pp.*,lp.*,l.organization,l.companyowner,l.organization_phone,l.marketing_id,l.user_id,u.fname,u.lname,un.fname as marketingPersonFname,un.lname as marketingPersonLname,l.agreement_signed_at',
      'pagination' => $page,
      'sort' => ' order by lp.id desc',
      'group' => '',
      'limit' => (($page - 1) * $limit) . "," . $limit,
      'join' => [
        [
          'table' => 'cp_leads l',
          'condition' => 'lp.lead_id = l.id'
        ],
        [
          'type' => 'LEFT JOIN',
          'table' => 'cp_users u',
          'condition' => 'l.user_id = u.id'
        ],
        [
          'type' => 'LEFT JOIN ',
          'table' => 'cp_users un',
          'condition' => 'l.marketing_id = un.id'
        ],
        [
          'table' => 'cp_pricing_plans pp',
          'condition' => 'lp.plan_id = pp.id'
        ],
      ],
      // 'debug' => true
    ];
    $res = joinSelect($param);
    return $res;
  }
}
