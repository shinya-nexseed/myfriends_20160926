<?php
    // DB接続
    $dsn = 'mysql:dbname=myfriends;host=localhost';
    $user = 'root';
    $password = 'mysql';
    $dbh = new PDO($dsn, $user, $password);
    $dbh->query('SET NAMES utf8');

    // 編集対象となる友達データ一件を取得
    $friend_id = $_GET['friend_id'];
    $sql = 'SELECT * FROM `friends` WHERE `friend_id`=' . $friend_id;

    $stmt = $dbh->prepare($sql);
    $stmt->execute();

    // フォームの値 (value) に取得したデータを表示
    $friends = $stmt->fetch(PDO::FETCH_ASSOC);

    echo '<br>';
    echo '<br>';
    echo '<pre>';
    var_dump($friends);
    echo '</pre>';
    echo $friends["friend_name"];

    // areasテーブルのデータ全件取得
    $sql = 'SELECT * FROM `areas` WHERE 1';
    $stmt = $dbh->prepare($sql);
    $stmt->execute();

    $areas = array();

    while (1) {
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($record == false) {
            break;
        }

        $areas[] = $record;
    }

    // $_POSTが存在すれば更新処理 (UPDATE)
    if (!empty($_POST)) {
        // UPDATE文を作成して実行
        $sql = 'UPDATE `friends` SET `friend_name`=?, `area_id`=?, `gender`=?, `age`=?
                                 WHERE `friend_id`=?';
        $edit_data[] = $_POST['name'];
        $edit_data[] = $_POST['area_id'];
        $edit_data[] = $_POST['gender'];
        $edit_data[] = $_POST['age'];
        $edit_data[] = $friend_id;

        $stmt = $dbh->prepare($sql);
        $stmt->execute($edit_data);

        // header()でindexに遷移
        header('Location: index.php');
        exit();
    }
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
        <legend>友達の編集</legend>
        <form method="post" action="edit.php?friend_id=<?php echo $friend_id; ?>" class="form-horizontal" role="form">
            <!-- 名前 -->
            <div class="form-group">
              <label class="col-sm-2 control-label">名前</label>
              <div class="col-sm-10">
                <input type="text" name="name" class="form-control" placeholder="例 : 山田　太郎" value="<?php echo $friends['friend_name']; ?>">
              </div>
            </div>
            <!-- 出身 -->
            <div class="form-group">
              <label class="col-sm-2 control-label">出身</label>
              <div class="col-sm-10">
                <select class="form-control" name="area_id">
                  <option value="0">出身地を選択</option>
                  <!--
                    ①new.phpを参考に47都道府県データを繰り返し表示
                    ②$friendsのarea_idと、繰り返し生成される$areaのarea_idが一致したらselectedをoptionタグに付ける
                   -->
                  <?php foreach ($areas as $area) : ?>
                    <?php if($area['area_id'] == $friends['area_id']): ?>
                      <option value="<?php echo $area['area_id']; ?>" selected><?php echo $area['area_name']; ?></option>
                    <?php else: ?>
                      <option value="<?php echo $area['area_id']; ?>"><?php echo $area['area_name']; ?></option>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <!-- 性別 -->
            <div class="form-group">
              <label class="col-sm-2 control-label">性別</label>
              <div class="col-sm-10">
                <select class="form-control" name="gender">
                  <option value="0">性別を選択</option>
                  <!--
                    if文を使って
                    もしDBから取得した友達データのgenderの値が0だった場合は男性にselectedを、
                    1だった場合は女性にselectedをつける
                   -->
                  <?php if($friends['gender'] == 0) : ?>
                    <option value="0" selected>男性</option>
                    <option value="1">女性</option>
                  <?php else: ?>
                    <option value="0">男性</option>
                    <option value="1" selected>女性</option>
                  <?php endif; ?>
                </select>
              </div>
            </div>
            <!-- 年齢 -->
            <div class="form-group">
              <label class="col-sm-2 control-label">年齢</label>
              <div class="col-sm-10">
                <input type="text" name="age" class="form-control" placeholder="例：27" value="<?php echo $friends['age']; ?>">
              </div>
            </div>

          <input type="submit" class="btn btn-default" value="更新">
        </form>
      </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
