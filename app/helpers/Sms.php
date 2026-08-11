<?php
/**
 * ELLCY — SMS Helper
 *
 * ⚠ IMPORTANT — read before relying on phone-OTP login in production:
 * This does NOT send a real text message yet. No SMS gateway is
 * configured (no Twilio / MSG91 / Fast2SMS / etc. account or API
 * key was provided), so send() currently just writes the OTP to
 * the PHP error log as a stand-in — good enough to test the whole
 * login flow end-to-end locally, but real users will never
 * receive a text until this is wired up to an actual provider.
 *
 * To go live: sign up with an SMS gateway that supports sending to
 * Indian mobile numbers, then replace the body of send() below
 * with that provider's API call (most are a single HTTP POST).
 * Keep the API key in an environment variable or a config file
 * that is NOT committed to the repo — never hardcode it here.
 */
class Sms {

    /** Send a 6-digit OTP to a normalized 10-digit Indian number.
     *  Returns true if the send call itself succeeded (or, in dev
     *  mode, always true since there's nothing that can fail). */
    public static function sendOtp(string $normalizedPhone, string $code): bool {
        $message = "Your ELLCY login code is {$code}. It expires in 5 minutes. Don't share this code with anyone.";

        // ── DEV MODE (current state) ────────────────────────────
        // No gateway configured — log instead of sending, so the
        // flow is fully testable locally. Remove this block once a
        // real provider is wired in below.
        error_log("[ELLCY DEV MODE] OTP for +91{$normalizedPhone}: {$code}");
        return true;

        // ── PRODUCTION — uncomment and fill in once you have a
        // gateway account. Example shape for a typical REST-based
        // SMS API (adjust fields/endpoint to your provider's docs):
        //
        // $response = @file_get_contents('https://api.yoursmsgateway.com/v1/send', false, stream_context_create([
        //     'http' => [
        //         'method'  => 'POST',
        //         'header'  => "Content-Type: application/json\r\nAuthorization: Bearer " . getenv('SMS_API_KEY') . "\r\n",
        //         'content' => json_encode([
        //             'to'      => '91' . $normalizedPhone,
        //             'message' => $message,
        //         ]),
        //     ],
        // ]));
        // return $response !== false;
    }
}
