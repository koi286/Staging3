<?php
// ========================
// 掲示板：PHP処理部
// ========================

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) session_start(); // セッション開始
require_once "db.php"; // DB接続ファイル読み込み

// ログインしていない場合はリダイレクト
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// 投稿処理
if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["post"])) {
    $user_id = $_SESSION["user_id"];
    $content = $_POST["post"];
    $parent_id = !empty($_POST["parent_id"]) ? intval($_POST["parent_id"]) : NULL;

    // SQL準備と実行
    $stmt = $conn->prepare("INSERT INTO posts (user_id, post, parent_id) VALUES (?, ?, ?)");
    if (!$stmt) die("Prepare failed: " . $conn->error);
    $stmt->bind_param("isi", $user_id, $content, $parent_id);
    $stmt->execute();
}

// 投稿一覧を取得（親投稿のみ）
$result = $conn->query("
    SELECT posts.id, posts.post, users.name
    FROM posts
    JOIN users ON posts.user_id = users.id
    WHERE posts.parent_id IS NULL
    ORDER BY posts.id DESC
");

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>掲示板</title>
    <link rel="stylesheet" href="style.css" />
</head>
</head>
<body>

<!-- ヘッダー部分 -->
<h2>自作掲示板</h2>
<p class="user">
    ログイン中: 
    <strong><?php echo htmlspecialchars($_SESSION["name"]); ?></strong>
    | <a href="logout.php">ログアウト</a>
    | <a href="register.php">新規登録</a>
    | <a href="mypage.php">マイページ</a>
</p>

<!-- 投稿フォーム -->
<form method="post">
    <textarea name="post" required></textarea><br>
    <button type="submit">投稿</button>
</form>

<hr>

<?php while ($row = $result->fetch_assoc()): ?>
    <?php $post_id = $row["id"]; ?>

    <!-- 投稿内容 -->
    <p class="post">
        <strong><?php echo htmlspecialchars($row["name"]); ?>:</strong><br>
        <?php echo nl2br(htmlspecialchars($row["post"])); ?>
    </p>

    <?php
    // 返信一覧の取得
    $replies = $conn->query("
        SELECT posts.post, users.name 
        FROM posts 
        JOIN users ON posts.user_id = users.id
        WHERE posts.parent_id = $post_id
        ORDER BY posts.id ASC
    ");
    while ($reply = $replies->fetch_assoc()):
    ?>
        <div class="reply">
            <strong><?php echo htmlspecialchars($reply["name"]); ?>:</strong><br>
            <?php echo nl2br(htmlspecialchars($reply["post"])); ?>
        </div>
    <?php endwhile; ?>

    <!-- 返信フォーム -->
    <form method="post" class="reply">
        <input type="hidden" name="parent_id" value="<?php echo $post_id; ?>">
        <textarea name="post" required></textarea><br>
        <button type="submit">返信</button>
    </form>

    <hr>
<?php endwhile; ?>

</body>
</html>
