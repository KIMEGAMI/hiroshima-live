<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>メンテナンス中 | 広島ライブ</title>
</head>
<body style="margin:0;background:#09090b;color:#fff;font-family:Arial,'Hiragino Kaku Gothic ProN','Yu Gothic',sans-serif;">
    <main style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;text-align:center;">
        <section>
            <h1 style="font-size:42px;margin:0 0 16px;">メンテナンス中</h1>
            <p style="font-size:16px;color:#d4d4d8;margin:0 0 24px;">
                現在、一時的にページを表示できません。しばらく時間をおいてから再度お試しください。
            </p>
            <p style="font-size:14px;color:#a1a1aa;margin:0;">
                エラーコード：{{ method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 400 }}
            </p>
        </section>
    </main>
</body>
</html>