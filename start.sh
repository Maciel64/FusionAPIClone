cp .env.example .env
./docker/bin/fusion build && sleep 5
./docker/bin/fusion up -d --remove-orphans && sleep 5
./docker/bin/fusion down && sleep 2
./docker/bin/fusion up -d --remove-orphans && sleep 2
./docker/bin/fusion composer install
./docker/bin/fusion artisan key:generate
./docker/bin/fusion artisan migrate:fresh --seed
# ./docker/bin/fusion artisan scribe:generate
./docker/bin/fusion artisan storage:link
./docker/bin/fusion artisan test
./docker/bin/fusion artisan migrate:fresh --seed