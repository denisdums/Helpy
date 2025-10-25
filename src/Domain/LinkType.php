<?php
namespace Helpy\Domain;

final class LinkType {
    public const VIDEO = 'video';
    public const DOC   = 'doc';
    public const CUSTOM= 'custom';

    public static function all(): array {
        return [self::VIDEO, self::DOC, self::CUSTOM];
    }
}
