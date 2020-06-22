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
    private Validation $validator;

    /**
     * @return array<string, array<int, array<int, string>|string>>
     */
    public function provideValidation(): array
    {
        return [
            '.env only' => [
                __DIR__.'/Fixtures/EnvOnly',
                [
                    '',
                    'Old .env entries',
                    '================',
                    '',
                    'The following entries need to be removed from your .env file:',
                    '',
                    '1 : VAR_1',
                    '',
                ],
            ],
            '.env.example only' => [
                __DIR__.'/Fixtures/ExampleOnly',
                [
                    '',
                    'New .env entries',
                    '================',
                    '',
                    'The following entries need to be added to your .env file:',
                    '',
                    '1 : VAR_1',
                    '',
                ],
            ],
            'matching' => [
                __DIR__.'/Fixtures/Matching',
                [],
            ],
            'matching empty' => [
                __DIR__.'/Fixtures/MatchingEmpty',
                [],
            ],
            'New and Old' => [
                __DIR__.'/Fixtures/NewAndOldVars',
                [
                    '',
                    'New .env entries',
                    '================',
                    '',
                    'The following entries need to be added to your .env file:',
                    '',
                    '1 : VAR_2',
                    '',
                    '',
                    'Old .env entries',
                    '================',
                    '',
                    'The following entries need to be removed from your .env file:',
                    '',
                    '1 : VAR_1',
                    '',
                ],
            ],
            'New only' => [
                __DIR__.'/Fixtures/NewVarAdded',
                [
                    '',
                    'New .env entries',
                    '================',
                    '',
                    'The following entries need to be added to your .env file:',
                    '',
                    '1 : VAR_1',
                    '',
                ],
            ],
            'No .env or .env.example' => [
                __DIR__.'/Fixtures/NoEnvironment',
                [],
            ],
            'Old var removed only' => [
                __DIR__.'/Fixtures/OldVarRemoved',
                [
                    '',
                    'Old .env entries',
                    '================',
                    '',
                    'The following entries need to be removed from your .env file:',
                    '',
                    '1 : VAR_1',
                    '',
                ],
            ],
            ];
    }

    protected function setUp(): void
    {
        $this->validator = new Validation();
    }

    /**
     * @param array<int, array<int, string>|string> $expectedMessages
     *
     * @dataProvider provideValidation
     */
    public function testValidation(string $directory, array $expectedMessages): void
    {
        $this->assertEquals($expectedMessages, $this->validator->validateEnvironmentFiles($directory));
    }
}
