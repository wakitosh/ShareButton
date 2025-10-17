<?php

declare(strict_types=1);

namespace ShareButton\Site\BlockLayout;

use Laminas\View\Renderer\PhpRenderer;
use Omeka\Api\Representation\SitePageBlockRepresentation;
use Omeka\Api\Representation\SitePageRepresentation;
use Omeka\Api\Representation\SiteRepresentation;
use Omeka\Site\BlockLayout\AbstractBlockLayout;

/**
 *
 */
class ShareButton extends AbstractBlockLayout {
  /**
   * The default partial view script.
   */
  public const PARTIAL_NAME = 'common/block-layout/sharing';

  /**
   *
   */
  public function getLabel() {
    // @translate
    return 'ShareButton';
  }

  /**
   *
   */
  public function form(
    PhpRenderer $view,
    SiteRepresentation $site,
    ?SitePageRepresentation $page = NULL,
    ?SitePageBlockRepresentation $block = NULL,
  ) {
    return '<p>'
            . $view->translate('Display the share buttons of the current page according to site settings.')
            . '</p>';
  }

  /**
   *
   */
  public function render(PhpRenderer $view, SitePageBlockRepresentation $block) {
    $page = $block->page();
    $site = $page->site();
    return $view->partial(self::PARTIAL_NAME, [
      'site' => $site,
      'page' => $page,
      'block' => $block,
    ]);
  }

}
