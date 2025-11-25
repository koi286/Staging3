<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status()===PHP_SESSION_NONE) session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare("SELECT name,email FROM users WHERE id=?");
$stmt->bind_param("i",$user_id);
$stmt->execute();
$stmt->bind_result($current_name,$current_email);
$stmt->fetch();
$stmt->close();

/* ---------------------------------------------------------
   退会処理（退会ボタンが押された場合のみ発動）
--------------------------------------------------------- */
if (isset($_POST["do_delete"])) {

    // チェックされているか確認
    if (isset($_POST["delete_account"]) && $_POST["delete_account"] == "1") {
        session_unset();
        session_destroy();
        header("Location: register.php?deleted=1");
        exit();
    } else {
        $msg = "退会する場合はチェックを入れてください。";
    }
}

/* ---------------------------------------------------------
   プロフィール更新処理（更新ボタンが押された場合のみ発動）
--------------------------------------------------------- */
if (isset($_POST["update_profile"])) {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if ($name !== "" && $email !== "") {

        if ($password !== "") {
            $hashed = hash("sha256",$password);
            $stmt = $conn->prepare("UPDATE users SET name=?,email=?,password=? WHERE id=?");
            $stmt->bind_param("sssi",$name,$email,$hashed,$user_id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET name=?,email=? WHERE id=?");
            $stmt->bind_param("ssi",$name,$email,$user_id);
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
<link rel="stylesheet" href="style.css">
</head>
<body>

<h2>マイページ</h2>
<p class="user">
    ログイン中: <strong><?php echo htmlspecialchars($_SESSION["name"]); ?></strong>
    | <a href="index.php">掲示板に戻る</a>
    | <a href="logout.php">ログアウト</a>
</p>

<?php if (isset($msg)) echo "<p class='message'>".htmlspecialchars($msg)."</p>"; ?>

<form method="post" class="mypage">

    <label>名前：</label><br>
    <input type="text" name="name" value="<?php echo htmlspecialchars($current_name); ?>" required><br>

    <label>メールアドレス：</label><br>
    <input type="email" name="email" value="<?php echo htmlspecialchars($current_email); ?>" required><br>

    <label>新しいパスワード（変更する場合のみ）：</label><br>
    <input type="password" name="password" placeholder="変更しない場合は空欄"><br>

    <!-- 更新専用ボタン -->
    <button type="submit" name="update_profile">更新</button>

    <hr>

    <label style="color:red;">
        <input type="checkbox" name="delete_account" value="1">
        退会する
    </label><br>

    <!-- 退会専用ボタン -->
    <button type="submit" name="do_delete" style="background:red;color:white;">
        退会実行
    </button>

</form>

</body>
</html>
