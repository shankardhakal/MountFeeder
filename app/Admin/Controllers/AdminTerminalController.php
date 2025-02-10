<?php

declare(strict_types=1);

namespace App\Admin\Controllers;

use Encore\Admin\Helpers\Controllers\TerminalController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;

class AdminTerminalController extends TerminalController
{
    /**
     * @return JsonResponse|string
     */
    public function runArtisan()
    {
        $command = Request::get('c', 'list');

        $schedules = Cache::get('schedule-commands', []);

        if (in_array($command, $schedules)) {
            return response()->json(['message'=>'Task has been scheduled already']);
        }

        $schedules[] = $command;

        Cache::put('schedule-commands', $schedules, now()->addMinutes(2));

        return response()->json(['message'=>'Successfully scheduled.']);
    }
}
