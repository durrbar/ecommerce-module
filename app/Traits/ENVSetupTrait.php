<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Traits;

use function Laravel\Prompts\info;
use function Laravel\Prompts\table;

trait ENVSetupTrait
{
    public function CheckENVExistOrNot(): bool
    {
        $envFilePath = base_path('.env');

        if (! file_exists($envFilePath)) {
            $this->error('.env file not found. Please create the .env file in API root directory and try again.');

            return false;
        }

        return true;
    }

    public function existingKeyValueInENV(array $targetKeys, string $envContent): array
    {
        $keyValuePairs = [];

        foreach ($targetKeys as $targetKey) {
            // Search for the key in the content
            preg_match("/^$targetKey=(.*)$/m", $envContent, $matches);

            // Check if the key was found
            if (isset($matches[1])) {
                $value = $matches[1];
                $keyValuePairs[] = [$targetKey, $value];
            } else {
                $keyValuePairs[] = [$targetKey, 'Not found'];
            }
        }

        // Display the key-value pairs in a table
        if (! empty($keyValuePairs)) {
            info('Your existing key & value on the .env file right now:');
            table(['Key', 'Value'], $keyValuePairs);
        } else {
            $this->error('No key-value pairs found in the .env file');
        }

        return $keyValuePairs;
    }
}
