<?php

namespace App\Traits;

use App\Models\Document;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait AcceptsTerms
{

    /**
     * Documents such as terms & conditions
     * @return BelongsToMany
     */
    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class, 'user_documents')->withTimestamps();
    }


    /**
     * Accept the latest versions of the T&C
     */
    public function acceptTerms()
    {
        $this->documents()->attach(Document::latestTNC()->id);
    }


    /**
     * Check if the user has accepted the latest terms
     */
    public function hasAcceptedTerms()
    {
        return $this->documents->contains(Document::latestTNC()->id);
    }

}
