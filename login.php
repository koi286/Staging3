<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $hashed = hash("sha256", $password);

    $stmt = $conn->prepare("SELECT id,name FROM users WHERE email=? AND password=?");
    $stmt->bind_param("ss",$email,$hashed);
    $stmt->execute();
    $stmt->bind_result($id,$name);
    if ($stmt->fetch()) {
        $_SESSION["user_id"] = $id;
        $_SESSION["name"] = $name;
        header("Location: index.php");
        exit();
    } else {
        $error = "メールまたはパスワードが違います。";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>ログイン</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<h2>ログイン</h2>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

<form method="post">
    メール: <input type="email" name="email" required><br>
    パスワード: <input type="password" name="password" required><br>
    <button type="submit">ログイン</button>
</form>

<p><a href="register.php">新規登録はこちら</a></p>

</body>
</html>
