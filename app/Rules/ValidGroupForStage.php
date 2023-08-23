<?php

namespace App\Rules;

use App\Models\Group\Group;
use App\Models\Stages\Stage;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidGroupForStage implements ValidationRule
{


    protected $stageId;

    public function __construct($stageId)
    {
        $this->stageId = $stageId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $stage = Stage::find($this->stageId);

        if (!$stage) {
            $fail('Invalid stage ID.');
        }

        $group = Group::find($value);

        if (!$group) {
            $fail('Invalid group ID.');
        }

        if ($group->stage_id != $stage->id) {
            $fail('The selected group is not valid for the requested stage.');
        }
    }
}