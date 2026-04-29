<?php

namespace App\Services;

class WebRtcConfigService
{
    public function iceServers(): array
    {
        $servers = [];

        $stunUrls = $this->normalizeUrls(config('services.webrtc.stun_urls', []));
        if ($stunUrls !== []) {
            $servers[] = ['urls' => $stunUrls];
        }

        $turnUrls = $this->normalizeUrls(config('services.webrtc.turn_urls', []));
        if ($turnUrls !== []) {
            $turnServer = ['urls' => $turnUrls];

            $username = trim((string) config('services.webrtc.turn_username', ''));
            $credential = trim((string) config('services.webrtc.turn_credential', ''));

            if ($username !== '' && $credential !== '') {
                $turnServer['username'] = $username;
                $turnServer['credential'] = $credential;
            }

            $servers[] = $turnServer;
        }

        if ($servers === []) {
            $servers[] = [
                'urls' => [
                    'stun:stun.l.google.com:19302',
                    'stun:stun1.l.google.com:19302',
                ],
            ];
        }

        return $servers;
    }

    private function normalizeUrls(array|string|null $value): array
    {
        if (is_string($value)) {
            $value = explode(',', $value);
        }

        if (! is_array($value)) {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn ($url) => trim((string) $url),
            $value
        )));
    }
}
