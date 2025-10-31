<?php
namespace Helpy\Application;

use Helpy\DB\LinkRepository;
use Helpy\DB\OptionsRepository;
use Helpy\Domain\Scope;
use Helpy\Domain\Link;

class HelpyService {
    public function __construct(
        private LinkRepository $links = new LinkRepository(),
        private OptionsRepository $options = new OptionsRepository(),
    ) {}

    public function getLinksForPostType(string $postType): array {
        $rows = $this->links->getByScope(Scope::POST_TYPE, $postType);
        if (empty($rows)) {
            $rows = $this->links->getByScope(Scope::GLOBAL, Scope::GLOBAL);
        }
        return array_map(fn($r) => (new Link($r))->toArray(), $rows);
    }

    public function getGlobalLinks(): array {
        $rows = $this->links->getByScope(Scope::GLOBAL, Scope::GLOBAL);
        return array_map(fn($r) => (new Link($r))->toArray(), $rows);
    }

    public function getLinksForTerm(string $taxonomy, int $termId): array {
        $rows = $this->links->getByScope(Scope::TERM, (string)$termId);
        if (!empty($rows)) {
            return array_map(fn($r) => (new Link($r))->toArray(), $rows);
        }
        $rows = $this->links->getByScope(Scope::TAXONOMY, $taxonomy);
        if (!empty($rows)) {
            return array_map(fn($r) => (new Link($r))->toArray(), $rows);
        }

        $rows = $this->links->getByScope(Scope::GLOBAL, Scope::GLOBAL);
        return array_map(fn($r) => (new Link($r))->toArray(), $rows);
    }

    public function getTicketing(): array {
        $t = $this->options->get('ticketing', []);
        $t = wp_parse_args($t, [
            'enabled'        => false,
            'base_url'       => '',
            'project'        => '',
            'new_issue_path' => '/new?project={project}',
            'button_label'   => 'Create ticket',
        ]);
        return $t;
    }

    public function buildTicketUrl(array $ctx = []): ?string {
        $t = $this->getTicketing();
        if (empty($t['enabled']) || empty($t['base_url'])) return null;

        $repl = [
            '{project}'  => rawurlencode($t['project'] ?? ''),
            '{title}'    => rawurlencode($ctx['title'] ?? ''),
            '{postId}'   => rawurlencode((string)($ctx['postId'] ?? '')),
            '{postType}' => rawurlencode((string)($ctx['postType'] ?? '')),
            '{taxonomy}' => rawurlencode((string)($ctx['taxonomy'] ?? '')),
            '{termId}'   => rawurlencode((string)($ctx['termId'] ?? '')),
            '{term}'     => rawurlencode((string)($ctx['term'] ?? '')),
        ];
        $path = strtr((string)$t['new_issue_path'], $repl);
        return rtrim($t['base_url'], '/') . '/' . ltrim($path, '/');
    }

    public function getTicketButtonLabel(): string {
        $t = $this->getTicketing();
        return $t['button_label'] ?? 'Create ticket';
    }   
}
