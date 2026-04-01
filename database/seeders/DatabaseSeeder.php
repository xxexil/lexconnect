<?php

namespace Database\Seeders;

use App\Models\Consultation;
use App\Models\Conversation;
use App\Models\LawyerProfile;
use App\Models\Message;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin super-user
        User::create([
            'name'     => 'LexConnect Admin',
            'email'    => 'admin@lexconnect.com',
            'password' => Hash::make('admin1234'),
            'role'     => 'admin',
        ]);

        // create client
        $client = User::create([
            'name'     => 'Alex Johnson',
            'email'    => 'alex@lexconnect.com',
            'password' => Hash::make('password'),
            'role'     => 'client',
            'avatar'   => 'https://randomuser.me/api/portraits/men/1.jpg',
        ]);

        // lawyers data
        $lawyerData = [
            ['name'=>'Sarah Mitchell',  'email'=>'sarah@lexconnect.com', 'avatar'=>'https://randomuser.me/api/portraits/women/44.jpg',
             'specialty'=>'Corporate Law','firm'=>'Santiago Carpio Law Offices','hourly_rate'=>8500,'experience_years'=>15,'location'=>'Makati, Metro Manila','rating'=>4.9,'reviews_count'=>127,'is_certified'=>true,'availability_status'=>'available'],
            ['name'=>'James Thornton',  'email'=>'james@lexconnect.com', 'avatar'=>'https://randomuser.me/api/portraits/men/32.jpg',
             'specialty'=>'Family Law','firm'=>'Thornton & Associates','hourly_rate'=>7500,'experience_years'=>20,'location'=>'Quezon City, Metro Manila','rating'=>4.8,'reviews_count'=>203,'is_certified'=>true,'availability_status'=>'busy'],
            ['name'=>'Dr. Priya Sharma','email'=>'priya@lexconnect.com', 'avatar'=>'https://randomuser.me/api/portraits/women/68.jpg',
             'specialty'=>'Immigration Law','firm'=>'Global Immigration Partners','hourly_rate'=>4500,'experience_years'=>11,'location'=>'Cebu City, Cebu','rating'=>4.7,'reviews_count'=>89,'is_certified'=>true,'availability_status'=>'available'],
            ['name'=>'Amanda Foster',   'email'=>'amanda@lexconnect.com','avatar'=>'https://randomuser.me/api/portraits/women/56.jpg',
             'specialty'=>'Personal Injury','firm'=>'Foster & Williams LLP','hourly_rate'=>3500,'experience_years'=>9,'location'=>'Pasig, Metro Manila','rating'=>4.5,'reviews_count'=>94,'is_certified'=>true,'availability_status'=>'available'],
            ['name'=>'Robert Okafor',   'email'=>'robert@lexconnect.com','avatar'=>'https://randomuser.me/api/portraits/men/75.jpg',
             'specialty'=>'Employment Law','firm'=>'Reyes Okafor Legal Group','hourly_rate'=>5000,'experience_years'=>13,'location'=>'Taguig, Metro Manila','rating'=>4.7,'reviews_count'=>141,'is_certified'=>true,'availability_status'=>'available'],
            ['name'=>'Lisa Nakamura',   'email'=>'lisa@lexconnect.com',  'avatar'=>'https://randomuser.me/api/portraits/women/90.jpg',
             'specialty'=>'Real Estate','firm'=>'Nakamura & Cruz Law Firm','hourly_rate'=>4000,'experience_years'=>7,'location'=>'Davao City, Davao del Sur','rating'=>4.6,'reviews_count'=>67,'is_certified'=>true,'availability_status'=>'available'],
            ['name'=>'Robert Chen',     'email'=>'chen@lexconnect.com',  'avatar'=>'https://randomuser.me/api/portraits/men/50.jpg',
             'specialty'=>'Real Estate','firm'=>'Chen Law Group','hourly_rate'=>5500,'experience_years'=>10,'location'=>'Mandaluyong, Metro Manila','rating'=>4.4,'reviews_count'=>55,'is_certified'=>false,'availability_status'=>'available'],
        ];

        $lawyerUsers = [];
        foreach ($lawyerData as $index => $data) {
            $profileFields = ['specialty','firm','hourly_rate','experience_years','location','bio','is_certified','availability_status','rating','reviews_count'];
            $profile = array_intersect_key($data, array_flip($profileFields));
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'avatar'   => $data['avatar'],
                'password' => Hash::make('password'),
                'role'     => 'lawyer',
            ]);

            // Add dummy GCash info for some lawyers to test
            $gcashInfo = [];
            if ($index % 2 == 0 || $data['name'] === 'Amanda Foster') {
                $gcashInfo = [
                    'gcash_number' => '09' . str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT),
                    'gcash_qr' => null, // We'll leave QR null as we don't have files
                ];
            }

            LawyerProfile::create(array_merge(['user_id' => $user->id], $profile, $gcashInfo));
            $lawyerUsers[] = $user;
        }

        [$sarah, $james, $priya, $amanda, $robert_o, $lisa, $chen] = $lawyerUsers;

        // Consultations
        $c1 = Consultation::create(['code'=>'CONS-2026-0312','client_id'=>$client->id,'lawyer_id'=>$sarah->id,
            'scheduled_at'=>'2026-03-13 10:00:00','duration_minutes'=>60,'type'=>'video','status'=>'upcoming','price'=>8500]);
        $c2 = Consultation::create(['code'=>'CONS-2026-0315','client_id'=>$client->id,'lawyer_id'=>$james->id,
            'scheduled_at'=>'2026-03-15 14:30:00','duration_minutes'=>45,'type'=>'video','status'=>'upcoming','price'=>5625]);
        $c3 = Consultation::create(['code'=>'CONS-2026-0310','client_id'=>$client->id,'lawyer_id'=>$priya->id,
            'scheduled_at'=>'2026-03-10 09:00:00','duration_minutes'=>60,'type'=>'video','status'=>'completed','price'=>4500]);
        $c4 = Consultation::create(['code'=>'CONS-2026-0228','client_id'=>$client->id,'lawyer_id'=>$chen->id,
            'scheduled_at'=>'2026-02-28 11:00:00','duration_minutes'=>90,'type'=>'video','status'=>'completed','price'=>8250]);
        $c5 = Consultation::create(['code'=>'CONS-2026-0215','client_id'=>$client->id,'lawyer_id'=>$amanda->id,
            'scheduled_at'=>'2026-02-15 15:00:00','duration_minutes'=>60,'type'=>'video','status'=>'cancelled','price'=>3500]);

        // Payments
        Payment::create(['client_id'=>$client->id,'lawyer_id'=>$sarah->id,  'consultation_id'=>$c1->id,'amount'=>8500,'status'=>'pending']);
        Payment::create(['client_id'=>$client->id,'lawyer_id'=>$james->id,  'consultation_id'=>$c2->id,'amount'=>5625,'status'=>'pending']);
        Payment::create(['client_id'=>$client->id,'lawyer_id'=>$priya->id,  'consultation_id'=>$c3->id,'amount'=>4500,'status'=>'paid']);
        Payment::create(['client_id'=>$client->id,'lawyer_id'=>$chen->id,   'consultation_id'=>$c4->id,'amount'=>8250,'status'=>'paid']);
        Payment::create(['client_id'=>$client->id,'lawyer_id'=>$amanda->id, 'consultation_id'=>$c5->id,'amount'=>3500,'status'=>'refunded']);

        // Conversations & Messages
        $conv1 = Conversation::create(['client_id'=>$client->id,'lawyer_id'=>$sarah->id]);
        Message::create(['conversation_id'=>$conv1->id,'sender_id'=>$sarah->id,'body'=>'Hello Alex! I wanted to follow up on your contract review. I have gone through the initial draft and I have several observations.','read_at'=>now()]);
        Message::create(['conversation_id'=>$conv1->id,'sender_id'=>$client->id,'body'=>'Hi Sarah! Thank you for getting back to me so quickly. I am looking forward to hearing your thoughts.','read_at'=>now()]);
        Message::create(['conversation_id'=>$conv1->id,'sender_id'=>$sarah->id,'body'=>'Regarding your corporate contract review — please review the attached documents before our session tomorrow. I have highlighted the key clauses that need your attention.','read_at'=>now()]);
        Message::create(['conversation_id'=>$conv1->id,'sender_id'=>$client->id,'body'=>'Got it! I will review the documents this evening and prepare my questions for the session.','read_at'=>now()]);
        Message::create(['conversation_id'=>$conv1->id,'sender_id'=>$sarah->id,'body'=>'Perfect. Also, I would like to discuss the liability clause in Section 4. It might need some revisions to better protect your interests.']);

        $conv2 = Conversation::create(['client_id'=>$client->id,'lawyer_id'=>$james->id]);
        Message::create(['conversation_id'=>$conv2->id,'sender_id'=>$james->id,'body'=>'Your session is confirmed for 03/15 at 2:30 PM. Please be ready 5 minutes early.','read_at'=>now()]);

        $conv3 = Conversation::create(['client_id'=>$client->id,'lawyer_id'=>$priya->id]);
        Message::create(['conversation_id'=>$conv3->id,'sender_id'=>$priya->id,'body'=>'Thank you for your payment! Your immigration case has been reviewed successfully.','read_at'=>now()]);

        $conv4 = Conversation::create(['client_id'=>$client->id,'lawyer_id'=>$chen->id]);
        Message::create(['conversation_id'=>$conv4->id,'sender_id'=>$chen->id,'body'=>'I have sent you the real estate documents for your review.','read_at'=>now()]);

        $conv5 = Conversation::create(['client_id'=>$client->id,'lawyer_id'=>$amanda->id]);
        Message::create(['conversation_id'=>$conv5->id,'sender_id'=>$amanda->id,'body'=>'Your refund has been processed. Sorry the consultation had to be cancelled.','read_at'=>now()]);

        // Law firm demo accounts
        $firm1 = User::create([
            'name'     => 'Morrison & Associates',
            'email'    => 'morrison@lexconnect.com',
            'password' => Hash::make('password'),
            'role'     => 'law_firm',
        ]);
        \App\Models\LawFirmProfile::create([
            'user_id'       => $firm1->id,
            'firm_name'     => 'Morrison & Associates',
            'tagline'       => 'Excellence in Corporate and Commercial Law',
            'description'   => 'A premier law firm specializing in corporate transactions, mergers & acquisitions, and commercial litigation with over 20 years of experience serving clients in the Philippines.',
            'address'       => '88 Valero Street, Salcedo Village',
            'city'          => 'Makati, Metro Manila',
            'website'       => 'https://morrisonlaw.ph',
            'phone'         => '+63 2 8888 1234',
            'founded_year'  => 2004,
            'firm_size'     => 'medium',
            'specialties'   => ['Corporate Law','Commercial Litigation','Mergers & Acquisitions','Tax Law'],
            'is_verified'   => true,
            'rating'        => 4.8,
            'reviews_count' => 94,
        ]);

        $firm2 = User::create([
            'name'     => 'Hartwell Legal Group',
            'email'    => 'hartwell@lexconnect.com',
            'password' => Hash::make('password'),
            'role'     => 'law_firm',
        ]);
        \App\Models\LawFirmProfile::create([
            'user_id'       => $firm2->id,
            'firm_name'     => 'Hartwell Legal Group',
            'tagline'       => 'Trusted Counsel for Families and Individuals',
            'description'   => 'A boutique law firm focused on family law, estate planning, and personal injury cases. Known for our compassionate approach and client-first philosophy.',
            'address'       => '15 Escolta Street, Binondo',
            'city'          => 'Manila, Metro Manila',
            'website'       => 'https://hartwelllegal.ph',
            'phone'         => '+63 2 8777 5678',
            'founded_year'  => 2011,
            'firm_size'     => 'small',
            'specialties'   => ['Family Law','Estate Planning','Personal Injury','Employment Law'],
            'is_verified'   => true,
            'rating'        => 4.6,
            'reviews_count' => 57,
        ]);
    }
}
