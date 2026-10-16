<?php

namespace App\Services;

use App\Models\SocialIdentity;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use LogicException;

class GoogleIdentityService
{
    public function link(User $user, array $claims): SocialIdentity
    {
        return DB::transaction(function () use ($user, $claims): SocialIdentity {
            $identity = SocialIdentity::where('provider', 'google')
                ->where('provider_user_id', $claims['provider_user_id'])
                ->lockForUpdate()
                ->first();

            if ($identity && $identity->user_id !== $user->id) {
                throw new LogicException('That Google account is already connected to another client account.');
            }

            $existingForUser = SocialIdentity::where('user_id', $user->id)->where('provider', 'google')->first();
            if ($existingForUser && $existingForUser->provider_user_id !== $claims['provider_user_id']) {
                throw new LogicException('A different Google account is already connected to this client account.');
            }

            return SocialIdentity::updateOrCreate(
                ['provider' => 'google', 'provider_user_id' => $claims['provider_user_id']],
                [
                    'user_id' => $user->id,
                    'provider_email' => $claims['provider_email'],
                    'avatar_url' => $claims['avatar_url'],
                ],
            );
        });
    }

    public function claims(array $raw, string $providerUserId, string $email, string $name, ?string $avatarUrl): array
    {
        return [
            'provider_user_id' => $providerUserId,
            'provider_email' => $email,
            'name' => $name,
            'avatar_url' => $avatarUrl,
            'email_verified' => (bool) ($raw['email_verified'] ?? false),
        ];
    }
}
