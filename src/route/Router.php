<?php

namespace Charmaine\StudentGradeApi\Route;

require_once __DIR__ . '/../../vendor/autoload.php';

use Charmaine\StudentGradeApi\Controllers\StudentController;

header("Content-Type: application/json");

$studentController = new StudentController();

$requestMethod = $_SERVER['REQUEST_METHOD'];

$requestPath = isset($_GET['url']) ? explode("/", trim($_GET['url'], "/")) : [];

if (!isset($requestPath[0])) {
    sendResponse(["message" => "Invalid request."], 400);
    exit;
}

$endpoint = $requestPath[0];

$id = isset($requestPath[1]) ? $requestPath[1] : null;

switch ($requestMethod) {
    case "POST":
        if ($endpoint === "addStudent") {
            $requestData = getJsonRequestBody();
            sendResponse($studentController->addStudent($requestData['name'], $requestData['midtermScore'], $requestData['finalScore']));
        }
        break;

    case "GET":
        if ($endpoint === "getAllStudents") {
            sendResponse($studentController->getAllStudents());
        } elseif ($endpoint === "getStudentGrade" && $id) {
            sendResponse($studentController->getStudentGrade($id));
        } elseif ($endpoint === "getAllStudentGrades") {
            sendResponse($studentController->getAllStudentGrades());
        }
        break;

    case "PUT":
        if ($endpoint === "updateStudent" && $id) {
            $requestData = getJsonRequestBody();
            sendResponse($studentController->updateStudent($id, $requestData['midtermScore'], $requestData['finalScore']));
        }
        break;

    case "DELETE":
        if ($endpoint === "deleteStudent" && $id) {
            sendResponse($studentController->deleteStudent($id));
        }
        break;

    default:
        sendResponse(["message" => "Invalid request method."], 405);
}

/**
 * @return array
 */
function getJsonRequestBody() {
    $jsonData = file_get_contents("php://input");
    return json_decode($jsonData, true) ?? [];
}

/**
 * @param mixed 
 * @param int
 */
function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}
