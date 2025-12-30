<?php

namespace App\Livewire\Pages;

use App\Models\Page;
use App\Models\Website;
use Livewire\Component;
use Livewire\WithPagination;

class Listing extends Component
{
    use WithPagination;

    public Website $website;

    public string $search = '';
    public ?string $selectedPageId = null;

    /** @var int|string */
    public $perPage = 10;

    public array $perPageOptions = [10, 50, 100, 1000, 'all'];

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'page' => ['except' => 1],
    ];

    public ?Page $viewerPage = null;
    public bool $viewerOpen = false;

    public function mount(Website $website)
    {
        $this->website = $website;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingPerPage($value)
    {
        if (!in_array($value, $this->perPageOptions, true)) {
            $this->perPage = 10;
            return;
        }

        $this->resetPage();
    }

    public function selectPage(string $pageId): void
    {
        // logger()->info('Selected page', ['id' => $pageId]);
        $this->selectedPageId = $pageId;
    }

    public function explorePage(string $pageId): void
    {
        $this->selectedPageId = $pageId;
    }

    protected function resolvePerPage(): int
    {
        if ($this->perPage === 'all') {
            // Hard safety cap to avoid accidental 100k row render
            return min(
                5000,
                $this->website->pages()->count()
            );
        }

        return (int) $this->perPage;
    }

    public function openViewer(string $pageId): void
    {
        $this->viewerPage = Page::findOrFail($pageId);
        $this->viewerOpen = true;
    }

    public function closeViewer(): void
    {
        $this->viewerOpen = false;
    }

    public function render()
    {
        $query = $this->website
            ->pages()
            ->when($this->search !== '', function ($query) {
                $query->where('path', 'like', '%' . $this->search . '%');
            })
            ->orderBy('path');

        $pages = $query->paginate(
            $this->resolvePerPage()
        );

        return view('livewire.pages.listing', [
            'pages' => $pages,
            'totalCount' => $pages->total(),
        ]);
    }
}
