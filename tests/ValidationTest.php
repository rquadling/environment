<?php

/**
 * RQuadling/Environment
 *
 * LICENSE
 *
 * This is free and unencumbered software released into the public domain.
 *
 * Anyone is free to copy, modify, publish, use, compile, sell, or distribute this software, either in source code form or
 * as a compiled binary, for any purpose, commercial or non-commercial, and by any means.
 *
 * In jurisdictions that recognize copyright laws, the author or authors of this software dedicate any and all copyright
 * interest in the software to the public domain. We make this dedication for the benefit of the public at large and to the
 * detriment of our heirs and successors. We intend this dedication to be an overt act of relinquishment in perpetuity of
 * all present and future rights to this software under copyright law.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT
 * OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * For more information, please refer to <https://unlicense.org>
 *
 */

namespace RQuadlingTests\Environment;

use PHPUnit\Framework\TestCase;
use RQuadlingTests\Environment\Fixtures\Validation;

class ValidationTest extends TestCase
{
    /** @var Validation */
    private $validator;

    protected function setUp()
    {
        $this->validator = new Validation();
    }

    public function testValidationNoExampleOrEnv()
    {
        $directory = __DIR__.'/Fixtures/NoEnvironment';

        $this->assertFileNotExists($directory.'/.env');
        $this->assertFileNotExists($directory.'/.env.example');
        $this->assertEquals(Validation::VALIDATION_RESULT_OK, $this->validator->validateEnvironmentFiles($directory));
        $this->assertEquals(['No .env or .env.example files'], $this->validator->getMessages());
        $this->assertFileNotExists($directory.'/.env');
        $this->assertFileNotExists($directory.'/.env.example');
    }

    public function testValidationCopiesExampleToNewEnv()
    {
        $directory = __DIR__.'/Fixtures/ExampleOnly';

        $this->assertFileNotExists($directory.'/.env');
        $this->assertFileExists($directory.'/.env.example');
        $this->assertEquals(Validation::VALIDATION_RESULT_COPIED_EXAMPLE, $this->validator->validateEnvironmentFiles($directory));
        $this->assertFileExists($directory.'/.env');
        $this->assertFileExists($directory.'/.env.example');
        \unlink($directory.'/.env');

        $this->assertEquals(
            [
                '',
                'Copied default settings from .env.example to .env',
                '',
                'Please review the contents of the .env file.',
                '',
            ],
            $this->validator->getMessages()
        );
    }

    public function testValidationEnvOnly()
    {
        $directory = __DIR__.'/Fixtures/EnvOnly';

        $this->assertFileExists($directory.'/.env');
        $this->assertFileNotExists($directory.'/.env.example');
        $this->assertEquals(Validation::VALIDATION_RESULT_OK, $this->validator->validateEnvironmentFiles($directory));
        $this->assertFileExists($directory.'/.env');
        $this->assertFileNotExists($directory.'/.env.example');

        $this->assertEquals(
            [
                'No .env.example file',
            ],
            $this->validator->getMessages()
        );
    }

    /**
     * @dataProvider provideMatchingDirectories
     */
    public function testValidationMatching(string $directory)
    {
        $this->assertFileExists($directory.'/.env');
        $this->assertFileExists($directory.'/.env.example');
        $this->assertEquals(Validation::VALIDATION_RESULT_OK, $this->validator->validateEnvironmentFiles($directory));
        $this->assertFileExists($directory.'/.env');
        $this->assertFileExists($directory.'/.env.example');

        $this->assertEmpty($this->validator->getMessages());
    }

    public function provideMatchingDirectories()
    {
        return [
            'Matching but empty' => [__DIR__.'/Fixtures/MatchingEmpty'],
            'Matching' => [__DIR__.'/Fixtures/Matching'],
        ];
    }

    public function testValidationNewVarAdded()
    {
        $directory = __DIR__.'/Fixtures/NewVarAdded';
        $this->assertFileExists($directory.'/.env');

        $this->assertEquals(Validation::VALIDATION_RESULT_NEW_ENTRIES, $this->validator->validateEnvironmentFiles($directory));
        $this->assertEquals(
            [
                'New .env entries',
                '================',
                '',
                'The following entries need to be added to your .env file:',
                '',
                '1 : VAR_1',
                '',
            ],
            $this->validator->getMessages()
        );

        $this->assertFileExists($directory.'/.env');
    }

    public function testValidationOldVarRemoved()
    {
        $directory = __DIR__.'/Fixtures/OldVarRemoved';
        $this->assertFileExists($directory.'/.env');

        $this->assertEquals(Validation::VALIDATION_RESULT_OLD_ENTRIES, $this->validator->validateEnvironmentFiles($directory));
        $this->assertEquals(
            [
                'Old .env entries',
                '================',
                '',
                'The following entries need to be removed from your .env file:',
                '',
                '1 : VAR_1',
                '',
            ],
            $this->validator->getMessages()
        );

        $this->assertFileExists($directory.'/.env');
    }

    public function testValidationNewAndOldVars()
    {
        $directory = __DIR__.'/Fixtures/NewAndOldVars';
        $this->assertFileExists($directory.'/.env');

        $this->assertEquals(Validation::VALIDATION_RESULT_NEW_ENTRIES | Validation::VALIDATION_RESULT_OLD_ENTRIES, $this->validator->validateEnvironmentFiles($directory));
        $this->assertEquals(
            [
                'New .env entries',
                '================',
                '',
                'The following entries need to be added to your .env file:',
                '',
                '1 : VAR_2',
                '',
                'Old .env entries',
                '================',
                '',
                'The following entries need to be removed from your .env file:',
                '',
                '1 : VAR_1',
                '',
            ],
            $this->validator->getMessages()
        );

        $this->assertFileExists($directory.'/.env');
    }
}
