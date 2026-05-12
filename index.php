<?php
session_start();
include 'db.php'; // Database: lutong_bahay

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// --- 1. Fetch Username for the Header ---
$user_query = mysqli_query($conn, "SELECT username FROM users WHERE id = '$user_id'");
$user_data = mysqli_fetch_assoc($user_query);
$display_name = $user_data['username'] ?? "Guest";

// --- 2. Menu Data ---
$items = [
    ['name' => 'Pork Sisig', 'price' => 180, 'cat' => 'food', 'image' => 'sisig.jpg', 'desc' => 'Classic Pampanga style with egg'],
    ['name' => 'Chicken Adobo', 'price' => 150, 'cat' => 'food', 'image' => 'adobo.jpg', 'desc' => 'Savory soy-vinegar braised chicken'],
    ['name' => 'Pork Sinigang', 'price' => 220, 'cat' => 'food', 'image' => 'sinigang.jpg', 'desc' => 'Sour tamarind soup with fresh vegetables'],
    ['name' => 'Beef Caldereta', 'price' => 280, 'cat' => 'food', 'image' => 'caldereta.jpg', 'desc' => 'Rich tomato sauce with cheese'],
    ['name' => 'Beef Kare-Kare', 'price' => 300, 'cat' => 'food', 'image' => 'karekare.jpg', 'desc' => 'Peanut sauce stew with shrimp paste'],
    ['name' => 'Lechon Kawali', 'price' => 200, 'cat' => 'food', 'image' => 'letchon kawali.jpg', 'desc' => 'Deep-fried crispy pork belly'],
    ['name' => 'Pinakbet', 'price' => 120, 'cat' => 'food', 'image' => 'pinakbet.jpg', 'desc' => 'Assorted vegetables with shrimp paste'],
    ['name' => 'Bicol Express', 'price' => 170, 'cat' => 'food', 'image' => 'bicol express.jpg', 'desc' => 'Spicy pork in coconut milk'],
    ['name' => 'Siomai Rice', 'price' => 75, 'cat' => 'food', 'image' => 'siomai rice.jpg', 'desc' => '4pcs Pork Siomai with garlic rice'],
    ['name' => 'Beef Tapa', 'price' => 165, 'cat' => 'food', 'image' => 'beef tapa.jpg', 'desc' => 'Cured beef with egg and rice'],
    ['name' => 'Chicken Inasal', 'price' => 190, 'cat' => 'food', 'image' => 'chicken inasal.jpg', 'desc' => 'Bacolod style grilled chicken'],
    ['name' => 'Extra Rice', 'price' => 20, 'cat' => 'food', 'image' => 'extra rice.jpg', 'desc' => 'Steamed premium white rice'],
    ['name' => 'Sting', 'price' => 35, 'cat' => 'drink', 'image' => 'sting.jpg', 'desc' => 'Energy drink'],
    ['name' => 'Mountain Dew', 'price' => 30, 'cat' => 'drink', 'image' => 'mountain dew.jpg', 'desc' => 'Lemon-lime soda'],
    ['name' => 'Mineral Water', 'price' => 20, 'cat' => 'drink', 'image' => 'mineral water.jpg', 'desc' => 'Chilled bottled water'],
    ['name' => 'Coca-Cola', 'price' => 30, 'cat' => 'drink', 'image' => 'coca cola.jpg', 'desc' => 'Classic refreshing soda'],
    ['name' => "Sago't Gulaman", 'price' => 45, 'cat' => 'drink', 'image' => 'sago gulaman.jpg', 'desc' => 'Classic brown sugar cooler'],
    ['name' => 'Calamansi Juice', 'price' => 35, 'cat' => 'drink', 'image' => 'calamansi juice.jpg', 'desc' => 'Freshly squeezed'],
    ['name' => 'Melon Juice', 'price' => 40, 'cat' => 'drink', 'image' => 'melon juice.jpg', 'desc' => 'Sweet shredded melon drink'],
    ['name' => 'Buko Pandan', 'price' => 55, 'cat' => 'drink', 'image' => 'buko pandan.jpg', 'desc' => 'Creamy coconut dessert drink'],
    ['name' => 'Iced Tea Blend', 'price' => 30, 'cat' => 'drink', 'image' => 'Iced Tea Blend.jpg', 'desc' => 'Signature house-blend tea']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lutong Bahay | Home</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Great+Vibes&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

    <style>
        :root { --primary: #8B4513; --accent: #d62828; --bg: #fdf0d5; --white: #ffffff; }
        
        body { 
            background: linear-gradient(rgba(253, 240, 213, 0.96), rgba(253, 240, 213, 0.96)), url('luto.png');
            background-size: cover; background-position: center; background-attachment: fixed;
            font-family: 'Poppins', sans-serif;
        }

        .navbar-custom { background: var(--white); box-shadow: 0 4px 12px rgba(0,0,0,0.05); padding: 12px 0; }
        .navbar-brand { font-family: 'Great Vibes', cursive; font-size: 2rem; color: var(--primary) !important; }
        
        .content-wrapper { display: flex; gap: 30px; padding: 30px; max-width: 1600px; margin: 0 auto; }
        .menu-side { flex: 1; }
        
        .search-container { position: relative; margin-bottom: 20px; }
        .search-container i { position: absolute; left: 20px; top: 50%; transform: translateY(-50%); color: var(--primary); }
        .search-container input { padding: 14px 14px 14px 50px; border-radius: 50px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.05); width: 100%; font-size: 0.9rem; }

        .filter-btn { border-radius: 50px; padding: 8px 22px; border: none; background: white; font-weight: 600; transition: 0.3s; margin-right: 8px; font-size: 0.85rem; }
        .filter-btn.active { background: var(--primary); color: white; }

        .food-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
        .food-card { background: white; border-radius: 20px; overflow: hidden; position: relative; transition: 0.4s; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.03); }
        .food-card:hover { transform: translateY(-8px); box-shadow: 0 12px 25px rgba(0,0,0,0.1); }
        
        .food-img-box { height: 160px; width: 100%; overflow: hidden; position: relative; }
        .food-img-box img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
        .price-tag { position: absolute; top: 12px; left: 12px; background: white; padding: 3px 10px; border-radius: 50px; font-weight: 800; color: var(--accent); font-size: 0.8rem; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        
        .food-info { padding: 15px; }
        .food-info h6 { font-weight: 700; font-size: 1.1rem; margin-bottom: 5px; color: #333; }
        .food-info p { font-size: 0.75rem; color: #777; margin-bottom: 12px; height: 32px; overflow: hidden; line-height: 1.2; }

        .add-btn { background: var(--primary); color: white; border: none; width: 100%; padding: 8px; border-radius: 12px; font-weight: 700; transition: 0.3s; font-size: 0.85rem; }

        .sidebar-panel { width: 360px; background: white; border-radius: 25px; padding: 25px; height: calc(100vh - 110px); position: sticky; top: 90px; box-shadow: 0 10px 40px rgba(0,0,0,0.05); display: flex; flex-direction: column; }
        
        .nav-tabs-custom { display: flex; gap: 15px; border-bottom: 2px solid #f0f0f0; margin-bottom: 15px; }
        .tab-link { cursor: pointer; padding-bottom: 8px; font-weight: 700; font-size: 0.9rem; color: #bbb; transition: 0.3s; }
        .tab-link.active { color: var(--primary); border-bottom: 2px solid var(--primary); }

        .scroll-area { flex-grow: 1; overflow-y: auto; }
        
        .cart-card { display: flex; justify-content: space-between; align-items: center; background: #fcfcfc; padding: 10px; border-radius: 12px; margin-bottom: 8px; border: 1px solid #f0f0f0; }
        .qty-box { display: flex; align-items: center; gap: 8px; }
        .qty-box button { width: 22px; height: 22px; border-radius: 4px; border: none; background: #ddd; font-weight: bold; font-size: 0.8rem; }

        .status-row { padding: 12px; background: #f8f9fa; border-radius: 12px; margin-bottom: 10px; border-left: 4px solid var(--primary); }
        .status-badge { font-size: 0.7rem; padding: 3px 8px; border-radius: 50px; font-weight: 700; text-transform: uppercase; }
        .Pending { background: #fff3cd; color: #856404; }
        .Cooking { background: #cfe2ff; color: #084298; }
        .Delivered { background: #d1e7dd; color: #0f5132; }

        @media (max-width: 992px) {
            .content-wrapper { flex-direction: column; padding: 15px; }
            .sidebar-panel { width: 100%; position: static; height: auto; }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-custom sticky-top animate__animated animate__fadeInDown">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="navbar-brand fw-bold" href="#"><i class="fas fa-utensils me-2"></i>Lutong Bahay</a>
        <div class="d-flex align-items-center gap-3">
            <span class="fw-bold d-none d-md-block" style="color: var(--primary)">Hi, <?= htmlspecialchars($display_name) ?>!</span>
            <button onclick="confirmLogout()" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold">Logout</button>
        </div>
    </div>
</nav>

<div class="content-wrapper">
    <div class="menu-side">
        <div class="search-container animate__animated animate__fadeIn">
            <i class="fas fa-search"></i>
            <input type="text" id="menuSearch" placeholder="What would you like to eat today?" onkeyup="runMenuFilter()">
        </div>

        <div class="mb-4 animate__animated animate__fadeIn">
            <button class="filter-btn active" onclick="setCat('all', this)">All Items</button>
            <button class="filter-btn" onclick="setCat('food', this)">Food</button>
            <button class="filter-btn" onclick="setCat('drink', this)">Drinks</button>
        </div>

        <div class="food-grid" id="menu-grid">
            <?php $delay = 0; foreach ($items as $i): ?>
            <div class="food-card animate__animated animate__fadeInUp" style="animation-delay: <?= $delay ?>s" 
                 data-cat="<?= $i['cat'] ?>" data-name="<?= strtolower($i['name']) ?>">
                
                <span class="price-tag">₱<?= $i['price'] ?></span>
                
                <div class="food-img-box">
                    <img src="<?= $i['image'] ?>" alt="<?= $i['name'] ?>">
                </div>
                
                <div class="food-info text-center">
                    <h6><?= $i['name'] ?></h6>
                    
                    <div class="fw-bold text-danger mb-1" style="font-size: 1.1rem;">
                        ₱<?= number_format($i['price'], 2) ?>
                    </div>
                    
                    <p><?= $i['desc'] ?></p>
                    
                    <button class="add-btn" onclick="addToCart('<?= addslashes($i['name']) ?>', <?= $i['price'] ?>)">
                        <i class="fas fa-plus-circle me-1"></i> Add Order
                    </button>
                </div>
            </div>
            <?php $delay += 0.03; endforeach; ?>
        </div>
    </div>

    <aside class="sidebar-panel animate__animated animate__fadeInRight">
        <div class="nav-tabs-custom">
            <div class="tab-link active" id="tab-cart" onclick="switchSidebar('cart')">My Order</div>
            <div class="tab-link" id="tab-status" onclick="switchSidebar('status')">Status Tracking</div>
        </div>

        <div id="view-cart" class="scroll-area">
            <div id="cart-content" class="text-center mt-5">
                <i class="fas fa-shopping-basket fa-3x mb-3 text-muted opacity-50"></i>
                <p class="text-muted">Empty cart</p>
            </div>
        </div>

        <div id="view-status" class="scroll-area d-none">
            <?php
            $orders = mysqli_query($conn, "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY id DESC");
            if(mysqli_num_rows($orders) > 0) {
                while($o = mysqli_fetch_assoc($orders)) {
                    echo "<div class='status-row animate__animated animate__fadeInRight'>
                            <div class='d-flex justify-content-between align-items-start'>
                                <div>
                                    <div class='fw-bold' style='font-size:0.8rem;'>Order #{$o['id']}</div>
                                    <div class='fw-bold text-danger'>₱".number_format($o['total_amount'], 2)."</div>
                                </div>
                                <span class='status-badge {$o['status']}'>{$o['status']}</span>
                            </div>
                          </div>";
                }
            } else { echo "<div class='text-center mt-5 text-muted'>No orders found.</div>"; }
            ?>
        </div>

        <div class="pt-3 border-top" id="sidebar-footer">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-bold text-muted">Total Amount</span>
                <h3 id="grand-total" class="fw-bold text-danger mb-0">₱0</h3>
            </div>
            <button class="btn w-100 rounded-pill py-3 fw-bold shadow-sm" style="background:#ffc439; color:#111" onclick="handleCheckout()">
                <i class="fab fa-paypal me-2"></i> Pay with PayPal
            </button>
        </div>
    </aside>
</div>

<script>
    let cart = [];

    function switchSidebar(view) {
        document.getElementById('tab-cart').classList.toggle('active', view === 'cart');
        document.getElementById('tab-status').classList.toggle('active', view === 'status');
        document.getElementById('view-cart').classList.toggle('d-none', view !== 'cart');
        document.getElementById('view-status').classList.toggle('d-none', view !== 'status');
        document.getElementById('sidebar-footer').classList.toggle('d-none', view !== 'cart');
    }

    function addToCart(name, price) {
        let exists = cart.find(i => i.name === name);
        if(exists) { exists.qty++; } else { cart.push({name, price, qty: 1}); }
        renderCart();
        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Added!', showConfirmButton: false, timer: 700 });
    }

    function renderCart() {
        const container = document.getElementById('cart-content');
        if (cart.length === 0) {
            container.innerHTML = `<i class="fas fa-shopping-basket fa-3x mb-3 text-muted opacity-50"></i><p class="text-muted">Empty cart</p>`;
            document.getElementById('grand-total').innerText = "₱0";
            return;
        }

        container.innerHTML = cart.map((item, idx) => `
            <div class="cart-card animate__animated animate__fadeIn">
                <div class="text-start">
                    <div class="fw-bold" style="font-size:0.85rem">${item.name}</div>
                    <div class="text-danger small fw-bold">₱${item.price}</div>
                </div>
                <div class="qty-box">
                    <button onclick="changeQty(${idx}, -1)">-</button>
                    <span class="fw-bold small">${item.qty}</span>
                    <button onclick="changeQty(${idx}, 1)">+</button>
                </div>
            </div>`).join('');

        let sum = cart.reduce((total, i) => total + (i.price * i.qty), 0);
        document.getElementById('grand-total').innerText = "₱" + sum.toLocaleString();
    }

    function changeQty(idx, delta) {
        if(cart[idx].qty + delta > 0) cart[idx].qty += delta;
        else cart.splice(idx, 1);
        renderCart();
    }

    function runMenuFilter() {
        let query = document.getElementById('menuSearch').value.toLowerCase();
        let cat = document.querySelector('.filter-btn.active').innerText.toLowerCase();
        document.querySelectorAll('.food-card').forEach(card => {
            let nameMatch = card.getAttribute('data-name').includes(query);
            let catMatch = cat.includes('all') || cat.includes(card.getAttribute('data-cat'));
            card.style.display = (nameMatch && catMatch) ? "block" : "none";
        });
    }

    function setCat(cat, btn) {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        runMenuFilter();
    }

    function handleCheckout() {
        if (cart.length === 0) {
            Swal.fire('Empty Cart', 'Please add some items to your cart first.', 'warning');
            return;
        }

        let total = cart.reduce((sum, i) => sum + (i.price * i.qty), 0);

        Swal.fire({
            title: '<i class="fab fa-paypal" style="color: #0070ba;"></i> PayPal Checkout',
            html: `
                <div class="text-start p-2">
                    <p class="small text-muted mb-3">Pay to <strong>Lutong Bahay Store</strong>: <span class="text-primary fw-bold">₱${total.toLocaleString()}</span></p>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted uppercase">PAYPAL EMAIL</label>
                        <input type="email" id="pp-email" class="form-control" placeholder="example@email.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">PASSWORD</label>
                        <input type="password" id="pp-pass" class="form-control" placeholder="••••••••">
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Pay Now',
            confirmButtonColor: '#0070ba',
            cancelButtonText: 'Cancel',
            preConfirm: () => {
                const email = Swal.getPopup().querySelector('#pp-email').value;
                const pass = Swal.getPopup().querySelector('#pp-pass').value;
                if (!email || !pass) {
                    Swal.showValidationMessage(`Please enter your PayPal details`);
                }
                return { email: email };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing Payment...',
                    html: 'Connecting to PayPal secure servers',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                        fetch('save_order.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ cart: cart, total: total })
                        })
                        .then(() => {
                            setTimeout(() => {
                                confetti({ particleCount: 150, spread: 70, origin: { y: 0.6 } });
                                Swal.fire('Success!', 'Your payment has been processed.', 'success').then(() => location.reload());
                            }, 1500);
                        });
                    }
                });
            }
        });
    }

    function confirmLogout() {
        Swal.fire({ 
            title: 'Ready to logout?', 
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, Logout'
        }).then(r => { if(r.isConfirmed) window.location.href='logout.php'; });
    }
</script>
</body>
</html>