<?php
// === SQL Server Connection ===
$serverName = "localhost"; // use your SSMS server name if different
$connectionInfo = [
    "Database" => "SurveyManagementDB",
    "TrustServerCertificate" => true
];
$conn = sqlsrv_connect($serverName, $connectionInfo);

if(!$conn){
    die("Connection failed! Check server or driver.<br>");
}

// === Get form data ===
$fullname = $_POST['fullname'] ?? '';
$email    = $_POST['email'] ?? '';
$age      = $_POST['age'] ?? null;
$gender   = $_POST['gender'] ?? '';
$city     = $_POST['city'] ?? '';
$q1       = $_POST['q1'] ?? '';
$q2       = $_POST['q2'] ?? '';
$q3       = $_POST['q3'] ?? '';

// === Validate required fields ===
if(empty($fullname) || empty($email)){
    die("Full Name and Email are required!");
}

// === Insert Respondent ===
$sqlRespondent = "INSERT INTO Respondents (FullName, Email, Age, Gender, City)
                  VALUES (?, ?, ?, ?, ?)";
$paramsRespondent = [$fullname, $email, $age, $gender, $city];
$resultRespondent = sqlsrv_query($conn, $sqlRespondent, $paramsRespondent);

if(!$resultRespondent){
    die("Error inserting respondent.<br>" . print_r(sqlsrv_errors(), true));
}

// Get the RespondentID of the newly inserted row
$respondentID = sqlsrv_insert_id($conn);

// === Insert Responses ===
$responses = [
    1 => $q1,
    2 => $q2,
    3 => $q3
];

foreach($responses as $questionID => $answer){
    $sqlResponse = "INSERT INTO Responses (RespondentID, QuestionID, Answer)
                    VALUES (?, ?, ?)";
    $paramsResponse = [$respondentID, $questionID, $answer];
    $resultResponse = sqlsrv_query($conn, $sqlResponse, $paramsResponse);

    if(!$resultResponse){
        die("Error inserting response for question $questionID.<br>" . print_r(sqlsrv_errors(), true));
    }
}

// === Success message ===
echo "<h2>Thank you, $fullname!</h2>";
echo "<p>Your survey has been submitted successfully.</p>";
echo "<a href='survey_form.html'>Go back to the survey</a>";

// === Close connection ===
sqlsrv_close($conn);
?>
