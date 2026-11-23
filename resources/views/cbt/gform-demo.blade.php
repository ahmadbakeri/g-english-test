<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Google Form CBT</title>
    <style>
        body { margin: 0; padding: 24px 16px; background: #f0ebf8; font-family: 'Segoe UI', Roboto, Arial, sans-serif; color: #202124; }
        .wrap { max-width: 640px; margin: 0 auto; display: flex; flex-direction: column; gap: 14px; }
        .card { background: #fff; border: 1px solid #dadce0; border-radius: 10px; padding: 22px 24px; }
        .header-card { border-top: 10px solid #0a4282; }
        h1 { margin: 0 0 8px 0; font-size: 24px; font-weight: 700; }
        p { margin: 0; font-size: 14px; color: #5f6368; line-height: 1.5; }
        .q-title { font-size: 15px; font-weight: 600; margin-bottom: 14px; }
        .opt { display: flex; align-items: center; gap: 10px; padding: 8px 10px; border-radius: 6px; cursor: pointer; font-size: 14px; }
        .opt:hover { background: #f8f9fa; }
        .btn { background: #0a4282; color: #fff; border: none; padding: 11px 26px; border-radius: 6px; font-size: 14px; font-weight: 700; cursor: pointer; }
        .btn:hover { background: #083366; }
        .badge { display: inline-block; background: #fbb900; color: #0a4282; font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 999px; margin-bottom: 8px; text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="wrap">
        @if(!empty($submitted))
            <div class="card header-card">
                <span class="badge">Terkirim</span>
                <h1>Jawaban Anda telah direkam</h1>
                <p style="margin-top: 8px;">Terima kasih telah menyelesaikan kuis testing ini. Sekarang silakan klik tombol <strong>Akhiri Tes</strong> di bagian kanan atas layar.</p>
            </div>
        @else
            <form method="POST" action="{{ url('/cbt-gform-demo') }}" class="wrap" style="margin: 0; max-width: 100%;">
                @csrf
                <div class="card header-card">
                    <span class="badge">Google Form Testing CBT</span>
                    <h1>English Quick Quiz (Demo Form)</h1>
                    <p>Pilih jawaban yang tepat lalu klik tombol <strong>Kirim</strong> di bagian paling bawah untuk memunculkan tombol <strong>Akhiri Tes</strong>.</p>
                </div>

                <div class="card">
                    <div class="q-title">1. She ___ to school every morning. <span style="color:#d93025">*</span></div>
                    <label class="opt"><input type="radio" name="q1" value="go" required> go</label>
                    <label class="opt"><input type="radio" name="q1" value="goes"> goes</label>
                    <label class="opt"><input type="radio" name="q1" value="going"> going</label>
                    <label class="opt"><input type="radio" name="q1" value="gone"> gone</label>
                </div>

                <div class="card">
                    <div class="q-title">2. They ___ playing football right now. <span style="color:#d93025">*</span></div>
                    <label class="opt"><input type="radio" name="q2" value="is" required> is</label>
                    <label class="opt"><input type="radio" name="q2" value="am"> am</label>
                    <label class="opt"><input type="radio" name="q2" value="are"> are</label>
                    <label class="opt"><input type="radio" name="q2" value="be"> be</label>
                </div>

                <div class="card">
                    <div class="q-title">3. Yesterday, I ___ a new English book. <span style="color:#d93025">*</span></div>
                    <label class="opt"><input type="radio" name="q3" value="buy" required> buy</label>
                    <label class="opt"><input type="radio" name="q3" value="buys"> buys</label>
                    <label class="opt"><input type="radio" name="q3" value="bought"> bought</label>
                    <label class="opt"><input type="radio" name="q3" value="buying"> buying</label>
                </div>

                <div>
                    <button type="submit" class="btn">Kirim / Submit</button>
                </div>
            </form>
        @endif
    </div>
</body>
</html>
