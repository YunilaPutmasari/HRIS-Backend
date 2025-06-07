<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 40px;">
    <div
        style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <h2 style="color: #333333;">Reset Password</h2>
        <p style="color: #555555;">
            Hai {{ $user->name ?? $user->email }},<br><br>
            Kami menerima permintaan untuk mengatur ulang password Anda. Klik tombol di bawah untuk melanjutkan.
        </p>

        <p style="text-align: center; margin: 30px 0;">
            <a href="{{ $url }}"
                style="background-color: #4f46e5; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold;">
                Reset Password
            </a>
        </p>

        <p style="color: #999999; font-size: 14px;">
            Jika Anda tidak meminta reset password, Anda dapat mengabaikan email ini.
        </p>

        <hr style="border: none; border-top: 1px solid #e0e0e0; margin: 30px 0;">

        <p style="color: #999999; font-size: 12px; text-align: center;">
            © {{ date('Y') }} {{ config('app.name') }}. Semua hak dilindungi.
        </p>
    </div>
</body>

</html>
