<?php

declare(strict_types=1);

namespace ShareButton\View\Helper;

use Laminas\View\Helper\AbstractHelper;

/**
 * View helper to render share buttons and required assets (Sharing-compatible).
 */
class ShareButton extends AbstractHelper {
  /**
   * The default partial view script.
   */
  public const PARTIAL_NAME = 'common/share-buttons';

  /**
   * Show sharing buttons of the current resource according to site settings.
   *
   * The current resources is an item, a media or a page.
   */
  public function __invoke(): string {
    $view = $this->getView();
    $siteSetting = $view->plugin('siteSetting');

    $enabledMethods = $siteSetting('sharing_methods');
    if (!$enabledMethods) {
      return '';
    }

    $assetUrl = $view->plugin('assetUrl');
    $currentSite = $view->plugin('currentSite');
    $headScript = $view->plugin('headScript');
    $siteSlug = $currentSite()->slug();

    // Facebook SDK is loaded in the partial when needed.
    if (in_array('twitter', $enabledMethods)) {
      $headScript->appendFile(
            'https://platform.twitter.com/widgets.js',
            'text/javascript',
            [
              'id' => 'twitter-js',
              'defer' => 'defer',
              'async' => 'async',
            ]
        );
    }

    if (in_array('tumblr', $enabledMethods)) {
      $headScript->appendFile(
            'https://assets.tumblr.com/share-button.js',
            'text/javascript',
            [
              'id' => 'tumblr-js',
              'defer' => 'defer',
              'async' => 'async',
            ]
        );
    }

    if (in_array('pinterest', $enabledMethods)) {
      $headScript->appendFile(
            'https://assets.pinterest.com/js/pinit.js',
            'text/javascript',
            [
              'id' => 'pinterest',
              'defer' => 'defer',
              'async' => 'async',
            ]
        );
    }

    $headScript->appendFile(
          $assetUrl('js/sharing.js', 'ShareButton'),
          'text/javascript',
          [
            'defer' => 'defer',
            'async' => 'async',
          ]
      );
    $view->headLink()->appendStylesheet($assetUrl('css/sharing.css', 'ShareButton'));

    return $view->partial(self::PARTIAL_NAME, [
      'enabledMethods' => $enabledMethods,
      'itemId' => isset($view->item) ? $view->item->id() : FALSE,
      'mediaId' => isset($view->media) ? $view->media->id() : FALSE,
      'pageId' => isset($view->page) ? $view->page->id() : FALSE,
      'siteSlug' => $siteSlug,
      'displayAsButton' => (bool) $siteSetting('sharing_display_as_button'),
    ]);
  }

}
