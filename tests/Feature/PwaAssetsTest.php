<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class PwaAssetsTest extends TestCase
{
    /** @test [CONFIRMED] manifest.json exists and is valid JSON */
    public function manifest_json_exists_and_is_valid(): void
    {
        $path = public_path('manifest.json');
        $this->assertFileExists($path);

        $content = File::get($path);
        $manifest = json_decode($content, true);

        $this->assertIsArray($manifest, 'manifest.json must be valid JSON');
        $this->assertArrayHasKey('name', $manifest);
        $this->assertArrayHasKey('icons', $manifest);
    }

    /** @test [CONFIRMED] all icons referenced in manifest.json exist on disk */
    public function icons_referenced_in_manifest_exist(): void
    {
        $manifest = json_decode(File::get(public_path('manifest.json')), true);

        foreach ($manifest['icons'] as $icon) {
            $src = ltrim($icon['src'], '/');
            $iconPath = public_path($src);

            $this->assertFileExists($iconPath, "Icon file missing: {$src}");

            // Verify size if sizes attribute exists and is square (e.g., 192x192)
            if (isset($icon['sizes']) && preg_match('/^(\d+)x\d+$/', $icon['sizes'], $matches)) {
                $expectedSize = (int) $matches[1];
                $imageSize = getimagesize($iconPath);
                
                $this->assertNotFalse($imageSize, "File is not a valid image: {$src}");
                $this->assertEquals($expectedSize, $imageSize[0], "Width mismatch for {$src}");
                $this->assertEquals($expectedSize, $imageSize[1], "Height mismatch for {$src}");
            }
        }
    }

    /** @test [CONFIRMED] service worker exists */
    public function service_worker_exists(): void
    {
        $this->assertFileExists(public_path('sw.js'));
    }

    /** @test [CONFIRMED] apple-touch-icon exists and is linked in layout */
    public function apple_touch_icon_exists(): void
    {
        $this->assertFileExists(public_path('apple-touch-icon.png'));
    }
}
