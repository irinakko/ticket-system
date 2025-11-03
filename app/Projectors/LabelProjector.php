<?php

namespace App\Projectors;

use App\Events\Domain\LabelEvents\LabelCreated;
use App\Events\Domain\LabelEvents\LabelDeleted;
use App\Events\Domain\LabelEvents\LabelUpdated;
use App\Models\Label;
use Illuminate\Support\Facades\DB;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class LabelProjector extends Projector
{
    public function onLabelCreated(LabelCreated $event): void
    {
        Label::create([
            'id' => $event->labelId,
            'name' => $event->attributes['name'],
        ]);
    }

    public function onLabelUpdated(LabelUpdated $event): void
    {
        $label = Label::find($event->labelId);

        if ($label) {
            $label->update($event->newAttributes);
        }
    }

    public function onLabelDeleted(LabelDeleted $event): void
    {
        DB::table('label_ticket')
            ->where('label_id', $event->labelId)
            ->delete();
        Label::find($event->labelId)?->delete();
    }
}
