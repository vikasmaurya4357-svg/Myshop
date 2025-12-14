<?php
include "db.php";
$total_items = 0;
if(isset($_SESSION['user_id'])){
    $uid = $_SESSION['user_id'];
    $cart_count_q = mysqli_query($conn, "SELECT SUM(quantity) as total FROM addcard WHERE user_id='$uid'");
    $count_row = mysqli_fetch_assoc($cart_count_q);
    $total_items = $count_row['total'] ?? 0;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyShop</title>
    <style>
        :root {
            --primary: #0a4ecb;
        }
        body {
            margin:0;
            font-family: 'Arial', sans-serif;
            background:#f0f2f8;
        }
        /* HEADER */
        .header{
    background:linear-gradient(90deg,#4b6cb7,#182848);
    padding:14px 28px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    box-shadow:0 6px 20px rgba(24,40,72,0.18);
    position:sticky;
    top:0;
    z-index:100;
}

.header .logo{
    color:#ffecb3;
    font-size:1.9rem;
    font-weight:800;
    text-decoration:none;
}

.search-box{
    flex:1;
    display:flex;
    background:white;
    border-radius:6px;
    max-width:600px;
    margin:0 15px;
}

.search-box input{
    width:100%;
    padding:12px;
    border:none;
    outline:none;
    font-size:15px;
}

.search-box button{
    background:#0a4ecb;
    border:none;
    padding:0 20px;
    color:white;
    font-size:15px;
    cursor:pointer;
}
.search-box button:hover{
    background:#053b9c;
}
.user-container{
    display:flex;
    align-items:center;
    gap:12px;
}

.user-icon,
.cart-icon{
    width:42px;
    height:42px;
    background:white;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    position:relative;
    font-size:22px;
}

.user-icon svg{
    width:26px;
    height:26px;
    fill:#000;
}

.cart-count{
    position:absolute;
    top:-5px;
    right:-5px;
    background:red;
    color:white;
    padding:2px 6px;
    border-radius:50%;
    font-size:12px;
}


        .sidebar{
            width:220px;
            padding:20px;
            position:fixed;
            top:70px;
            left:0;
            height:calc(100% - 70px);
            background:#f7f9ff;
            box-shadow:2px 0 15px rgba(0,0,0,0.05);
        }
        .user-box{
            text-align:center;
            padding:20px;
            background:#fff;
            border-radius:12px;
            box-shadow:0 6px 15px rgba(0,0,0,0.1);
            margin-bottom:30px;
        }
        .user-box img, .user-box svg{
            width:90px;
            height:90px;
            border-radius:50%;
        }
        .user-name{
            margin-top:10px;
            font-weight:700;
            font-size:16px;
        }
        .menu-box{
            background:#fff;
            padding:20px;
            border-radius:12px;
            box-shadow:0 6px 15px rgba(0,0,0,0.1);
        }
        .menu-box h2{
            font-size:16px;
            font-weight:700;
            margin-bottom:15px;
        }
        .menu-box ul{list-style:none;padding:0;margin:0;}
        .menu-box ul li{margin-bottom:15px;}
        .menu-box ul li a{
            text-decoration:none;
            color:#222;
            display:block;
            padding:8px 12px;
            border-radius:8px;
            transition:0.3s;
        }
        .menu-box ul li a:hover{
            background:#e0f0ff;
            color:var(--primary);
        }

        /* MAIN CONTENT */
        .main-content{
            margin-left:240px;
            padding:100px 30px 30px 30px;
        }
        @media(max-width:768px){
            .sidebar{position:relative;width:100%;height:auto;top:0;}
            .main-content{margin-left:0;padding:120px 20px 20px;}
        }
    </style>
</head>
<body>
    <header class="header">
        <a href="index.php" class="logo">MyShop</a>

        <form class="search-box" action="all_category.php" method="GET">
            <input type="text" name="my_search" placeholder="Search products..." required>
            <button type="submit">Search</button>
        </form>

        <div class="user-container">
            <a href="card.php" class="cart-icon">
                🛒 <span class="cart-count"><?php echo $total_items; ?></span>

            </a>
        </div>
    </header>

    <aside class="sidebar">
        <div class="user-box">
            <?php if(isset($user['image']) && $user['image'] != ''): ?>
                <img src="./image/<?php echo $user['image']; ?>" alt="User Image">
            <?php else: ?>
                <svg viewBox="0 0 24 24">
                    <path d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 
                    2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z"/>
                </svg>
            <?php endif; ?>
            <p class="user-name"><?php echo $user['name']; ?></p>
        </div>

        <div class="menu-box">
            <h2>User Menu</h2>
            <ul>
                <li><a href="index.php">Continue Shopping</a></li>
                <li><a href="my_profile.php">My Profile</a></li>
                <li><a href="my_orders.php">My Orders</a></li>
                <li><a href="my_address.php">My Address</a></li>
                <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
                    <li><a href="admin/dashboard.php">Go Admin</a></li>
                <?php endif; ?>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </aside>

   
</body>
</html>
