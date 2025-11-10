<?php
// ========================
// マイページ：PHP処理部
// ========================

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) session_start();
require_once "db.php";

// ログイン確認
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// ユーザー情報取得
$user_id = $_SESSION["user_id"];
$user_stmt = $conn->prepare("SELECT name, email FROM users WHERE id=?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_stmt->bind_result($current_name, $current_email);
$user_stmt->fetch();
$user_stmt->close();

// 更新処理
if (isset($_POST["update_profile"])) {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (!empty($name) && !empty($email)) {
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET name=?, email=?, password=? WHERE id=?");
            $stmt->bind_param("sssi", $name, $email, $hashed_password, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET name=?, email=? WHERE id=?");
            $stmt->bind_param("ssi", $name, $email, $user_id);
        }
        $stmt->execute();
        $_SESSION["name"] = $name;
        $msg = "プロフィールを更新しました。";
    } else {
        $msg = "名前とメールアドレスは必須です。";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>マイページ</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>

<h2>マイページ</h2>
<p class="user">
    ログイン中: 
    <strong><?php echo htmlspecialchars($_SESSION["name"]); ?></strong>
    | <a href="index.php">掲示板に戻る</a>
    | <a href="logout.php">ログアウト</a>
</p>

<?php if (isset($msg)) echo "<p class='message'>" . htmlspecialchars($msg) . "</p>"; ?>

<form method="post" class="mypage">
    <label>名前：</label><br>
    <input type="text" name="name" value="<?php echo htmlspecialchars($current_name); ?>" required><br>
    <label>メールアドレス：</label><br>
    <input type="email" name="email" value="<?php echo htmlspecialchars($current_email); ?>" required><br>
    <label>新しいパスワード（変更する場合のみ）：</label><br>
    <input type="password" name="password" placeholder="変更しない場合は空欄"><br>
    <button type="submit" name="update_profile">更新</button>
</form>

</body>
</html>
