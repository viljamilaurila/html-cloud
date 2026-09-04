<?php

use Illuminate\Support\Facades\Schedule;

// Hard-deletes expired documents (App\Models\Document is MassPrunable).
Schedule::command('model:prune')->daily();
