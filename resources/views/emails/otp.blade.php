<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kode OTP Reset Password</title>
    <style>
        body {
            font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            background-color: #f0f4f8;
            padding: 40px 0;
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
        .header {
            background-color: #0b5ed7; /* Warna primary HRDApps (biru) */
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: 0.5px;
        }
        .content {
            padding: 35px 30px;
            color: #334155;
            line-height: 1.6;
            font-size: 15px;
        }
        .otp-container {
            background-color: #f8fafc;
            border: 2px dashed #0b5ed7;
            border-radius: 10px;
            text-align: center;
            padding: 25px 20px;
            margin: 30px 0;
        }
        .otp-title {
            margin: 0 0 10px 0;
            font-size: 13px;
            text-transform: uppercase;
            font-weight: 700;
            color: #64748b;
            letter-spacing: 1px;
        }
        .otp-code {
            font-size: 38px;
            font-weight: 800;
            color: #0b5ed7;
            letter-spacing: 8px;
            margin: 0;
        }
        .footer {
            background-color: #f8fafc;
            padding: 25px 30px;
            text-align: center;
            font-size: 13px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
        .text-bold {
            font-weight: 700;
            color: #1e293b;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>HRDApps</h1>
            </div>
            <div class="content">
                <p>Halo,</p>
                <p>Kami menerima permintaan untuk melakukan <strong>reset password</strong> pada akun Anda di HRDApps Management System.</p>
                <p>Untuk melanjutkan proses ini, silakan masukkan kode OTP (<em>One-Time Password</em>) berikut:</p>
                
                <div class="otp-container">
                    <p class="otp-title">Kode OTP Anda</p>
                    <p class="otp-code">{{ $otp }}</p>
                </div>
                
                <p>Kode OTP di atas bersifat rahasia dan hanya berlaku. <span class="text-bold">Mohon untuk tidak membagikan kode ini</span> kepada siapa pun, termasuk pihak perusahaan demi keamanan akun Anda.</p>
                <p>Jika Anda tidak merasa melakukan permintaan reset password, Anda dapat mengabaikan dan menghapus email ini. Akun Anda tetap aman.</p>
                
                <br>
                <p>Salam hangat,<br><span class="text-bold">Tim Sistem HRDApps</span></p>
            </div>
            <div class="footer">
                <p>&copy; {{ date('Y') }} HRDApps Management System.<br>Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </div>
</body>
</html>
