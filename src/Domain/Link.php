<?php
namespace Helpy\Domain;

class Link {
    public string $label;
    public string $url;
    public string $target;
    public int $sort_order;

    public function __construct(array $a) {
        $this->label = (string)($a['label'] ?? '');
        $this->url = (string)($a['url'] ?? '');
        $this->target = (string)($a['target'] ?? '_blank');
        $this->sort_order = intval($a['sort_order'] ?? 0);
    }

    public function toArray(): array {
        return [
            'label' => $this->label,
            'url' => $this->url,
            'target' => $this->target,
            'sort_order' => $this->sort_order,
        ];
    }
}
