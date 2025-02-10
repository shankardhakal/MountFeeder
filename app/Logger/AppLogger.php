<?php

declare(strict_types=1);

namespace App\Logger;

use App\Admin\Models\Feed;
use Monolog\Formatter\LineFormatter;
use Monolog\Formatter\LogstashFormatter;
use Monolog\Handler\SocketHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class AppLogger
{
    /**
     * @param  array  $config
     * @return LoggerInterface
     */
    public function __invoke(array $config): LoggerInterface
    {
            $handler = new StreamHandler(storage_path(sprintf('logs/%s.log', date('Y-m-d'))));
            $handler->setFormatter(new LineFormatter());

        return new class(config('app.server', 'unknown'), [$handler]) extends Logger {
            protected static array $context = [];

            /**
             * @param  int  $level
             * @param  string  $message
             * @param  array  $context
             * @return bool
             */
            public function addRecord(int $level, string $message, array $context = []): bool
            {
                $context = array_merge($context, self::$context);

                $enricher = function (Feed $feed) {
                    return [
                        'feed_slug'   => $feed->get(Feed::FIELD_SLUG),
                        'feed_name' => $feed->get(Feed::FIELD_STORE_NAME),
                        'website'     => $feed->website->name,
                        'website_url' => $feed->website->url,
                    ];
                };

                $enrichedContext = [];

                foreach ($context as $key => $value) {
                    if ($key instanceof Feed) {
                        $enrichedContext = array_merge($enrichedContext, $enricher($key));
                        continue;
                    }

                    if ($value instanceof Feed) {
                        $enrichedContext = array_merge($enrichedContext, $enricher($value));
                        continue;
                    }

                    $enrichedContext[$key] = $value;
                }

                return parent::addRecord($level, $message, $enrichedContext);
            }

            /**
             * @param  array  $context
             */
            public static function addContext(array $context)
            {
                foreach ($context as $contextKey => $contextValue) {
                    self::$context[$contextKey] = $contextValue;
                }
            }
        };
    }
}
