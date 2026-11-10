<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\HotelComplianceProfile;
use App\Support\NativeAppKey;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Encryption\Encrypter;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;

class NativeAppKeyTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->directory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'hellen-key-' . bin2hex(random_bytes(8));

        mkdir($this->directory);
    }

    protected function tearDown(): void
    {
        Model::encryptUsing(null);

        (new Filesystem())->remove($this->directory);

        parent::tearDown();
    }

    #[Test]
    public function installations_have_distinct_persistent_keys_and_isolated_credentials(): void
    {
        mkdir($this->path('other'));

        $first = NativeAppKey::load($this->directory);
        $second = NativeAppKey::load($this->path('other'));

        $this->assertSame($first, NativeAppKey::load($this->directory));
        $this->assertNotSame($first, $second);

        Model::encryptUsing(new Encrypter(base64_decode(substr($first, 7)), 'AES-256-CBC'));

        $profile = new HotelComplianceProfile();
        $profile->credentials = ['secret' => 'LAB-TRA-TOKEN-0000-not-real'];
        $ciphertext = $profile->getAttributes()['credentials'];

        $this->assertStringNotContainsString('LAB-TRA-TOKEN-0000-not-real', $ciphertext);
        $this->assertSame('LAB-TRA-TOKEN-0000-not-real', $profile->credentials['secret']);

        Model::encryptUsing(new Encrypter(base64_decode(substr($second, 7)), 'AES-256-CBC'));

        $otherProfile = new HotelComplianceProfile();
        $otherProfile->setRawAttributes(['credentials' => $ciphertext]);

        $this->expectException(DecryptException::class);

        $otherProfile->credentials;
    }

    #[Test]
    public function missing_or_invalid_existing_keys_fail_without_replacement(): void
    {
        NativeAppKey::load($this->directory);

        file_put_contents($this->path('app.key'), 'invalid');

        try {
            NativeAppKey::load($this->directory);

            $this->fail('An invalid key must not be replaced.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('invalid', $exception->getMessage());
            $this->assertSame('invalid', file_get_contents($this->path('app.key')));
        }

        unlink($this->path('app.key'));

        $this->expectExceptionMessage('encryption key is missing');

        NativeAppKey::load($this->directory);
    }

    #[Test]
    public function inaccessible_directory_fails(): void
    {
        $this->expectException(RuntimeException::class);

        NativeAppKey::load($this->path('missing'));
    }

    private function path(string $name): string
    {
        return $this->directory . DIRECTORY_SEPARATOR . $name;
    }
}
