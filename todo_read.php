<?php
session_start();
include("functions.php");
check_session_id();

$pdo = connect_to_db();

//$sql = "SELECT * FROM todo_table ORDER BY deadline ASC";
//$sql = "SELECT id FROM users_table";
//$sql = 'SELECT * FROM todo_table LEFT OUTER JOIN (SELECT todo_id, COUNT(id) AS like_count FROM like_table GROUP BY todo_id) AS result_table ON todo_table.id = result_table.todo_id';
$sql = "SELECT todo_table.id,todo_table.todo,todo_table.deadline,users_table.username,like_count,users_table.id AS user_id FROM todo_table LEFT OUTER JOIN users_table ON todo_table.user_id=users_table.id LEFT OUTER JOIN (SELECT todo_id,COUNT(id) AS like_count FROM like_table GROUP BY todo_id) AS result_table ON todo_table.id=result_table.todo_id";
$stmt = $pdo->prepare($sql);

try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}
$user_id = $_SESSION['user_id'];
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
//var_dump($result);
//exit();
$output = "";
foreach ($result as $record) {
  if($user_id === $record["user_id"]){
    $output .= "
      <tr>
        <td>{$record["deadline"]}</td>
        <td>{$record["todo"]}</td>
        <td>{$record["username"]}</td>
        <td><a href='like_create.php?user_id={$user_id}&todo_id={$record["id"]}'>like{$record["like_count"]}</a></td>
        <td><a href='todo_edit.php?id={$record["id"]}'>edit</a></td>
        <td><a href='todo_delete.php?id={$record["id"]}'>delete</a></td>
      </tr>
  ";}
  else{
    $output .= "
      <tr>
        <td>{$record["deadline"]}</td>
        <td>{$record["todo"]}</td>
        <td>{$record["username"]}</td>
        <td><a href='like_create.php?user_id={$user_id}&todo_id={$record["id"]}'>like{$record["like_count"]}</a></td>
      </tr>
    ";
  }
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DB連携型todoリスト（一覧画面）</title>
</head>

<body>
  <fieldset>
    <legend>DB連携型todoリスト（一覧画面）</legend>
    <a href="todo_input.php">入力画面</a>
    <a href="todo_logout.php">logout</a>
    <table>
      <thead>
        <tr>
          <th>deadline</th>
          <th>todo</th>
          <th></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?= $output ?>
      </tbody>
    </table>
  </fieldset>
</body>

</html>