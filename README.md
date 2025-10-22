php artisan config:clear

touch database/database_shard_0.sqlite
touch database/database_shard_1.sqlite

php artisan shard:test 123
php artisan shard:test 456

php artisan migrate --database=sqlite_shard_0
php artisan migrate --database=sqlite_shard_1

php artisan app:retrieve-beaches-from-cetesb
php artisan app:update-registers-from-cetesb

eduardo@EduHome:~/unip/aps/backend$ echo "=== SHARD 0 ===" && php artisan tinker --execute="echo 'Beaches: ' . \App\Models\Beach::on('sqlite_shard_0')->count() . PHP_EOL; echo 'Registers: ' . \App\Models\Register::on('sqlite_shard_0')->count() . PHP_EOL;" && echo "=== SHARD 1 ===" && php artisan tinker --execute="echo 'Beaches: ' . \App\Models\Beach::on('sqlite_shard_1')->count() . PHP_EOL; echo 'Registers: ' . \App\Models\Register::on('sqlite_shard_1')->count() . PHP_EOL;"
=== SHARD 0 ===
Beaches: 86
Registers: 86
=== SHARD 1 ===
Beaches: 89
Registers: 86
eduardo@EduHome:~/unip/aps/backend$ php artisan tinker --execute="echo '=== Total counts ===' . PHP_EOL; echo 'Beaches: ' . (\App\Models\Beach::on('sqlite_shard_0')->count() + \App\Models\Beach::on('sqlite_shard_1')->count()) . PHP_EOL; echo 'Registers: ' . (\App\Models\Register::on('sqlite_shard_0')->count() + \App\Models\Register::on('sqlite_shard_1')->count()) . PHP_EOL;"
=== Total counts ===
Beaches: 175
Registers: 172



eduardo@EduHome:~/unip/aps/backend$ php artisan tinker --execute="\$s0b = \Illuminate\Support\Facades\DB::connection('sqlite_shard_0')->table('beaches')->count(); \$s0r = \Illuminate\Support\Facades\DB::connection('sqlite_shard_0')->table('registers')->count(); \$s1b = \Illuminate\Support\Facades\DB::connection('sqlite_shard_1')->table('beaches')->count(); \$s1r = \Illuminate\Support\Facades\DB::connection('sqlite_shard_1')->table('registers')->count(); echo '=== FINAL COUNTS ===' . PHP_EOL; echo 'Shard 0: ' . \$s0b . ' beaches, ' . \$s0r . ' registers' . PHP_EOL; echo 'Shard 1: ' . \$s1b . ' beaches, ' . \$s1r . ' registers' . PHP_EOL; echo 'TOTAL: ' . (\$s0b + \$s1b) . ' beaches, ' . (\$s0r + \$s1r) . ' registers' . PHP_EOL;"
=== FINAL COUNTS ===
Shard 0: 86 beaches, 86 registers
Shard 1: 89 beaches, 89 registers
TOTAL: 175 beaches, 175 registers