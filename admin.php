<?php
session_start();
include 'db.php'; // Database: lutong_bahay

// Basic Security check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// --- 1. Fetch Admin Info ---
$user_query = mysqli_query($conn, "SELECT username FROM users WHERE id = '$user_id'");
$user_data = mysqli_fetch_assoc($user_query);
$display_name = $user_data['username'] ?? "Admin";

// --- 2. Stats Calculation ---
$total_sales_query = mysqli_query($conn, "SELECT SUM(total_amount) as total FROM orders WHERE status = 'Delivered'");
$total_sales = mysqli_fetch_assoc($total_sales_query)['total'] ?? 0;

$pending_orders_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM orders WHERE status != 'Delivered'");
$pending_count = mysqli_fetch_assoc($pending_orders_query)['count'] ?? 0;

// --- 3. Update Status Logic ---
if(isset($_GET['update_id']) && isset($_GET['new_status'])) {
    $oid = mysqli_real_escape_string($conn, $_GET['update_id']);
    $nst = mysqli_real_escape_string($conn, $_GET['new_status']);
    mysqli_query($conn, "UPDATE orders SET status = '$nst' WHERE id = '$oid'");
    header("Location: admin.php?success=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Lutong Bahay</title>
   
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root { --primary: #8B4513; --accent: #d62828; --bg: #fdf0d5; --sidebar-w: 280px; }
        body { background-color: #f8f9fa; font-family: 'Poppins', sans-serif; overflow-x: hidden; }

        /* Sidebar */
        .admin-sidebar { width: var(--sidebar-w); height: 100vh; background: var(--primary); color: white; position: fixed; padding: 25px; z-index: 1000; }
        .main-content { margin-left: var(--sidebar-w); padding: 40px; min-height: 100vh; }
       
        .nav-link { color: rgba(255,255,255,0.7); margin-bottom: 8px; border-radius: 12px; transition: 0.3s; cursor: pointer; border: none; background: transparent; width: 100%; text-align: left; display: flex; align-items: center; padding: 12px 15px; }
        .nav-link i { width: 25px; font-size: 1.1rem; }
        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.1); color: white; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }

        /* Sections */
        .admin-section { display: none; }
        .admin-section.active { display: block; animation: fadeIn 0.4s ease forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        /* Modern Cards */
        .stat-card { background: white; border-radius: 20px; padding: 25px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.03); height: 100%; transition: 0.3s; }
        .stat-icon { width: 50px; height: 50px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 15px; }

        /* Custom Table */
        .custom-table-card { background: white; border-radius: 20px; padding: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); border: none; }
        .table thead th { border: none; color: #999; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; padding: 20px; }
        .table tbody td { padding: 20px; vertical-align: middle; border-bottom: 1px solid #f8f9fa; font-size: 0.9rem; }

        .status-pill { padding: 6px 14px; border-radius: 50px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; }
        .Pending { background: #fff3cd; color: #856404; }
        .Cooking { background: #cfe2ff; color: #084298; }
        .Delivered { background: #d1e7dd; color: #0f5132; }

        /* Logout Area */
        .settings-container { height: 75vh; display: flex; align-items: center; justify-content: center; }
        .logout-box {
            max-width: 450px;
            width: 100%;
            background: white;
            border-radius: 30px;
            padding: 50px;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.02);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .logout-box:hover { transform: scale(1.03); box-shadow: 0 30px 60px rgba(139, 69, 19, 0.1); }
        .logout-box i { font-size: 5rem; color: var(--primary); margin-bottom: 25px; transition: 0.4s; }

        .settings-area { background: rgba(0,0,0,0.15); border-radius: 15px; padding: 10px; margin-top: 30px; }
    </style>
</head>
<body>

<div class="admin-sidebar">
    <div class="mb-5 px-3">
        <h4 class="fw-bold mb-0 text-white"><i class="fas fa-utensils me-2"></i>Lutong Bahay</h4>
        <div class="badge bg-light text-dark opacity-75 mt-1">ADMIN PANEL</div>
    </div>

    <nav class="nav flex-column">
        <button class="nav-link active" onclick="showSection('dashboard', this)"><i class="fas fa-home"></i> Dashboard</button>
        <button class="nav-link" onclick="showSection('orders', this)"><i class="fas fa-receipt"></i> Orders</button>
        <button class="nav-link" onclick="showSection('customers', this)"><i class="fas fa-user-friends"></i> Customers</button>
       
        <div class="settings-area">
            <button class="nav-link" onclick="showSection('settings', this)"><i class="fas fa-cog"></i> Settings</button>
            <a href="index.php" class="nav-link text-white text-decoration-none"><i class="fas fa-external-link-alt"></i> View Shop</a>
        </div>
    </nav>
</div>

<main class="main-content">
   
    <!-- Dashboard Section -->
    <div id="dashboard" class="admin-section active">
        <div class="mb-5">
            <h2 class="fw-bold">Welcome, <?= htmlspecialchars($display_name) ?></h2>
            <p class="text-muted">Here is what's happening with your restaurant today.</p>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="fas fa-money-bill-wave"></i></div>
                    <span class="text-muted small fw-bold">TOTAL REVENUE</span>
                    <h3 class="fw-bold mb-0">₱<?= number_format($total_sales, 2) ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="fas fa-shopping-cart"></i></div>
                    <span class="text-muted small fw-bold">ACTIVE ORDERS</span>
                    <h3 class="fw-bold mb-0"><?= $pending_count ?> Orders</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="fas fa-store"></i></div>
                    <span class="text-muted small fw-bold">STORE STATUS</span>
                    <h3 class="fw-bold text-success mb-0">ONLINE</h3>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <h5 class="fw-bold mb-4">Sales Analytics (Weekly)</h5>
            <canvas id="salesChart" height="100"></canvas>
        </div>
    </div>

    <!-- Orders Section -->
    <div id="orders" class="admin-section">
        <h3 class="fw-bold mb-4">Live Orders</h3>
        <div class="custom-table-card table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $orders_list = mysqli_query($conn, "SELECT orders.*, users.username FROM orders JOIN users ON orders.user_id = users.id ORDER BY id DESC");
                    while($row = mysqli_fetch_assoc($orders_list)): ?>
                    <tr>
                        <td class="fw-bold text-muted">#<?= $row['id'] ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($row['username']) ?></td>
                        <td>₱<?= number_format($row['total_amount'], 2) ?></td>
                        <td><span class="status-pill <?= $row['status'] ?>"><?= $row['status'] ?></span></td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light rounded-pill px-3 dropdown-toggle" type="button" data-bs-toggle="dropdown">Update</button>
                                <ul class="dropdown-menu border-0 shadow">
                                    <li><a class="dropdown-item" href="?update_id=<?= $row['id'] ?>&new_status=Cooking">Cooking</a></li>
                                    <li><a class="dropdown-item text-success" href="?update_id=<?= $row['id'] ?>&new_status=Delivered">Delivered</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Customers Section -->
    <div id="customers" class="admin-section">
        <h3 class="fw-bold mb-4">Registered Customers</h3>
        <div class="custom-table-card table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr><th>User ID</th><th>Username</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php
                    $users_list = mysqli_query($conn, "SELECT id, username FROM users");
                    while($u = mysqli_fetch_assoc($users_list)): ?>
                    <tr>
                        <td class="text-muted">#<?= $u['id'] ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($u['username']) ?></td>
                        <td><button class="btn btn-sm btn-dark px-3 rounded-pill" onclick="viewProfile('<?= $u['username'] ?>')">View Profile</button></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Settings Section -->
    <div id="settings" class="admin-section">
        <div class="settings-container">
            <div class="logout-box">
                <i class="fas fa-user-shield"></i>
                <h3 class="fw-bold mb-2">Admin Security</h3>
                <p class="text-muted mb-4 px-3">Ready to end your session? Make sure all orders are updated before logging out.</p>
                <button onclick="confirmLogout()" class="btn btn-danger btn-lg w-100 rounded-pill py-3 fw-bold shadow-sm">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout From Admin
                </button>
            </div>
        </div>
    </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function showSection(sectionId, btn) {
        document.querySelectorAll('.admin-section').forEach(s => s.classList.remove('active'));
        document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
        document.getElementById(sectionId).classList.add('active');
        btn.classList.add('active');
    }

    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Revenue (₱)',
                data: [1200, 1900, 1500, 2500, 2200, 3000, <?= $total_sales ?>],
                borderColor: '#8B4513',
                backgroundColor: 'rgba(139, 69, 19, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });

    function viewProfile(name) {
        Swal.fire({ title: 'Customer Profile', text: `Viewing details for ${name}`, icon: 'info', confirmButtonColor: '#8B4513' });
    }

    function confirmLogout() {
        Swal.fire({
            title: 'Logout Admin?',
            text: "Your session will be closed safely.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, Logout'
        }).then((result) => { if (result.isConfirmed) window.location.href = 'logout.php'; });
    }
</script>

<?php if(isset($_GET['success'])): ?>
<script>
    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Action Successful', showConfirmButton: false, timer: 2000 });
</script>
<?php endif; ?>

</body>
</html>