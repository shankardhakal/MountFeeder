<?php

namespace App\Listeners;

use App\Events\ImportCompleted;

class UpdateImportCompleted
{
    /**
     * Handle the event.
     *
     * @param  ImportCompleted  $event
     * @return void
     */
    public function handle(ImportCompleted $event)
    {
        $event->getFeed()
            ->setFeedImportedAt(now())
            ->update();
    }
}
