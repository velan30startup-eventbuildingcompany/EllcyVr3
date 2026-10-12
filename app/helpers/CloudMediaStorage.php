<?php
/** Persistent admin media storage for serverless production deployments. */
final class CloudMediaStorage
{
    public static function isConfigured(): bool
    {
        return self::cloudName() !== '' && self::apiKey() !== '' && self::apiSecret() !== '';
    }

    /** @return array{url:string,key:string,provider:string} */
    public static function upload(string $filePath, string $resourceType, string $folder = 'ellcy/services'): array
    {
        if (!self::isConfigured()) throw new RuntimeException('Cloud media storage is not configured.');
        if (!in_array($resourceType, ['image', 'video'], true) || !is_file($filePath)) {
            throw new InvalidArgumentException('Invalid media upload.');
        }
        if (!function_exists('curl_init')) throw new RuntimeException('The PHP cURL extension is required for cloud uploads.');

        $timestamp = time();
        $signature = sha1('folder=' . $folder . '&timestamp=' . $timestamp . self::apiSecret());
        $handle = curl_init(sprintf(
            'https://api.cloudinary.com/v1_1/%s/%s/upload',
            rawurlencode(self::cloudName()),
            $resourceType
        ));
        curl_setopt_array($handle, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT => $resourceType === 'video' ? 90 : 30,
            CURLOPT_POSTFIELDS => [
                'file' => new CURLFile($filePath),
                'api_key' => self::apiKey(),
                'timestamp' => (string)$timestamp,
                'folder' => $folder,
                'signature' => $signature,
            ],
        ]);
        $body = curl_exec($handle);
        $status = (int)curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
        $error = curl_error($handle);
        curl_close($handle);
        $payload = is_string($body) ? json_decode($body, true) : null;
        if ($status < 200 || $status >= 300 || !is_array($payload) || empty($payload['secure_url']) || empty($payload['public_id'])) {
            error_log('[ELLCY] Cloud media upload failed: HTTP ' . $status . ' ' . $error);
            throw new RuntimeException('The cloud media upload did not complete.');
        }
        return ['url' => (string)$payload['secure_url'], 'key' => (string)$payload['public_id'], 'provider' => 'cloudinary'];
    }

    public static function delete(string $key, string $resourceType): void
    {
        if (!self::isConfigured() || $key === '' || !in_array($resourceType, ['image', 'video'], true) || !function_exists('curl_init')) return;
        $timestamp = time();
        $signature = sha1('public_id=' . $key . '&timestamp=' . $timestamp . self::apiSecret());
        $handle = curl_init(sprintf(
            'https://api.cloudinary.com/v1_1/%s/%s/destroy',
            rawurlencode(self::cloudName()),
            $resourceType
        ));
        curl_setopt_array($handle, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_POSTFIELDS => [
                'public_id' => $key,
                'api_key' => self::apiKey(),
                'timestamp' => (string)$timestamp,
                'signature' => $signature,
            ],
        ]);
        curl_exec($handle);
        curl_close($handle);
    }

    private static function cloudName(): string { return trim((string)getenv('CLOUDINARY_CLOUD_NAME')); }
    private static function apiKey(): string { return trim((string)getenv('CLOUDINARY_API_KEY')); }
    private static function apiSecret(): string { return trim((string)getenv('CLOUDINARY_API_SECRET')); }
}
