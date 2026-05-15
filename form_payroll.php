<!DOCTYPE html>
<html>
<head>
    <title>Employee Payroll</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }
        h2 {
            color: #333;
        }
        .employee {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .employee h3 {
            margin-top: 0;
        }
        label {
            display: inline-block;
            width: 120px;
        }
        input[type="text"], input[type="number"] {
            width: 200px;
            padding: 5px;
            margin-bottom: 5px;
        }
        input[type="submit"] {
            padding: 10px 20px;
            background-color: #0066cc;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
        input[type="submit"]:hover {
            background-color: #004a99;
        }
        #addBtn {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        #addBtn:hover {
            background-color: #1e7e34;
        }
        .removeBtn {
            padding: 5px 12px;
            background-color: #dc3545;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            margin-top: 5px;
        }
        .removeBtn:hover {
            background-color: #a71d2a;
        }
    </style>
</head>
<body>

<h2>Employee Payroll Form</h2>

<form action="payroll.php" method="post">

    <div id="employeeContainer">
        <div class="employee">
            <h3>Employee 1</h3>
            <label>Name:</label>
            <input type="text" name="name[]" required><br>
            <label>Hours Worked:</label>
            <input type="number" name="hours[]" min="0" step="0.1" required><br>
        </div>
    </div>

    <button type="button" id="addBtn" onclick="addEmployee()">+ Add Employee</button><br><br>

    <input type="submit" value="Calculate Payroll">

</form>

<script>
    let employeeCount = 1;

    function addEmployee() {
        employeeCount++;
        const container = document.getElementById('employeeContainer');

        const div = document.createElement('div');
        div.className = 'employee';
        div.id = 'employee' + employeeCount;

        div.innerHTML = `
            <h3>Employee ${employeeCount}</h3>
            <label>Name:</label>
            <input type="text" name="name[]" required><br>
            <label>Hours Worked:</label>
            <input type="number" name="hours[]" min="0" step="0.1" required><br>
            <button type="button" class="removeBtn" onclick="removeEmployee('employee${employeeCount}')">Remove</button>
        `;

        container.appendChild(div);
    }

    function removeEmployee(id) {
        const el = document.getElementById(id);
        if (el) {
            el.remove();
            renumberEmployees();
        }
    }

    function renumberEmployees() {
        const employees = document.querySelectorAll('.employee');
        employees.forEach((emp, index) => {
            emp.querySelector('h3').textContent = 'Employee ' + (index + 1);
        });
        employeeCount = employees.length;
    }
</script>

</body>
</html>
