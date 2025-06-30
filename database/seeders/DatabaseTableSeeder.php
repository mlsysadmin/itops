<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class DatabaseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param int $count
     * @return void
     */
    public function run($count = 10): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < $count; $i++) {
            DB::table('databases')->insert([
                'database_name' => $faker->unique()->word . '_DB',
                'private_ip' => $faker->ipv4,
                'hostname' => $faker->domainWord . '-server',
                'os' => $faker->randomElement(['Ubuntu', 'CentOS', 'Debian', 'Windows']),
                'os_version' => $faker->randomElement(['20.04', '7', '10', '22.04']),
                'rdbms' => $faker->randomElement(['MySQL', 'PostgreSQL', 'SQLServer']),
                'db_edition' => $faker->randomElement(['Community', 'Enterprise']),
                'db_version' => $faker->randomElement(['8.0.34', '5.7.42', '13.3', '2019']),
                'instance_type' => $faker->randomElement(['Master', 'Slave']),
                'cloud_provider' => $faker->randomElement(['AWS', 'Azure', 'GCP']),
                'uptime' => $faker->randomFloat(2, 1, 1000),
                'cpu_allocated' => $faker->numberBetween(1, 16),
                'cpu_util' => $faker->randomFloat(2, 1, 100),
                'mem_allocated' => $faker->randomElement(['2GB', '4GB', '8GB', '16GB']),
                'mem_util' => $faker->randomFloat(2, 1, 100),
                'data_util' => $faker->randomFloat(2, 1, 100),
                'data_total' => $faker->randomElement(['100GB', '200GB', '500GB']),
                'data_used' => $faker->randomElement(['50GB', '120GB', '390GB']),
                'data_free' => $faker->randomElement(['50GB', '80GB', '110GB']),
                'root_util' => $faker->randomFloat(2, 1, 100),
                'root_total' => $faker->randomElement(['50GB', '100GB']),
                'root_used' => $faker->randomElement(['16.22GB', '45.67GB']),
                'root_free' => $faker->randomElement(['33.78GB', '54.33GB']),
                'mysql_status' => $faker->randomElement(['Active', 'Inactive']),
                'replication_status' => $faker->randomElement(['Active', 'Inactive', 'Delayed']),
                'replica_master_host' => $faker->optional()->ipv4,
                'created_date' => now(),
                'updated_date' => now(),
            ]);
        }
    }
}
