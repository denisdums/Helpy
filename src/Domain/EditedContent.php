<?php
namespace Helpy\Domain;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * The content open in an edit screen: a post, or an order stored by WooCommerce in its own tables (HPOS).
 */
final class EditedContent {
    private function __construct(
        private int $id,
        private string $postType,
        private string $title,
    ) {}

    public static function from(\WP_Post|\WC_Order $edited): self {
        if ($edited instanceof \WC_Order) {
            /* translators: %s: order number */
            $title = sprintf(__('Order #%s', 'helpy'), $edited->get_order_number());

            return new self($edited->get_id(), $edited->get_type(), $title);
        }

        return new self($edited->ID, $edited->post_type, get_the_title($edited));
    }

    public function getPostType(): string {
        return $this->postType;
    }

    /**
     * @return array{postId: int, postType: string, title: string}
     */
    public function getTicketContext(): array {
        return [
            'postId'   => $this->id,
            'postType' => $this->postType,
            'title'    => $this->title,
        ];
    }
}
