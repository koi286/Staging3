<?php
if (session_status()===PHP_SESSION_NONE) session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// 投稿取得（親投稿のみ）
$result = $conn->query("
    SELECT posts.id, posts.post, users.name
    FROM posts
    LEFT JOIN users ON posts.user_id = users.id
    WHERE posts.parent_id IS NULL
    ORDER BY posts.id DESC
");

?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>掲示板</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<h2>掲示板</h2>
<p class="user">
    ログイン中: <strong><?php echo htmlspecialchars($_SESSION["name"]); ?></strong>
    | <a href="logout.php">ログアウト</a>
    | <a href="register.php">新規登録</a>
    | <a href="mypage.php">マイページ</a>
</p>

<form method="post">
    <textarea name="post" required></textarea><br>
    <button type="submit">投稿</button>
</form>

<hr>

<?php while ($row = $result->fetch_assoc()): 
    $post_id = $row["id"];
    $username = $row["name"] ?? "退会ユーザー";
?>
<p class="post">
    <strong><?php echo htmlspecialchars($username); ?>:</strong><br>
    <?php echo nl2br(htmlspecialchars($row["post"])); ?>
</p>

<?php
// 返信取得（ユーザー名も表示）
$replies = $conn->query("
    SELECT posts.post, users.name
    FROM posts
    LEFT JOIN users ON posts.user_id = users.id
    WHERE posts.parent_id = $post_id
    ORDER BY posts.id ASC
");

while ($reply = $replies->fetch_assoc()):
    $reply_name = $reply["name"] ?? "退会ユーザー";
?>
<div class="reply">
    <strong><?php echo htmlspecialchars($reply_name); ?>:</strong><br>
    <?php echo nl2br(htmlspecialchars($reply["post"])); ?>
</div>
<?php endwhile; ?>

<form method="post" class="reply">
    <input type="hidden" name="parent_id" value="<?php echo $post_id; ?>">
    <textarea name="post" required></textarea><br>
    <button type="submit">返信</button>
</form>

<hr>
<?php endwhile; ?>

</body>
</html>
