<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:update-registers-from-cetesb')->daily();

// Lembrar de adicionar: * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1 no arquivo do crontab do servidor