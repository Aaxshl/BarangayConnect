<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model {
    protected $fillable = ['key', 'value'];

    const MANAGED_ROLES = [
        'secretary'    => 'Barangay Secretary',
        'councilor'    => 'Barangay Kagawad (Councilor)',
        'staff'        => 'Barangay Staff / Tanod',
        'sk_chairman'  => 'SK Chairman',
        'sk_councilor' => 'SK Councilor',
    ];

    const PERMISSIONS = [
        'Navigation & Pages' => [
            'nav.residents'     => 'View Residents Menu',
            'nav.households'    => 'View Households Menu',
            'nav.documents'     => 'View Documents Menu',
            'nav.services'      => 'View Service Logs Menu',
            'nav.requests'      => 'View Citizen Requests Menu',
            'nav.mapping'       => 'View Issue Mapping Menu',
            'nav.qr'            => 'View QR Verification Menu',
            'nav.reports'       => 'View Reports Menu',
            'nav.announcements' => 'View Announcements Menu',
        ],
        'Residents & Households' => [
            'residents.create_edit' => 'Add & Edit Resident / Household records',
            'residents.delete'      => 'Delete Resident / Household records',
        ],
        'Document Management' => [
            'documents.create'    => 'Issue / Request New Document',
            'documents.process'   => 'Approve & Process Document',
            'documents.release'   => 'Release Document to Resident',
            'documents.reject'    => 'Reject / Cancel Document Request',
            'documents.print'     => 'Print Official PDF Certificates',
            'documents.templates' => 'Manage Document Templates',
        ],
        'Citizen Requests / Reports' => [
            'requests.assign'  => 'Assign Staff / Officer to Request',
            'requests.status'  => 'Update Status & Resolve Case',
            'requests.convert' => 'Convert to Blotter / Service Log',
            'requests.delete'  => 'Close / Delete Citizen Request',
        ],
        'Service Logs & Blotter' => [
            'services.create' => 'Create New Incident / Service Log',
            'services.edit'   => 'Edit Service Log Details',
            'services.assign' => 'Assign Officer & Schedule Service',
            'services.status' => 'Update Status, Resolve & Close',
        ],
        'Announcements' => [
            'announcements.create'  => 'Create Draft Announcement',
            'announcements.publish' => 'Publish Live, Schedule & Archive',
            'announcements.delete'  => 'Permanently Delete Announcement',
        ],
        'Reports & Export Hub' => [
            'reports.view'          => 'View Reports & Statistics',
            'reports.export_single' => 'Export Individual Reports (PDF/Excel)',
            'reports.export_zip'    => 'Download Batch ZIP Archive',
        ],
    ];

    const DEFAULT_ROLE_PERMISSIONS = [
        'secretary' => [
            'nav.residents' => true, 'nav.households' => true, 'nav.documents' => true,
            'nav.services' => true, 'nav.requests' => true, 'nav.mapping' => true,
            'nav.qr' => true, 'nav.reports' => true, 'nav.announcements' => true,
            'residents.create_edit' => true, 'residents.delete' => false,
            'documents.create' => true, 'documents.process' => true, 'documents.release' => true,
            'documents.reject' => true, 'documents.print' => true, 'documents.templates' => true,
            'requests.assign' => true, 'requests.status' => true, 'requests.convert' => true, 'requests.delete' => false,
            'services.create' => true, 'services.edit' => true, 'services.assign' => true, 'services.status' => true,
            'announcements.create' => true, 'announcements.publish' => false, 'announcements.delete' => false,
            'reports.view' => true, 'reports.export_single' => true, 'reports.export_zip' => true,
        ],
        'councilor' => [
            'nav.residents' => true, 'nav.households' => true, 'nav.documents' => true,
            'nav.services' => true, 'nav.requests' => true, 'nav.mapping' => true,
            'nav.qr' => true, 'nav.reports' => true, 'nav.announcements' => true,
            'residents.create_edit' => false, 'residents.delete' => false,
            'documents.create' => false, 'documents.process' => false, 'documents.release' => false,
            'documents.reject' => false, 'documents.print' => false, 'documents.templates' => false,
            'requests.assign' => false, 'requests.status' => false, 'requests.convert' => false, 'requests.delete' => false,
            'services.create' => false, 'services.edit' => false, 'services.assign' => false, 'services.status' => false,
            'announcements.create' => false, 'announcements.publish' => false, 'announcements.delete' => false,
            'reports.view' => true, 'reports.export_single' => false, 'reports.export_zip' => false,
        ],
        'staff' => [
            'nav.residents' => true, 'nav.households' => false, 'nav.documents' => false,
            'nav.services' => true, 'nav.requests' => true, 'nav.mapping' => true,
            'nav.qr' => true, 'nav.reports' => false, 'nav.announcements' => true,
            'residents.create_edit' => false, 'residents.delete' => false,
            'documents.create' => false, 'documents.process' => false, 'documents.release' => false,
            'documents.reject' => false, 'documents.print' => false, 'documents.templates' => false,
            'requests.assign' => false, 'requests.status' => true, 'requests.convert' => false, 'requests.delete' => false,
            'services.create' => false, 'services.edit' => false, 'services.assign' => false, 'services.status' => true,
            'announcements.create' => false, 'announcements.publish' => false, 'announcements.delete' => false,
            'reports.view' => false, 'reports.export_single' => false, 'reports.export_zip' => false,
        ],
        'sk_chairman' => [
            'nav.residents' => true, 'nav.households' => false, 'nav.documents' => false,
            'nav.services' => false, 'nav.requests' => false, 'nav.mapping' => false,
            'nav.qr' => false, 'nav.reports' => false, 'nav.announcements' => true,
            'residents.create_edit' => false, 'residents.delete' => false,
            'documents.create' => false, 'documents.process' => false, 'documents.release' => false,
            'documents.reject' => false, 'documents.print' => false, 'documents.templates' => false,
            'requests.assign' => false, 'requests.status' => false, 'requests.convert' => false, 'requests.delete' => false,
            'services.create' => false, 'services.edit' => false, 'services.assign' => false, 'services.status' => false,
            'announcements.create' => true, 'announcements.publish' => true, 'announcements.delete' => true,
            'reports.view' => false, 'reports.export_single' => false, 'reports.export_zip' => false,
        ],
        'sk_councilor' => [
            'nav.residents' => true, 'nav.households' => false, 'nav.documents' => false,
            'nav.services' => false, 'nav.requests' => false, 'nav.mapping' => false,
            'nav.qr' => false, 'nav.reports' => false, 'nav.announcements' => true,
            'residents.create_edit' => false, 'residents.delete' => false,
            'documents.create' => false, 'documents.process' => false, 'documents.release' => false,
            'documents.reject' => false, 'documents.print' => false, 'documents.templates' => false,
            'requests.assign' => false, 'requests.status' => false, 'requests.convert' => false, 'requests.delete' => false,
            'services.create' => false, 'services.edit' => false, 'services.assign' => false, 'services.status' => false,
            'announcements.create' => true, 'announcements.publish' => false, 'announcements.delete' => false,
            'reports.view' => false, 'reports.export_single' => false, 'reports.export_zip' => false,
        ],
    ];

    const DEFAULT_AGE_BRACKETS = [
        [
            'category' => 'Childhood & Youth',
            'brackets' => [
                ['min' => 0, 'max' => 12, 'label' => 'Children (Infancy, Toddlerhood, & Middle Childhood)'],
                ['min' => 13, 'max' => 17, 'label' => 'Adolescents / Teens'],
            ],
        ],
        [
            'category' => 'Adult Demographics',
            'brackets' => [
                ['min' => 18, 'max' => 24, 'label' => 'Young Adults (Transitioning into higher education or early workforce)'],
                ['min' => 25, 'max' => 34, 'label' => 'Early Career & Family-Building'],
                ['min' => 35, 'max' => 44, 'label' => 'Mid-Career & Established Adulthood'],
                ['min' => 45, 'max' => 54, 'label' => 'Mature Adulthood'],
                ['min' => 55, 'max' => 64, 'label' => 'Pre-Retirement Years'],
            ],
        ],
        [
            'category' => 'Seniors',
            'brackets' => [
                ['min' => 65, 'max' => null, 'label' => 'Senior Citizens & Retirees'],
            ],
        ],
    ];

    public static function get($key, $default = null) {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value) {
        return self::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /**
     * Retrieve demographic age brackets configured for legislative/councilor overview.
     */
    public static function getAgeBrackets(): array {
        $stored = self::get('demographic_age_brackets');
        if ($stored) {
            $decoded = json_decode($stored, true);
            if (is_array($decoded) && !empty($decoded)) {
                return $decoded;
            }
        }
        return self::DEFAULT_AGE_BRACKETS;
    }

    /**
     * Store demographic age brackets configuration.
     */
    public static function setAgeBrackets(array $brackets): void {
        self::set('demographic_age_brackets', json_encode($brackets));
    }

    /**
     * Retrieve the active permissions matrix (merged with defaults).
     */
    public static function getPermissionsMatrix(): array {
        $storedJson = self::get('role_permissions');
        $stored = $storedJson ? json_decode($storedJson, true) : [];

        $matrix = self::DEFAULT_ROLE_PERMISSIONS;
        if (is_array($stored)) {
            foreach ($matrix as $role => $perms) {
                if (isset($stored[$role]) && is_array($stored[$role])) {
                    foreach ($perms as $permKey => $defaultVal) {
                        $matrix[$role][$permKey] = !empty($stored[$role][$permKey]);
                    }
                }
            }
        }

        return $matrix;
    }

    /**
     * Store updated permissions matrix into database.
     */
    public static function setPermissionsMatrix(array $matrix): void {
        self::set('role_permissions', json_encode($matrix));
    }
}
