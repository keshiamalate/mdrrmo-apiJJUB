<?php
/**
 * POST /api/incidents/create.php
 * Fields (JSON or multipart/form-data):
 *   emergency_type, description, persons_affected, latitude, longitude,
 *   gps_accuracy (optional), photo (optional file, multipart only)
 * Success: 201 {"success":true,"message":"...","incident_id":N}
 * Failure: 4xx/5xx {"success":false,"message":"...","errors":{...}}
 */
require_once __DIR__ . '/../../../src/database.php';

send_cors_headers();
handle_preflight();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(405, ['success' => false, 'message' => 'Use POST to submit a report.']);
}

[$clean, $errors] = validate_incident(read_input());
[$photo, $photoError] = read_photo();
if ($photoError) {
    $errors['photo'] = $photoError;
}

if ($errors) {
    json_response(422, ['success' => false, 'message' => reset($errors), 'errors' => $errors]);
}

try {
    $id = insert_incident($clean, $photo);
} catch (Throwable $e) {
    error_log('create.php: ' . $e->getMessage());
    json_response(500, ['success' => false, 'message' => 'The server could not save the report.']);
}

json_response(201, [
    'success'     => true,
    'message'     => "Your report has been saved. Reference #$id.",
    'incident_id' => $id,
]);
