<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class PreventSqlInjection
{
    /**
     * Pola pola serangan SQL Injection yang sering digunakan hacker/eksploitasi.
     */
    protected array $dangerousPatterns = [
        '/\b(UNION\s+SELECT|UNION\s+ALL\s+SELECT)\b/i',
        '/\b(DROP\s+TABLE|DROP\s+DATABASE|TRUNCATE\s+TABLE)\b/i',
        '/\b(INSERT\s+INTO.+VALUES|DELETE\s+FROM|UPDATE.+SET)\b/i',
        '/\b(INFORMATION_SCHEMA\.(TABLES|COLUMNS)|SCHEMA\(\))\b/i',
        '/\b(BENCHMARK\s*\(|SLEEP\s*\(|WAITFOR\s+DELAY)\b/i',
        '/\b(OR\s+1\s*=\s*1|OR\s+\'1\'\s*=\s*\'1\'|OR\s+"1"\s*=\s*"1")\b/i',
        '/\b(AND\s+1\s*=\s*1|AND\s+1\s*=\s*2)\b/i',
        '/(\-\-|\#|\/\*).*$/i',
        '/\b(EXEC\s*\(|EXECUTE\s*\(|XP_CMDSHELL)\b/i',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Pengecualian field teks panjang seperti rich text editor / CV agar format html tidak false positive
        $inputs = $request->except(['_token', 'password', 'password_confirmation', 'cv_text', 'keterangan']);

        foreach ($inputs as $key => $value) {
            if (is_string($value)) {
                $decodedValue = urldecode($value);
                foreach ($this->dangerousPatterns as $pattern) {
                    if (preg_match($pattern, $value) || preg_match($pattern, $decodedValue)) {
                        Log::warning("SQL Injection attempt blocked!", [
                            'ip' => $request->ip(),
                            'url' => $request->fullUrl(),
                            'field' => $key,
                            'payload' => $value,
                            'user_agent' => $request->userAgent()
                        ]);

                        if ($request->expectsJson() || $request->ajax()) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Permintaan diblokir karena terdeteksi pola input yang berbahaya (SQL Injection Protection).'
                            ], 400);
                        }

                        return back()->withErrors([
                            $key => 'Karakter atau format input tidak diizinkan oleh sistem keamanan.'
                        ])->with('error', 'Sistem keamanan memblokir permintaan ini karena mengandung karakter berbahaya.');
                    }
                }
            }
        }

        return $next($request);
    }
}
