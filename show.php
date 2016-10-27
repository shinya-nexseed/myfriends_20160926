<?php
    // DBへの接続
    $dsn = 'mysql:dbname=myfriends;host=localhost';
    $user = 'root';
    $password = 'mysql';
    $dbh = new PDO($dsn, $user, $password);
    $dbh->query('SET NAMES utf8');

    // 削除処理
    if (!empty($_GET['action']) && $_GET['action'] == 'delete') {
        // action=delete&friend_id=4
        // $_GET = array('kawasaki' => 'kento', 'friend_id' => '4');

        $sql = 'DELETE FROM `friends` WHERE `friend_id` = ' . $_GET['friend_id'];

        $stmt = $dbh->prepare($sql);
        $stmt->execute();

        header('Location: index.php');
        exit();
    }

    // areasテーブルからパラメータのarea_idを使用してデータ1レコードを取得
    // $_GET = array('area_id'=>20);
    $area_id = $_GET['area_id'];

    $sql = 'SELECT `area_name` FROM `areas` WHERE `area_id` = ' . $area_id;

    $stmt = $dbh->prepare($sql);
    $stmt->execute();

    // 1レコード分しか取得しないのでfetchも1回で良い
    $area = $stmt->fetch(PDO::FETCH_ASSOC);

    // 友達データの取得をする
    // friendsテーブルからデータを取得する
    $sql = 'SELECT * FROM `friends` WHERE `area_id` = ' . $area_id;
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    // Object型のデータをArray型に変換する
    $friends = array();

    // 男女カウント用の変数
    $male = 0;
    $female = 0;

    while (1) {
        // fetchする
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        // 空だったらbreak
        if ($record == false) {
          break;
        }
        // $friends配列に値を代入する
        $friends[] = $record;

        // 男女のカウントプログラム
        if ($record['gender'] == 0) { // その友達が男だったら
            $male++; // 自己代入文
            // $male = $male + 1;
        } elseif ($record['gender'] == 1) { // その友達が女だったら
            $female++;
        }
    }

    // echo '<pre>';
    // var_dump($friends);
    // echo '</pre>';

    // HTMLと連携して表示する

    echo '<br>';
    echo '<br>';
    // var_dump($area);
    // echo $male;
    // echo '<br>';
    // echo $female;

 ?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>myFriends</title>

    <!-- Bootstrap -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="assets/css/form.css" rel="stylesheet">
    <link href="assets/css/timeline.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

  </head>
  <body>
  <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
          <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header page-scroll">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="index.php"><span class="strong-title"><i class="fa fa-facebook-square"></i> My friends</span></a>
          </div>
          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav navbar-right">
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-md-4 content-margin-top">
      <legend><?php echo $area['area_name']; ?>の友達</legend>
      <div class="well">男性：<?php echo $male; ?>名　女性：<?php echo $female; ?>名</div>
        <table class="table table-striped table-hover table-condensed">
          <thead>
            <tr>
              <th><div class="text-center">名前</div></th>
              <th><div class="text-center"></div></th>
            </tr>
          </thead>
          <tbody>
            <!-- 友達の名前を表示 -->
            <?php foreach ($friends as $friend) : ?>
            <tr>
              <td><div class="text-center"><?php echo $friend['friend_name']; ?></div></td>
              <td>
                <div class="text-center">
                  <a href="edit.php?friend_id=<?php echo $friend['friend_id']; ?>"><i class="fa fa-pencil"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="javascript:void(0);" onclick="destroy(<?php echo $friend['friend_id']; ?>);"><i class="fa fa-trash"></i></a>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>

          </tbody>
        </table>

        <input type="button" class="btn btn-default" value="新規作成" onClick="location.href='new.php'">
      </div>
    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
      // JavaScriptコード
      // JavaScriptとは、ブラウザ上で実行されるスクリプト言語
      function destroy(friend_id) { // 自作関数 (組込み関数)
        // alert('ほげ'); // ポップアップでメッセージを表示する

        var del = confirm('削除しますか？'); // OKを押すとtrueを返し、cancelを押すとfalseを返す

        if (del == true) {
          // OKの処理

          // PHPでいうheader('Location: show.php?.........');
          location.href = 'show.php?action=delete&friend_id=' + friend_id;
          return true;

        } else {
          // cancelの処理
          return false;
        }

      }
    </script>
  </body>
</html>












