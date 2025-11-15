<?php

namespace App\Services;

use App\Models\SystemSetting;

class SystemSettingService
{
    /**
     * Get a setting value by key with caching
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        // Try to get from cache first
        $cacheKey = "system_setting_{$key}";
        $cached = cache()->get($cacheKey);
        
        if ($cached !== null) {
            return $cached;
        }
        
        $setting = SystemSetting::where('key', $key)->first();
        $value = $setting ? $setting->value : $default;
        
        // Cache the value for 1 hour
        cache()->put($cacheKey, $value, now()->addHour());
        
        return $value;
    }

    /**
     * Set a setting value
     *
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @param string $group
     * @param string|null $description
     * @return SystemSetting
     */
    public function set(string $key, $value, string $type = 'string', string $group = 'general', string $description = null): SystemSetting
    {
        $setting = SystemSetting::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'description' => $description,
            ]
        );
        
        // Clear the cache for this setting
        cache()->forget("system_setting_{$key}");
        
        return $setting;
    }

    /**
     * Get all settings in a specific group
     *
     * @param string $group
     * @return \Illuminate\Support\Collection
     */
    public function getByGroup(string $group)
    {
        return SystemSetting::byGroup($group)->get();
    }

    /**
     * Check if a boolean setting is enabled
     *
     * @param string $key
     * @param bool $default
     * @return bool
     */
    public function isEnabled(string $key, bool $default = true): bool
    {
        $value = $this->get($key, $default);
        
        if (is_bool($value)) {
            return $value;
        }
        
        if (is_string($value)) {
            return strtolower($value) === 'true' || $value === '1';
        }
        
        return (bool) $value;
    }

    /**
     * Clear the cache for a specific setting
     *
     * @param string $key
     * @return void
     */
    public function clearCache(string $key): void
    {
        cache()->forget("system_setting_{$key}");
    }

    /**
     * Clear all settings cache
     *
     * @return void
     */
    public function clearAllCache(): void
    {
        // In Laravel, we can't directly clear keys with a pattern
        // So we'll have to clear all application cache
        // Alternatively, we could store key names and clear them individually
        cache()->clear();
    }
}