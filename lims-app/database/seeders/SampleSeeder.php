<?php

namespace Database\Seeders;

use App\Models\Sample;
use Illuminate\Database\Seeder;

class SampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $firstNames = [
            'Aiyana',
            'Budi',
            'Citra',
            'Dani',
            'Eka',
            'Fajar',
            'Gita',
            'Hendra',
            'Ina',
            'Joko',
            'Karina',
            'Luki',
            'Maya',
            'Nadia',
            'Ozi',
            'Prasetyo',
            'Quentin',
            'Rini',
            'Siti',
            'Toni',
            'Ulfa',
            'Vicky',
            'Wati',
            'Xander',
            'Yuki',
            'Zara',
            'Ahmad',
            'Bella',
            'Chandra',
            'Desti',
            'Edi',
            'Fiona',
            'Gunawan',
            'Hana',
            'Irfan',
            'Jasmine',
            'Karim',
            'Lela',
            'Mira',
            'Nanda',
            'Okta',
            'Putri',
            'Qisda',
            'Rafiq',
            'Sandi',
            'Tika',
            'Udin',
            'Vina',
            'Wahyu',
            'Xenia',
            'Yadi',
            'Zainab',
            'Aditya',
            'Brigita',
            'Cahya',
            'Dewi',
            'Erfan',
            'Fitri',
            'Gilang',
            'Hesti',
        ];

        $lastNames = [
            'Ebert',
            'Bahrudin',
            'Cahyani',
            'Damayanti',
            'Effendi',
            'Fauziah',
            'Gunawan',
            'Hartono',
            'Indah',
            'Juwono',
            'Kurniawan',
            'Luqman',
            'Maulana',
            'Nasution',
            'Oktavian',
            'Prabowo',
            'Qadri',
            'Rahman',
            'Soetrisno',
            'Tirtawinata',
            'Usman',
            'Venugopal',
            'Wijaya',
            'Xavier',
            'Yusuf',
            'Zaini',
            'Abdillah',
            'Basuki',
            'Cakrawinata',
            'Dayak',
            'Eka',
            'Fitriana',
            'Gunardi',
            'Hasibuan',
            'Iskandar',
            'Jasmine',
            'Kadafi',
            'Lubis',
            'Mulyadi',
            'Nurdin',
            'Osman',
            'Pohan',
            'Qiram',
            'Rachman',
            'Soemardi',
            'Tandiono',
            'Uctara',
            'Vanderbilt',
            'Wardhana',
            'Xiomara',
            'Yanto',
            'Zulkifli',
            'Adnan',
            'Burhanudin',
            'Chamim',
            'Dariyanto',
            'Erlan',
            'Firdaus',
            'Gianto',
            'Hartadi',
        ];

        for ($i = 1; $i <= 200; $i++) {
            // Generate sequential sample code: SAMPLE-000001, SAMPLE-000002, etc
            $sampleCode = 'SAMPLE-' . str_pad($i, 6, '0', STR_PAD_LEFT);

            // Generate random full name
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $fullName = $firstName . ' ' . $lastName;

            Sample::create([
                'sample_code' => $sampleCode,
                'patient_name' => $fullName,
                'status' => 'waiting_thermal',
            ]);
        }
    }
}
