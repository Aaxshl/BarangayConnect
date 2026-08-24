<?php
namespace App\Http\Controllers;

use App\Models\{Document, DocumentTemplate};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentTemplateController extends Controller {
    public function index() {
        $types = Document::TYPES;
        $templates = [];
        foreach (array_keys($types) as $type) {
            $templates[$type] = DocumentTemplate::getTemplateFor($type);
        }
        return view('admin.documents.templates.index', compact('types', 'templates'));
    }

    public function edit($type) {
        if (!array_key_exists($type, Document::TYPES)) {
            abort(404, 'Invalid document type.');
        }
        $template = DocumentTemplate::getTemplateFor($type);
        $typeName = Document::TYPES[$type];
        
        $placeholders = [
            '{RESIDENT_NAME}' => "Full Name of Resident",
            '{CIVIL_STATUS}'  => "Civil Status (Single, Married, etc.)",
            '{RESIDENT_ADDRESS}' => "Full Address of Resident",
            '{PURPOSE}'        => "Purpose of Document Request",
            '{DOC_NUMBER}'     => "Unique Document Number (e.g. DOC-2026-0001)",
            '{ISSUE_DATE}'     => "Date of Issue (e.g. August 07, 2026)",
            '{BARANGAY_NAME}'  => "Barangay Name from Settings",
            '{BARANGAY_ADDRESS}' => "Barangay Address from Settings",
            '{CAPTAIN_NAME}'   => "Barangay Captain Name",
        ];

        return view('admin.documents.templates.edit', compact('template', 'type', 'typeName', 'placeholders'));
    }

    public function update(Request $request, $type) {
        if (!array_key_exists($type, Document::TYPES)) {
            abort(404);
        }

        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'header_text'     => 'nullable|string',
            'body_template'   => 'required|string',
            'footer_text'     => 'nullable|string',
            'show_logo'       => 'nullable|boolean',
            'signatory_title' => 'nullable|string|max:255',
            'signatory_name'  => 'nullable|string|max:255',
            'custom_logo'     => 'nullable|image|max:2048',
        ]);

        $template = DocumentTemplate::getTemplateFor($type);
        
        $validated['show_logo'] = $request->has('show_logo');

        if ($request->hasFile('custom_logo')) {
            if ($template->custom_logo && Storage::disk('public')->exists($template->custom_logo)) {
                Storage::disk('public')->delete($template->custom_logo);
            }
            $validated['custom_logo'] = $request->file('custom_logo')->store('templates', 'public');
        }

        $template->update($validated);

        return redirect()->route('admin.documents.templates.index')->with('success', 'Document template updated successfully.');
    }

    public function reset($type) {
        $template = DocumentTemplate::where('document_type', $type)->first();
        if ($template) {
            if ($template->custom_logo && Storage::disk('public')->exists($template->custom_logo)) {
                Storage::disk('public')->delete($template->custom_logo);
            }
            $template->delete();
        }
        DocumentTemplate::createDefaultTemplate($type);
        return back()->with('success', 'Document template reset to default.');
    }
}
