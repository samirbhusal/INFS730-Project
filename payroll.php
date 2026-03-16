
<!DOCTYPE html>
<html>
<head>
    <title>Employee Payroll</title>
</head>
<body>

<h2>Employee Payroll Calculator</h2>

<?php
$rate = 18; // $18 per hour

if (isset($_POST['name']) && isset($_POST['hours'])) {

    $names = $_POST['name'];   // array of employee names from form
    $hours = $_POST['hours'];  // array of hours worked from form

    $totalPayroll = 0;

    for ($i = 0; $i < count($names); $i++) {
        if ($names[$i] != "" && $hours[$i] != "") {
            $pay = $hours[$i] * $rate;
            $totalPayroll += $pay;

            echo "Employee: " . htmlspecialchars($names[$i]) . "<br>";
            echo "Hours Worked: " . $hours[$i] . "<br>";
            echo "Pay: $" . $pay . "<br><br>";
        }
    }

    echo "<h3>Total Payroll: $" . $totalPayroll . "</h3>";

} else {
    echo "Please submit the form first.";
}
?>