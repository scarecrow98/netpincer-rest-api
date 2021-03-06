<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('partners')->insert([
            'partner_type_id' => 1,
            'name' => 'Centrum Étterem',
            'address' => '7020 Dunaföldvár, Paksi utca 32.',
            'location' => null,
            'description' => 'Lorem ipsum dolor sit amet',
            'image' => 'cover.png',
            'color_style' => 'e4e4e4',
            'login_id' => Str::random(6),
            'password' => Hash::make('asdasdasd'),
            'courier_share_percent' => 0.12,
            'partner_type_id' => 2
        ]);

        DB::table('partners')->insert([
            'partner_type_id' => 1,
            'name' => 'Vár Étterem',
            'address' => '7020 Dunaföldvár, Vár utca 1.',
            'location' => null,
            'description' => 'Lorem ipsum dolor sit amet',
            'image' => 'cover.png',
            'color_style' => 'e4e4e4',
            'login_id' => Str::random(6),
            'password' => Hash::make('asdasdasd'),
            'courier_share_percent' => 0.12,
            'partner_type_id' => 2
        ]);

        DB::table('partners')->insert([
            'partner_type_id' => 1,
            'name' => 'Kerék csárda',
            'address' => '7020 Dunaföldvár, Újvárosi út 45.',
            'location' => null,
            'description' => 'Lorem ipsum dolor sit amet',
            'image' => 'cover.png',
            'color_style' => 'e4e4e4',
            'login_id' => Str::random(6),
            'password' => Hash::make('asdasdasd'),
            'courier_share_percent' => 0.12,
            'partner_type_id' => 2
        ]);

        DB::table('partners')->insert([
            'partner_type_id' => 1,
            'name' => 'Kele csárda',
            'address' => '7020 Dunaföldvár, Paksi utca 68',
            'location' => DB::raw('point(18.9179809, 46.8001719)'),
            'description' => 'Lorem ipsum dolor sit amet',
            'image' => 'cover.png',
            'color_style' => 'e4e4e4',
            'login_id' => Str::random(6),
            'password' => Hash::make('asdasdasd'),
            'courier_share_percent' => 0.12,
            'partner_type_id' => 2
        ]);
    }
}
