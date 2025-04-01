<?php
require_once 'vendor/autoload.php';
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
class JWTHandler
{
    private $secret_key;
    public function __construct()
    {
        $this->secret_key = "HUTECH"; // Thay thế bằng khóa bí mật của bạn
    }
    // Tạo JWT
    public function encode($data)
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600; // jwt valid for 1 hour from the issued time
        $payload = array(
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'data' => $data
        );
        return JWT::encode($payload, $this->secret_key, 'HS256');
        // $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        // $base64Header = base64_encode($header);
        // $base64Payload = base64_encode(json_encode($payload));
        // $signature = hash_hmac('sha256', $base64Header . "." . $base64Payload, $this->secretKey, true);
        // $base64Signature = base64_encode($signature);

        // return $base64Header . "." . $base64Payload . "." . $base64Signature;
    }
    // Giải mã JWT
    public function decode($jwt)
    {
        try {
            $decoded = JWT::decode($jwt, new Key($this->secret_key, 'HS256'));
            return (array) $decoded->data;
        } catch (Exception $e) {
            return null;
        }
    //     $parts = explode('.', $jwt);
    //     if (count($parts) !== 3) {
    //         return null; // JWT không hợp lệ
    //     }

    //     $header = base64_decode($parts[0]);
    //     $payload = base64_decode($parts[1]);
    //     $signatureProvided = $parts[2];

    //     // Tạo lại chữ ký để so sánh
    //     $signature = hash_hmac('sha256', $parts[0] . "." . $parts[1], $this->secretKey, true);
    //     $base64Signature = base64_encode($signature);

    //     if ($base64Signature !== $signatureProvided) {
    //         return null; // Chữ ký không khớp
    //     }

    //     return json_decode($payload, true); // Trả về payload nếu hợp lệ
    // }
    // }
    }
}
?>