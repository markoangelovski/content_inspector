<div>
    <flux:breadcrumbs class="mb-6">
        <flux:breadcrumbs.item href="{{ route('websites.listing') }}" wire:navigate>Websites</flux:breadcrumbs.item>
        <flux:breadcrumbs.item href="{{ route('websites.detail', $website) }}" wire:navigate>{{ $website->name }}
        </flux:breadcrumbs.item>
        <flux:breadcrumbs.item>Pages</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <x-header :name="$website->name" :url="$website->url" />


    @forelse ($website->pages as $page)
        <div>
            <a href="{{ $page->url }}" target="_blank">{{ $page->path }}</a>
        </div>

    @empty
        No pages available
    @endforelse
</div>
