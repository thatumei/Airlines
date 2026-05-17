<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tripType   = $_POST['tripType'] === 'round' ? '往復' : '片道';
    $depCode    = $_POST['departure']  ?? '';
    $destCode   = $_POST['destination']?? '';
    $depDate    = $_POST['depDate']    ?? '';
    $retDate    = (!empty($_POST['retDate'])) ? $_POST['retDate'] : '---';
    $seatClass  = $_POST['seatClass']  ?? '';
    $timestamp  = date("Y-m-d H:i:s");

    // デフォルト表示（変換できなかった場合用）
    $departureName   = $depCode;
    $destinationName = $destCode;

    // JSONファイルを読み込んで空港名にマッピング変換する処理
    if (file_exists('airports.json')) {
        $jsonString = file_get_contents('airports.json');
        $airportsData = json_decode($jsonString, true);

        if (is_array($airportsData)) {
            foreach ($airportsData as $airport) {
                if ($airport['code'] === $depCode) {
                    $departureName = $airport['name'];
                }
                if ($airport['code'] === $destCode) {
                    $destinationName = $airport['name'];
                }
            }
        }
    }

    // CSV保存処理（日本語の空港名に直して保存）
    $reserveData = [$timestamp, $tripType, $departureName, $destinationName, $depDate, $retDate, $seatClass];
    $file = fopen('data.csv', 'a');
    fputcsv($file, $reserveData, ",", "\"", "\\");
    fclose($file);
    ?>
    <!DOCTYPE html>
    <html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>予約完了 | レモン航空</title>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="style.css">
    </head>
    <body>

        <header class="site-header">
            <div class="header-container">
                <h1 class="logo">
                    <span class="material-icons">flight_takeoff</span>
                    <span>Lemon Airlines</span>
                </h1>
            </div>
        </header>

        <main class="main-content">
            <div class="result-card">
                <div class="success-title">
                    <span class="material-icons">check_circle</span>
                    <span>サーバーへの予約データ保存が完了しました。</span>
                </div>
                
                <h3>予約内容の確認</h3>
                <table>
                    <tr><th>予約照会日時</th><td><?php echo $timestamp; ?></td></tr>
                    <tr><th>旅程タイプ</th><td><?php echo $tripType; ?></td></tr>
                    <tr><th>出発地</th><td><?php echo $departureName; ?> (<?php echo $depCode; ?>)</td></tr>
                    <tr><th>目的地</th><td><?php echo $destinationName; ?> (<?php echo $destCode; ?>)</td></tr>
                    <tr><th>出発日</th><td><?php echo $depDate; ?></td></tr>
                    <tr><th>帰着日</th><td><?php echo $retDate; ?></td></tr>
                    <tr><th>座席クラス</th><td><?php echo $seatClass; ?></td></tr>
                </table>

                <div style="text-align: center; margin-top: 30px;">
                    <a href="index.html" class="btn-back">
                        <span class="material-icons">arrow_back</span> トップページへ戻る
                    </a>
                </div>
            </div>
        </main>

        <footer class="site-footer">
            <div class="footer-container">
                <p class="copyright">&copy; 2026 Lemon Airlines. All Rights Reserved.</p>
            </div>
        </footer>

    </body>
    </html>
    <?php
} else {
    header("Location: index.html");
    exit;
}
?>