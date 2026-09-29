<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Pemberitahuan Hasil Seleksi - {{ config('app.name', 'HRDApps') }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            background-color: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #334155;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            background-color: #f8fafc;
            padding: 40px 0;
        }
        .container {
            max-width: 570px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .header {
            text-align: center;
            padding: 30px 20px 20px 20px;
            background-color: #ffffff;
        }
        .brand-logo {
            text-decoration: none;
            display: inline-block;
        }
        .brand-hrd {
            color: #2563eb;
            font-weight: 900;
            font-size: 28px;
            letter-spacing: -0.5px;
        }
        .brand-apps {
            color: #0f172a;
            font-weight: 900;
            font-size: 28px;
            letter-spacing: -0.5px;
        }
        .accent-bar {
            height: 4px;
            background: linear-gradient(90deg, #2563eb 0%, #3b82f6 50%, #60a5fa 100%);
            width: 100%;
        }
        .content {
            padding: 35px 40px;
            font-size: 15px;
            line-height: 1.7;
            color: #334155;
        }
        .greeting {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 16px;
        }
        .info-box {
            background-color: #f8fafc;
            border-left: 4px solid #94a3b8;
            border-radius: 6px;
            padding: 16px 20px;
            margin: 24px 0;
            color: #475569;
            font-size: 14px;
            line-height: 1.6;
        }
        .signature {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #475569;
            font-size: 14px;
        }
        .signature strong {
            color: #0f172a;
        }
        .footer {
            background-color: #f8fafc;
            padding: 24px 30px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <!-- Accent gradient line -->
            <div class="accent-bar"></div>

            <!-- Header with exact HRDApps branding -->
            <div class="header">
                <a href="{{ config('app.url') }}" class="brand-logo">
                    <span class="brand-hrd">HRD</span><span class="brand-apps">Apps</span>
                </a>
            </div>

            <!-- Body Content -->
            <div class="content">
                <div class="greeting">Halo, {{ $applicantName }}!</div>

                <p>
                    Terima kasih telah meluangkan waktu dan minat Anda untuk melamar di perusahaan kami melalui sistem rekrutmen <strong>HRDApps</strong>.
                </p>

                <p>
                    Kami telah meninjau kualifikasi dan berkas CV yang Anda kirimkan. Setelah melalui proses evaluasi yang cermat, saat ini kami memutuskan untuk <strong>belum dapat melanjutkan</strong> lamaran Anda ke tahap berikutnya karena penyesuaian kebutuhan tim kami saat ini.
                </p>

                <div class="info-box">
                    Data dan profil CV Anda tetap tersimpan dengan aman dalam basis data talenta kami. Apabila di masa mendatang terbuka kesempatan yang selaras dengan keahlian Anda, tim rekrutmen kami akan menghubungi Anda kembali.
                </div>

                <p>
                    Kami sangat mengapresiasi usaha dan antusiasme Anda, serta mendoakan kesuksesan dalam perjalanan karier profesional Anda ke depan.
                </p>

                <div class="signature">
                    Salam hangat,<br>
                    <strong>Tim Rekrutmen & HRDApps</strong>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p style="margin: 0;">&copy; {{ date('Y') }} {{ config('app.name', 'HRDApps') }}. Seluruh hak cipta dilindungi.</p>
                <p style="margin: 4px 0 0 0;">Email ini dibuat dan dikirim secara otomatis oleh sistem rekrutmen.</p>
            </div>
        </div>
    </div>
</body>
</html>
