<?php

namespace App\Models;

use App\Models\Traits\TenantAware;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoTutorial extends Model
{
    use HasFactory, TenantAware;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'tenant_id',
        'title',
        'description',
        'video_url',
        'thumbnail_url',
        'provider',
        'duration',
        'is_active',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'duration' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the tenant that owns the video tutorial.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the embedded video URL for iframe
     */
    public function getEmbeddedUrlAttribute()
    {
        $videoId = $this->extractVideoId();

        if ($this->provider === 'youtube') {
            return "https://www.youtube.com/embed/{$videoId}";
        } elseif ($this->provider === 'vimeo') {
            return "https://player.vimeo.com/video/{$videoId}";
        }

        return $this->video_url;
    }

    /**
     * Extract video ID from various URL formats
     */
    private function extractVideoId()
    {
        if ($this->provider === 'youtube') {
            // Match various YouTube URL formats
            $pattern = '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';
            preg_match($pattern, $this->video_url, $matches);
            return $matches[1] ?? null;
        } elseif ($this->provider === 'vimeo') {
            // Match Vimeo URL
            $pattern = '/(?:https?:\/\/)?(?:www\.)?vimeo\.com\/(\d+)/';
            preg_match($pattern, $this->video_url, $matches);
            return $matches[1] ?? null;
        }

        return null;
    }
}
