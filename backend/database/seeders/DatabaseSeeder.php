<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Page;
use App\Models\NewsPost;
use App\Models\ScheduleEntry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create default superadmin
        User::create([
            'name' => 'Administrators',
            'email' => 'admin@skola.lv',
            'password' => Hash::make('Admin1234!'),
            'role' => 'superadmin',
        ]);

        // Create default pages
        Page::create([
            'slug' => 'vesture',
            'title' => 'Skolas Vēsture',
            'content' => '<h2>Mūsu skolas vēsture</h2><p>Skola tika dibināta 1920. gadā un kopš tā laika kalpo kā izglītības centrs mūsu kopienai. Gadu desmitu laikā mēs esam lepojušies ar izciliem absolventiem un augstu mācību standartu.</p><p>1950. gadā skola paplašinājās, pievienojot jaunas klases un sporta infrastruktūru. Šodien mēs turpinām attīstīties un pielāgoties mūsdienu izglītības prasībām, saglabājot tradīcijas un vērtības, kas mūs padara unikālus.</p>',
        ]);

        Page::create([
            'slug' => 'skoleniem',
            'title' => 'Skolēniem',
            'content' => '<h2>Informācija skolēniem</h2><p>Šeit atradīsiet visu svarīgo informāciju skolēniem - stundu sarakstu, ārpusskolas aktivitātes, bibliotēkas pakalpojumus un daudz ko citu.</p><h3>Svarīgi resursi</h3><ul><li>Stundu saraksts atrodams sadaļā "Stundu saraksts"</li><li>Bibliotēka strādā katru dienu no 8:00 līdz 17:00</li><li>Skolas padomes sanāksmes notiek katru mēnesi</li></ul>',
        ]);

        Page::create([
            'slug' => 'skolotajiem',
            'title' => 'Skolotājiem',
            'content' => '<h2>Informācija skolotājiem</h2><p>Šī sadaļa ir paredzēta mūsu skolotājiem un atbalsta personālam. Šeit atradīsiet svarīgus dokumentus, mācību plānus un skolas iekšējos noteikumus.</p><h3>Noderīgi resursi</h3><ul><li>Stundu plānojums un mācību programmas</li><li>Pedagogu padomes sanāksmes</li><li>Profesionālās pilnveides iespējas</li><li>Skolas iekšējie normatīvie dokumenti</li></ul>',
        ]);

        // Sample news
        NewsPost::create([
            'title' => 'Jaunais mācību gads sācies!',
            'content' => 'Ar prieku paziņojam, ka jaunais mācību gads ir veiksmīgi sācies. Visiem skolēniem un skolotājiem novēlam produktīvu un saturīgu mācību gadu!',
            'published_at' => now(),
        ]);

        NewsPost::create([
            'title' => 'Skolas olimpiādes rezultāti',
            'content' => 'Apsveicam mūsu skolēnus ar izcilajiem rezultātiem reģionālajās olimpiādēs matemātikā un dabaszinātnēs. Jūs esat mūsu lepnums!',
            'published_at' => now()->subDays(3),
        ]);

        // Sample schedule
        $entries = [
            ['group_name' => '10.A', 'subject' => 'Matemātika', 'teacher' => 'A. Bērziņš', 'day_of_week' => 'Pirmdiena', 'start_time' => '08:00', 'end_time' => '08:45'],
            ['group_name' => '10.A', 'subject' => 'Latviešu valoda', 'teacher' => 'I. Kalniņa', 'day_of_week' => 'Pirmdiena', 'start_time' => '08:55', 'end_time' => '09:40'],
            ['group_name' => '10.A', 'subject' => 'Fizika', 'teacher' => 'M. Ozoliņš', 'day_of_week' => 'Otrdiena', 'start_time' => '08:00', 'end_time' => '08:45'],
            ['group_name' => '11.B', 'subject' => 'Bioloģija', 'teacher' => 'V. Liepa', 'day_of_week' => 'Pirmdiena', 'start_time' => '08:00', 'end_time' => '08:45'],
            ['group_name' => '11.B', 'subject' => 'Vēsture', 'teacher' => 'J. Zariņš', 'day_of_week' => 'Trešdiena', 'start_time' => '10:00', 'end_time' => '10:45'],
        ];

        foreach ($entries as $entry) {
            ScheduleEntry::create($entry);
        }
    }
}
