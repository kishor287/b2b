<?php

namespace Panel\Server\Classes\Controller;

require_once('Classes/Controller/BaseController.php');

use Exception;
use PDO;
use Panel\Server\Classes\Controller\BaseController;

class LeadActivityController extends BaseController
{

    public function __construct(){
        parent::__construct();
    }
    public function getActivities(int $leadId): array
    {
        $query = "SELECT * FROM cp_lead_activity_timeline WHERE lead_id=:lead_id";
        $readyQuery = $this->connection->prepare($query);
        $readyQuery->bindValue(':lead_id', $leadId, PDO::PARAM_INT);
        $readyQuery->execute();
        $result = $readyQuery->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function setActivity(array $data) {
        $leadId = $data['leadId'];
        $userId = $_SESSION['uId'];
        $tag = $data['tag'];
        $description = $data['description'];
        $url = $data['url'];
        
        $query = "INSERT INTO cp_lead_activity_timeline (lead_id, user_id, tag,description, url) VALUES (:lead_id, :user_id, :tag,:description, :url)";
    
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(':lead_id', $leadId, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':tag', $tag, PDO::PARAM_STR);
            $stmt->bindValue(':description', $description, PDO::PARAM_STR);
            $stmt->bindValue(':url', $url, PDO::PARAM_STR);
            $stmt->execute();
            return ['id' => $this->connection->lastInsertId()];
        } catch (Exception $e) {
            // Handle the error as needed
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    

}