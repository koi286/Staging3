<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "db.php";

$error = "";
$success = "";

// 退会後メッセージ
if (isset($_GET["deleted"])) {
    $msg_deleted = "ユーザー情報を削除しました。新しく登録できます。";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if ($name === "" || $email === "" || $password === "") {
        $error = "すべての項目を入力してください。";
    } else {
        $hashed = hash("sha256", $password);
        $stmt = $conn->prepare("INSERT INTO users (name,email,password) VALUES (?,?,?)");
        if (!$stmt) die("Prepare failed: ".$conn->error);
        $stmt->bind_param("sss",$name,$email,$hashed);
        if ($stmt->execute()) {
            $success = "ユーザー登録が完了しました。<a href='login.php'>ログインはこちら</a>";
        } else {
            $error = "登録に失敗しました: ".$stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>新規登録</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<h2>新規登録</h2>

<?php if (!empty($msg_deleted)) echo "<p class='message'>{$msg_deleted}</p>"; ?>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
<?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>

<form method="post">
    名前: <input type="text" name="name" required><br>
    メール: <input type="email" name="email" required><br>
    パスワード: <input type="password" name="password" required><br>
    <button type="submit">登録</button>
</form>

<p><a href="login.php">ログイン画面へ戻る</a></p>

</body>
</html>
