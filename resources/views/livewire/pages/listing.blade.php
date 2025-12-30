<div>
    <flux:breadcrumbs class="mb-6">
        <flux:breadcrumbs.item href="{{ route('websites.listing') }}" wire:navigate>Websites</flux:breadcrumbs.item>
        <flux:breadcrumbs.item href="{{ route('websites.detail', $website) }}" wire:navigate>{{ $website->name }}
        </flux:breadcrumbs.item>
        <flux:breadcrumbs.item>Pages</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <x-header :name="$website->name" :url="$website->url" />

    <div
        class="
        flex flex-col rounded-xl border border-gray-200 bg-white dark:border-zinc-800 dark:bg-zinc-900
    ">
        {{-- Header --}}
        <div class="p-4 border-b border-gray-200 dark:border-zinc-800">
            <div class="flex items-center justify-between mb-4 gap-4">
                <h2 class="text-lg font-medium text-gray-900 dark:text-zinc-100">
                    Pages
                </h2>

                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-500 dark:text-zinc-500">
                        {{ $totalCount }} pages
                    </span>

                    {{-- Per-page selector --}}
                    <div class="flex items-center gap-2 text-sm">
                        <span class="text-gray-500 dark:text-zinc-400">
                            Show
                        </span>

                        <select wire:model.live="perPage"
                            class="
                    rounded-md border px-2 py-1
                    bg-white text-gray-900 border-gray-300
                    focus:ring-blue-500 focus:border-blue-500

                    dark:bg-zinc-800 dark:text-zinc-100
                    dark:border-zinc-700
                ">
                            @foreach ($perPageOptions as $option)
                                <option value="{{ $option }}">
                                    {{ is_numeric($option) ? $option : 'All' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <div wire:loading wire:target="perPage" class="text-xs text-gray-400">
                    Updating…
                </div>
            </div>

            {{-- Search --}}
            <div class="relative">
                {{-- Heroicon: magnifying-glass --}}
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="
                    absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4
                    text-gray-400 dark:text-zinc-500
                "
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m21 21-4.35-4.35M17 11a6 6 0 1 1-12 0 6 6 0 0 1 12 0Z" />
                </svg>

                <flux:input wire:model.live.debounce.300ms="search" placeholder="Search pages by path"
                    class=" w-full bg-white text-gray-900 placeholder-gray-400 border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-zinc-800 dark:text-zinc-100 dark:placeholder-zinc-500 dark:border-zinc-700" />

            </div>

        </div>

        {{-- Table --}}
        <div class="overflow-auto">
            <div class="p-4">
                <table class="w-full text-sm">
                    <thead
                        class="
                        text-left
                        text-gray-500
                        dark:text-zinc-400
                    ">
                        <tr
                            class="
                            border-b
                            border-gray-200
                            dark:border-zinc-800
                        ">
                            <th class="pb-2 pr-4">URL</th>
                            <th class="pb-2 pr-4">Path</th>
                            <th class="pb-2 pr-4">Slug</th>
                            <th class="pb-2 pr-4">Date Created</th>
                            <th class="pb-2">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($pages as $page)
                            <tr wire:key="page-{{ $page->id }}"
                                class="border-b transition-colors border-gray-200 hover:bg-gray-50 dark:border-zinc-800/50 dark:hover:bg-zinc-800/30 {{ $selectedPageId === $page->id ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400' : 'text-gray-700 dark:text-zinc-300' }}">
                                <td class="py-3 pr-4 truncate max-w-[300px]" title="{{ $page->url }}">
                                    <button type="button" wire:click="selectPage({{ $page->id }})"
                                        class="w-full text-left cursor-pointer truncate" title="{{ $page->url }}">
                                        {{ $page->url }}
                                    </button>
                                </td>

                                <td class="py-3 pr-4">
                                    {{ $page->path }}
                                </td>

                                <td class="py-3 pr-4 text-gray-400 dark:text-zinc-400">
                                    {{ $page->slug }}
                                </td>

                                <td class="py-3 pr-4 text-xs text-gray-500 dark:text-zinc-500">
                                    {{ $page->created_at->format('Y-m-d H:i') }}
                                </td>

                                <td class="py-3">
                                    <button wire:click.stop="explorePage({{ $page->id }})"
                                        class="px-3 py-1 rounded text-xs flex items-center gap-1 bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-200 transition-colors"
                                        title="Explore in tree">
                                        {{-- Heroicon: eye --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.036 12.322a1 1 0 0 1 0-.644C3.423 7.51 7.36 4.5 12 4.5s8.577 3.01 9.964 7.178a1 1 0 0 1 0 .644C20.577 16.49 16.64 19.5 12 19.5s-8.577-3.01-9.964-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        Explore
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-8 text-gray-500 dark:text-zinc-500">
                                    No pages found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination --}}
                @if ($perPage !== 'all')
                    <div class="mt-4">
                        {{ $pages->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>


</div>
