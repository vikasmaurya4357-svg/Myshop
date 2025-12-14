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
    header("Location: login.php");
    exit;
}
$addresses = mysqli_query($conn, "SELECT * FROM user_addresses WHERE user_id='$user_id' ORDER BY id DESC");

// Add new address
if(isset($_POST['add_address'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $pin = mysqli_real_escape_string($conn, $_POST['pin']);
    $locality = mysqli_real_escape_string($conn, $_POST['locality']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $landmark = mysqli_real_escape_string($conn, $_POST['landmark']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);

    $insert = "INSERT INTO user_addresses (user_id, name, mobile, pin, locality, address, city, state, landmark, address_type) 
               VALUES ('$user_id','$name','$mobile','$pin','$locality','$address','$city','$state','$landmark','$type')";
    mysqli_query($conn, $insert);
    header("Location: my_address.php");
    exit;
}

// Update address
if(isset($_POST['update_address'])){
    $address_id = $_POST['address_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $pin = mysqli_real_escape_string($conn, $_POST['pin']);
    $locality = mysqli_real_escape_string($conn, $_POST['locality']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $landmark = mysqli_real_escape_string($conn, $_POST['landmark']);
    $address_type = mysqli_real_escape_string($conn, $_POST['address_type']);

    $update_sql = "UPDATE user_addresses SET 
        name='$name', mobile='$mobile', pin='$pin', locality='$locality', 
        address='$address', city='$city', state='$state', landmark='$landmark', address_type='$address_type'
        WHERE id='$address_id' AND user_id='$user_id'";

    mysqli_query($conn, $update_sql);
    header("Location: my_address.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Addresses</title>
<link rel="stylesheet" href="my_addresss.css">

</head>
<body>
    <?php include 'share.php'; ?>
<div class="main-content">
    <button class="add-btn" onclick="toggleForm()">Add New Address</button>

    <div id="newAddressForm" style="display:none;">
        <form method="POST" class="edit-form">
            <input type="text" name="name" placeholder="Name" required>
            <input type="text" name="mobile" placeholder="Mobile" required>
            <input type="text" name="pin" placeholder="PIN Code" required>
            <input type="text" name="locality" placeholder="Locality" required>
            <textarea name="address" placeholder="Address" required></textarea>
            <input type="text" name="city" placeholder="City" required>
            <input type="text" name="state" placeholder="State" required>
            <input type="text" name="landmark" placeholder="Landmark">
            <select name="type" required>
                <option value="">Select Address Type</option>
                <option value="Home">Home</option>
                <option value="Office">Office</option>
                <option value="Other">Other</option>
            </select>
            <button type="submit" name="add_address">Save Address</button>
        </form>
    </div>

    <!-- Loop through existing addresses -->
    <?php
    if(mysqli_num_rows($addresses) > 0){
        while($row = mysqli_fetch_assoc($addresses)){ ?>
            <div class="address-box" id="address-<?php echo $row['id']; ?>">
                <h3>
                    <?php echo $row['address_type']; ?>
                    <button class="edit-btn" onclick="toggleEditForm(<?php echo $row['id']; ?>)">Edit</button>
                </h3>

                <!-- Display section -->
                <div class="address-display" id="display-<?php echo $row['id']; ?>">
                    <p><strong>Name:</strong> <?php echo $row['name']; ?></p>
                    <p><strong>Mobile:</strong> <?php echo $row['mobile']; ?></p>
                    <p><strong>PIN:</strong> <?php echo $row['pin']; ?></p>
                    <p><strong>Locality:</strong> <?php echo $row['locality']; ?></p>
                    <p><strong>Address:</strong> <?php echo $row['address']; ?></p>
                    <p><strong>City:</strong> <?php echo $row['city']; ?></p>
                    <p><strong>State:</strong> <?php echo $row['state']; ?></p>
                    <?php if($row['landmark'] != ''): ?>
                        <p><strong>Landmark:</strong> <?php echo $row['landmark']; ?></p>
                    <?php endif; ?>
                </div>

                <!-- Edit Form -->
                <form method="POST" class="edit-form" id="form-<?php echo $row['id']; ?>" style="display:none;">
                    <input type="hidden" name="address_id" value="<?php echo $row['id']; ?>">
                    <input type="text" name="name" value="<?php echo $row['name']; ?>" placeholder="Name">
                    <input type="text" name="mobile" value="<?php echo $row['mobile']; ?>" placeholder="Mobile">
                    <input type="text" name="pin" value="<?php echo $row['pin']; ?>" placeholder="Pin">
                    <input type="text" name="locality" value="<?php echo $row['locality']; ?>" placeholder="Locality">
                    <input type="text" name="address" value="<?php echo $row['address']; ?>" placeholder="Address">
                    <input type="text" name="city" value="<?php echo $row['city']; ?>" placeholder="City">
                    <input type="text" name="state" value="<?php echo $row['state']; ?>" placeholder="State">
                    <input type="text" name="landmark" value="<?php echo $row['landmark']; ?>" placeholder="Landmark">
                    <select name="address_type">
                        <option value="Home" <?php if($row['address_type']=="Home") echo "selected"; ?>>Home</option>
                        <option value="Office" <?php if($row['address_type']=="Office") echo "selected"; ?>>Office</option>
                        <option value="Other" <?php if($row['address_type']=="Other") echo "selected"; ?>>Other</option>
                    </select>
                    <button type="submit" name="update_address">Save</button>
                </form>
            </div>
    <?php
        }
    } else {
        echo "<p>No addresses found.</p>";
    }
    ?>
</div>

<script>
function toggleForm(){
    let form = document.getElementById('newAddressForm');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

function toggleEditForm(id){
    let form = document.getElementById('form-'+id);
    let display = document.getElementById('display-'+id);
    if(form.style.display === "none"){
        form.style.display = "block";
        display.style.display = "none";
    } else {
        form.style.display = "none";
        display.style.display = "block";
    }
}
</script>

</body>
</html>
