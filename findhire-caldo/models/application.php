<?php
require_once("./core/database.php");

class Application extends Database {

    public function getApplicationsByJobPostId($jobPostId) {
        try {
            $dbh = $this->connect();
            $stmt = $dbh->prepare("SELECT a.ApplicationID, u.Username, a.CoverLetter, a.ResumePath, a.Status, a.UpdatedAt 
                                   FROM Applications a
                                   JOIN Users u ON a.ApplicantID = u.UserID
                                   WHERE a.JobPostID = :jobPostId");
            $stmt->bindParam(':jobPostId', $jobPostId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error fetching applications: " . $e->getMessage();
            return [];
        }
    }

    public function acceptApplication($applicationId) {
        try {
            $dbh = $this->connect();
            $stmt = $dbh->prepare("UPDATE Applications SET Status = 'Accepted', UpdatedAt = NOW() WHERE ApplicationID = :applicationId");
            $stmt->bindParam(':applicationId', $applicationId, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            echo "Error accepting application: " . $e->getMessage();
            return false;
        }
    }

    public function rejectApplication($applicationId) {
        try {
            $dbh = $this->connect();
            $stmt = $dbh->prepare("UPDATE Applications SET Status = 'Rejected', UpdatedAt = NOW() WHERE ApplicationID = :applicationId");
            $stmt->bindParam(':applicationId', $applicationId, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            echo "Error rejecting application: " . $e->getMessage();
            return false;
        }
    }

    public function getApplicationDetails($applicationId) {
        try {
            $dbh = $this->connect();
            $stmt = $dbh->prepare("SELECT a.ApplicationID, u.Username, a.CoverLetter, a.ResumePath, a.Status, a.UpdatedAt
                                   FROM Applications a
                                   JOIN Users u ON a.ApplicantID = u.UserID
                                   WHERE a.ApplicationID = :applicationId");
            $stmt->bindParam(':applicationId', $applicationId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error fetching application details: " . $e->getMessage();
            return [];
        }
    }
}