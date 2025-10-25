<?php
namespace Helpy\Domain;

class RedmineConfig {
    public bool $enabled = false;
    public string $base_url = '';
    public string $project = '';
    public string $new_issue_path = '/projects/{project}/issues/new';

    public function __construct(array $a) {
        $this->enabled = !empty($a['enabled']);
        $this->base_url = (string)($a['base_url'] ?? '');
        $this->project = (string)($a['project'] ?? '');
        $this->new_issue_path = (string)($a['new_issue_path'] ?? '/projects/{project}/issues/new');
    }

    public function buildNewIssueUrl(): ?string {
        if (!$this->enabled || !$this->base_url || !$this->project) return null;
        $path = str_replace('{project}', rawurlencode($this->project), $this->new_issue_path);
        return untrailingslashit($this->base_url) . '/' . ltrim($path, '/');
    }

    public function toArray(): array {
        return [
            'enabled' => $this->enabled,
            'base_url' => $this->base_url,
            'project' => $this->project,
            'new_issue_path' => $this->new_issue_path,
        ];
    }
}
