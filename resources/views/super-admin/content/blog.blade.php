@extends('super-admin.layouts.app')

@section('title', 'Blog Management')
@section('page-title', 'Blog Management')

@push('styles')
<style>
    /* Remove unwanted white space at the top and center content */
    .main-content {
        margin-left: 280px;
        min-height: 100vh;
        background: #f8f9fa;
        padding: 0; /* Remove default padding */
    }
    
    .navbar {
        background: white !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 0; /* Remove margin bottom */
        border: none;
        padding: 0.75rem 1rem; /* Reduce padding */
    }
    
    .content-wrapper {
        padding: 1.5rem 2rem; /* Reduce top padding */
        max-width: 1400px; /* Set max width for better centering */
        margin: 0 auto; /* Center the content */
        width: 100%;
    }
    
    /* Fix card spacing and alignment */
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        transition: transform 0.3s ease;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
        min-height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        border-radius: 15px;
        padding: 1.5rem;
    }
    
    .stat-card.success {
        background: linear-gradient(135deg, var(--success-color) 0%, #00f2fe 100%);
    }
    
    .stat-card.warning {
        background: linear-gradient(135deg, var(--warning-color) 0%, #38f9d7 100%);
    }
    
    .stat-card.danger {
        background: linear-gradient(135deg, var(--danger-color) 0%, #fee140 100%);
    }
    
    .content-table-container {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }
    
    .table-responsive {
        border-radius: 15px;
    }
    
    .table {
        margin-bottom: 0;
    }
    
    .table th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        padding: 1rem;
    }
    
    .table td {
        padding: 1rem;
        vertical-align: middle;
    }
    
    .btn {
        border-radius: 8px;
        padding: 0.5rem 1.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
    }
    
    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    }
    
    .search-filters {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .form-control {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 0.75rem;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .badge {
        border-radius: 6px;
        padding: 0.5em 0.75em;
        font-size: 0.75em;
    }
    
    .pagination {
        justify-content: center;
        margin-top: 2rem;
    }
    
    .pagination .page-link {
        border-radius: 8px;
        margin: 0 0.25rem;
        border: none;
        color: var(--primary-color);
    }
    
    .pagination .page-link:hover {
        background-color: var(--primary-color);
        color: white;
    }
    
    .pagination .page-item.active .page-link {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
        }
        
        .content-wrapper {
            padding: 1rem;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .search-filters {
            padding: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Statistics Overview -->
    <div class="stats-grid">
        <div class="card stat-card">
            <div>
                <h4 class="mb-2">{{ number_format($blogStats['total_posts']) }}</h4>
                <p class="mb-0">Total Posts</p>
            </div>
        </div>
        <div class="card stat-card success">
            <div>
                <h4 class="mb-2">{{ number_format($blogStats['published_posts']) }}</h4>
                <p class="mb-0">Published</p>
            </div>
        </div>
        <div class="card stat-card warning">
            <div>
                <h4 class="mb-2">{{ number_format($blogStats['draft_posts']) }}</h4>
                <p class="mb-0">Drafts</p>
            </div>
        </div>
        <div class="card stat-card danger">
            <div>
                <h4 class="mb-2">{{ number_format($blogStats['total_views']) }}</h4>
                <p class="mb-0">Total Views</p>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="search-filters">
        <form method="GET" action="{{ route('super-admin.content.blog') }}">
            <div class="row">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search Posts</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Search by title or content...">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="category" class="form-label">Category</label>
                    <select class="form-control" id="category" name="category">
                        <option value="">All Categories</option>
                        @foreach($categories as $key => $value)
                        <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Create New Post Button -->
    <div class="mb-3">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPostModal">
            <i class="fas fa-plus"></i> Create New Post
        </button>
    </div>

    <!-- Blog Posts Table -->
    <div class="content-table-container">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Views</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $post)
                    <tr>
                        <td>
                            <h6 class="mb-1">{{ $post->title }}</h6>
                            <small class="text-muted">{{ Str::limit($post->excerpt, 100) }}</small>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $categories[$post->category] ?? $post->category }}</span>
                        </td>
                        <td>
                            @if($post->status === 'published')
                            <span class="badge bg-success">Published</span>
                            @elseif($post->status === 'draft')
                            <span class="badge bg-warning">Draft</span>
                            @elseif($post->status === 'scheduled')
                            <span class="badge bg-info">Scheduled</span>
                            @endif
                        </td>
                        <td>{{ number_format($post->views_count) }}</td>
                        <td>{{ \Carbon\Carbon::parse($post->created_at)->format('M d, Y') }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewPost({{ $post->id }})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-warning" onclick="editPost({{ $post->id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deletePost({{ $post->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-file-alt fa-3x mb-3"></i>
                                <h5>No blog posts found</h5>
                                <p>Create your first blog post to get started.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $posts->links() }}
    </div>
</div>

<!-- Create Post Modal -->
<div class="modal fade" id="createPostModal" tabindex="-1" aria-labelledby="createPostModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createPostModalLabel">Create New Blog Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('super-admin.content.blog.create') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                    <option value="scheduled">Scheduled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-control" id="category" name="category" required>
                                    @foreach($categories as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="featured_image" class="form-label">Featured Image</label>
                                <input type="file" class="form-control" id="featured_image" name="featured_image" accept="image/*">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="excerpt" class="form-label">Excerpt</label>
                        <textarea class="form-control" id="excerpt" name="excerpt" rows="3" placeholder="Brief description of the post..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="10" required placeholder="Write your blog post content here..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tags" class="form-label">Tags</label>
                        <input type="text" class="form-control" id="tags" name="tags" placeholder="Tag1, Tag2, Tag3">
                        <small class="form-text text-muted">Separate tags with commas</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Post</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function viewPost(postId) {
    // Implement view post functionality
    console.log('Viewing post:', postId);
}

function editPost(postId) {
    // Implement edit post functionality
    console.log('Editing post:', postId);
}

function deletePost(postId) {
    if (confirm('Are you sure you want to delete this post?')) {
        // Implement delete post functionality
        console.log('Deleting post:', postId);
    }
}
</script>
@endpush
