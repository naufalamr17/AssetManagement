<?php

namespace Tests\Unit;

use App\Services\AssetCodeGenerator;
use PHPUnit\Framework\TestCase;

class AssetCodeGeneratorTest extends TestCase
{
    public function test_kes_prefix_and_asset_parts_are_generated_consistently(): void
    {
        $attributes = (new AssetCodeGenerator())->attributes('KES', 2500000, 'Site Molore', 'Bangunan');

        $this->assertSame('KES', $attributes['company']);
        $this->assertSame('FAT & GA', $attributes['pic_dept']);
        $this->assertSame('KES FG 03-03', $attributes['prefix']);
    }

    public function test_mlp_keeps_its_existing_unprefixed_format(): void
    {
        $attributes = (new AssetCodeGenerator())->attributes('MLP', 1000000, 'Office Kendari', 'Peralatan');

        $this->assertSame('GA', $attributes['pic_dept']);
        $this->assertSame('GA 02-02', $attributes['prefix']);
    }
}
