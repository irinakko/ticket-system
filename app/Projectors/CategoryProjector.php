<?php

namespace App\Projectors;

use App\Events\Domain\CategoryEvents\CategoryCreated;
use App\Events\Domain\CategoryEvents\CategoryDeleted;
use App\Events\Domain\CategoryEvents\CategoryUpdated;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class CategoryProjector extends Projector
{
    public function onCategoryCreated(CategoryCreated $event): void
    {
        Category::create([
            'id' => $event->categoryId,
            'name' => $event->attributes['name'],
        ]);
    }

    public function onCategoryUpdated(CategoryUpdated $event): void
    {
        $category = Category::find($event->categoryId);

        if ($category) {
            $category->update($event->newAttributes);
        }
    }

    public function onCategoryDeleted(CategoryDeleted $event): void
    {
        DB::table('category_ticket')
            ->where('category_id', $event->categoryId)
            ->delete();
        Category::find($event->categoryId)?->delete();
    }
}
