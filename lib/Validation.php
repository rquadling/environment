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

namespace RQuadling\Environment;

use josegonzalez\Dotenv\Loader;
use UpdateHelper\UpdateHelper;
use UpdateHelper\UpdateHelperInterface;

class Validation implements UpdateHelperInterface
{
    const VALIDATION_RESULT_OK = 0;
    const VALIDATION_RESULT_COPIED_EXAMPLE = 1;
    const VALIDATION_RESULT_NEW_ENTRIES = 2;
    const VALIDATION_RESULT_OLD_ENTRIES = 4;

    /** @var string[] */
    private $messages = [];

    /** @return string[] */
    public function getMessages(): array
    {
        return $this->messages;
    }

    // @codeCoverageIgnoreStart
    public function check(UpdateHelper $helper)
    {
        $this->validateEnvironmentFiles(\dirname($helper->getComposerFilePath()));
        $helper->write($this->getMessages());
    }

    // @codeCoverageIgnoreEnd

    protected function validateEnvironmentFiles(string $rootDirectory): int
    {
        $envFilename = \sprintf('%s/.env', $rootDirectory);
        $exampleFilename = \sprintf('%s.example', $envFilename);

        $result = self::VALIDATION_RESULT_OK;
        if (!\file_exists($envFilename) && \file_exists($exampleFilename)) {
            \copy($exampleFilename, $envFilename);
            $this->messages = [
                '',
                'Copied default settings from .env.example to .env',
                '',
                'Please review the contents of the .env file.',
                '',
            ];
            $result = self::VALIDATION_RESULT_COPIED_EXAMPLE;
        } elseif (\file_exists($envFilename) && !\file_exists($exampleFilename)) {
            $this->messages = [
                '',
                'No .env.example file',
                '',
            ];
        } elseif (\file_exists($envFilename) && \file_exists($exampleFilename)) {
            $example = (new Loader($exampleFilename))->parse()->toArray();
            $envvar = (new Loader($envFilename))->parse()->toArray();

            $toBeAdded = \array_diff_key($example, $envvar);
            $toBeRemoved = \array_diff_key($envvar, $example);

            $mapper = function (array $envvars) {
                return \array_map(
                    function (string $newEnvVar) {
                        static $count = 0;

                        return \sprintf('%d : %s', ++$count, $newEnvVar);
                    },
                    \array_keys($envvars)
                );
            };

            $this->messages = [];
            if ($toBeAdded) {
                $this->messages = \array_merge(
                    $this->messages,
                    [
                        '',
                        'New .env entries',
                        '================',
                        '',
                        'The following entries need to be added to your .env file:',
                        '',
                    ],
                    $mapper($toBeAdded),
                    [
                        '',
                    ]
                );
                $result += self::VALIDATION_RESULT_NEW_ENTRIES;
            }
            if ($toBeRemoved) {
                $this->messages = \array_merge(
                    $this->messages,
                    [
                        '',
                        'Old .env entries',
                        '================',
                        '',
                        'The following entries need to be removed from your .env file:',
                        '',
                    ],
                    $mapper($toBeRemoved),
                    [
                        '',
                    ]
                );
                $result += self::VALIDATION_RESULT_OLD_ENTRIES;
            }
        } else {
            $this->messages = [
                '',
                'No .env or .env.example files',
                '',
            ];
        }

        return $result;
    }
}
