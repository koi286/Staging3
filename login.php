<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// セッション開始（既に開始済みなら無視）
if (session_status() === PHP_SESSION_NONE) session_start();

require_once "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // ユーザー検索
    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($id, $name, $hashed);

    if ($stmt->fetch()) {
        // 両対応: password_hash() 方式 or 旧 hash("sha256") 方式
        if (password_verify($password, $hashed) || $hashed === hash("sha256", $password)) {
            $_SESSION["user_id"] = $id;
            $_SESSION["name"] = $name;
            header("Location: index.php");
            exit;
        } else {
            $error = "パスワードが違います。";
        }
    } else {
        $error = "ユーザーが見つかりません。";
    }
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

<?php if ($error) echo "<p style='color:red;'>".htmlspecialchars($error)."</p>"; ?>

<form method="post">
    メール: <input type="email" name="email" required><br>
    パスワード: <input type="password" name="password" required><br>
    <button type="submit">ログイン</button>
</form>

<p><a href="register.php">新規登録はこちら</a></p>

</body>
</html>
