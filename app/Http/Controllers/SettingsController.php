<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index(Request $request, ?string $group = 'general'): View
    {
        $groups = Setting::select('group')
            ->distinct()
            ->pluck('group')
            ->sort()
            ->values();

        if (! in_array($group, $groups->toArray())) {
            $group = $groups->first() ?? 'general';
        }

        $settings = Setting::group($group)
            ->get()
            ->keyBy('key');

        // Sync email settings with .env values
        if ($group === 'email') {
            $settings = $this->syncEnvWithDatabase($settings);
        }

        return view('settings.index', compact('group', 'groups', 'settings'));
    }

    /**
     * Sync .env values with database settings for email.
     */
    protected function syncEnvWithDatabase($settings)
    {
        $envMappings = [
            'mail_mailer' => 'MAIL_MAILER',
            'mail_host' => 'MAIL_HOST',
            'mail_port' => 'MAIL_PORT',
            'mail_username' => 'MAIL_USERNAME',
            'mail_password' => 'MAIL_PASSWORD',
            'mail_encryption' => 'MAIL_ENCRYPTION',
            'mail_from_address' => 'MAIL_FROM_ADDRESS',
            'mail_from_name' => 'MAIL_FROM_NAME',
        ];

        foreach ($envMappings as $settingKey => $envKey) {
            if ($settings->has($settingKey)) {
                $envValue = env($envKey);

                // Update database value if different from env (but don't save to DB, just display)
                if ($envValue !== null && $envValue !== $settings->get($settingKey)->value) {
                    $settings->get($settingKey)->value = $envValue;
                }
            }
        }

        return $settings;
    }

    /**
     * Update settings for a specific group.
     */
    public function update(Request $request, string $group)
    {
        $settings = Setting::group($group)->get()->keyBy('key');

        foreach ($settings as $key => $setting) {
            $value = $request->input($key);

            // Handle file uploads for image types
            if ($setting->type === 'image' && $request->hasFile($key)) {
                // Delete old image if exists
                if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                    Storage::disk('public')->delete($setting->value);
                }

                $path = $request->file($key)->store('settings', 'public');
                $value = $path;
            }

            // Handle checkbox/boolean values
            if (in_array($setting->type, ['checkbox', 'boolean'])) {
                $value = $request->has($key) ? '1' : '0';
            }

            $setting->update(['value' => $value]);
        }

        // Update .env file for email settings
        if ($group === 'email') {
            $this->updateEnvFile($settings);
        }

        return redirect()
            ->route('settings.group', ['group' => $group])
            ->with('success', ucfirst($group).' settings updated successfully.');
    }

    /**
     * Update .env file with email settings.
     */
    protected function updateEnvFile($settings): void
    {
        $envPath = base_path('.env');

        if (! file_exists($envPath)) {
            return;
        }

        $envContent = file_get_contents($envPath);

        // Mapping of setting keys to env keys
        $envMappings = [
            'mail_mailer' => 'MAIL_MAILER',
            'mail_host' => 'MAIL_HOST',
            'mail_port' => 'MAIL_PORT',
            'mail_username' => 'MAIL_USERNAME',
            'mail_password' => 'MAIL_PASSWORD',
            'mail_encryption' => 'MAIL_ENCRYPTION',
            'mail_from_address' => 'MAIL_FROM_ADDRESS',
            'mail_from_name' => 'MAIL_FROM_NAME',
        ];

        foreach ($envMappings as $settingKey => $envKey) {
            if ($settings->has($settingKey)) {
                $value = $settings->get($settingKey)->value;

                // Escape special characters and wrap in quotes
                if ($value !== '' && $value !== null) {
                    $escapedValue = '"'.str_replace('"', '\\"', $value).'"';
                } else {
                    $escapedValue = '""';
                }

                // Check if the key exists in .env
                $pattern = '/^'.$envKey.'=.*/m';

                if (preg_match($pattern, $envContent)) {
                    // Update existing key
                    $envContent = preg_replace(
                        $pattern,
                        $envKey.'='.$escapedValue,
                        $envContent
                    );
                } else {
                    // Add new key if not exists
                    $envContent .= "\n".$envKey.'='.$escapedValue;
                }
            }
        }

        file_put_contents($envPath, $envContent);
    }

    /**
     * Delete a setting value (for images/files).
     */
    public function deleteValue(string $key)
    {
        $setting = Setting::where('key', $key)->firstOrFail();

        if ($setting->type === 'image' && $setting->value && Storage::disk('public')->exists($setting->value)) {
            Storage::disk('public')->delete($setting->value);
        }

        $setting->update(['value' => null]);

        return back()->with('success', 'Setting value cleared successfully.');
    }

    /**
     * Add a new project status.
     */
    public function addProjectStatus(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:50|regex:/^[a-z0-9-]+$/',
            'label' => 'required|string|max:100',
            'icon' => 'required|string|max:10',
            'color' => 'required|string|max:100',
        ]);

        $statusesSetting = Setting::where('key', 'project_statuses')->first();

        if (! $statusesSetting) {
            // Create the setting if it doesn't exist
            $statuses = [];
        } else {
            $statuses = $statusesSetting->getValue();
        }

        // Check if key already exists
        $existingKey = collect($statuses)->firstWhere('key', $validated['key']);
        if ($existingKey) {
            return back()->with('error', 'Status key already exists.');
        }

        $statuses[] = [
            'key' => $validated['key'],
            'label' => $validated['label'],
            'icon' => $validated['icon'],
            'color' => $validated['color'],
            'default' => false,
        ];

        Setting::updateOrCreate(
            ['key' => 'project_statuses'],
            [
                'group' => 'project',
                'value' => json_encode($statuses),
                'type' => 'json',
                'label' => 'Project Statuses',
                'description' => 'Define available project statuses with icons and colors',
                'is_public' => false,
            ]
        );

        return back()->with('success', 'Project status added successfully.');
    }

    /**
     * Update a project status.
     */
    public function updateProjectStatus(Request $request, string $key)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:100',
            'icon' => 'required|string|max:10',
            'color' => 'required|string|max:100',
        ]);

        $statusesSetting = Setting::where('key', 'project_statuses')->firstOrFail();
        $statuses = $statusesSetting->getValue();

        // Find and update the status
        $found = false;
        foreach ($statuses as &$status) {
            if ($status['key'] === $key) {
                $status['label'] = $validated['label'];
                $status['icon'] = $validated['icon'];
                $status['color'] = $validated['color'];
                $found = true;
                break;
            }
        }

        if (! $found) {
            return back()->with('error', 'Project status not found.');
        }

        $statusesSetting->update(['value' => json_encode($statuses)]);

        return back()->with('success', 'Project status updated successfully.');
    }

    /**
     * Delete a project status.
     */
    public function deleteProjectStatus(string $key)
    {
        $statusesSetting = Setting::where('key', 'project_statuses')->firstOrFail();
        $statuses = $statusesSetting->getValue();

        // Don't allow deleting default statuses
        $defaultStatuses = ['nra', 'pending', 'in-progress', 'completed', 'on-hold', 'cancelled'];
        if (in_array($key, $defaultStatuses)) {
            return back()->with('error', 'Cannot delete default project status.');
        }

        // Remove the status
        $statuses = collect($statuses)->reject(fn ($status) => $status['key'] === $key)->values()->toArray();

        $statusesSetting->update(['value' => json_encode($statuses)]);

        return back()->with('success', 'Project status deleted successfully.');
    }
}
