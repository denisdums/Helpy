<?php
namespace Helpy\Domain;

if ( ! defined( 'ABSPATH' ) ) exit;

final class Scope {
    public const GLOBAL    = 'global';
    public const POST_TYPE = 'post_type';
    public const TAXONOMY  = 'taxonomy';
    public const TERM      = 'term';
}
