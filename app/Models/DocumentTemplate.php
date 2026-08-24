<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocumentTemplate extends Model {
    use HasFactory;

    protected $fillable = [
        'document_type',
        'title',
        'header_text',
        'body_template',
        'footer_text',
        'show_logo',
        'custom_logo',
        'signatory_title',
        'signatory_name',
    ];

    protected $casts = [
        'show_logo' => 'boolean',
    ];

    public static function getTemplateFor($type) {
        $template = static::where('document_type', $type)->first();
        if (!$template) {
            $template = static::createDefaultTemplate($type);
        }
        return $template;
    }

    public static function createDefaultTemplate($type) {
        $typeName = Document::TYPES[$type] ?? ucwords(str_replace('_', ' ', $type));
        
        $defaults = [
            'barangay_clearance' => [
                'title' => 'BARANGAY CLEARANCE',
                'header_text' => "Republic of the Philippines\nProvince of Laguna · City of San Pedro\nBARANGAY SAN JOSE\nOFFICE OF THE BARANGAY CAPTAIN",
                'body_template' => "TO WHOM IT MAY CONCERN:\n\nThis is to certify that {RESIDENT_NAME}, of legal age, {CIVIL_STATUS}, Filipino, is a bona fide resident of {BARANGAY_NAME}, {BARANGAY_ADDRESS}.\n\nThis certification is issued upon the request of the above-named individual for the purpose of {PURPOSE}.\n\nBased on official records of this barangay, the subject person has NO DEROGATORY RECORD filed against him/her as of this date.",
                'footer_text' => 'This document is NOT valid without the official seal and signature of the Barangay Captain.',
                'show_logo' => true,
                'signatory_title' => 'Barangay Captain',
                'signatory_name' => '',
            ],
            'certificate_residency' => [
                'title' => 'CERTIFICATE OF RESIDENCY',
                'header_text' => "Republic of the Philippines\nProvince of Laguna · City of San Pedro\nBARANGAY SAN JOSE\nOFFICE OF THE BARANGAY CAPTAIN",
                'body_template' => "TO WHOM IT MAY CONCERN:\n\nThis is to certify that {RESIDENT_NAME}, of legal age, {CIVIL_STATUS}, Filipino, is a permanent and bona fide resident of {RESIDENT_ADDRESS}.\n\nThis certification is issued upon request of the interested party for {PURPOSE} and for whatever legal intent it may serve.",
                'footer_text' => 'Valid for six (6) months from the date of issuance.',
                'show_logo' => true,
                'signatory_title' => 'Barangay Captain',
                'signatory_name' => '',
            ],
            'certificate_indigency' => [
                'title' => 'CERTIFICATE OF INDIGENCY',
                'header_text' => "Republic of the Philippines\nProvince of Laguna · City of San Pedro\nBARANGAY SAN JOSE\nOFFICE OF THE BARANGAY CAPTAIN",
                'body_template' => "TO WHOM IT MAY CONCERN:\n\nThis is to certify that {RESIDENT_NAME}, residing at {RESIDENT_ADDRESS}, belongs to an indigent family in this barangay with low income.\n\nThis certificate is issued upon the request of the bearer for the purpose of {PURPOSE}.",
                'footer_text' => 'Issued for social welfare, medical, or educational financial assistance.',
                'show_logo' => true,
                'signatory_title' => 'Barangay Captain',
                'signatory_name' => '',
            ],
            'business_clearance' => [
                'title' => 'BARANGAY BUSINESS CLEARANCE',
                'header_text' => "Republic of the Philippines\nProvince of Laguna · City of San Pedro\nBARANGAY SAN JOSE\nOFFICE OF THE BARANGAY CAPTAIN",
                'body_template' => "TO WHOM IT MAY CONCERN:\n\nClearance is hereby granted to {RESIDENT_NAME} to operate business/establishment at {RESIDENT_ADDRESS}.\n\nThis clearance is issued for the purpose of {PURPOSE} subject to full compliance with municipal ordinances and laws.",
                'footer_text' => 'Subject to revocation upon violation of barangay rules.',
                'show_logo' => true,
                'signatory_title' => 'Barangay Captain',
                'signatory_name' => '',
            ],
            'barangay_permit' => [
                'title' => 'BARANGAY PERMIT',
                'header_text' => "Republic of the Philippines\nProvince of Laguna · City of San Pedro\nBARANGAY SAN JOSE\nOFFICE OF THE BARANGAY CAPTAIN",
                'body_template' => "TO WHOM IT MAY CONCERN:\n\nPermission is hereby granted to {RESIDENT_NAME} for {PURPOSE} located at {RESIDENT_ADDRESS}.\n\nThis permit is subject to existing barangay health and safety regulations.",
                'footer_text' => 'Keep posted at event/activity premises.',
                'show_logo' => true,
                'signatory_title' => 'Barangay Captain',
                'signatory_name' => '',
            ],
        ];

        $data = $defaults[$type] ?? [
            'title' => strtoupper($typeName),
            'header_text' => "Republic of the Philippines\nProvince of Laguna · City of San Pedro\nBARANGAY SAN JOSE\nOFFICE OF THE BARANGAY CAPTAIN",
            'body_template' => "TO WHOM IT MAY CONCERN:\n\nThis is to certify that {RESIDENT_NAME} requested this document for the purpose of {PURPOSE}.",
            'footer_text' => 'Official Barangay Document.',
            'show_logo' => true,
            'signatory_title' => 'Barangay Captain',
            'signatory_name' => '',
        ];

        return static::create(array_merge(['document_type' => $type], $data));
    }
}
