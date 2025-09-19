<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmbedFormController extends Controller
{
    public function show(Request $request)
    {
        $formId = $request->get('form_id');
        $origin = $request->header('Origin');
        $allowedOrigin = $this->validateOrigin($origin);

        // Accept style parameters
        $styles = [
            'primary_color' => $request->get('primary_color', '#4CAF50'),
            'button_color' => $request->get('button_color', null),
            'font_family' => $request->get('font_family', 'inherit'),
            'border_radius' => $request->get('border_radius', '4px'),
            'input_border_color' => $request->get('input_border_color', '#ddd'),
        ];

        return view('embed.form', [
            'formId' => $formId,
            'parentOrigin' => $allowedOrigin,
            'styles' => $styles
        ]);
    }

    public function submit(Request $request)
    {
        // Validate origin
        $origin = $request->header('Origin');

        if (!$this->isAllowedOrigin($origin)) {
            return response()->json([
                'success' => false,
                'message' => 'Origin not allowed'
            ], 403);
        }

        // Validate form data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:5000',
            // Add other fields as needed
        ]);

        // Add form_id and other metadata
        $validated['form_id'] = $request->input('form_id');
        $validated['submitted_from'] = $origin;
        $validated['ip_address'] = $request->ip();

        // Process form submission
        // $submission = FormSubmission::create($validated);

        info('Form submitted successfully');

        return response()->json([
            'success' => true,
            'message' => 'Form submitted successfully',
            'submission_id' => 1
        ]);
    }

    /**
     * Validate and return the allowed origin
     *
     * @param string|null $origin
     * @return string
     */
    private function validateOrigin($origin)
    {
        if (!$origin) {
            return '*';
        }

        if ($this->isAllowedOrigin($origin)) {
            return $origin;
        }

        // Return wildcard if origin not in allowed list
        // Or you could return the first allowed domain as fallback
        return '*';
    }

    /**
     * Check if origin is in the allowed list
     *
     * @param string|null $origin
     * @return bool
     */
    private function isAllowedOrigin($origin)
    {
        if (!$origin) {
            return false;
        }

        $allowedDomains = config('embed.allowed_domains', []);

        // If allowed domains is empty, allow all (development mode)
        if (empty($allowedDomains)) {
            return true;
        }

        // Check exact match
        if (in_array($origin, $allowedDomains)) {
            return true;
        }

        // Optional: Check wildcard domains (*.example.com)
        foreach ($allowedDomains as $allowed) {
            if ($this->matchesWildcard($origin, $allowed)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if origin matches wildcard pattern
     *
     * @param string $origin
     * @param string $pattern
     * @return bool
     */
    private function matchesWildcard($origin, $pattern)
    {
        // Convert wildcard pattern to regex
        // *.example.com becomes https://.*\.example\.com
        if (strpos($pattern, '*') !== false) {
            $regex = str_replace(
                ['*', '.'],
                ['.*', '\.'],
                $pattern
            );
            return preg_match('/^' . $regex . '$/i', $origin) === 1;
        }

        return false;
    }
}
