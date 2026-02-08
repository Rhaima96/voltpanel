<?php

namespace Rhaima\VoltPanel\Auth;

use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorAuthentication
{
    protected Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    public function generateSecretKey(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    public function getQRCodeUrl(string $email, string $secret): string
    {
        return $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $email,
            $secret
        );
    }

    public function verify(string $secret, string $code): bool
    {
        return $this->google2fa->verifyKey($secret, $code);
    }

    public function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];

        for ($i = 0; $i < $count; $i++) {
            $codes[] = bin2hex(random_bytes(4)) . '-' . bin2hex(random_bytes(4));
        }

        return $codes;
    }

    public function hashRecoveryCodes(array $codes): array
    {
        return array_map(fn($code) => Hash::make($code), $codes);
    }

    public function verifyRecoveryCode(string $code, array $hashedCodes): bool
    {
        foreach ($hashedCodes as $hashedCode) {
            if (Hash::check($code, $hashedCode)) {
                return true;
            }
        }

        return false;
    }
}
