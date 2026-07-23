<?php

namespace App\Http\Controllers\Admin\Government;

use App\Http\Controllers\Controller;
use App\Models\Government\Testimonial;
use App\Models\Government\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TestimonialController extends Controller
{
    /**
     * Display a listing of the testimonials.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $testimonials = Testimonial::orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin.government.testimonials.index', compact('testimonials'));
    }

    /**
     * Show the form for creating a new testimonial.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $services = Service::pluck('title', 'id');
        
        return view('admin.government.testimonials.create', compact('services'));
    }

    /**
     * Store a newly created testimonial in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'position' => 'nullable|max:255',
            'content' => 'required',
            'rating' => 'nullable|integer|min:1|max:5',
            'avatar' => 'nullable|image|max:2048',
            'status' => 'required|in:active,inactive',
            'is_featured' => 'boolean',
            'service_id' => 'nullable|exists:government_services,id',
        ]);
        
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('government/testimonials', 'public');
            $validatedData['avatar'] = $path;
        }
        
        $validatedData['is_featured'] = $request->has('is_featured');
        
        try {
            Testimonial::create($validatedData);
            return redirect()->route('admin.government.testimonials.index')
                ->with('success', 'Testimonial created successfully');
        } catch (\Exception $e) {
            Log::error('Failed to create testimonial: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create testimonial: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified testimonial.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $services = Service::pluck('title', 'id');
        
        return view('admin.government.testimonials.edit', compact('testimonial', 'services'));
    }

    /**
     * Update the specified testimonial in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $testimonial = Testimonial::findOrFail($id);
        
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'position' => 'nullable|max:255',
            'content' => 'required',
            'rating' => 'nullable|integer|min:1|max:5',
            'avatar' => 'nullable|image|max:2048',
            'status' => 'required|in:active,inactive',
            'service_id' => 'nullable|exists:government_services,id',
        ]);
        
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($testimonial->avatar && Storage::disk('public')->exists($testimonial->avatar)) {
                Storage::disk('public')->delete($testimonial->avatar);
            }
            
            $path = $request->file('avatar')->store('government/testimonials', 'public');
            $validatedData['avatar'] = $path;
        }
        
        $validatedData['is_featured'] = $request->has('is_featured');
        
        try {
            $testimonial->update($validatedData);
            return redirect()->route('admin.government.testimonials.index')
                ->with('success', 'Testimonial updated successfully');
        } catch (\Exception $e) {
            Log::error('Failed to update testimonial: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update testimonial: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified testimonial from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        
        try {
            // Delete avatar if exists
            if ($testimonial->avatar && Storage::disk('public')->exists($testimonial->avatar)) {
                Storage::disk('public')->delete($testimonial->avatar);
            }
            
            $testimonial->delete();
            return redirect()->route('admin.government.testimonials.index')
                ->with('success', 'Testimonial deleted successfully');
        } catch (\Exception $e) {
            Log::error('Failed to delete testimonial: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete testimonial: ' . $e->getMessage());
        }
    }
    
    /**
     * Toggle featured status of the testimonial.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggleFeatured($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->is_featured = !$testimonial->is_featured;
        $testimonial->save();
        
        return redirect()->route('admin.government.testimonials.index')
            ->with('success', 'Testimonial featured status updated');
    }
    
    /**
     * Toggle status of the testimonial.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggleStatus($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->status = ($testimonial->status === 'active') ? 'inactive' : 'active';
        $testimonial->save();
        
        return redirect()->route('admin.government.testimonials.index')
            ->with('success', 'Testimonial status updated');
    }
} 