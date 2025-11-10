<h2>新規登録</h2>

<?php
if ($error) echo "<p style='color:red;'>$error</p>";
if ($success) echo "<p style='color:green;'>$success</p>";
?>

<form method="post">
    名前: <input type="text" name="name" required><br>
    メール: <input type="email" name="email" required><br>
    パスワード: <input type="password" name="password" required><br>
    <button type="submit">登録</button>
</form>

<p><a href="login.php">ログイン画面へ戻る</a></p>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// セッションは不要（登録時点ではログインしていないため）
require_once "db.php";

$error = "";
$success = "";

// フォームが送信された場合
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if ($name === "" || $email === "" || $password === "") {
        $error = "すべての項目を入力してください。";
    } else {
        // パスワードを SHA256 でハッシュ化
        $hashed = hash("sha256", $password);

        // INSERT
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("sss", $name, $email, $hashed);

        if ($stmt->execute()) {
            $success = "登録が完了しました。<a href='login.php'>ログインはこちら</a>";
        } else {
            // エラー内容を簡単に表示
            $error = "登録に失敗しました: " . $stmt->error;
        }
    }
}
?>

