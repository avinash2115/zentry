<?php

namespace App\Assistants\Files\Drivers\AWS\S3;

use App\Assistants\Files\Drivers\Contracts\Quotable;
use App\Assistants\Files\Drivers\ValueObjects\Quota;
use App\Exceptions\Handler;
use Arr;
use Aws\S3\Exception\S3Exception;
use Exception;
use League\Flysystem\AwsS3v3\AwsS3Adapter;

/**
 * Class Adapter
 *
 * @package App\Assistants\Files\Drivers\AWS\S3
 */
class Adapter extends AwsS3Adapter implements Quotable
{
    /**
     * @inheritDoc
     */
    public function quota(string $path = null): Quota
    {
        $command = $this->s3Client->getCommand(
            'listObjects',
            [
                'Bucket'  => $this->bucket,
                'Prefix'  => rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR,
            ]
        );

        $used = 0;

        try {
            $result = $this->s3Client->execute($command);

            if (Arr::has($result, 'Contents')) {
                foreach($result['Contents'] as $content) {
                    $used += (int) Arr::get($content, 'Size', 0);
                }
            }
        } catch (Exception $e) {
            report($e);
        }

        return new Quota($used, 0);
    }
}
