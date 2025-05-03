<?php
include "./checkSession.php";
include "./db.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Profile - Admin Panel</title>
    <link rel="icon" href="./logo.jpg" type="image/x-icon" />
    <link rel="stylesheet" href="./admin/css/<?php getTheme(); ?>" />
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.11.2/build/css/alertify.min.css" />
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.11.2/build/alertify.min.js"></script>

    <style>
        .form-control {
            background: #fff !important;
            height: 38px;
            font-size: 13px;
        }

        .form-control:focus {
            border: 1px solid #000066;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            color: #111;
            font-size: 13px;
            font-weight: 650;
        }

        .btns {
            margin-top: 20px;
            word-spacing: 7px;
        }

        .btns button {
            padding: 10px;
            width: 100px;
            border: none;
            border-radius: 4px;
            color: #fff;
            font-size: 15px;
            background-color: #000066;
        }

        .btns button:hover {
            background: #E71C23 !important;
        }

        .col-md-6 button {
            width: 100%;
            margin-top: 20px;
            background: #E71C23;
            padding: 7px;
            border: none;
            color: #fff;
            font-weight: 600;
            border-radius: 4px;
        }

        .col-md-6 button:hover {
            background: #0ABDE3 !important;
        }

        button.updateImage:hover {
            background-color: #2ecc72 !important;
        }
    </style>
</head>

<body onload="getAdminProfileRecord()">
    <div class="page-container">
        <?php include "./header.php"; ?>

        <ul class="breadcrumb">
            <li><a href="./dashboard.php">Dashboard</a></li>
            <li class="active">Edit Profile</li>
        </ul>

        <div class="page-content-wrap container">
            <span style="padding:10px;background:#000066;color:#fff;font-weight:600;font-size:17px;display:block;">
                <i class="fa fa-edit"></i> Edit Profile
                <span style="float:right;cursor:pointer;" class="fa fa-image" title="Change Profile Picture" data-toggle="modal" data-target="#myModal"></span>
            </span>

            <div id="editProfile"></div>
        </div>

        <!-- Logout Confirmation -->
        <div class="message-box animated fadeIn" data-sound="alert" id="mb-signout">
            <div class="mb-container">
                <div class="mb-middle">
                    <div class="mb-title"><span class="fa fa-sign-out"></span> Log <strong>Out</strong>?</div>
                    <div class="mb-content">
                        <p>Are you sure you want to log out?</p>
                        <p>Press No to stay. Press Yes to log out.</p>
                    </div>
                    <div class="mb-footer">
                        <div class="pull-right">
                            <a href="./logout.php" class="btn btn-success btn-lg">Yes</a>
                            <button class="btn btn-default btn-lg mb-control-close">No</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include "./preload.php"; ?>
        <?php include "./mainScripts.php"; ?>

        <script>
            function getAdminProfileRecord() {
                let emp_id = <?php echo json_encode($_SESSION["emp_id"]); ?>;
                $.ajax({
                    url: `getEditProfile.php?emp_id=${emp_id}`,
                    success: function(data) {
                        $("#editProfile").html(data);
                    }
                });
            }
        </script>
</body>

</html>

<?php
// Update Image
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["updateImage"])) {
    $emp_id = $_POST["emp_id"];
    $emp_email = $_POST["emp_email"];

    if (isset($_FILES["newImage"]) && $_FILES["newImage"]["error"] === UPLOAD_ERR_OK) {
        $image_ext = strtolower(pathinfo($_FILES["newImage"]["name"], PATHINFO_EXTENSION));
        $allowed_exts = ["jpg", "jpeg", "png", "gif"];

        if (in_array($image_ext, $allowed_exts)) {
            $image_name = $emp_email . ".jpg";
            $upload_path = "./admin/employee/" . $image_name;

            if (move_uploaded_file($_FILES["newImage"]["tmp_name"], $upload_path)) {
                $stmt = $conn->prepare("UPDATE employee SET image=? WHERE emp_id=?");
                $stmt->bind_param("si", $image_name, $emp_id);

                if ($stmt->execute()) {
                    echo "<script>alertify.success('Profile Picture Updated!');</script>";
                } else {
                    echo "<script>alertify.error('Failed to update image in DB!');</script>";
                }
                $stmt->close();
            } else {
                echo "<script>alertify.error('Failed to upload image!');</script>";
            }
        } else {
            echo "<script>alertify.error('Invalid image format!');</script>";
        }
    } else {
        echo "<script>alertify.error('No image uploaded or upload error.');</script>";
    }
}

// Update Profile Info
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["edit"])) {
    $emp_id = $_POST["emp_id"];
    $emp_name = $_POST["emp_name"];
    $emp_position = $_POST["emp_position"];
    $emp_dob = $_POST["emp_dob"];
    $emp_gender = $_POST["emp_gender"];
    $emp_mobile = $_POST["emp_mobile"];

    $stmt = $conn->prepare("UPDATE employee SET emp_name=?, emp_position=?, emp_gender=?, emp_dob=?, emp_mobile=? WHERE emp_id=?");
    $stmt->bind_param("sssssi", $emp_name, $emp_position, $emp_gender, $emp_dob, $emp_mobile, $emp_id);

    if ($stmt->execute()) {
        echo "<script>alertify.success('Profile details updated!');</script>";
    } else {
        echo "<script>alertify.error('Failed to update profile details.');</script>";
    }
    $stmt->close();
}
?>
