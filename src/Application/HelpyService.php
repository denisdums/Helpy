<?php
namespace Helpy\Application;

use Helpy\DB\LinkRepository;
use Helpy\DB\RedmineRepository;
use Helpy\Domain\Scope;
use Helpy\Domain\Link;

class HelpyService {
    public function __construct(
        private LinkRepository $links = new LinkRepository(),
        private RedmineRepository $redmine = new RedmineRepository(),
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

    public function getRedmine(): array {
        return $this->redmine->get();
    }
}
