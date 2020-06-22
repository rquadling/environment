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
    // @codeCoverageIgnoreStart
    public function check(UpdateHelper $helper): void
    {
        $helper->write($this->validateEnvironmentFiles(\dirname($helper->getComposerFilePath())));
    }

    // @codeCoverageIgnoreEnd

    /**
     * @return string[]
     */
    protected function validateEnvironmentFiles(string $rootDirectory): array
    {
        $envFilename = \sprintf('%s/.env', $rootDirectory);
        $exampleFilename = \sprintf('%s.example', $envFilename);

        $envVars = \file_exists($envFilename) ? (new Loader($envFilename))->parse()->toArray() : [];
        $exampleEnvVars = \file_exists($exampleFilename) ? (new Loader($exampleFilename))->parse()->toArray() : [];

        $toBeAdded = \array_diff_key($exampleEnvVars, $envVars);
        $toBeRemoved = \array_diff_key($envVars, $exampleEnvVars);

        $mapper = function (array $envvars) {
            return \array_map(
                function (string $newEnvVar) {
                    static $count = 0;

                    return \sprintf('%d : %s', ++$count, $newEnvVar);
                },
                \array_keys($envvars)
            );
        };

        $result = [];
        if ($toBeAdded) {
            $result = \array_merge(
                $result,
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
        }
        if ($toBeRemoved) {
            $result = \array_merge(
                $result,
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
        }

        return $result;
    }
}
