<?php
namespace Helpy\Application;

use Helpy\DB\LinkRepository;
use Helpy\DB\OptionsRepository;
use Helpy\Domain\Scope;

class ImportExportService {
    public function __construct(
        private LinkRepository $links = new LinkRepository(),
        private OptionsRepository $options = new OptionsRepository(),
    ) {}

    public function export(): array {
        $grouped = $this->links->getAllGrouped();
        $ticket  = $this->options->get('ticketing', []);

        // Post types
        $pt = [];
        foreach (($grouped['post_type'] ?? []) as $slug => $rows) {
            $pt[$slug] = array_map([$this,'mapRow'], $rows);
        }
        // Taxonomies
        $tx = [];
        foreach (($grouped['taxonomy'] ?? []) as $tax => $rows) {
            $tx[$tax] = array_map([$this,'mapRow'], $rows);
        }

        return [
            'version' => 3,
            'links' => [
                'global'    => array_map([$this,'mapRow'], $grouped['global'] ?? []),
                'post_type' => $pt,
                'taxonomy'  => $tx,
            ],
            'ticketing' => $ticket,
        ];
    }

    private function mapRow(array $r): array {
        return [
            'label'      => $r['label'],
            'url'        => $r['url'],
            'target'     => $r['target'],
            'sort_order' => (int)$r['sort_order'],
        ];
    }

    public function import(array $data): void {
        // Global
        $this->links->deleteScope(Scope::GLOBAL, Scope::GLOBAL);
        if (!empty($data['links']['global'])) {
            $this->links->bulkInsert(Scope::GLOBAL, Scope::GLOBAL, $data['links']['global']);
        }
        // Post type
        if (!empty($data['links']['post_type']) && is_array($data['links']['post_type'])) {
            foreach ($data['links']['post_type'] as $slug => $items) {
                $this->links->deleteScope(Scope::POST_TYPE, $slug);
                $this->links->bulkInsert(Scope::POST_TYPE, $slug, $items);
            }
        }
        // Taxonomy
        if (!empty($data['links']['taxonomy']) && is_array($data['links']['taxonomy'])) {
            foreach ($data['links']['taxonomy'] as $tax => $items) {
                $this->links->deleteScope(Scope::TAXONOMY, $tax);
                $this->links->bulkInsert(Scope::TAXONOMY, $tax, $items);
            }
        }

        // Ticketing
        if (!empty($data['ticketing'])) {
            (new OptionsRepository())->set('ticketing', $data['ticketing']);
        }
    }
}
