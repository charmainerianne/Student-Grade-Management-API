<?php

namespace Charmaine\StudentGradeApi\Controllers;

use Charmaine\StudentGradeApi\Config\Database;
use PDO;
use PDOException;

class StudentController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function checkStatus() {
        return "Student Controller is Active.";
    }

    public function addStudent($name, $midtermScore, $finalScore) {
        try {
            $sql = "INSERT INTO students (name, midterm_score, final_score) VALUES (:name, :midterm_score, :final_score)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'name' => $name,
                'midterm_score' => $midtermScore,
                'final_score' => $finalScore
            ]);
            return ["message" => "Student added successfully"];
        } catch (PDOException $e) {
            return ["error" => "SQL Error: " . $e->getMessage()];
        }
    }

    public function getAllStudents() {
        $sql = "SELECT * FROM students";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function calculateFinalGrade($midtermScore, $finalScore) {
        return (0.4 * $midtermScore) + (0.6 * $finalScore);
    }

    private function determinePassStatus($finalGrade) {
        return ($finalGrade >= 75) ? "Pass" : "Fail";
    }

    public function getStudentGrade($id) {
        $sql = "SELECT name, midterm_score, final_score FROM students WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$student) {
            return ["error" => "Student not found"];
        }

        $student['final_grade'] = $this->calculateFinalGrade($student['midterm_score'], $student['final_score']);
        $student['status'] = $this->determinePassStatus($student['final_grade']);

        return $student;
    }

    public function updateStudent($id, $midtermScore, $finalScore) {
        try {
            $checkSql = "SELECT id FROM students WHERE id = :id";
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->execute(['id' => $id]);

            if (!$checkStmt->fetch(PDO::FETCH_ASSOC)) {
                return ["error" => "Student not found"];
            }

            $sql = "UPDATE students SET midterm_score = :midterm_score, final_score = :final_score WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'id' => $id,
                'midterm_score' => $midtermScore,
                'final_score' => $finalScore
            ]);

            return ($stmt->rowCount() > 0) 
                ? ["message" => "Student updated successfully"] 
                : ["error" => "No changes made"];
        } catch (PDOException $e) {
            return ["error" => "SQL Error: " . $e->getMessage()];
        }
    }

    public function deleteStudent($id) {
        try {
            $sql = "DELETE FROM students WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);

            return ($stmt->rowCount() > 0) 
                ? ["message" => "Student deleted successfully"] 
                : ["error" => "Student not found"];
        } catch (PDOException $e) {
            return ["error" => "SQL Error: " . $e->getMessage()];
        }
    }

    public function getAllStudentGrades() {
        $sql = "SELECT id, name, midterm_score, final_score FROM students";
        $stmt = $this->db->query($sql);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($students as &$student) {
            $student['final_grade'] = $this->calculateFinalGrade($student['midterm_score'], $student['final_score']);
            $student['status'] = $this->determinePassStatus($student['final_grade']);
        }

        return $students;
    }
}
