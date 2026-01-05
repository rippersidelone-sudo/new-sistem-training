<?php

// app/Models/CategoryPrerequisite.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryPrerequisite extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'prerequisite_id',
    ];

    /**
     * Get the category that has this prerequisite
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Get the prerequisite category
     */
    public function prerequisite()
    {
        return $this->belongsTo(Category::class, 'prerequisite_id');
    }

    /**
     * Check if prerequisite creates a circular dependency
     */
    public static function wouldCreateCircularDependency($categoryId, $prerequisiteId)
    {
        // Check if prerequisiteId already depends on categoryId (directly or indirectly)
        return self::hasTransitiveDependency($prerequisiteId, $categoryId);
    }

    /**
     * Check transitive dependency recursively
     */
    private static function hasTransitiveDependency($fromCategoryId, $toCategoryId, $visited = [])
    {
        // Prevent infinite loop
        if (in_array($fromCategoryId, $visited)) {
            return false;
        }

        // If direct match found
        if ($fromCategoryId == $toCategoryId) {
            return true;
        }

        $visited[] = $fromCategoryId;

        // Get all prerequisites of fromCategoryId
        $prerequisites = self::where('category_id', $fromCategoryId)
            ->pluck('prerequisite_id');

        // Check each prerequisite recursively
        foreach ($prerequisites as $prereqId) {
            if (self::hasTransitiveDependency($prereqId, $toCategoryId, $visited)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all prerequisite categories for a category (recursive)
     */
    public static function getAllPrerequisites($categoryId)
    {
        $prerequisites = [];
        self::collectPrerequisites($categoryId, $prerequisites);
        return Category::whereIn('id', $prerequisites)->get();
    }

    /**
     * Collect prerequisites recursively
     */
    private static function collectPrerequisites($categoryId, &$collected = [], $visited = [])
    {
        if (in_array($categoryId, $visited)) {
            return;
        }

        $visited[] = $categoryId;

        $directPrereqs = self::where('category_id', $categoryId)
            ->pluck('prerequisite_id');

        foreach ($directPrereqs as $prereqId) {
            if (!in_array($prereqId, $collected)) {
                $collected[] = $prereqId;
                self::collectPrerequisites($prereqId, $collected, $visited);
            }
        }
    }
}
