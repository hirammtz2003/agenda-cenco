@if(isset($breadcrumbs) && count($breadcrumbs) > 0)
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-light p-3 rounded">
        @foreach($breadcrumbs as $key => $breadcrumb)
            @if($breadcrumb['url'] && !$loop->last)
                <li class="breadcrumb-item">
                    <a href="{{ $breadcrumb['url'] }}" class="text-decoration-none">
                        <i class="fas {{ $breadcrumb['icon'] ?? 'fa-circle' }} me-1"></i>
                        {{ $breadcrumb['name'] }}
                    </a>
                </li>
            @else
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas {{ $breadcrumb['icon'] ?? 'fa-circle' }} me-1"></i>
                    {{ $breadcrumb['name'] }}
                </li>
            @endif
        @endforeach
    </ol>
</nav>
@endif

<style>
.breadcrumb {
    background-color: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 0;
}
.breadcrumb-item a {
    color: #a01508;
    transition: color 0.3s;
}
.breadcrumb-item a:hover {
    color: #4e0d0d;
    text-decoration: underline !important;
}
.breadcrumb-item.active {
    color: #6c757d;
    font-weight: 500;
}
.breadcrumb-item + .breadcrumb-item::before {
    content: ">";
    color: #6c757d;
}
</style>