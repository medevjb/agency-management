<?php

namespace Tests\Unit;

use App\Models\Profile;
use App\Services\PlatformFeeCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformFeeCalculatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_rate_for_platform_name(): void
    {
        $this->assertSame(0.20, PlatformFeeCalculator::rateForPlatform('fiverr'));
        $this->assertSame(0.10, PlatformFeeCalculator::rateForPlatform('upwork'));
    }

    public function test_rate_matching_is_case_insensitive(): void
    {
        $this->assertSame(0.10, PlatformFeeCalculator::rateForPlatform('UPWORK'));
    }

    public function test_unknown_or_missing_platform_falls_back_to_the_default_rate(): void
    {
        $this->assertSame(0.20, PlatformFeeCalculator::rateForPlatform('unknown'));
        $this->assertSame(0.20, PlatformFeeCalculator::rateForPlatform(null));
    }

    public function test_rate_for_profile_reads_the_platform_off_the_profile(): void
    {
        $upwork = Profile::factory()->create(['platform' => 'upwork']);
        $fiverr = Profile::factory()->create(['platform' => 'fiverr']);

        $this->assertSame(0.10, PlatformFeeCalculator::rateFor($upwork->id));
        $this->assertSame(0.20, PlatformFeeCalculator::rateFor($fiverr->id));
        $this->assertSame(0.20, PlatformFeeCalculator::rateFor(null));
    }

    public function test_net_value_applies_the_platform_cut(): void
    {
        $this->assertSame(80.0, PlatformFeeCalculator::netFor(100.0, null));

        $upwork = Profile::factory()->create(['platform' => 'upwork']);
        $this->assertSame(90.0, PlatformFeeCalculator::netFor(100.0, $upwork->id));
    }
}
