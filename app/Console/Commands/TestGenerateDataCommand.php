<?php

namespace App\Console\Commands;

use App\Utils\Files\FileHelper;
use App\Utils\StringHelper;

class TestGenerateDataCommand extends Command
{
    protected $signature = 'test:generate-data {--max=10} {--one-time-event=1} {--membership-event=2}';

    protected $max;
    protected $oneTimeEventId;
    protected $membershipEventId;

    protected function go()
    {
        $this->max = intval($this->option('max'));
        $this->oneTimeEventId = intval($this->option('one-time-event'));
        $this->membershipEventId = intval($this->option('membership-event'));

        $this->generateOneTimeEventWatcherRegister();
        $this->generateMembershipEventWatcherRegister();
    }

    protected function generateOneTimeEventWatcherRegister()
    {
        $fileHelper = FileHelper::getInstance();
        $path = $fileHelper->checkDirectory(base_path('.tests/one_time_event_watcher_register'));
        $orders = [];
        foreach (range(1, $this->max) as $order) {
            $order = StringHelper::fill($order, strlen($this->max), 0);
            $orders[] = $order;
            file_put_contents(
                $fileHelper->concatPath($path, sprintf('%s.json', $order)),
                json_encode([
                    '_register' => 1,
                    'event' => $this->oneTimeEventId,
                    'email' => sprintf('test.%s@example.com', $order),
                    'display_name' => sprintf('Test %s', $order),
                    'company_name' => sprintf('Company %s', $order),
                    'terms_of_service' => true,
                    'app_event_watching_path' => 'o/watch',
                ])
            );
        }
        file_put_contents($fileHelper->concatPath($path, 'data.csv'), implode("\n", $orders));
    }

    protected function generateMembershipEventWatcherRegister()
    {
        $fileHelper = FileHelper::getInstance();
        $path = $fileHelper->checkDirectory(base_path('.tests/membership_event_watcher_register'));
        $orders = [];
        foreach (range(1, $this->max) as $order) {
            $order = StringHelper::fill($order, strlen($this->max), 0);
            $orders[] = $order;
            file_put_contents(
                $fileHelper->concatPath($path, sprintf('%s.json', $order)),
                json_encode([
                    '_register' => 1,
                    'event' => $this->membershipEventId,
                    'terms_of_service' => true,
                    'app_event_watching_path' => 'm/watch',
                ])
            );
        }
        file_put_contents($fileHelper->concatPath($path, 'data.csv'), implode("\n", $orders));
    }
}
