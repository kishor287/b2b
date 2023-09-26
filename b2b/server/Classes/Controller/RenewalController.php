<?php

namespace Panel\Server\Classes\Controller;

use Panel\Server\Classes\Controller\BaseController;

class RenewalController extends BaseController
{

    protected string $table;
    public function __construct()
    {
        parent::__construct();
        $this->table = 'cp_lead_payments';
    }

    
    public function show($supportId = '', $marketingId = '',$page = 1, $limit = 10)
    {
        $condition = '';
        if ($supportId) {
            $condition .= ' AND l.user_id="' . $supportId . '"';
        }
        if ($marketingId) {
            $condition .= ' AND l.marketing_id="' . $marketingId . '"';
        }
        $renewalDate = date('Y-m-d', strtotime('+15 days'));
        $subquery = "SELECT 1 FROM {$this->table} AS lp2 WHERE lp2.lead_id = lp.lead_id AND lp2.to_date > lp.to_date";
        $param = [
            'tb' => "{$this->table} as lp",
            'where' => "where DATE(lp.to_date) <= '$renewalDate' AND NOT EXISTS ($subquery) $condition",
            'col' => 'pp.title as planName,lp.*,l.organization,l.companyowner,l.organization_phone,l.marketing_id,l.user_id,u.fname,u.lname,un.fname as marketingPersonFname,un.lname as marketingPersonLname,l.agreement_signed_at',
            'pagination' => $page,
            // 'sort' => ' order by lp.to_date desc',
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
    public function showForSupport($supportId,$page=1,$limit=10,)
    {
        return $this->show($supportId,'',$page,$limit);
    }
    
    public function showForMarketing($marketingId,$page,$limit)
    {
        return $this->show('', $marketingId,$page,$limit);
    }
    
}
