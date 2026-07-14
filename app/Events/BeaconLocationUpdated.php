<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BeaconLocationUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly string $token,
        public readonly float  $lat,
        public readonly float  $lng,
        public readonly ?int   $batteryLevel,
        public readonly ?float $speed,
    ) {}

    public function broadcastOn(): Channel
    {
        // Channel publik berdasarkan token — siapapun yang punya URL bisa subscribe
        return new Channel("beacon.{$this->token}");
    }

    public function broadcastAs(): string
    {
        return 'location.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'lat'           => $this->lat,
            'lng'           => $this->lng,
            'battery_level' => $this->batteryLevel,
            'speed'         => $this->speed,
            'timestamp'     => now()->toIso8601String(),
        ];
    }
}
