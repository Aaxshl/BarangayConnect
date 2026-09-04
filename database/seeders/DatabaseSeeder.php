<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\{User, Household, Resident, Document, ServiceLog, CitizenRequest, Announcement, Setting, SkProgram};

class DatabaseSeeder extends Seeder {
    public function run(): void {
        // Settings
        $defaults = [
            'barangay_name'   => 'Barangay San Jose',
            'barangay_address'=> 'San Pedro City, Laguna',
            'contact_number'  => '(049) 123-4567',
            'email'           => 'brgy.sanjose@spcl.gov.ph',
            'captain_name'    => 'Hon. Ricardo Santos',
            'system_name'     => 'SmartBarangay',
            'report_header'   => 'Republic of the Philippines',
            'fiscal_year'     => date('Y'),
        ];
        foreach ($defaults as $k => $v) Setting::set($k, $v);

        // Users for all 7 Roles
        $captain = User::firstOrCreate(
            ['email' => 'captain@brgy.gov.ph'],
            ['name' => 'Hon. Ricardo Santos', 'password' => Hash::make('password'), 'role' => 'captain', 'status' => 'active']
        );
        $admin = User::firstOrCreate(
            ['email' => 'admin@brgy.gov.ph'],
            ['name' => 'Joven Reyes', 'password' => Hash::make('password'), 'role' => 'administrator', 'status' => 'active']
        );
        $councilor = User::firstOrCreate(
            ['email' => 'councilor@brgy.gov.ph'],
            ['name' => 'Hon. Teresa Mendoza', 'password' => Hash::make('password'), 'role' => 'councilor', 'status' => 'active']
        );
        $secretary = User::firstOrCreate(
            ['email' => 'secretary@brgy.gov.ph'],
            ['name' => 'Leny Aguilar', 'password' => Hash::make('password'), 'role' => 'secretary', 'status' => 'active']
        );
        $staff = User::firstOrCreate(
            ['email' => 'staff@brgy.gov.ph'],
            ['name' => 'Marco Villanueva', 'password' => Hash::make('password'), 'role' => 'staff', 'status' => 'active']
        );
        $skChair = User::firstOrCreate(
            ['email' => 'sk.chair@brgy.gov.ph'],
            ['name' => 'Hon. Joshua Ramos', 'password' => Hash::make('password'), 'role' => 'sk_chairman', 'status' => 'active']
        );
        $skCouncilor = User::firstOrCreate(
            ['email' => 'sk.councilor@brgy.gov.ph'],
            ['name' => 'Chloe Bautista', 'password' => Hash::make('password'), 'role' => 'sk_councilor', 'status' => 'active']
        );

        // Households
        $h1 = Household::firstOrCreate(['household_id'=>'HH-0012'], ['address'=>'123 Purok 1, Zone A','purok'=>'Purok 1','zone'=>'Zone A','contact_number'=>'09171234567','number_of_members'=>5]);
        $h2 = Household::firstOrCreate(['household_id'=>'HH-0045'], ['address'=>'456 Purok 2, Zone B','purok'=>'Purok 2','zone'=>'Zone B','contact_number'=>'09281234567','number_of_members'=>3]);
        $h3 = Household::firstOrCreate(['household_id'=>'HH-0078'], ['address'=>'789 Purok 3, Zone A','purok'=>'Purok 3','zone'=>'Zone A','contact_number'=>'09351234567','number_of_members'=>2]);

        // Residents
        $r1 = Resident::firstOrCreate(
            ['contact_number' => '09171234567'],
            ['first_name'=>'Juan','last_name'=>'dela Cruz','middle_name'=>'Santos','birthdate'=>'1990-03-15','gender'=>'male','civil_status'=>'married','address'=>'123 Purok 1, Zone A','purok'=>'Purok 1','zone'=>'Zone A','occupation'=>'Farmer','household_id'=>$h1->id,'status'=>'active','created_by'=>$admin->id]
        );
        $r2 = Resident::firstOrCreate(
            ['contact_number' => '09281234567'],
            ['first_name'=>'Maria','last_name'=>'Santos','middle_name'=>'Reyes','birthdate'=>'1996-07-22','gender'=>'female','civil_status'=>'single','address'=>'456 Purok 2, Zone B','purok'=>'Purok 2','zone'=>'Zone B','occupation'=>'IT Professional','household_id'=>$h2->id,'status'=>'active','created_by'=>$admin->id]
        );
        $r3 = Resident::firstOrCreate(
            ['contact_number' => '09351234567'],
            ['first_name'=>'Pedro','last_name'=>'Reyes','middle_name'=>'Cruz','birthdate'=>'1962-11-08','gender'=>'male','civil_status'=>'widowed','address'=>'789 Purok 3, Zone A','purok'=>'Purok 3','zone'=>'Zone A','occupation'=>'Retired','household_id'=>$h3->id,'status'=>'active','created_by'=>$admin->id]
        );

        $h1->update(['head_resident_id' => $r1->id]);
        $h2->update(['head_resident_id' => $r2->id]);
        $h3->update(['head_resident_id' => $r3->id]);

        // Documents
        Document::firstOrCreate(
            ['document_number' => 'DOC-'.date('Y').'-0001'],
            ['resident_id'=>$r1->id,'document_type'=>'barangay_clearance','purpose'=>'Employment','number_of_copies'=>1,'issue_date'=>today(),'status'=>'released','issued_by'=>$admin->id]
        );
        Document::firstOrCreate(
            ['document_number' => 'DOC-'.date('Y').'-0002'],
            ['resident_id'=>$r2->id,'document_type'=>'certificate_residency','purpose'=>'Bank requirement','number_of_copies'=>2,'issue_date'=>today(),'status'=>'pending_pickup','issued_by'=>$admin->id]
        );
        Document::firstOrCreate(
            ['document_number' => 'DOC-'.date('Y').'-0003'],
            ['resident_id'=>$r3->id,'document_type'=>'certificate_indigency','purpose'=>'Government assistance','number_of_copies'=>1,'issue_date'=>today()->subDay(),'status'=>'released','issued_by'=>$admin->id]
        );

        // Service Logs
        ServiceLog::firstOrCreate(
            ['log_number' => 'SL-0001'],
            ['service_type'=>'mediation','resident_id'=>$r1->id,'description'=>'Property boundary dispute with neighbor.','date_of_service'=>today(),'status'=>'in_progress','assigned_to'=>$admin->id,'created_by'=>$admin->id]
        );
        ServiceLog::firstOrCreate(
            ['log_number' => 'SL-0002'],
            ['service_type'=>'community_assistance','resident_id'=>$r2->id,'description'=>'Requested assistance for senior parent medical needs.','date_of_service'=>today()->subDay(),'status'=>'resolved','assigned_to'=>$admin->id,'created_by'=>$admin->id]
        );

        // Citizen Requests
        CitizenRequest::firstOrCreate(
            ['tracking_number' => 'REQ-'.date('Y').'-0001'],
            ['resident_id'=>$r2->id,'request_type'=>'broken_streetlight','description'=>'The streetlight near the sari-sari store on Purok 2 main road has been broken for 2 weeks.','location'=>'Purok 2, Zone B near sari-sari store','latitude'=>14.3506,'longitude'=>121.0453,'status'=>'under_review']
        );
        CitizenRequest::firstOrCreate(
            ['tracking_number' => 'REQ-'.date('Y').'-0002'],
            ['resident_id'=>$r3->id,'request_type'=>'clogged_drainage','description'=>'Drainage near basketball court is overflowing causing flooding during rain.','location'=>'Zone B basketball court area','latitude'=>14.3512,'longitude'=>121.0461,'status'=>'in_progress','assigned_to'=>$admin->id]
        );
        CitizenRequest::firstOrCreate(
            ['tracking_number' => 'REQ-'.date('Y').'-0003'],
            ['resident_id'=>$r1->id,'request_type'=>'road_damage','description'=>'Large potholes on main road near Purok 1 entrance.','location'=>'Purok 1 entrance, main road','latitude'=>14.3498,'longitude'=>121.0445,'status'=>'resolved','assigned_to'=>$admin->id,'resolved_at'=>now()->subDays(2),'resolution_note'=>'Road patched by infrastructure team.']
        );

        // Announcements
        Announcement::firstOrCreate(
            ['title' => 'Barangay Fiesta — July 15, '.date('Y')],
            ['body'=>'Join us for the annual Barangay San Jose fiesta at the covered court. Activities include cultural shows, laro ng lahi, and community feeding. Everyone is welcome!','announcement_type'=>'community_event','status'=>'published','published_at'=>now()->subDays(7),'created_by'=>$admin->id]
        );
        Announcement::firstOrCreate(
            ['title' => 'Free Vaccination Drive'],
            ['body'=>'Anti-rabies and flu vaccines available for all residents. Bring your barangay ID or any valid government ID. 8 AM – 5 PM at the Barangay Hall.','announcement_type'=>'health_advisory','status'=>'published','published_at'=>now()->subDays(9),'created_by'=>$admin->id]
        );
        Announcement::firstOrCreate(
            ['title' => 'Road Closure — Calle Rizal'],
            ['body'=>'Calle Rizal will be temporarily closed for drainage repair works. Please use the Purok 3 road as an alternative route. We apologize for any inconvenience.','announcement_type'=>'public_advisory','status'=>'draft','created_by'=>$admin->id]
        );

        // Youth Residents (Ages 15-30)
        $y1 = Resident::firstOrCreate(
            ['contact_number' => '09181112233'],
            [
                'first_name'     => 'Mark Anthony',
                'last_name'      => 'Reyes',
                'middle_name'    => 'Santos',
                'birthdate'      => date('Y') - 16 . '-05-12', // 16 yrs old (Teen)
                'gender'         => 'male',
                'civil_status'   => 'single',
                'address'        => '123 Purok 1, Zone A',
                'purok'          => 'Purok 1',
                'zone'           => 'Zone A',
                'occupation'     => 'High School Student',
                'household_id'   => $h1->id,
                'status'         => 'active',
                'created_by'     => $admin->id
            ]
        );

        $y2 = Resident::firstOrCreate(
            ['contact_number' => '09192223344'],
            [
                'first_name'     => 'Bea Nicole',
                'last_name'      => 'Santos',
                'middle_name'    => 'Dela Cruz',
                'birthdate'      => date('Y') - 21 . '-08-19', // 21 yrs old (Young Adult)
                'gender'         => 'female',
                'civil_status'   => 'single',
                'address'        => '456 Purok 2, Zone B',
                'purok'          => 'Purok 2',
                'zone'           => 'Zone B',
                'occupation'     => 'College Student',
                'household_id'   => $h2->id,
                'status'         => 'active',
                'created_by'     => $admin->id
            ]
        );

        $y3 = Resident::firstOrCreate(
            ['contact_number' => '09203334455'],
            [
                'first_name'     => 'Justin Kyle',
                'last_name'      => 'Villanueva',
                'middle_name'    => 'Bautista',
                'birthdate'      => date('Y') - 27 . '-02-10', // 27 yrs old (Adult Youth)
                'gender'         => 'male',
                'civil_status'   => 'single',
                'address'        => '789 Purok 3, Zone A',
                'purok'          => 'Purok 3',
                'zone'           => 'Zone A',
                'occupation'     => 'Junior Web Developer',
                'household_id'   => $h3->id,
                'status'         => 'active',
                'created_by'     => $admin->id
            ]
        );

        // SK Programs & Initiatives
        SkProgram::firstOrCreate(
            ['title' => 'Annual Inter-Purok Youth Basketball & Volleyball League'],
            [
                'category'            => 'sports_and_wellness',
                'description'         => 'Promoting camaraderie, sportsmanship, and physical wellness among barangay youth. Open to all residents aged 15-30 across all puroks.',
                'location'            => 'Barangay Covered Court',
                'budget'              => 45000.00,
                'target_participants' => 180,
                'start_date'          => today()->addDays(5),
                'end_date'            => today()->addDays(35),
                'status'              => 'ongoing',
                'coordinator_id'      => $skCouncilor->id,
                'created_by'          => $skChair->id,
                'remarks'             => 'Referees and jerseys procured. Team registration ongoing.',
            ]
        );

        SkProgram::firstOrCreate(
            ['title' => 'SK Linggo ng Kabataan: Leadership & Anti-Drug Abuse Seminar'],
            [
                'category'            => 'leadership_and_governance',
                'description'         => 'A comprehensive two-day workshop covering youth civic engagement, public speaking, mental health, and substance abuse prevention with guest speakers from NYC and PDEA.',
                'location'            => 'Barangay Multi-Purpose Hall',
                'budget'              => 25000.00,
                'target_participants' => 120,
                'start_date'          => today()->addDays(14),
                'end_date'            => today()->addDays(15),
                'status'              => 'approved',
                'coordinator_id'      => $skChair->id,
                'created_by'          => $skChair->id,
                'remarks'             => 'Approved by SK council. Invitations distributed to local schools.',
            ]
        );

        SkProgram::firstOrCreate(
            ['title' => 'Libreng College Entrance Exam (UPCAT/PUPCET) Review Program'],
            [
                'category'            => 'education_and_scholarship',
                'description'         => 'Free weekend intensive tutorial sessions and review materials for graduating senior high school students aiming for state universities.',
                'location'            => 'Barangay E-Library & Learning Hub',
                'budget'              => 35000.00,
                'target_participants' => 60,
                'start_date'          => today()->addDays(30),
                'end_date'            => today()->addDays(60),
                'status'              => 'proposed',
                'coordinator_id'      => $skCouncilor->id,
                'created_by'          => $skCouncilor->id,
                'remarks'             => 'Awaiting partner volunteer teachers from university alumni.',
            ]
        );

        SkProgram::firstOrCreate(
            ['title' => 'Tapat Ko, Linis Ko: Youth River Clean-up & Tree Planting Drive'],
            [
                'category'            => 'environmental_protection',
                'description'         => 'Environmental rehabilitation of the local riverbanks and planting 200 native seedlings to prevent soil erosion and promote ecological balance.',
                'location'            => 'Barangay Riverbank & Purok 3 Green Zone',
                'budget'              => 15000.00,
                'target_participants' => 90,
                'start_date'          => today()->subDays(20),
                'end_date'            => today()->subDays(20),
                'status'              => 'completed',
                'coordinator_id'      => $skCouncilor->id,
                'created_by'          => $skChair->id,
                'remarks'             => 'Successfully planted 210 seedlings with 95 volunteer youth participants.',
            ]
        );
    }
}
