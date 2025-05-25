<?php

class Notification {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function send($userId, $message, $type = 'info') {
        $query = "INSERT INTO notifications (user_id, message, type, created_at) 
                 VALUES (:user_id, :message, :type, NOW())";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':type', $type);
        
        return $stmt->execute();
    }

    public function getUnread($userId) {
        $query = "SELECT * FROM notifications 
                 WHERE user_id = :user_id AND read_at IS NULL 
                 ORDER BY created_at DESC";
                 
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAsRead($notificationId) {
        $query = "UPDATE notifications 
                 SET read_at = NOW() 
                 WHERE id = :id";
                 
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $notificationId);
        
        return $stmt->execute();
    }

    public function markAllAsRead($userId) {
        $query = "UPDATE notifications 
                 SET read_at = NOW() 
                 WHERE user_id = :user_id AND read_at IS NULL";
                 
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        
        return $stmt->execute();
    }
}
