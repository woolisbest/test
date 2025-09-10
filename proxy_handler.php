<?php
// ユーザーが送信したURLを取得
if (isset($_GET['url'])) {
    $url = $_GET['url'];

    // 入力されたURLが有効かどうか確認
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        
        // cURLを使って外部サイトにリクエストを送信
        $ch = curl_init();

        // cURLオプションの設定
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);  // リダイレクトを許可
        curl_setopt($ch, CURLOPT_HEADER, true);  // ヘッダーも取得
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);  // ユーザーエージェントの設定

        // リクエスト実行
        $response = curl_exec($ch);

        // ステータスコードを取得
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // エラーチェック
        if(curl_errno($ch)) {
            echo 'cURLエラー: ' . curl_error($ch);
        }

        // cURLセッションを閉じる
        curl_close($ch);

        // ステータスコードが200の場合、レスポンスを返す
        if ($status_code == 200) {
            // ヘッダー部分を分ける
            list($header, $body) = explode("\r\n\r\n", $response, 2);

            // レスポンスヘッダーを送信
            foreach (explode("\r\n", $header) as $hdr) {
                // 必要に応じてヘッダーをフィルタリングすることもできます
                if (stripos($hdr, 'Content-Type:') === 0 || stripos($hdr, 'Content-Length:') === 0) {
                    header($hdr);
                }
            }

            // HTMLボディ部分を出力
            echo $body;
        } else {
            echo "エラー: サイトにアクセスできませんでした (HTTPコード: $status_code)";
        }
    } else {
        echo "無効なURLです。";
    }
} else {
    echo "URLが指定されていません。";
}
?>
