<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\{User, Household, Resident, Document, ServiceLog, CitizenRequest, Announcement, Setting};

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

        // Users
        $admin = User::create([
            'name'    => 'Joven Reyes',
            'email'   => 'admin@brgy.gov.ph',
            'password'=> Hash::make('password'),
            'role'    => 'administrator',
            'status'  => 'active',
        ]);
        User::create([
            'name'    => 'Leny Aguilar',
            'email'   => 'secretary@brgy.gov.ph',
            'password'=> Hash::make('password'),
            'role'    => 'secretary',
            'status'  => 'active',
        ]);
        User::create([
            'name'    => 'Marco Villanueva',
            'email'   => 'staff@brgy.gov.ph',
            'password'=> Hash::make('password'),
            'role'    => 'staff',
            'status'  => 'active',
        ]);

        // Households
        $h1 = Household::create(['household_id'=>'HH-0012','address'=>'123 Purok 1, Zone A','purok'=>'Purok 1','zone'=>'Zone A','contact_number'=>'09171234567','number_of_members'=>5]);
        $h2 = Household::create(['household_id'=>'HH-0045','address'=>'456 Purok 2, Zone B','purok'=>'Purok 2','zone'=>'Zone B','contact_number'=>'09281234567','number_of_members'=>3]);
        $h3 = Household::create(['household_id'=>'HH-0078','address'=>'789 Purok 3, Zone A','purok'=>'Purok 3','zone'=>'Zone A','contact_number'=>'09351234567','number_of_members'=>2]);

        // Residents
        $r1 = Resident::create(['first_name'=>'Juan','last_name'=>'dela Cruz','middle_name'=>'Santos','birthdate'=>'1990-03-15','gender'=>'male','civil_status'=>'married','address'=>'123 Purok 1, Zone A','purok'=>'Purok 1','zone'=>'Zone A','contact_number'=>'09171234567','occupation'=>'Farmer','household_id'=>$h1->id,'status'=>'active','created_by'=>$admin->id]);
        $r2 = Resident::create(['first_name'=>'Maria','last_name'=>'Santos','middle_name'=>'Reyes','birthdate'=>'1996-07-22','gender'=>'female','civil_status'=>'single','address'=>'456 Purok 2, Zone B','purok'=>'Purok 2','zone'=>'Zone B','contact_number'=>'09281234567','occupation'=>'IT Professional','household_id'=>$h2->id,'status'=>'active','created_by'=>$admin->id]);
        $r3 = Resident::create(['first_name'=>'Pedro','last_name'=>'Reyes','middle_name'=>'Cruz','birthdate'=>'1962-11-08','gender'=>'male','civil_status'=>'widowed','address'=>'789 Purok 3, Zone A','purok'=>'Purok 3','zone'=>'Zone A','contact_number'=>'09351234567','occupation'=>'Retired','household_id'=>$h3->id,'status'=>'active','created_by'=>$admin->id]);

        $h1->update(['head_resident_id' => $r1->id]);
        $h2->update(['head_resident_id' => $r2->id]);
        $h3->update(['head_resident_id' => $r3->id]);

        // Documents
        Document::create(['document_number'=>'DOC-'.date('Y').'-0001','resident_id'=>$r1->id,'document_type'=>'barangay_clearance','purpose'=>'Employment','number_of_copies'=>1,'issue_date'=>today(),'status'=>'released','issued_by'=>$admin->id]);
        Document::create(['document_number'=>'DOC-'.date('Y').'-0002','resident_id'=>$r2->id,'document_type'=>'certificate_residency','purpose'=>'Bank requirement','number_of_copies'=>2,'issue_date'=>today(),'status'=>'pending_pickup','issued_by'=>$admin->id]);
        Document::create(['document_number'=>'DOC-'.date('Y').'-0003','resident_id'=>$r3->id,'document_type'=>'certificate_indigency','purpose'=>'Government assistance','number_of_copies'=>1,'issue_date'=>today()->subDay(),'status'=>'released','issued_by'=>$admin->id]);

        // Service Logs
        ServiceLog::create(['log_number'=>'SL-0001','service_type'=>'mediation','resident_id'=>$r1->id,'description'=>'Property boundary dispute with neighbor.','date_of_service'=>today(),'status'=>'in_progress','assigned_to'=>$admin->id,'created_by'=>$admin->id]);
        ServiceLog::create(['log_number'=>'SL-0002','service_type'=>'community_assistance','resident_id'=>$r2->id,'description'=>'Requested assistance for senior parent medical needs.','date_of_service'=>today()->subDay(),'status'=>'resolved','assigned_to'=>$admin->id,'created_by'=>$admin->id]);

        // Citizen Requests
        CitizenRequest::create(['tracking_number'=>'REQ-'.date('Y').'-0001','resident_id'=>$r2->id,'request_type'=>'broken_streetlight','description'=>'The streetlight near the sari-sari store on Purok 2 main road has been broken for 2 weeks.','location'=>'Purok 2, Zone B near sari-sari store','latitude'=>14.3506,'longitude'=>121.0453,'status'=>'under_review']);
        CitizenRequest::create(['tracking_number'=>'REQ-'.date('Y').'-0002','resident_id'=>$r3->id,'request_type'=>'clogged_drainage','description'=>'Drainage near basketball court is overflowing causing flooding during rain.','location'=>'Zone B basketball court area','latitude'=>14.3512,'longitude'=>121.0461,'status'=>'in_progress','assigned_to'=>$admin->id]);
        CitizenRequest::create(['tracking_number'=>'REQ-'.date('Y').'-0003','resident_id'=>$r1->id,'request_type'=>'road_damage','description'=>'Large potholes on main road near Purok 1 entrance.','location'=>'Purok 1 entrance, main road','latitude'=>14.3498,'longitude'=>121.0445,'status'=>'resolved','assigned_to'=>$admin->id,'resolved_at'=>now()->subDays(2),'resolution_note'=>'Road patched by infrastructure team.']);

        // Announcements
        Announcement::create(['title'=>'Barangay Fiesta — July 15, '.date('Y'),'body'=>'Join us for the annual Barangay San Jose fiesta at the covered court. Activities include cultural shows, laro ng lahi, and community feeding. Everyone is welcome!','announcement_type'=>'community_event','status'=>'published','published_at'=>now()->subDays(7),'created_by'=>$admin->id]);
        Announcement::create(['title'=>'Free Vaccination Drive','body'=>'Anti-rabies and flu vaccines available for all residents. Bring your barangay ID or any valid government ID. 8 AM – 5 PM at the Barangay Hall.','announcement_type'=>'health_advisory','status'=>'published','published_at'=>now()->subDays(9),'created_by'=>$admin->id]);
        Announcement::create(['title'=>'Road Closure — Calle Rizal','body'=>'Calle Rizal will be temporarily closed for drainage repair works. Please use the Purok 3 road as an alternative route. We apologize for any inconvenience.','announcement_type'=>'public_advisory','status'=>'draft','created_by'=>$admin->id]);
    }
}
