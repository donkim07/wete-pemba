@extends('layouts.admin')

@section('title', __('Testimonials Management'))

@section('styles')
<style>
    /* Responsive table styles */
    @media (max-width: 767.98px) {
        .responsive-table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .table-responsive-custom thead th {
            white-space: nowrap;
        }
        
        .img-thumbnail {
            max-width: 40px;
        }
        
        .btn-group-sm .btn {
            padding: .25rem .4rem;
            font-size: .75rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Manage Testimonials') }}</h4>
                    <div class="card-tools">
                        <a href="{{ route('admin.government.testimonials.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus-circle"></i> {{ __('Add New Testimonial') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @include('admin.partials.alerts')

                    @if($testimonials->isEmpty())
                        <div class="alert alert-info">
                            {{ __('No testimonials found. Click the "Add New Testimonial" button to create one.') }}
                        </div>
                    @else
                        <div class="responsive-table-wrapper">
                            <table class="table table-hover table-striped table-responsive-custom">
                                <thead>
                                    <tr>
                                        <th>{{ __('Avatar') }}</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Position') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Featured') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($testimonials as $testimonial)
                                        <tr>
                                            <td>
                                                @if($testimonial->avatar)
                                                    <img src="{{ asset('storage/' . $testimonial->avatar) }}" alt="{{ $testimonial->name }}" class="img-thumbnail" width="50">
                                                @else
                                                    <span class="badge bg-secondary">{{ __('No Image') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $testimonial->name }}</td>
                                            <td>{{ $testimonial->position ?? '-' }}</td>
                                            <td>
                                                @if($testimonial->status === 'active')
                                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($testimonial->is_featured)
                                                    <span class="badge bg-info">{{ __('Featured') }}</span>
                                                @else
                                                    <span class="badge bg-light text-dark">{{ __('Regular') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.government.testimonials.edit', $testimonial->id) }}" class="btn btn-info" title="{{ __('Edit') }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-{{ $testimonial->status === 'active' ? 'warning' : 'success' }}" 
                                                        onclick="document.getElementById('toggle-status-form-{{ $testimonial->id }}').submit();" 
                                                        title="{{ $testimonial->status === 'active' ? __('Deactivate') : __('Activate') }}">
                                                        <i class="fas fa-{{ $testimonial->status === 'active' ? 'ban' : 'check' }}"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-{{ $testimonial->is_featured ? 'secondary' : 'info' }}" 
                                                        onclick="document.getElementById('toggle-featured-form-{{ $testimonial->id }}').submit();" 
                                                        title="{{ $testimonial->is_featured ? __('Unmark Featured') : __('Mark as Featured') }}">
                                                        <i class="fas fa-{{ $testimonial->is_featured ? 'star-half-alt' : 'star' }}"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $testimonial->id }}" title="{{ __('Delete') }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>

                                                <form id="toggle-status-form-{{ $testimonial->id }}" action="{{ route('admin.government.testimonials.toggle-status', $testimonial->id) }}" method="POST" class="d-none">
                                                    @csrf
                                                    @method('PUT')
                                                </form>

                                                <form id="toggle-featured-form-{{ $testimonial->id }}" action="{{ route('admin.government.testimonials.toggle-featured', $testimonial->id) }}" method="POST" class="d-none">
                                                    @csrf
                                                    @method('PUT')
                                                </form>
                                                
                                                <!-- Delete Modal -->
                                                <div class="modal fade" id="deleteModal{{ $testimonial->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $testimonial->id }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="deleteModalLabel{{ $testimonial->id }}">{{ __('Confirm Delete') }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>{{ __('Are you sure you want to delete this testimonial from :name?', ['name' => $testimonial->name]) }}</p>
                                                                <p class="text-danger"><small>{{ __('This action cannot be undone.') }}</small></p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                                                <form action="{{ route('admin.government.testimonials.destroy', $testimonial->id) }}" method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger">{{ __('Yes, Delete') }}</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{ $testimonials->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 