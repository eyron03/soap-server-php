

<?php
// Setting up error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create SOAP client
$client = new SoapClient('http://localhost/php-create-soap-service/ctof.wsdl');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    try {
        if ($action == 'create') {
            // Create Employee
            $name = $_POST['name'];
            $email = $_POST['email'];
            $position = $_POST['position'];
            $response = $client->createEmployee($name, $email, $position);
            echo json_encode(['message' => $response]);
        } elseif ($action == 'read') {
            // Read all Employees
            $response = $client->readAllEmployees();
            echo json_encode($response);
        } elseif ($action == 'update') {
            // Update Employee
            $id = $_POST['id'];
            $name = $_POST['name'];
            $email = $_POST['email'];
            $position = $_POST['position'];
            $response = $client->updateEmployee($id, $name, $email, $position);
            echo json_encode(['message' => $response]);
        } elseif ($action == 'delete') {
            // Delete Employee
            $id = $_POST['id'];
            $response = $client->deleteEmployee($id);
            echo json_encode(['message' => $response]);
        }
    } catch (SoapFault $sf) {
        echo json_encode(['error' => $sf->getMessage()]);
    }
    exit; 
}

$pdo = new PDO('mysql:host=localhost;dbname=employee', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Fetch all employees from the database
$stmt = $pdo->prepare("SELECT * FROM employees");
$stmt->execute();
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Employee Management</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            margin: 20px;
            background-color: #f8f9fa;
        }
        .table-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
<?php 

try {
    $x = 5; // Temperature in Celsius
    $response = $client->celsiusToFahrenheit($x); // Convert to Fahrenheit
    ?>

    <div class="container text-center mt-5">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title">Convert <?php echo $x; ?> Degrees Celsius to Fahrenheit</h2>
                <h4 class="card-text"><?php echo $x; ?> Degrees Celsius is equivalent to <?php echo $response; ?> Degrees Fahrenheit</h4>
            </div>
        </div>
    </div>

    <?php
} catch (SoapFault $sf) {
    ?>

    <div class="container text-center mt-5">
        <div class="alert alert-danger" role="alert">
            <strong>Error:</strong> <?php echo $sf->getMessage(); ?>
        </div>
    </div>

    <?php
}
?>

<br>

<div class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="row justify-content-center w-100">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center">
                    <h3>Employees Information</h3>
    <button class="btn btn-primary" data-toggle="modal" data-target="#addEmployeeModal">Add Employee</button>
    </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered border-success mx-auto text-center">
                            <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Position</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="employeeTableBody">
            <?php if (!empty($employees)): ?>
                <?php foreach ($employees as $employee): ?>
                    <tr id="employee-<?= $employee['id']; ?>">
                        <td><?= htmlspecialchars($employee['id']); ?></td>
                        <td><?= htmlspecialchars($employee['name']); ?></td>
                        <td><?= htmlspecialchars($employee['email']); ?></td>
                        <td><?= htmlspecialchars($employee['position']); ?></td>
                        <td>
                            <button class="btn btn-warning" onclick="showEditEmployee(<?= $employee['id']; ?>, '<?= htmlspecialchars($employee['name']); ?>', '<?= htmlspecialchars($employee['email']); ?>', '<?= htmlspecialchars($employee['position']); ?>')">Edit</button>
                            <button class="btn btn-danger" onclick="deleteEmployee(<?= $employee['id']; ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">No employees found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
                           
    </div>
</div>
    
	
<!-- Add Employee Modal -->
<div class="modal fade" id="addEmployeeModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addEmployeeForm">
                <div class="modal-header">
                    <h5 class="modal-title">Add Employee</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Position</label>
                        <input type="text" class="form-control" name="position" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Employee</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Employee Modal -->
<div class="modal fade" id="editEmployeeModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editEmployeeForm">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Employee</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="editEmployeeId">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" id="editEmployeeName" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" id="editEmployeeEmail" required>
                    </div>
                    <div class="form-group">
                        <label>Position</label>
                        <input type="text" class="form-control" name="position" id="editEmployeePosition" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Employee</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    // Load employees on page load
    loadEmployees();

    // Add employee
    $('#addEmployeeForm').submit(function (e) {
        e.preventDefault();
        $.post('', $(this).serialize() + '&action=create', function (response) {
            response = JSON.parse(response);
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: response.message || 'Employee added successfully!',
            }).then(() => {
                location.reload(); // Reload the page
            });
            $('#addEmployeeModal').modal('hide');
        }).fail(function () {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong!',
            });
        });
    });

    // Load employees function
    function loadEmployees() {
        $.post('', { action: 'read' }, function (response) {
            response = JSON.parse(response);
            let employeeRows = '';
            for (let employee of response) {
                employeeRows += `
                    <tr id="employee-${employee.id}">
                        <td>${employee.id}</td>
                        <td>${employee.name}</td>
                        <td>${employee.email}</td>
                        <td>${employee.position}</td>
                        <td>
                            <button class="btn btn-warning" onclick="showEditEmployee(${employee.id}, '${employee.name}', '${employee.email}', '${employee.position}')">Edit</button>
                            <button class="btn btn-danger" onclick="deleteEmployee(${employee.id})">Delete</button>
                        </td>
                    </tr>`;
            }
            $('#employeeTableBody').html(employeeRows);
        });
    }

    // Show edit employee modal
    window.showEditEmployee = function (id, name, email, position) {
        $('#editEmployeeId').val(id);
        $('#editEmployeeName').val(name);
        $('#editEmployeeEmail').val(email);
        $('#editEmployeePosition').val(position);
        $('#editEmployeeModal').modal('show');
    };

    // Update employee
    $('#editEmployeeForm').submit(function (e) {
        e.preventDefault();
        $.post('', $(this).serialize() + '&action=update', function (response) {
            response = JSON.parse(response);
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: response.message || 'Employee updated successfully!',
            }).then(() => {
                location.reload(); // Reload the page
            });
            $('#editEmployeeModal').modal('hide');
        }).fail(function () {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong!',
            });
        });
    });

    // Delete employee
    window.deleteEmployee = function (id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('', { action: 'delete', id: id }, function (response) {
                    response = JSON.parse(response);
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: response.message || 'Employee deleted successfully!',
                    }).then(() => {
                        location.reload(); // Reload the page
                    });
                }).fail(function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!',
                    });
                });
            }
        });
    };
});
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
