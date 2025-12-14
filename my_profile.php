<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$sql = "SELECT * FROM users WHERE id='$user_id'";
$result = mysqli_query($conn, $sql);
if(mysqli_num_rows($result) > 0){
    $user = mysqli_fetch_assoc($result);
} else {
    echo "User not found!";
    exit;
}

// Update user info
if(isset($_POST['update_name'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    mysqli_query($conn, "UPDATE users SET name='$name' WHERE id='$user_id'");
    header("Location: my_profile.php");
    exit;
}

if(isset($_POST['update_email'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    mysqli_query($conn, "UPDATE users SET email='$email' WHERE id='$user_id'");
    header("Location: my_profile.php");
    exit;
}

if(isset($_POST['update_mobile'])){
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    mysqli_query($conn, "UPDATE users SET mobile='$mobile' WHERE id='$user_id'");
    header("Location: my_profile.php");
    exit;
}

if(isset($_POST['update_address'])){
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    mysqli_query($conn, "UPDATE users SET address='$address' WHERE id='$user_id'");
    header("Location: my_profile.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile</title>
<link rel="stylesheet" href="my_profil.css">
</head>
<body>
<?php include 'share.php'; ?>

<div class="user-master-box">

    <!-- NAME -->
    <div class="info-box">
        <div class="top-row">
            <h2>Name</h2>
            <button class="edit-btn" onclick="openEdit('name')">Edit</button>
        </div>

        <div class="data-box">
            <p><?php echo $user['name']; ?></p>
        </div>

        <form id="editNameForm" method="POST" style="display:none;">
            <input type="text" name="name" value="<?php echo $user['name']; ?>">
            <button type="submit" class="save-btn" name="update_name">Save</button>
        </form>
    </div>

    <!-- EMAIL -->
    <div class="info-box">
        <div class="top-row">
            <h2>Email</h2>
            <button class="edit-btn" onclick="openEdit('email')">Edit</button>
        </div>

        <div class="data-box">
            <p><?php echo $user['email']; ?></p>
        </div>

        <form id="editEmailForm" method="POST" style="display:none;">
            <input type="email" name="email" value="<?php echo $user['email']; ?>">
            <button type="submit" class="save-btn" name="update_email">Save</button>
        </form>
    </div>

    <!-- MOBILE -->
    <div class="info-box">
        <div class="top-row">
            <h2>Mobile</h2>
            <button class="edit-btn" onclick="openEdit('mobile')">Edit</button>
        </div>

        <div class="data-box">
            <p><?php echo $user['mobile']; ?></p>
        </div>

        <form id="editMobileForm" method="POST" style="display:none;">
            <input type="text" name="mobile" value="<?php echo $user['mobile']; ?>">
            <button type="submit" class="save-btn" name="update_mobile">Save</button>
        </form>
    </div>

    <!-- ADDRESS -->
    <div class="info-box">
        <div class="top-row">
            <h2>Address</h2>
            <button class="edit-btn" onclick="openEdit('address')">Edit</button>
        </div>

        <div class="data-box">
            <p><?php echo $user['address']; ?></p>
        </div>

        <form id="editAddressForm" method="POST" style="display:none;">
            <input type="text" name="address" value="<?php echo $user['address']; ?>">
            <button type="submit" class="save-btn" name="update_address">Save</button>
        </form>
    </div>

</div>

<script>
document.getElementById("userIcon").addEventListener("click", function() {
    let menu = document.getElementById("dropdownMenu");
    menu.style.display = (menu.style.display === "block") ? "none" : "block";
});

// CART DROPDOWN
let cartIcon = document.getElementById("cartIcon");
let cartDropdown = document.getElementById("cartDropdown");
cartIcon.addEventListener("click", () => {
    cartDropdown.style.display = cartDropdown.style.display === "block" ? "none" : "block";
});

// Close menus on outside click
document.addEventListener("click", (e) => {
    if (!document.getElementById("userIcon").contains(e.target) && !document.getElementById("dropdownMenu").contains(e.target)) {
        document.getElementById("dropdownMenu").style.display = "none";
    }
    if (!cartIcon.contains(e.target) && !cartDropdown.contains(e.target)) {
        cartDropdown.style.display = "none";
    }
});

function openEdit(field) {
    document.getElementById("edit" + capitalize(field) + "Form").style.display = "block";
}

function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}
</script>

</body>
</html>
