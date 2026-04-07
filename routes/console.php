<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('submissions:remove-expired')->daily();
