<?php
namespace Helpy\Application;

use Helpy\DB\LinkRepository;
use Helpy\DB\RedmineRepository;
use Helpy\Domain\Scope;

class ImportExportService {
    public function __construct(
        private LinkRepository $links = new LinkRepository(),
        private RedmineRepository $redmine = new RedmineRepository(),
    ) {}

    public function export(): array {
        $grouped = $this->links->getAllGrouped();
        $redmine = $this->redmine->get();
        $postType = $grouped['post_type'] ?? [];
        $pt = [];
        foreach ($postType as $slug => $rows) {
            $pt[$slug] = array_map(function($r){
                return [
                    'label' => $r['label'],
                    'url' => $r['url'],
                    'type' => $r['type'],
                    'icon' => $r['icon'],
                    'target' => $r['target'],
                    'sort_order' => (int)$r['sort_order'],
                ];
            }, $rows);
        }

        return [
            'version' => 1,
            'links' => [
                'global' => array_map(function($r){
                    return [
                        'label' => $r['label'],
                        'url' => $r['url'],
                        'type' => $r['type'],
                        'icon' => $r['icon'],
                        'target' => $r['target'],
                        'sort_order' => (int)$r['sort_order'],
                    ];
                }, $grouped['global'] ?? []),
                'post_type' => $pt,
            ],
            'redmine' => [
                'enabled' => (bool)$redmine['enabled'],
                'base_url' => (string)$redmine['base_url'],
                'project' => (string)$redmine['project'],
                'new_issue_path' => (string)$redmine['new_issue_path'],
            ]
        ];
    }

    public function import(array $data): void {
        // purge et remplit
        $this->links->deleteScope(Scope::GLOBAL, Scope::GLOBAL);
        if (!empty($data['links']['global'])) {
            $this->links->bulkInsert(Scope::GLOBAL, Scope::GLOBAL, $data['links']['global']);
        }
        if (!empty($data['links']['post_type']) && is_array($data['links']['post_type'])) {
            foreach ($data['links']['post_type'] as $slug => $items) {
                $this->links->deleteScope(Scope::POST_TYPE, $slug);
                $this->links->bulkInsert(Scope::POST_TYPE, $slug, $items);
            }
        }
        if (!empty($data['redmine'])) {
            (new RedmineRepository())->save($data['redmine']);
        }
    }
}
